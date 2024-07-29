# PHP Option Type Documentation

This documentation covers the implementation of a Rust-like Option Type for PHP. The Option type is used for representing optional values. It has two variants: `Some`, representing the presence of a value, and `None`, representing the absence of a value.

## Table of Contents

1. [Option Interface](#option-interface)
2. [Usage](#usage)
3. [Methods](#methods)
4. [Complementary Packages](#complementary-packages)
5. [Static Analysis](#static-analysis)
6. [Contributing](#contributing)
7. [Security Vulnerabilities](#security-vulnerabilities)
8. [License](#license)

## Option Interface

The `Option` interface defines the contract for both `Some` and `None` classes.

```php
<?php

namespace Brimmar\PhpOption\Interfaces;

/**
 * @template T
 */
interface Option
{
    // ... (methods will be documented below)
}
```

## Usage

Here are several examples showcasing the use and utility of the Option type:

### Example 1: User Profile Management

```php
<?php
use Brimmar\PhpOption\Some;
use Brimmar\PhpOption\None;
use Brimmar\PhpOption\Interfaces\Option;

class UserProfile
{
    private $data = [];

    public function setField(string $field, $value): void
    {
        $this->data[$field] = $value;
    }

    public function getField(string $field): Option
    {
        return isset($this->data[$field]) ? new Some($this->data[$field]) : new None();
    }

    public function getDisplayName(): string
    {
        return $this->getField('display_name')
            ->orElse(fn() => $this->getField('username'))
            ->unwrapOr('Anonymous');
    }

    public function getAge(): Option
    {
        return $this->getField('age')
            ->andThen(function($age) {
                return is_numeric($age) && $age > 0 && $age < 120 ? new Some((int)$age) : new None();
            });
    }
}

$profile = new UserProfile();
$profile->setField('username', 'johndoe');
$profile->setField('age', '30');

echo $profile->getDisplayName(); // Output: johndoe

$age = $profile->getAge()
    ->map(fn($age) => "User is $age years old")
    ->unwrapOr("Age not provided or invalid");
echo $age; // Output: User is 30 years old

$email = $profile->getField('email')
    ->map(fn($email) => "Contact: $email")
    ->unwrapOr("No email provided");
echo $email; // Output: No email provided
```

### Example 2: Configuration Management with Option and Result

This example demonstrates how Option and Result types can work together for robust configuration management.

```php
<?php
use Brimmar\PhpOption\Some;
use Brimmar\PhpOption\None;
use Brimmar\PhpResult\Ok;
use Brimmar\PhpResult\Err;

class ConfigManager
{
    private $configs = [];

    public function getConfig(string $key): Option
    {
        return isset($this->configs[$key]) ? new Some($this->configs[$key]) : new None();
    }

    public function setConfig(string $key, $value): void
    {
        $this->configs[$key] = $value;
    }

    public function getRequiredConfig(string $key): Result
    {
        return $this->getConfig($key)
            ->ok()
            ->mapErr(fn() => "Required configuration '$key' is missing");
    }

    public function getDatabaseUrl(): Result
    {
        return $this->getRequiredConfig('database_url')
            ->andThen(function($url) {
                $parsed = parse_url($url);
                return isset($parsed['scheme'], $parsed['host'], $parsed['path'])
                    ? new Ok($url)
                    : new Err("Invalid database URL format");
            });
    }
}

$manager = new ConfigManager();
$manager->setConfig('database_url', 'mysql://localhost/mydb');
$manager->setConfig('debug', true);

$debugMode = $manager->getConfig('debug')
    ->unwrapOr(false);
echo $debugMode ? "Debug mode is ON" : "Debug mode is OFF"; // Output: Debug mode is ON

$databaseUrl = $manager->getDatabaseUrl()
    ->map(fn($url) => "Connected to: $url")
    ->unwrapOr("Failed to connect to database");
echo $databaseUrl; // Output: Connected to: mysql://localhost/mydb

$apiKey = $manager->getRequiredConfig('api_key')
    ->match(
        Ok: fn($key) => "API Key: $key",
        Err: fn($error) => "Error: $error",
    );
echo $apiKey; // Output: Error: Required configuration 'api_key' is missing
```

### Example 3: Optional Chaining with Option Type

This example demonstrates how the Option type can be used to safely chain method calls, similar to optional chaining in other languages.

```php
<?php
use Brimmar\PhpOption\Some;
use Brimmar\PhpOption\None;
use Brimmar\PhpOption\Interfaces\Option;

class Address
{
    public function __construct(public string $street, public string $city, public string $country) {}
}

class User
{
    public function __construct(public string $name, private ?Address $address = null) {}

    public function getAddress(): Option
    {
        return $this->address ? new Some($this->address) : new None();
    }
}

class UserRepository
{
    private $users = [];

    public function addUser(User $user): void
    {
        $this->users[] = $user;
    }

    public function findUserByName(string $name): Option
    {
        $user = array_values(array_filter($this->users, fn($u) => $u->name === $name))[0] ?? null;
        return $user ? new Some($user) : new None();
    }
}

$repo = new UserRepository();
$repo->addUser(new User("Alice", new Address("123 Main St", "Springfield", "USA")));
$repo->addUser(new User("Bob"));

function getUserCountry(UserRepository $repo, string $name): string
{
    return $repo->findUserByName($name)
        ->andThen(fn($user) => $user->getAddress())
        ->map(fn($address) => $address->country)
        ->unwrapOr("Country not found");
}

echo getUserCountry($repo, "Alice"); // Output: USA
echo getUserCountry($repo, "Bob");   // Output: Country not found
echo getUserCountry($repo, "Charlie"); // Output: Country not found
```

### Methods

#### `isSome(): bool`

Returns `true` if the option is a `Some` value.

**Example:**

```php
$option = new Some(42);
echo $option->isSome(); // true

$option = new None();
echo $option->isSome(); // false
```

#### `isSomeAnd(callable $fn): bool`

Returns `true` if the option is a `Some` value and the value inside of it matches a predicate.

**Example:**

```php
$option = new Some(42);
echo $option->isSomeAnd(fn($value) => $value > 40); // true
echo $option->isSomeAnd(fn($value) => $value < 40); // false

$option = new None();
echo $option->isSomeAnd(fn($value) => $value > 0); // false
```

#### `isNone(): bool`

Returns `true` if the option is a `None` value.

**Example:**

```php
$option = new Some(42);
echo $option->isNone(); // false

$option = new None();
echo $option->isNone(); // true
```

#### `iter(): Iterator`

Returns an iterator over the possibly contained value.

**Example:**

```php
$option = new Some(42);
foreach ($option->iter() as $value) {
    echo $value; // 42
}

$option = new None();
foreach ($option->iter() as $value) {
    // This block will never be executed
}
```

#### `unwrap(): mixed`

Returns the contained `Some` value or throws an exception if the value is `None`.

**Example:**

```php
$option = new Some(42);
echo $option->unwrap(); // 42

$option = new None();
$option->unwrap(); // Throws RuntimeException
```

#### `expect(string $msg): mixed`

Returns the contained `Some` value or throws an exception with a provided custom message if the value is `None`.

**Example:**

```php
$option = new Some(42);
echo $option->expect("Value should be present"); // 42

$option = new None();
$option->expect("Value is required"); // Throws RuntimeException with message "Value is required"
```

#### `flatten(): Option`

Converts from `Option<Option<T>>` to `Option<T>`.

**Example:**

```php
$option = new Some(new Some(42));
$flattened = $option->flatten();
echo $flattened->unwrap(); // 42

$option = new Some(new None());
$flattened = $option->flatten();
echo $flattened->isNone(); // true
```

#### `unwrapOr(mixed $default): mixed`

Returns the contained `Some` value or a provided default.

**Example:**

```php
$option = new Some(42);
echo $option->unwrapOr(0); // 42

$option = new None();
echo $option->unwrapOr(0); // 0
```

#### `unwrapOrElse(callable $default): mixed`

Returns the contained `Some` value or computes it from a closure.

**Example:**

```php
$option = new Some(42);
echo $option->unwrapOrElse(fn() => 0); // 42

$option = new None();
echo $option->unwrapOrElse(fn() => 0); // 0
```

#### `map(callable $fn): Option`

Maps an `Option<T>` to `Option<U>` by applying a function to a contained value.

**Example:**

```php
$option = new Some(42);
$mapped = $option->map(fn($x) => $x * 2);
echo $mapped->unwrap(); // 84

$option = new None();
$mapped = $option->map(fn($x) => $x * 2);
echo $mapped->isNone(); // true
```

#### `mapOr(mixed $default, callable $fn): mixed`

Applies a function to the contained value (if any), or returns a default (if not).

**Example:**

```php
$option = new Some(42);
echo $option->mapOr(0, fn($x) => $x * 2); // 84

$option = new None();
echo $option->mapOr(0, fn($x) => $x * 2); // 0
```

#### `mapOrElse(callable $default, callable $fn): mixed`

Applies a function to the contained value (if any), or computes a default (if not).

**Example:**

```php
$option = new Some(42);
echo $option->mapOrElse(fn() => 0, fn($x) => $x * 2); // 84

$option = new None();
echo $option->mapOrElse(fn() => 0, fn($x) => $x * 2); // 0
```

#### `inspect(callable $fn): Option`

Calls the provided closure with a reference to the contained value (if `Some`).

**Example:**

```php
$option = new Some(42);
$option->inspect(function($x) { echo "Got: $x"; }); // Outputs: Got: 42
echo $option->unwrap(); // 42

$option = new None();
$option->inspect(function($x) { echo "Got: $x"; }); // No output
```

#### `okOr(mixed $error, ?string $okClassName = '\Brimmar\PhpResult\Ok'): mixed`

Transforms the `Option<T>` into a `Result<T, E>`, mapping `Some(v)` to `Ok(v)` and `None` to `Err(error)`.

**Example:**

```php
$option = new Some(42);
$result = $option->okOr("No value");
echo $result->unwrap(); // 42

$option = new None();
$result = $option->okOr("No value");
echo $result->unwrapErr(); // "No value"
```

#### `okOrElse(callable $error, ?string $okClassName = '\Brimmar\PhpResult\Ok'): mixed`

Transforms the `Option<T>` into a `Result<T, E>`, mapping `Some(v)` to `Ok(v)` and `None` to `Err(error())`.

**Example:**

```php
$option = new Some(42);
$result = $option->okOrElse(fn() => "No value");
echo $result->unwrap(); // 42

$option = new None();
$result = $option->okOrElse(fn() => "No value");
echo $result->unwrapErr(); // "No value"
```

#### `and(Option $opt): Option`

Returns `None` if the option is `None`, otherwise returns `opt`.

**Example:**

```php
$option1 = new Some(42);
$option2 = new Some(10);
$result = $option1->and($option2);
echo $result->unwrap(); // 10

$option1 = new None();
$option2 = new Some(10);
$result = $option1->and($option2);
echo $result->isNone(); // true
```

#### `andThen(callable $fn): Option`

Returns `None` if the option is `None`, otherwise calls `fn` with the wrapped value and returns the result.

**Example:**

```php
$option = new Some(42);
$result = $option->andThen(fn($x) => new Some($x * 2));
echo $result->unwrap(); // 84

$option = new None();
$result = $option->andThen(fn($x) => new Some($x * 2));
echo $result->isNone(); // true
```

#### `or(Option $opt): Option`

Returns the option if it contains a value, otherwise returns `opt`.

**Example:**

```php
$option1 = new Some(42);
$option2 = new Some(10);
$result = $option1->or($option2);
echo $result->unwrap(); // 42

$option1 = new None();
$option2 = new Some(10);
$result = $option1->or($option2);
echo $result->unwrap(); // 10
```

#### `orElse(callable $fn): Option`

Returns the option if it contains a value, otherwise calls `fn` and returns the result.

**Example:**

```php
$option = new Some(42);
$result = $option->orElse(fn() => new Some(10));
echo $result->unwrap(); // 42

$option = new None();
$result = $option->orElse(fn() => new Some(10));
echo $result->unwrap(); // 10
```

#### `transpose(?string $okClassName = '\Brimmar\PhpResult\Ok', ?string $errClassName = '\Brimmar\PhpResult\Err'): mixed`

Transposes an `Option` of a `Result` into a `Result` of an `Option`.

**Example:**

```php
$option = new Some(new Ok(42));
$result = $option->transpose();
echo $result->unwrap()->unwrap(); // 42

$option = new Some(new Err("error"));
$result = $option->transpose();
echo $result->unwrapErr(); // "error"

$option = new None();
$result = $option->transpose();
echo $result->unwrap()->isNone(); // true
```

#### `xor(Option $opt): Option`

Returns `Some` if exactly one of `$this`, `$opt` is `Some`, otherwise returns `None`.

**Example:**

```php
$option1 = new Some(42);
$option2 = new None();
$result = $option1->xor($option2);
echo $result->unwrap(); // 42

$option1 = new Some(42);
$option2 = new Some(10);
$result = $option1->xor($option2);
echo $result->isNone(); // true
```

#### `zip(Option $other): Option`

Zips `$this` with another `Option`.

**Example:**

```php
$option1 = new Some(42);
$option2 = new Some("hello");
$result = $option1->zip($option2);
print_r($result->unwrap()); // [42, "hello"]

$option1 = new Some(42);
$option2 = new None();
$result = $option1->zip($option2);
echo $result->isNone(); // true
```

#### `zipWith(Option $other, callable $fn): Option`

Zips `$this` and another `Option` with function `$fn`.

**Example:**

```php
$option1 = new Some(42);
$option2 = new Some(10);
$result = $option1->zipWith($option2, fn($a, $b) => $a + $b);
echo $result->unwrap(); // 52

$option1 = new Some(42);
$option2 = new None();
$result = $option1->zipWith($option2, fn($a, $b) => $a + $b);
echo $result->isNone(); // true
```

#### `unzip(): array`

Unzips an option containing a tuple of two options.

**Example:**

```php
$option = new Some([42, "hello"]);
[$a, $b] = $option->unzip();
echo $a->unwrap(); // 42
echo $b->unwrap(); // "hello"

$option = new None();
[$a, $b] = $option->unzip();
echo $a->isNone(); // true
echo $b->isNone(); // true
```

#### `match(callable $Some, callable $None): mixed`

Applies a function to retrieve a contained value.

**Example:**

```php
$option = new Some(42);
$result = $option->match(
    Some: fn($x) => "Value is $x",
    None: fn() => "No value"
);
echo $result; // "Value is 42"

$option = new None();
$result = $option->match(
    Some: fn($x) => "Value is $x",
    None: fn() => "No value"
);
echo $result; // "No value"
```

#### `filter(callable $predicate): Option`

Returns `None` if the option is `None`, otherwise calls `predicate` with the wrapped value and returns:

- `Some(t)` if `predicate` returns `true` (where `t` is the wrapped value)
- `None` if `predicate` returns `false`

**Example:**

```php
$option = new Some(42);
$result = $option->filter(fn($x) => $x > 40);
echo $result->unwrap(); // 42

$option = new Some(42);
$result = $option->filter(fn($x) => $x < 40);
echo $result->isNone(); // true

$option = new None();
$result = $option->filter(fn($x) => $x > 0);
echo $result->isNone(); // true
```

## Complementary Packages

This package works well with the PHP Result Type package, which implements the Result Type. Some methods in this package, such as `okOr` and `okOrElse`, return Result types.

[PhpResult](https://github.com/brimmar/phpresult)

## Static Analysis

We recommend using PHPStan for static code analysis. This package includes custom PHPStan rules to enhance type checking for Option types. To enable these rules, add the following to your PHPStan configuration:

```sh
composer require brimmar/phpstan-rustlike-option-extension --dev
```

```neon
// phpstan.neon
includes:
    - vendor/brimmar/phpstan-rustlike-option-extension/extension.neon
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review our [security policy](SECURITY.md) on how to report security vulnerabilities.

## License

This project is licensed under the MIT License. Please see [LICENSE.md](LICENSE.md) for more information.
