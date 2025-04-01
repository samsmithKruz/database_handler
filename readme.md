# Database Handler
*A reusable PHP database handler supporting SQL (MySQL, PostgreSQL) and NoSQL (MongoDB, Redis).*

![Packagist](https://img.shields.io/packagist/v/samsmithkruz/database-handler) ![License](https://img.shields.io/badge/license-MIT-green)  

## Overview
`database-handler` is a lightweight, flexible, and reusable PHP package that provides a unified interface for interacting with both **SQL** and **NoSQL** databases.  
It simplifies database operations and supports **MySQL, PostgreSQL, MongoDB, and Redis**, making it ideal for modern web applications.

---

## Features
✅ **Multi-Database Support** - Works with MySQL, PostgreSQL, MongoDB, and Redis.  
✅ **Unified API** - Consistent methods across different database types.  
✅ **PSR-4 Autoloading** - Easy to integrate into any PHP project.  
✅ **Flexible Queries** - Raw queries, transactions, and batch operations supported.  
✅ **Exception Handling** - Catches and logs errors gracefully.  
✅ **Lightweight & Fast** - Designed for efficiency with minimal dependencies.  

---

## Installation
Install via **Composer**:  
```sh
composer require samsmithkruz/database-handler
```

---

## Configuration
Create a database configuration array based on your preferred database.  

### MySQL / PostgreSQL Example
```php
use SamsmithKruz\Database\Database;

$config = [
    'driver'   => 'mysql', // or 'pgsql' for PostgreSQL
    'host'     => '127.0.0.1',
    'port'     => 3306,
    'database' => 'test_db',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8mb4'
];

$db = new Database($config);
```

### MongoDB Example
```php
use SamsmithKruz\Database\Database;

$config = [
    'driver'   => 'mongodb',
    'uri'      => 'mongodb://127.0.0.1:27017',
    'database' => 'test_db'
];

$db = new Database($config);
```

### Redis Example
```php
use SamsmithKruz\Database\Database;

$config = [
    'driver'   => 'redis',
    'scheme'   => 'tcp', // Optional
    'host' => '127.0.0.1',
    'timeout'   => '0.0', // Seconds
    'port' => 6379
];

$db = new Database($config);
```

---

## Usage Guide
### Insert Data
```php
$data = ['name' => 'John Doe', 'email' => 'john@example.com'];
$db->insert('users', $data);
```

### Find One Record
```php
$user = $db->findOne('users', ['email' => 'john@example.com']);
print_r($user);
```

### Find Multiple Records
```php
$users = $db->findMany('users', ['status' => 'active']);
```

### Update Data
```php
$db->updateOne('users', ['email' => 'john@example.com'], ['status' => 'inactive']);
```

### Delete Data
```php
$db->deleteOne('users', ['email' => 'john@example.com']);
```

### Raw Query (MongoDB)
```php
$query = [['$match' => ['status' => 'active']]];
$results = $db->rawQuery('users', $query);
```

---

## Running Tests
Ensure you have **PHPUnit** installed and run:  
```sh
vendor/bin/phpunit --configuration phpunit.xml
```

---

## Contributing
We welcome contributions! 🚀  

1. Fork the repository  
2. Create a new feature branch (`git checkout -b feature-name`)  
3. Make your changes  
4. Submit a pull request  

See [`CONTRIBUTING.md`](CONTRIBUTING.md) for more details.  

---

## License
This package is licensed under the **MIT License**.  
See the [`LICENSE`](LICENSE) file for details.  

---

## Author
👤 **Samuel Benny (Smith Kruz)**  
🔗 GitHub: [samsmithkruz](https://github.com/samsmithkruz)  
🔗 LinkedIn: [smithkruz](https://linkedin.com/in/smithkruz)  
