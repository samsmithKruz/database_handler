<?php

namespace SamsmithKruz\Database\Contracts;

interface DatabaseHandlerInterface
{
    public function connect(): void;

    public function query(string $query, array $params = []): mixed;

    public function insert(string $table, array $data): bool;

    public function update(string $table, array $data, array $conditions): bool;

    public function delete(string $table, array $conditions): bool;

    public function select(string $table, array $columns = ['*'], array $conditions = []): array;

    public function beginTransaction(): void;

    public function commitTransaction(): void;

    public function rollbackTransaction(): void;

    public function close(): void;
}
