<?php

namespace SamsmithKruz\Database\Contracts;

interface SQLInterface
{
    /**
     * Prepares and executes an SQL query.
     *
     * @param string $sql The SQL query string.
     * @return SQLInterface The current instance to allow method chaining.
     */
    public function query(string $sql): SQLInterface;

    /**
     * Binds a parameter to the prepared query.
     *
     * @param string $param The parameter placeholder (e.g., ":id").
     * @param mixed $value The value to bind to the parameter.
     * @param string|null $type The data type of the parameter (e.g., PDO::PARAM_INT).
     * @return SQLInterface The current instance to allow method chaining.
     */
    public function bind(string $param, $value, $type = null): SQLInterface;

    /**
     * Executes the prepared statement.
     *
     * @return bool True if execution is successful, false otherwise.
     */
    public function execute(): bool;

    /**
     * Fetches all results from the executed query.
     *
     * @return array An array of result objects.
     */
    public function resultSet(): array;

    /**
     * Fetches a single result from the executed query.
     *
     * @return object The single result object.
     */
    public function single(): object;

    /**
     * Returns the number of rows affected by the last query.
     *
     * @return int The number of rows affected.
     */
    public function rowCount(): int;

    /**
     * Starts a new database transaction.
     *
     * @param string $isolationLevel The isolation level of the transaction (e.g., "READ COMMITTED").
     */
    public function beginTransaction(string $isolationLevel = "SERIALIZABLE"): void;

    /**
     * Commits the current database transaction.
     */
    public function commitTransaction(): void;

    /**
     * Rolls back the current database transaction.
     */
    public function rollbackTransaction(): void;

    /**
     * Retrieves error information for the last database operation.
     *
     * @return array An array containing the error code and message.
     */
    public function errorInfo(): array;

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @return string The last inserted ID.
     */
    public function lastInsertId(): string;

    /**
     * Begins a savepoint within the transaction.
     *
     * @param string $name The name of the savepoint.
     */
    public function beginSavepoint(string $name): void;

    /**
     * Rolls back to a savepoint within the transaction.
     *
     * @param string $name The name of the savepoint.
     */
    public function rollbackSavepoint(string $name): void;

    /**
     * Releases a savepoint within the transaction.
     *
     * @param string $name The name of the savepoint.
     */
    public function releaseSavepoint(string $name): void;

    /**
     * Returns the database's current version or the version of the DBMS being used.
     *
     * @return string The version of the database.
     */
    public function getVersion(): string;

    /**
     * Retrieves the last error code for the current database connection.
     *
     * @return string The error code from the last query.
     */
    public function lastErrorCode(): string;

    /**
     * Retrieves the last error message for the current database connection.
     *
     * @return string The error message from the last query.
     */
    public function lastErrorMessage(): string;

    /**
     * Executes an SQL query and returns the first column of the first row.
     *
     * @param string $sql The SQL query string.
     * @return mixed The value of the first column of the first row.
     */
    public function fetchColumn(string $sql);

    /**
     * Fetches a single row from the executed query.
     *
     * @return array An associative array representing a row.
     */
    public function fetchRow(): array;
}
