<?php

namespace SamsmithKruz\Database\Contracts;

interface NoSQLInterface
{
    /**
     * Inserts a new document or record into a collection/table.
     *
     * @param string $collection The collection or table name.
     * @param array $data The data to be inserted.
     * @return mixed The inserted ID or true on success.
     */
    public function insert(string $collection, array $data);

    /**
     * Finds a single document or record by criteria.
     *
     * @param string $collection The collection or table name.
     * @param array $filter The filter criteria.
     * @return array|null The found document or null if not found.
     */
    public function findOne(string $collection, array $filter): ?array;

    /**
     * Finds multiple documents or records based on a filter.
     *
     * @param string $collection The collection or table name.
     * @param array $filter The filter criteria.
     * @param array $options Optional query options (limit, sort, projection, etc.).
     * @return array The list of matched documents.
     */
    public function findMany(string $collection, array $filter, array $options = []): array;

    /**
     * Updates a single document or record based on a filter.
     *
     * @param string $collection The collection or table name.
     * @param array $filter The filter criteria.
     * @param array $update The update data.
     * @return bool True on success, false on failure.
     */
    public function updateOne(string $collection, array $filter, array $update): bool;

    /**
     * Updates multiple documents or records based on a filter.
     *
     * @param string $collection The collection or table name.
     * @param array $filter The filter criteria.
     * @param array $update The update data.
     * @return int Number of updated documents.
     */
    public function updateMany(string $collection, array $filter, array $update): int;

    /**
     * Deletes a single document or record based on a filter.
     *
     * @param string $collection The collection or table name.
     * @param array $filter The filter criteria.
     * @return bool True on success, false on failure.
     */
    public function deleteOne(string $collection, array $filter): bool;

    /**
     * Deletes multiple documents or records based on a filter.
     *
     * @param string $collection The collection or table name.
     * @param array $filter The filter criteria.
     * @return int Number of deleted documents.
     */
    public function deleteMany(string $collection, array $filter): int;

    /**
     * Counts documents or records that match a filter.
     *
     * @param string $collection The collection or table name.
     * @param array $filter The filter criteria.
     * @return int The count of matching documents.
     */
    public function count(string $collection, array $filter): int;

    /**
     * Executes a raw query for databases that support direct query execution.
     *
     * @param string $query The raw query string.
     * @param array $params Parameters for the query.
     * @return mixed Query result based on implementation.
     */
    public function rawQuery(string $collection, array $query = []): array;

    /**
     * Starts a transaction for databases that support transactions.
     *
     * @return void
     */
    public function beginTransaction(): void;

    /**
     * Commits a transaction for databases that support transactions.
     *
     * @return void
     */
    public function commitTransaction(): void;

    /**
     * Rolls back a transaction for databases that support transactions.
     *
     * @return void
     */
    public function rollbackTransaction(): void;

    /**
     * Retrieves the last inserted ID.
     *
     * @return mixed The last inserted ID.
     */
    public function lastInsertId();

    /**
     * Returns error information from the last executed operation.
     *
     * @return array|null The error details or null if no error occurred.
     */
    public function errorInfo(): ?array;
}
