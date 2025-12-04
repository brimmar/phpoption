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
    ) {
    }

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

    /**
     * @return Option<T>
     */
    public function flatten(): Option
    {
        if ($this->value instanceof Option) {
            /** @var Option<T> */
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

    /**
     * @template U
     *
     * @param  callable(T): U  $fn
     * @return Option<U>
     */
    public function map(callable $fn): Option
    {
        /** @var Some<U> */
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

    /**
     * @return Option<T>
     */
    public function inspect(callable $fn): Option
    {
        $fn($this->value);

        return $this;
    }

    public function okOr(mixed $error, ?string $okClassName = '\Brimmar\PhpResult\Ok'): mixed
    {
        try {
            /** @var class-string $okClassName */
            $ok = new $okClassName($this->value);

            return $ok;
        } catch (ReflectionException $e) {
            return new None();
        }
    }

    public function okOrElse(callable $error, ?string $okClassName = '\Brimmar\PhpResult\Ok'): mixed
    {
        try {
            /** @var class-string $okClassName */
            $ok = new $okClassName($this->value);

            return $ok;
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
        if ($opt instanceof None) {
            /** @var None<U> */
            return new None();
        }

        return $opt;
    }

    /**
     * @template U
     *
     * @param  callable(T): Option<U>  $fn
     * @return Option<U>
     */
    public function andThen(callable $fn): Option
    {
        return $fn($this->value);
    }

    /**
     * @param  Option<T>  $opt
     * @return Option<T>
     */
    public function or(Option $opt): Option
    {
        return $this;
    }

    /**
     * @param  callable(): Option<T>  $fn
     * @return Option<T>
     */
    public function orElse(callable $fn): Option
    {
        return $this;
    }

    public function transpose(?string $okClassName = '\Brimmar\PhpResult\Ok', ?string $errClassName = '\Brimmar\PhpResult\Err'): mixed
    {
        $okClassName = $okClassName ?? '\Brimmar\PhpResult\Ok';
        $errClassName = $errClassName ?? '\Brimmar\PhpResult\Err';

        /** @phpstan-ignore-next-line */
        if (is_a($this->value, $okClassName)) {
            $val = $this->value;
            /** @phpstan-ignore-next-line */
            $innerValue = $val->unwrap();
            try {
                /** @var class-string $okClassName */
                $ok = new $okClassName(new Some($innerValue));

                return $ok;
            } catch (ReflectionException $e) {
                return new None();
            }
        } elseif (is_a($this->value, $errClassName)) { // @phpstan-ignore-line
            $val = $this->value;
            /** @phpstan-ignore-next-line */
            $innerValue = $val->unwrapErr();
            try {
                /** @var class-string $errClassName */
                $err = new $errClassName($innerValue);

                return $err;
            } catch (ReflectionException $e) {
                return new None();
            }
        }

        return $this;
    }

    /**
     * @return Option<T>
     */
    public function xor(Option $opt): Option
    {
        if ($opt instanceof None) {
            return $this;
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
        if ($other instanceof None) {
            /** @var None<array{T, U}> */
            return new None();
        }

        /** @var Some<mixed> $other */
        $otherSome = $other;

        /** @var Some<array{T, U}> */
        return new Some([$this->value, $other->unwrap()]);
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
        if ($other instanceof None) {
            /** @var None<R> */
            return new None();
        }

        /** @var Some<R> */
        return new Some($fn($this->value, $other->unwrap()));
    }

    /**
     * @return array{Option<mixed>, Option<mixed>}
     */
    public function unzip(): array
    {
        if (! is_array($this->value)) {
            return [new None(), new None()];
        }

        return [new Some($this->value[0]), new Some($this->value[1])];
    }

    public function match(callable $Some, callable $None): mixed
    {
        return $Some($this->value);
    }

    /**
     * @return Option<T>
     */
    public function filter(callable $predicate): Option
    {
        if ($predicate($this->value)) {
            return $this;
        }

        /** @var None<T> */
        return new None();
    }
}
