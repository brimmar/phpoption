# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2025-12-04

### Changed
- Improved type safety with better PHPDoc annotations for generic types
- Enhanced type inference for `map()`, `zip()`, `zipWith()`, and other methods
- Better IDE autocomplete support through improved return type annotations

### Fixed
- Fixed inconsistent constructor calls (now using `new None()` instead of `new None`)
- Improved type casting in `zip()` method for better type safety

## [1.0.0] - 2024-08-04

### Added
- Initial release of the Option Type implementation
