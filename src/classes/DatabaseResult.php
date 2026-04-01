<?php
declare(strict_types=1);

class DatabaseResult
{
    private PDOStatement $statement;
    private array $rows = [];
    private int $position = 0;
    private bool $fetched = false;

    public function __construct(PDOStatement $statement)
    {
        $this->statement = $statement;
    }

    private function ensureRows(): void
    {
        if ($this->fetched) {
            return;
        }

        $this->rows = $this->statement->fetchAll(PDO::FETCH_ASSOC);
        $this->position = 0;
        $this->fetched = true;
    }

    public function fetch_assoc(): ?array
    {
        $this->ensureRows();

        if ($this->position >= count($this->rows)) {
            return null;
        }

        return $this->rows[$this->position++];
    }

    public function fetch_all($mode = null): array
    {
        $this->ensureRows();
        return $this->rows;
    }

    public function num_rows(): int
    {
        $this->ensureRows();
        return count($this->rows);
    }

    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }

    public function execute(array $params = []): bool
    {
        return $this->statement->execute($params);
    }
}
