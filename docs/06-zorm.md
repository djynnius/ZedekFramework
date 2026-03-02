# ZORM -- Object Relational Mapper

## Overview

ZORM is Zedek's built-in ORM that wraps PDO. It supports MySQL, SQLite, PostgreSQL, and MSSQL. ZORM provides a static API for common database operations without writing raw SQL.

## Configuration

Database settings are stored in `config/db.conf`:

```json
{
    "adapter": "mysql",
    "host": "localhost",
    "port": "3306",
    "db": "myapp",
    "user": "root",
    "pass": "secret"
}
```

Supported adapters: `mysql`, `sqlite`, `postgre`, `mssql`

For SQLite, set `db` to `default` (uses `databases/app.db`) or provide a full path to your database file.

## Basic Usage

### Setting the Table

All operations require setting the active table first:

```php
ZORM::table("users");
```

### Reading Records

```php
// Get all records
$users = ZORM::read();

// Same as read()
$users = ZORM::rows();
```

Returns an array of `stdClass` objects.

### Getting a Single Record

```php
// By ID
$user = ZORM::record(1);

// By column and value
$user = ZORM::record("email", "john@example.com");

// Access fields as properties
echo $user->name;
echo $user->email;
```

### Adding Records

```php
ZORM::table("users");
ZORM::add([
    "name" => "John Doe",
    "email" => "john@example.com",
    "created_by" => 1
]);
```

A `created_at` timestamp is automatically added.

### Updating Records

```php
// Update by ID
ZORM::table("users");
ZORM::update(1, [
    "name" => "Jane Doe",
    "email" => "jane@example.com"
]);

// Update by column and value
ZORM::update("email", "john@example.com", [
    "name" => "John Smith"
]);
```

An `updated_at` timestamp is automatically added.

### Deleting Records

```php
// Remove by ID
ZORM::table("users");
ZORM::remove(1);

// Remove by column and value
ZORM::remove("email", "john@example.com");
```

### Counting Records

```php
ZORM::table("users");
$count = ZORM::count();
```

### Checking Existence

```php
ZORM::table("users");

// By ID
ZORM::exists(1);

// By column and value
ZORM::exists("email", "john@example.com");

// By multiple conditions
ZORM::exists(["email" => "john@example.com", "role" => "admin"]);
```

### Finding Records

```php
ZORM::table("users");

// Search with LIKE
$results = ZORM::find("name", "John");

// First match
$user = ZORM::findFirst("name", "John");

// Last match
$user = ZORM::findLast("name", "John");
```

### Navigation

```php
ZORM::table("users");

$first = ZORM::first();           // First record
$last = ZORM::last();             // Last record
$prev = ZORM::previous($id);      // Previous record
$next = ZORM::next($id);          // Next record
```

## ORMRecord

When you retrieve a single record with `ZORM::record()`, you get an `ORMRecord` object. You can modify its properties and persist changes:

```php
ZORM::table("users");
$user = ZORM::record(1);

// Read
echo $user->name;

// Modify and save
$user->name = "Updated Name";
$user->commit();

// Delete
$user->destroy();
```

## Creating Tables

```php
ZORM::create("posts", [
    "title" => "varchar(255)",
    "body" => "text",
    "author_id" => "int"
]);
```

The `id`, `created_by`, `created_at`, `updated_by`, and `updated_at` columns are added automatically.

## Raw SQL

For queries that go beyond the ORM API:

```php
// Execute any SQL
$result = ZORM::execute("SELECT * FROM users WHERE role = 'admin'");

// Get results as array of objects
$rows = ZORM::rows("SELECT * FROM users WHERE active = 1");
```

## Transactions

```php
ZORM::beginTransaction();

try {
    ZORM::table("accounts");
    ZORM::update(1, ["balance" => 900]);
    ZORM::update(2, ["balance" => 1100]);
    ZORM::commitTransaction();
} catch (\Exception $e) {
    ZORM::rollbackTransaction();
}
```

## Truncating Tables

```php
ZORM::table("logs");
ZORM::truncate("logs");
```
