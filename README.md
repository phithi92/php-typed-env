# PHP Typed Env

`php-typed-env` is a lightweight, **type-safe environment configuration library** for PHP.  
It parses `.env` files, validates environment variables against typed rules, and casts values to the desired PHP types.

This library is designed for **performance**, **strict typing**, and **developer-friendly APIs**.

---

## Features
- **Strict Typing**: Automatically casts `.env` variables to native PHP types (int, bool, float, arrays, etc.).
- **Validation**: Apply built-in constraints like `min`, `max`, `enum`, `pattern`, and more.
- **Fluent API**: Chainable syntax for easy schema definition.
- **Performance**: Optimized for speed and low memory footprint.
- **Zero Dependencies**: Plain PHP implementation.

---

## Installation
```bash
composer require phithi92/php-typed-env
```

---

## Quick Start
```php
use Phithi92\TypedEnv\EnvKit;
use Phithi92\TypedEnv\Schema;

$schema = Schema::build()
    ->string('APP_NAME')
    ->port('APP_PORT')
    ->string('APP_MODE')->enum(['development','production'])
    ->bool('APP_DEBUG')
    ->duration('CACHE_TTL')
    ->json('FEATURE_FLAGS')
    ->list('ALLOWED_HOSTS');

$env = EnvKit::load(__DIR__.'/.env', $schema);

echo $env['APP_NAME'];  // string
echo $env['APP_PORT'];  // int
var_dump($env['FEATURE_FLAGS']); // array
```

## Casting & Constraints

Casters convert raw `.env` values into PHP types, while constraints validate those values. Use casters on their own or chain constraints for additional validation.

```php
use Phithi92\TypedEnv\EnvKit;
use Phithi92\TypedEnv\Schema;

$schema = Schema::build()
    // Cast only: converts to bool without extra validation
    ->bool('APP_DEBUG')

    // Cast with constraints: integer between 1024 and 65535
    ->int('APP_PORT')->min(1024)->max(65535)

    // Another constraint example: restrict values to a set
    ->string('APP_ENV')->enum('dev', 'prod');

$env = EnvKit::load(__DIR__.'/.env', $schema);
```

---

## Exception Handling

`EnvKit::load()` **reads** the `.env` file and builds the environment values.  
It can throw only:

- `DotenvFileException` – the `.env` file is missing or unreadable.

**Syntax checking, casting, and constraint validation happen only when you call `$env->validate()`**, which can throw:

- `DotenvSyntaxException` – syntax errors in the `.env` file
- `MissingEnvVariableException` – a required variable is missing
- `CastException` – a value cannot be cast to the expected type
- `ConstraintException` – a value violates a validation rule

### Example

```php
use Phithi92\TypedEnv\EnvKit;
use Phithi92\TypedEnv\Schema;
use Phithi92\TypedEnv\Exception\{
    DotenvFileException,
    DotenvSyntaxException,
    MissingEnvVariableException,
    CastException,
    ConstraintException
};

$schema = Schema::build()->port('APP_PORT');

try {
    // Step 1: Load the .env file
    $env = EnvKit::load(__DIR__ . '/.env', $schema);
} catch (DotenvFileException $e) {
    // Handle file reading errors (missing/unreadable .env)
    // e.g., log the error or show a user-friendly message
}

try {
    // Step 2: Validate types, constraints, and syntax
    $env->validate();
} catch (MissingEnvVariableException | CastException | ConstraintException | DotenvSyntaxException $e) {
    // Handle validation, casting, or syntax issues
}
```

---

## Available Casters and Return Types

| Method | Return Type | Example Input(s) | Description |
|---------|------------|------------------|-------------|
| `string` | `string` | `foo`, `hello world`, `123` | Raw string value. |
| `bool` | `bool` | `true`, `false`, `1`, `0`, `yes`, `no` | Boolean values parsed flexibly. |
| `int` | `int` | `42`, `0`, `-100` | Casts value to integer. |
| `float` | `float` | `3.14`, `0.99`, `-1.5` | Floating-point values. |
| `duration` | `int` or `DateInterval` | `500ms`, `30s`, `1m`, `1h`, `2d` | Parses human-friendly durations. |
| `json` | `array` or `object` | `{"key":"value"}`, `[1,2,3]` | Decodes JSON values. |
| `list` | `array` | `a,b,c`, `a; b; c`, `item1|item2` | Parses lists with custom delimiters. |
| `url` | `string` | `https://example.com`, `http://localhost:8080` | Validates RFC-compliant URLs. |
| `email` | `string` | `user@example.com` | Valid email address. |
| `ip` | `string` | `192.168.0.1`, `::1` | IPv4 and IPv6 addresses. |
| `uuid` | `string` | `550e8400-e29b-41d4-a716-446655440000` | Any UUID version. |
| `uuid4` | `string` | `123e4567-e89b-12d3-a456-426614174000` | Strict UUID v4. |
| `size` | `int` | `10b`, `2kb`, `5mb`, `1gb` | Parses size units in bytes. |
| `port` | `int` | `80`, `443`, `8080` | Valid TCP/UDP port numbers. |
| `datetime` | `DateTimeImmutable` / `DateTime` | `2024-08-22T10:00:00Z`, `2024-08-22 10:00:00` | Date and time values. |
| `path` | `string` | `/var/www/html`, `./config` | Filesystem paths. |
| `chmod` | `int` | `755`, `644` | Unix file permissions. |
| `hex` | `string` | `a1b2c3`, `ff0044` | Hexadecimal strings. |
| `base64` | `string` | `YWJjZA==`, `SGVsbG8=` | Base64-encoded strings. |
| `numericString` | `string` | `12345`, `00099` | Numeric-only strings, kept as strings. |
| `regex` | `string` | `abc123`, `XYZ-789` | Validates custom regex patterns. |
| `locale` | `string` | `en_US`, `de_DE`, `fr_FR` | Locale strings. |
| `color` | `string` | `#fff`, `#ffffff`, `rgb(255,255,255)` | Colors in HEX or RGB/RGBA. |
| `urlPath` | `string` | `/api/v1/users`, `/products` | URL path strings. |
| `version` | `string` | `1.0.0`, `2.1.3-beta`, `3.0.0-rc1` | Semantic version strings. |
| `array` | `array` | `a,b,c`, `1|2|3` | Splits input into arrays. |

---

## Available Constraints

| Constraint | Description |
|-------------|-------------|
| `min` | Ensures value is greater than or equal to the given number. |
| `max` | Ensures value is less than or equal to the given number. |
| `enum` | Ensures value is one of the provided options. |
| `pattern` | Validates value matches a regex pattern. |
| `exists` | Checks that a path exists on disk. |
| `isFile` | Ensures path is a file. |
| `isDir` | Ensures path is a directory. |
| `isReadable` | Checks file or directory readability. |
| `isWritable` | Checks file or directory writability. |
| `isExecutable` | Checks execution permissions. |

---

## License
MIT License.
