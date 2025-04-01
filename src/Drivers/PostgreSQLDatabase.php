<?php

namespace SamsmithKruz\Database\Drivers;

use PDO;
use PDOException;
use SamsmithKruz\Database\Contracts\SQLInterface;

class PostgreSQLDatabase implements SQLInterface
{
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $port;

    private $dbh;
    private $stmt;

    public function __construct(array $config)
    {
        $this->host = $config['host'];
        $this->user = $config['user'];
        $this->pass = $config['pass'];
        $this->dbname = $config['dbname'];
        $this->port = $config['port'] ?? 5432;  // Default port for PostgreSQL is 5432

        // Set DSN for PostgreSQL
        $dsn = "pgsql:host={$this->host};dbname={$this->dbname};port={$this->port}";
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        try {
            // Create PDO instance for PostgreSQL
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            throw new \Exception("Connection failed: " . $e->getMessage());
        }
    }

    public function query(string $sql): SQLInterface
    {
        try {
            $this->stmt = $this->dbh->prepare($sql);
        } catch (PDOException $e) {
            throw new \Exception("Query preparation failed: " . $e->getMessage());
        }
        return $this;
    }

    public function bind(string $param, $value, $type = null): SQLInterface
    {
        try {
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

        return $this;
    }

    public function execute(): bool
    {
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Execution failed: " . $e->getMessage());
        }
    }

    public function resultSet(): array
    {
        try {
            $this->execute();
            return $this->stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new \Exception("Fetching result set failed: " . $e->getMessage());
        }
    }

    public function single(): object
    {
        try {
            $this->execute();
            return $this->stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new \Exception("Fetching single result failed: " . $e->getMessage());
        }
    }

    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    public function beginTransaction($isolationLevel = "SERIALIZABLE"): void
    {
        try {
            $this->dbh->exec("SET TRANSACTION ISOLATION LEVEL $isolationLevel");
            $this->dbh->beginTransaction();
        } catch (PDOException $e) {
            throw new \Exception("Starting transaction failed: " . $e->getMessage());
        }
    }

    public function commitTransaction(): void
    {
        try {
            $this->dbh->commit();
        } catch (PDOException $e) {
            throw new \Exception("Committing transaction failed: " . $e->getMessage());
        }
    }

    public function rollbackTransaction(): void
    {
        try {
            $this->dbh->rollback();
        } catch (PDOException $e) {
            throw new \Exception("Rolling back transaction failed: " . $e->getMessage());
        }
    }

    public function lastInsertId(): string
    {
        return $this->dbh->lastInsertId();
    }

    public function errorInfo(): array
    {
        return $this->stmt ? $this->stmt->errorInfo() : ['No statement initialized'];
    }

    public function beginSavepoint(string $savepointName): void
    {
        try {
            $this->dbh->exec("SAVEPOINT $savepointName");
        } catch (PDOException $e) {
            throw new \Exception("Creating savepoint failed: " . $e->getMessage());
        }
    }

    public function rollbackSavepoint(string $savepointName): void
    {
        try {
            $this->dbh->exec("ROLLBACK TO SAVEPOINT $savepointName");
        } catch (PDOException $e) {
            throw new \Exception("Rolling back to savepoint failed: " . $e->getMessage());
        }
    }

    public function releaseSavepoint(string $savepointName): void
    {
        try {
            $this->dbh->exec("RELEASE SAVEPOINT $savepointName");
        } catch (PDOException $e) {
            throw new \Exception("Releasing savepoint failed: " . $e->getMessage());
        }
    }

    public function getVersion(): string
    {
        try {
            return $this->dbh->getAttribute(PDO::ATTR_SERVER_VERSION);
        } catch (PDOException $e) {
            throw new \Exception("Fetching PostgreSQL version failed: " . $e->getMessage());
        }
    }

    public function lastErrorCode(): string
    {
        return (string)$this->dbh->errorCode();
    }

    public function lastErrorMessage(): string
    {
        return $this->dbh->errorInfo()[2] ?? 'No error message';
    }

    public function fetchColumn($columnNumber = 0): string
    {
        try {
            $this->execute();
            return $this->stmt->fetchColumn($columnNumber);
        } catch (PDOException $e) {
            throw new \Exception("Fetching column failed: " . $e->getMessage());
        }
    }

    public function fetchRow(): array
    {
        try {
            $this->execute();
            return $this->stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Fetching row failed: " . $e->getMessage());
        }
    }

    public function __destruct()
    {
        $this->dbh = null;
    }
}
