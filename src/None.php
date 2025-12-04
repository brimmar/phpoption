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

    /**
     * @return Option<T>
     */
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

    /**
     * @template U
     *
     * @param  callable(T): U  $fn
     * @return Option<U>
     */
    public function map(callable $fn): Option
    {
        /** @var None<U> */
        return new None();
    }

    public function mapOr(mixed $default, callable $fn): mixed
    {
        return $default;
    }

    public function mapOrElse(callable $default, callable $fn): mixed
    {
        return $default();
    }

    /**
     * @return Option<T>
     */
    public function inspect(callable $fn): Option
    {
        return $this;
    }

    public function okOr(mixed $error, ?string $errClassName = '\Brimmar\PhpResult\Err'): mixed
    {
        try {
            /** @var class-string $errClassName */
            $err = new $errClassName($error);

            return $err;
        } catch (ReflectionException $e) {
            return new None();
        }
    }

    public function okOrElse(callable $error, ?string $errClassName = '\Brimmar\PhpResult\Err'): mixed
    {
        try {
            /** @var class-string $errClassName */
            $err = new $errClassName($error());

            return $err;
        } catch (ReflectionException $e) {
            return new None();
        }
    }

    /**
     * @template U
     *
     * @param  Option<U>  $opt
     * @return Option<U>
     */
    public function and(Option $opt): Option
    {
        /** @var None<U> */
        return new None();
    }

    /**
     * @template U
     *
     * @param  callable(T): Option<U>  $fn
     * @return Option<U>
     */
    public function andThen(callable $fn): Option
    {
        /** @var None<U> */
        return new None();
    }

    /**
     * @param  Option<T>  $opt
     * @return Option<T>
     */
    public function or(Option $opt): Option
    {
        return $opt;
    }

    /**
     * @param  callable(): Option<T>  $fn
     * @return Option<T>
     */
    public function orElse(callable $fn): Option
    {
        return $fn();
    }

    public function transpose(?string $okClassName = '\Brimmar\PhpResult\Ok', ?string $errClassName = '\Brimmar\PhpResult\Err'): mixed
    {
        try {
            /** @var class-string $okClassName */
            $ok = new $okClassName(new None());

            return $ok;
        } catch (ReflectionException $e) {
            return new None();
        }
    }

    /**
     * @return Option<T>
     */
    public function xor(Option $opt): Option
    {
        if ($opt instanceof Some) {
            return $opt;
        }

        /** @var None<T> */
        return new None();
    }

    /**
     * @template U
     *
     * @param  Option<U>  $other
     * @return Option<array{T, U}>
     */
    public function zip(Option $other): Option
    {
        /** @var None<array{T, U}> */
        return new None();
    }

    /**
     * @template U
     * @template R
     *
     * @param  Option<U>  $other
     * @param  callable(T, U): R  $fn
     * @return Option<R>
     */
    public function zipWith(Option $other, callable $fn): Option
    {
        /** @var None<R> */
        return new None();
    }

    /**
     * @return array{Option<mixed>, Option<mixed>}
     */
    public function unzip(): array
    {
        return [new None(), new None()];
    }

    public function match(callable $Some, callable $None): mixed
    {
        return $None();
    }

    /**
     * @return Option<T>
     */
    public function filter(callable $predicate): Option
    {
        return $this;
    }
}
