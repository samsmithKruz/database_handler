<?php

namespace SamsmithKruz\Database\Drivers;

use SamsmithKruz\Database\Contracts\SQLInterface;
use PDO;
use PDOException;

class MySQLDatabase implements SQLInterface
{
    private PDO $pdo;
    private \PDOStatement $stmt;

    public function __construct(array $config)
    {
        try {
            // Establish the database connection using PDO
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};port={$config['port']}";
            $this->pdo = new PDO($dsn, $config['user'], $config['pass']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function query(string $sql): SQLInterface
    {
        // Prepare the SQL query
        try {
            $this->stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new \Exception("Query preparation failed: " . $e->getMessage());
        }
        return $this;
    }

    public function bind(string $param, $value, $type = null): SQLInterface
    {
        try{
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
        } catch (PDOException $e) {
            throw new \Exception("Binding parameter failed: " . $e->getMessage());
        }
        // Bind the parameter to the prepared statement
        return $this;
    }

    public function execute(): bool
    {
        // Execute the prepared statement
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Execution failed: " . $e->getMessage());
        }
    }

    public function resultSet(): array
    {
        // Fetch all results as an associative array
        try {
            return $this->stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new \Exception("Fetching result set failed: " . $e->getMessage());
        }
    }

    public function single(): object
    {
        // Fetch a single result as an object
        try {
            return $this->stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new \Exception("Fetching single result failed: " . $e->getMessage());
        }
    }

    public function rowCount(): int
    {
        // Return the number of rows affected by the last query
        try {
            return $this->stmt->rowCount();
        } catch (PDOException $e) {
            throw new \Exception("Fetching row count failed: " . $e->getMessage());
        }
    }

    public function beginTransaction(string $isolationLevel = "SERIALIZABLE"): void
    {
        // Start a database transaction
        try {
            $this->pdo->beginTransaction();
            $this->pdo->exec("SET TRANSACTION ISOLATION LEVEL $isolationLevel");
        } catch (PDOException $e) {
            throw new \Exception("Starting transaction failed: " . $e->getMessage());
        }
    }

    public function commitTransaction(): void
    {
        // Commit the current transaction
        try {
            $this->pdo->commit();
        } catch (PDOException $e) {
            throw new \Exception("Commit failed: " . $e->getMessage());
        }
    }

    public function rollbackTransaction(): void
    {
        // Rollback the current transaction
        try {
            $this->pdo->rollBack();
        } catch (PDOException $e) {
            throw new \Exception("Rollback failed: " . $e->getMessage());
        }
    }

    public function errorInfo(): array
    {
        // Retrieve error information for the last operation
        return $this->stmt->errorInfo();
    }

    public function lastInsertId(): string
    {
        // Return the last insert ID
        try {
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new \Exception("Fetching last insert ID failed: " . $e->getMessage());
        }
    }

    public function beginSavepoint(string $name): void
    {
        // Begin a savepoint within the transaction
        try {
            $this->pdo->exec("SAVEPOINT $name");
        } catch (PDOException $e) {
            throw new \Exception("Begin savepoint failed: " . $e->getMessage());
        }
    }

    public function rollbackSavepoint(string $name): void
    {
        // Rollback to a savepoint within the transaction
        try {
            $this->pdo->exec("ROLLBACK TO SAVEPOINT $name");
        } catch (PDOException $e) {
            throw new \Exception("Rollback to savepoint failed: " . $e->getMessage());
        }
    }

    public function releaseSavepoint(string $name): void
    {
        // Release a savepoint within the transaction
        try {
            $this->pdo->exec("RELEASE SAVEPOINT $name");
        } catch (PDOException $e) {
            throw new \Exception("Release savepoint failed: " . $e->getMessage());
        }
    }

    public function getVersion(): string
    {
        // Get the version of the database
        try {
            return $this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
        } catch (PDOException $e) {
            throw new \Exception("Getting version failed: " . $e->getMessage());
        }
    }

    public function lastErrorCode(): string
    {
        // Return the last error code for the connection
        return $this->pdo->errorCode();
    }

    public function lastErrorMessage(): string
    {
        // Return the last error message for the connection
        $errorInfo = $this->pdo->errorInfo();
        return $errorInfo[2] ?? '';
    }

    public function fetchColumn(string $sql)
    {
        // Execute a query and fetch the first column of the first row
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new \Exception("Fetching column failed: " . $e->getMessage());
        }
    }

    public function fetchRow(): array
    {
        // Fetch a single row from the result set
        try {
            return $this->stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Fetching row failed: " . $e->getMessage());
        }
    }
}
