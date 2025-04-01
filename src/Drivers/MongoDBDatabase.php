<?php

namespace SamsmithKruz\Database\Drivers;


use MongoDB\Client;
use MongoDB\Driver\Exception\Exception;
use SamsmithKruz\Database\Contracts\NoSQLInterface;

class MongoDBDatabase implements NoSQLInterface
{
    private Client $client;
    private $database;
    private $lastError;

    public function __construct(array $config)
    {
        try {
            $uri = $config['uri'] ?? 'mongodb://127.0.0.1:27017';
            $this->client = new Client($uri);
            $this->database = $this->client->selectDatabase($config['database']);
        } catch (\Exception $e) {
            throw new \Exception("MongoDB connection failed: " . $e->getMessage());
        }
    }

    public function updateMany(string $collection, array $filter, array $update): int
    {
        try {
            $result = $this->database->$collection->updateMany($filter, ['$set' => $update]);
            return $result->getModifiedCount();
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return 0;
        }
    }
    public function rawQuery(string $collection, array $query = []): array
    {
        try {
            $cursor = $this->database->$collection->aggregate($query);
            return iterator_to_array($cursor);
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }
    public function lastInsertId(): ?string
    {
        throw new \Exception("MongoDB generates ObjectId automatically.");
        // MongoDB does not have a concept of last insert ID like SQL databases.
    }

    public function insert(string $collection, array $data)
    {
        try {
            $result = $this->database->$collection->insertOne($data);
            return $result->getInsertedId();
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function findOne(string $collection, array $filter): ?array
    {
        try {
            $document = $this->database->$collection->findOne($filter);
            return $document ? (array) $document : null;
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return null;
        }
    }

    public function findMany(string $collection, array $filter, array $options = []): array
    {
        try {
            $cursor = $this->database->$collection->find($filter, $options);
            return iterator_to_array($cursor);
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function updateOne(string $collection, array $filter, array $update): bool
    {
        try {
            $result = $this->database->$collection->updateOne($filter, ['$set' => $update]);
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function deleteOne(string $collection, array $filter): bool
    {
        try {
            $result = $this->database->$collection->deleteOne($filter);
            return $result->getDeletedCount() > 0;
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function deleteMany(string $collection, array $filter): int
    {
        try {
            $result = $this->database->$collection->deleteMany($filter);
            return $result->getDeletedCount();
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return 0;
        }
    }

    public function count(string $collection, array $filter): int
    {
        try {
            return $this->database->$collection->countDocuments($filter);
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return 0;
        }
    }

    /**
     * Executes custom MongoDB commands (flexibility for advanced operations).
     */
    public function command(array $command)
    {
        try {
            return $this->database->command($command)->toArray();
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function beginTransaction(): void
    {
        try {
            $this->client->startSession()->startTransaction();
        } catch (\Exception $e) {
            throw new \Exception("Failed to start transaction: " . $e->getMessage());
        }
    }

    public function commitTransaction(): void
    {
        try {
            $this->client->startSession()->commitTransaction();
        } catch (\Exception $e) {
            throw new \Exception("Failed to commit transaction: " . $e->getMessage());
        }
    }

    public function rollbackTransaction(): void
    {
        try {
            $this->client->startSession()->abortTransaction();
        } catch (\Exception $e) {
            throw new \Exception("Failed to rollback transaction: " . $e->getMessage());
        }
    }

    public function errorInfo(): ?array
    {
        return $this->lastError ? [$this->lastError] : null;
    }
}
