<?php

declare(strict_types=1);

namespace Brimmar\PhpOption\Interfaces;

use Iterator;

/**
 * @template T
 */
interface Option
{
    /**
     * @phpstan-assert-if-true Some<T> $this
     */
    public function isSome(): bool;

    /**
     * @param  callable(T): bool  $fn
     *
     * @phpstan-assert-if-true Some<T> $this
     */
    public function isSomeAnd(callable $fn): bool;

    /**
     * @phpstan-assert-if-true None $this
     */
    public function isNone(): bool;

    public function iter(): Iterator;

    /**
     * @return T
     *
     * @throws \RuntimeException
     */
    public function unwrap(): mixed;

    /**
     * @return T
     *
     * @throws \RuntimeException
     */
    public function expect(string $msg): mixed;

    /**
     * @return T
     */
    public function flatten(): Option;

    /**
     * @template D
     *
     * @param  D  $default
     * @return T|D
     */
    public function unwrapOr(mixed $default): mixed;

    /**
     * @template D
     *
     * @param  callable(): D  $default
     * @return D
     */
    public function unwrapOrElse(callable $default): mixed;

    /**
     * @template U
     *
     * @param  callable(T): U  $fn
     * @return Option<U>
     */
    public function map(callable $fn): Option;

    /**
     * @template U
     *
     * @param  U  $default
     * @param  callable(T): U  $fn
     * @return U
     */
    public function mapOr(mixed $default, callable $fn): mixed;

    /**
     * @template U
     *
     * @param  callable(): U  $default
     * @param  callable(T): U  $fn
     * @return U
     */
    public function mapOrElse(callable $default, callable $fn): mixed;

    /**
     * @param  callable(T): void  $fn
     */
    public function inspect(callable $fn): self;

    /**
     * @template E
     *
     * @param  E  $error
     * @param  class-string<E>  $className
     */
    public function okOr(mixed $error, ?string $className = null): mixed;

    /**
     * @template E
     *
     * @param  callable(): E  $error
     * @param  class-string<E>  $className
     * @return E
     */
    public function okOrElse(callable $error, ?string $className = null): mixed;

    /**
     * @template U
     *
     * @param  Option<U>  $opt
     * @return Option<U>
     */
    public function and(Option $opt): Option;

    /**
     * @template U
     *
     * @param  callable(T): Option<U>  $fn
     * @return Option<U>
     */
    public function andThen(callable $fn): Option;

    /**
     * @template U
     *
     * @param  Option<U>  $opt
     * @return Option<U>
     */
    public function or(Option $opt): Option;

    /**
     * @template U
     *
     * @param  callable(): Option<U>  $fn
     * @return Option<U>
     */
    public function orElse(callable $fn): Option;

    /**
     * @template U
     * @template E
     *
     * @param  class-string<U>  $okClassName
     * @param  class-string<E>  $errClassName
     */
    public function transpose(?string $okClassName = null, ?string $errClassName = null): mixed;

    /**
     * @param  Option<T>  $opt
     * @return Option<T>
     */
    public function xor(Option $opt): Option;

    /**
     * @template U
     *
     * @param  Option<U>  $other
     * @return Option<array{T, U}>
     */
    public function zip(Option $other): Option;

    /**
     * @template U
     * @template R
     *
     * @param  Option<U>  $other
     * @param  callable(T, U): R  $fn
     * @return Option<R>
     */
    public function zipWith(Option $other, callable $fn): Option;

    /**
     * @return array{Option<T>, Option<U>}
     */
    public function unzip(): array;

    /**
     * @template U
     *
     * @param  callable(T): U  $Some
     * @param  callable(): U  $None
     * @return U
     */
    public function match(callable $Some, callable $None): mixed;

    /**
     * @param  callable(T): bool  $predicate
     * @return Option<T>
     */
    public function filter(callable $predicate): Option;
}
