<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once __DIR__ . '/../src/classes/Auth.php';
require_once __DIR__ . '/../src/classes/CSRFToken.php';

$logger = new Logger(LOG_BASE_PATH);
$auth = new Auth($db, $logger);
$error = '';
$username = '';
$showCaptcha = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Validator::sanitizeString($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrfToken = $_POST['_csrf_token'] ?? '';

    if (!CSRFToken::validate($csrfToken)) {
        $error = 'Token CSRF tidak valid.';
        $logger->warning('CSRF validation failed during login', ['ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
    } else {
        $result = $auth->login($username, $password, $_POST['g-recaptcha-response'] ?? null);
        if ($result['success']) {
            redirect('admin/dashboard.php');
        }
        $error = $result['message'];
    }
}

$showCaptcha = $auth->requiresCaptcha($username);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SMP Muhammadiyah</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style nonce="<?php echo htmlspecialchars(CSP_NONCE, ENT_QUOTES, 'UTF-8'); ?>">
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(0, 82, 204, 0.3);
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
        }
        
        .btn-login:hover {
            background: #003d99;
        }
        
        .error-message {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .info-box {
            background: #f0f8ff;
            border: 1px solid #0052CC;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🔐 Admin Panel</h1>
            <p>SMP Muhammadiyah Tahfidz Salatiga</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <?php echo CSRFToken::inputField(); ?>
            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response" value="">

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">Login</button>
        </form>
        
        <div class="info-box">
            <strong>Demo Akun:</strong><br>
            Username: admin<br>
            Password: admin123
        </div>
    </div>

    <?php if ($showCaptcha && RECAPTCHA_SITE_KEY): ?>
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo htmlspecialchars(RECAPTCHA_SITE_KEY, ENT_QUOTES, 'UTF-8'); ?>"></script>
        <script nonce="<?php echo htmlspecialchars(CSP_NONCE, ENT_QUOTES, 'UTF-8'); ?>">
            grecaptcha.ready(function() {
                grecaptcha.execute('<?php echo htmlspecialchars(RECAPTCHA_SITE_KEY, ENT_QUOTES, 'UTF-8'); ?>', {action: 'login'}).then(function(token) {
                    document.getElementById('g-recaptcha-response').value = token;
                });
            });
        </script>
    <?php elseif ($showCaptcha): ?>
        <script>
            console.warn('reCAPTCHA site key is not configured yet. Complete .env with RECAPTCHA_SITE_KEY and RECAPTCHA_SECRET_KEY.');
        </script>
    <?php endif; ?>
</body>
</html>
