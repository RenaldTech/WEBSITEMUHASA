<?php
declare(strict_types=1);

class Auth
{
    private Database $db;
    private Logger $logger;
    private int $maxAttempts = 5;
    private int $lockoutPeriod = 900;
    private int $captchaThreshold = 3;

    public function __construct(Database $db, Logger $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function login(string $username, string $password, ?string $recaptchaToken = null): array
    {
        $username = trim($username);

        if ($this->isLockedOut($username)) {
            return ['success' => false, 'message' => 'Akun dikunci sementara karena terlalu banyak percobaan gagal'];
        }

        if ($this->requiresCaptcha($username)) {
            if (empty(RECAPTCHA_SECRET_KEY)) {
                // RECAPTCHA_SECRET_KEY not set — CAPTCHA disabled. Set this in .env to enable brute force protection.
                $this->logger->warning('RECAPTCHA_SECRET_KEY not set — CAPTCHA disabled. Set this in .env to enable brute force protection.', ['username' => $username]);
            } elseif (!$this->validateRecaptcha($recaptchaToken)) {
                $this->recordLoginAttempt($username, false);
                $this->logger->warning('reCAPTCHA validation failed', ['username' => $username, 'ip' => $this->getIp()]);
                return ['success' => false, 'message' => 'Validasi reCAPTCHA gagal'];
            }
        }

        $user = $this->db->fetchOne('SELECT id, username, password, role, email FROM users WHERE username = ?', [$username]);

        if (empty($user) || !password_verify($password, $user['password'])) {
            $this->recordLoginAttempt($username, false);
            $this->logger->warning('Login failed', ['username' => $username, 'ip' => $this->getIp()]);
            return ['success' => false, 'message' => 'Username atau password salah'];
        }

        $this->recordLoginAttempt($username, true);
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];

        return ['success' => true];
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public function requiresCaptcha(string $username): bool
    {
        return $this->failedAttempts($username) >= $this->captchaThreshold;
    }

    public function isLockedOut(string $username): bool
    {
        return $this->failedAttempts($username) >= $this->maxAttempts;
    }

    private function failedAttempts(string $username): int
    {
        $row = $this->db->fetchOne(
            'SELECT COUNT(*) AS total FROM failed_logins WHERE username = ? AND success = 0 AND attempt_at > DATE_SUB(NOW(), INTERVAL ? SECOND)',
            [$username, $this->lockoutPeriod]
        );

        return (int)($row['total'] ?? 0);
    }

    private function recordLoginAttempt(string $username, bool $success): void
    {
        try {
            $this->db->execute(
                'INSERT INTO failed_logins (username, ip_address, attempt_at, success) VALUES (?, ?, NOW(), ?)',
                [$username, $this->getIp(), $success ? 1 : 0]
            );
        } catch (Throwable $e) {
            $this->logger->error('Unable to record failed login attempt', ['exception' => $e->getMessage()]);
        }
    }

    private function validateRecaptcha(?string $token): bool
    {
        if (empty(RECAPTCHA_SECRET_KEY)) {
            $this->logger->warning('RECAPTCHA_SECRET_KEY not set — validation skipped. Set this in .env to enable brute force protection.');
            return true;
        }

        if (empty($token)) {
            return false;
        }

        if (!function_exists('curl_init')) {
            $this->logger->error('cURL is not available for reCAPTCHA verification');
            return false;
        }

        $payload = http_build_query([
            'secret' => RECAPTCHA_SECRET_KEY,
            'response' => $token,
            'remoteip' => $this->getIp(),
        ]);

        $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $result = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($result === false || $httpCode !== 200) {
            $this->logger->error('reCAPTCHA request failed', ['http_code' => $httpCode, 'error' => $error]);
            return false;
        }

        $data = json_decode($result, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error('reCAPTCHA response JSON decode failed', ['error' => json_last_error_msg()]);
            return false;
        }

        if (empty($data['success']) || ($data['score'] ?? 0) < 0.5) {
            $this->logger->warning('reCAPTCHA validation failed', ['response' => $data]);
            return false;
        }

        return true;
    }

    private function getIp(bool $trustProxy = false): string
    {
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        if (!$trustProxy) {
            return $remoteAddr;
        }

        if (!in_array($remoteAddr, TRUSTED_PROXY_IPS, true)) {
            return $remoteAddr;
        }

        $xff = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
        if ($xff === '') {
            return $remoteAddr;
        }

        foreach (explode(',', $xff) as $forwardedIp) {
            $forwardedIp = trim($forwardedIp);
            if (filter_var($forwardedIp, FILTER_VALIDATE_IP)) {
                return $forwardedIp;
            }
        }

        return $remoteAddr;
    }
}
