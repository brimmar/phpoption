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
final class None implements Option
{
    public function isSome(): bool
    {
        return false;
    }

    public function isSomeAnd(callable $fn): bool
    {
        return false;
    }

    public function isNone(): bool
    {
        return true;
    }

    public function iter(): Iterator
    {
        return new ArrayIterator([]);
    }

    public function unwrap(): mixed
    {
        throw new \RuntimeException('Called unwrap on a None value');
    }

    public function expect(string $msg): mixed
    {
        throw new \RuntimeException($msg);
    }

    public function flatten(): Option
    {
        return $this;
    }

    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }

    public function unwrapOrElse(callable $default): mixed
    {
        return $default();
    }

    public function map(callable $fn): Option
    {
        return $this;
    }

    public function mapOr(mixed $default, callable $fn): mixed
    {
        return $default;
    }

    public function mapOrElse(callable $default, callable $fn): mixed
    {
        return $default();
    }

    public function inspect(callable $fn): Option
    {
        return $this;
    }

    public function okOr(mixed $error, ?string $errClassName = '\Brimmar\PhpResult\Err'): mixed
    {
        try {
            $err = new $errClassName($error);

            return $err;
        } catch (ReflectionException $e) {
            return new None;
        }
    }

    public function okOrElse(callable $error, ?string $errClassName = '\Brimmar\PhpResult\Err'): mixed
    {
        try {
            $err = new $errClassName($error());

            return $err;
        } catch (ReflectionException $e) {
            return new None;
        }
    }

    public function and(Option $opt): Option
    {
        return $this;
    }

    public function andThen(callable $fn): self
    {
        return $this;
    }

    public function or(Option $opt): Option
    {
        return $opt;
    }

    public function orElse(callable $fn): Option
    {
        return $fn();
    }

    public function transpose(?string $okClassName = '\Brimmar\PhpResult\Ok', ?string $errClassName = '\Brimmar\PhpResult\Err'): mixed
    {
        try {
            $ok = new $okClassName(new None);

            return $ok;
        } catch (ReflectionException $e) {
            return new None;
        }
    }

    public function xor(Option $opt): Option
    {
        if ($opt instanceof Some) {
            return $opt;
        }

        return new None;
    }

    public function zip(Option $other): Option
    {
        return new None;
    }

    public function zipWith(Option $other, callable $fn): Option
    {
        return new None;
    }

    public function unzip(): array
    {
        return [new None, new None];
    }

    public function match(callable $Some, callable $None): mixed
    {
        return $None();
    }

    public function filter(callable $predicate): Option
    {
        return $this;
    }
}
