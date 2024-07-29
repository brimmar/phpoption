<?php

declare(strict_types=1);

namespace Brimmar\PhpOption;

use ArrayIterator;
use Brimmar\PhpOption\Interfaces\Option;
use Iterator;
use ReflectionException;

/**
 * @template T
 *
 * @implements Option<T>
 */
final class Some implements Option
{
    public function __construct(
        private mixed $value,
    ) {}

    public function isSome(): bool
    {
        return true;
    }

    public function isSomeAnd(callable $fn): bool
    {
        return $fn($this->value);
    }

    public function isNone(): bool
    {
        return false;
    }

    public function iter(): Iterator
    {
        if (is_array($this->value)) {
            return new ArrayIterator($this->value);
        }

        return new ArrayIterator([$this->value]);
    }

    public function unwrap(): mixed
    {
        return $this->value;
    }

    public function expect(string $msg): mixed
    {
        return $this->value;
    }

    public function flatten(): Option
    {
        if ($this->value instanceof Option) {
            return $this->value;
        }

        return $this;
    }

    public function unwrapOr(mixed $default): mixed
    {
        return $this->value;
    }

    public function unwrapOrElse(callable $default): mixed
    {
        return $this->value;
    }

    public function map(callable $fn): Option
    {
        return new Some($fn($this->value));
    }

    public function mapOr(mixed $default, callable $fn): mixed
    {
        return $fn($this->value);
    }

    public function mapOrElse(callable $default, callable $fn): mixed
    {
        return $fn($this->value);
    }

    public function inspect(callable $fn): Option
    {
        $fn($this->value);

        return $this;
    }

    public function okOr(mixed $error, ?string $okClassName = '\Brimmar\PhpResult\Ok'): mixed
    {
        try {
            $ok = new $okClassName($this->value);

            return $ok;
        } catch (ReflectionException $e) {
            return new None;
        }
    }

    public function okOrElse(callable $error, ?string $okClassName = '\Brimmar\PhpResult\Ok'): mixed
    {
        try {
            $ok = new $okClassName($this->value);

            return $ok;
        } catch (ReflectionException $e) {
            return new None;
        }
    }

    public function and(Option $opt): Option
    {
        if ($opt instanceof None) {
            return new None;
        }

        return $opt;
    }

    public function andThen(callable $fn): Option
    {
        return $fn($this->value);
    }

    public function or(Option $opt): Option
    {
        return $this;
    }

    public function orElse(callable $fn): Option
    {
        return $this;
    }

    public function transpose(?string $okClassName = '\Brimmar\PhpResult\Ok', ?string $errClassName = '\Brimmar\PhpResult\Err'): mixed
    {
        if ($this->value instanceof $okClassName) {
            $innerValue = $this->unwrap()->unwrap();
            try {
                $ok = new $okClassName(new Some($innerValue));

                return $ok;
            } catch (ReflectionException $e) {
                return new None;
            }
        } elseif ($this->value instanceof $errClassName) {
            $innerValue = $this->unwrap()->unwrapErr();
            try {
                $err = new $errClassName($innerValue);

                return $err;
            } catch (ReflectionException $e) {
                return new None;
            }
        }

        return $this;
    }

    public function xor(Option $opt): Option
    {
        if ($opt instanceof None) {
            return $this;
        }

        return new None;
    }

    public function zip(Option $other): Option
    {
        if ($other instanceof None) {
            return new None;
        }

        return new Some([$this->value, $other->value]);
    }

    public function zipWith(Option $other, callable $fn): Option
    {
        if ($other instanceof None) {
            return new None;
        }

        return new Some($fn($this->value, $other->value));
    }

    public function unzip(): array
    {
        if (! is_array($this->value)) {
            return [new None, new None];
        }

        return [new Some($this->value[0]), new Some($this->value[1])];
    }

    public function match(callable $Some, callable $None): mixed
    {
        return $Some($this->value);
    }

    public function filter(callable $predicate): Option
    {
        return $predicate($this->value) ? $this : new None;
    }
}
