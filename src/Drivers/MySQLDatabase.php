<?php

namespace SamsmithKruz\Database\Drivers;

use PDO;
use PDOException;
use SamsmithKruz\Database\Contracts\DatabaseHandlerInterface;

class MySQLDatabase implements DatabaseHandlerInterface
{
    private PDO $connection;

    public function __construct(private array $config)
    {
        $this->connect();
    }

    public function connect(): void
    {
        try {
            $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']};port={$this->config['port']}";
            $this->connection = new PDO($dsn, $this->config['username'], $this->config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            throw new \Exception("MySQL Connection Failed: " . $e->getMessage());
        }
    }

    public function query(string $query, array $params = []): mixed
    {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    public function insert(string $table, array $data): bool
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

        return $this->query($sql, array_values($data))->rowCount() > 0;
    }

    public function update(string $table, array $data, array $conditions): bool
    {
        $setClause = implode(", ", array_map(fn($key) => "$key = ?", array_keys($data)));
        $whereClause = implode(" AND ", array_map(fn($key) => "$key = ?", array_keys($conditions)));

        $sql = "UPDATE $table SET $setClause WHERE $whereClause";
        return $this->query($sql, array_merge(array_values($data), array_values($conditions)))->rowCount() > 0;
    }

    public function delete(string $table, array $conditions): bool
    {
        $whereClause = implode(" AND ", array_map(fn($key) => "$key = ?", array_keys($conditions)));
        $sql = "DELETE FROM $table WHERE $whereClause";

        return $this->query($sql, array_values($conditions))->rowCount() > 0;
    }

    public function select(string $table, array $columns = ['*'], array $conditions = []): array
    {
        $columnsString = implode(", ", $columns);
        $whereClause = $conditions ? "WHERE " . implode(" AND ", array_map(fn($key) => "$key = ?", array_keys($conditions))) : "";
        $sql = "SELECT $columnsString FROM $table $whereClause";

        return $this->query($sql, array_values($conditions))->fetchAll();
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commitTransaction(): void
    {
        $this->connection->commit();
    }

    public function rollbackTransaction(): void
    {
        $this->connection->rollBack();
    }

    public function close(): void
    {
        $this->connection = null;
    }
}
