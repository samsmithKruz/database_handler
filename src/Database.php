<?php

namespace SamsmithKruz\Database;

use Exception;
use SamsmithKruz\Database\Drivers\{
    MySQLDatabase,
    PostgreSQLDatabase
};

class Database
{
    private $handler; // Will be dynamically assigned based on driver

    /**
     * Constructor that initializes the appropriate database handler
     * based on the configuration passed (e.g., MySQL, PostgreSQL, etc.).
     *
     * @param array $config Configuration array containing driver and connection details.
     * @throws Exception If the specified driver is not supported.
     */
    public function __construct(array $config)
    {
        $this->setDriver($config);
    }

    /**
     * Sets the database driver dynamically based on the configuration.
     * This allows different types of databases (SQL or NoSQL) to be supported.
     *
     * @param array $config Configuration array containing driver and connection details.
     * @throws Exception If the specified driver is not supported.
     */
    private function setDriver(array $config)
    {
        // Ensure the 'driver' key exists in the configuration
        if (!isset($config['driver'])) {
            $config['driver'] = 'mysql'; // Default to MySQL if not specified
        }

        switch ($config['driver']) {
            case 'mysql':
                // Dynamically instantiate the MySQL handler
                $this->handler = new MySQLDatabase($config);
                break;

            // Extend with other SQL or NoSQL drivers here (e.g., PostgreSQL, MongoDB)
            case 'pgsql':
                $this->handler = new PostgreSQLDatabase($config);
                break;

            // case 'mongodb':
            //     $this->handler = new MongoDBDatabase($config);
            //     break;

            default:
                throw new Exception("Driver {$config['driver']} is not supported.");
        }
    }

    /**
     * Magic method to forward calls to the handler for method chaining.
     * This allows methods like query, execute, fetch, etc., to be called directly on the Database instance.
     * 
     * @param string $method The method to forward to the handler.
     * @param array $args Arguments for the method being called.
     * @return mixed The result of the method call on the handler.
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->handler, $method)) {
            throw new Exception("Method {$method} does not exist on the handler.");
        }

        // Call the method on the handler and return its result (supports chaining)
        return call_user_func_array([$this->handler, $method], $args);
    }
}
