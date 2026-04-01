<?php
declare(strict_types=1);

require_once __DIR__ . '/DatabaseResult.php';

/**
 * Database wrapper using PDO prepared statements only.
 * WARNING: always use fetchOne(), fetchAll(), or execute() with bound parameters.
 */
class Database
{
    private PDO $pdo;

    public function __construct(string $dsn, string $username, string $password, array $options = [])
    {
        $this->pdo = new PDO($dsn, $username, $password, $options + DB_OPTIONS);
    }

    public function prepare(string $sql): PDOStatement
    {
        $this->_validateQuery($sql);
        return $this->pdo->prepare($sql);
    }

    public function query(string $sql)
    {
        $this->_validateQuery($sql);
        $statement = $this->pdo->query($sql);
        return new DatabaseResult($statement);
    }

    public function execute(string $sql, array $params = []): bool
    {
        $this->_validateQuery($sql);
        $stmt = $this->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * @deprecated Use prepared statements with execute(), fetchOne(), or fetchAll() instead.
     */
    public function escapeString(string $value): string
    {
        trigger_error('Database::escapeString is deprecated. Use prepared statements instead.', E_USER_DEPRECATED);
        $quoted = $this->pdo->quote($value);
        if ($quoted === false) {
            return trim($value);
        }
        return substr($quoted, 1, -1);
    }

    public function close(): void
    {
        // PDO does not require explicit close. Destroying object closes connection.
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $this->_validateQuery($sql);
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchOne(string $sql, array $params = []): ?array
    {
        $this->_validateQuery($sql);
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    private function _validateQuery(string $sql): void
    {
        if (preg_match('/\$[a-zA-Z_][a-zA-Z0-9_]*|\{\$[a-zA-Z_][a-zA-Z0-9_]*\}/', $sql)) {
            throw new InvalidArgumentException('Direct variable interpolation in SQL queries is prohibited. Use bound parameters instead.');
        }
    }
}
