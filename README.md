# Laravel Model Connections
[![Latest Version on Packagist](https://img.shields.io/packagist/v/jackbayliss/laravel-model-connection.svg?style=flat-square)](https://packagist.org/packages/jackbayliss/laravel-model-connection) [![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jackbayliss/laravel-model-connections/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jackbayliss/laravel-model-connections/actions?query=workflow%3Arun-tests+branch%3Amain) [![Total Downloads](https://img.shields.io/packagist/dt/jackbayliss/laravel-model-connection.svg?style=flat-square)](https://packagist.org/packages/jackbayliss/laravel-model-connection)

Define separate read and write database connections per Eloquent model.

## Why?

Laravel has built-in support for read/write connection splitting, but it's configured at the **connection level** in `config/database.php` — meaning every model using that connection shares the same read/write behaviour:
```php
// config/database.php — this applies globally to all models on this connection
'mysql' => [
    'read'  => ['host' => '192.168.1.2'],
    'write' => ['host' => '196.168.1.3'],
],
```

There's no first-party way to say "this specific model should read from a replica, but that model should always hit the primary." This package fills that gap — giving you per-model control over read and write connections without touching your connection config.

## Supports
- Laravel 12

## Installation
```bash
composer require jackbayliss/laravel-model-connection
```

## Usage
Add the `HasReadWriteConnection` trait to your model and define `$readConnection` and `$writeConnection`:
```php
use JackBayliss\LaravelModelConnection\Traits\HasReadWriteConnection;

class User extends Model
{
    use HasReadWriteConnection;

    protected $readConnection  = 'mysql_replica';
    protected $writeConnection = 'mysql_primary';
}
```

All reads (`first()`, `get()`, relationships, eager loads) will go to `mysql_replica`, and all writes (`create()`, `update()`, `delete()`) will go to `mysql_primary`.

If `$readConnection` or `$writeConnection` are not set, the trait falls back to the model's default `$connection`.

## Enum Support
You can use a backed enum instead of a plain string:
```php
enum DatabaseConnection: string
{
    case Primary = 'mysql_primary';
    case Replica = 'mysql_replica';
}

class User extends Model
{
    use HasReadWriteConnection;

    protected $readConnection  = DatabaseConnection::Replica;
    protected $writeConnection = DatabaseConnection::Primary;
}
```

## Runtime Override
You can override the connections at runtime using the fluent setters:
```php
$user = (new User)
    ->setReadConnection('mysql_replica_2')
    ->setWriteConnection('mysql_primary_2');
```

## Available Methods
| Method | Description |
|---|---|
| `getReadConnection()` | Returns the `Connection` instance for reads |
| `getWriteConnection()` | Returns the `Connection` instance for writes |
| `getReadConnectionName()` | Returns the read connection name as a string |
| `getWriteConnectionName()` | Returns the write connection name as a string |
| `setReadConnection($connection)` | Override the read connection at runtime |
| `setWriteConnection($connection)` | Override the write connection at runtime |

## Testing
```bash
composer test
```

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities
Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits
- [Jack Bayliss](https://github.com/jackbayliss)

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
