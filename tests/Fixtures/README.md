# Validation Test Fixtures

Test fixtures and helper classes for Validation package tests.

## Purpose

This directory contains:

- Test constraint classes for testing constraint functionality
- Mock objects for testing validator behavior
- Helper utilities for creating test scenarios

## Organization

- One constraint fixture per file under `Switon\\Validating\\Tests\\Fixtures`
- Keep fixtures PSR-4 friendly and single-declaration per file

## Usage

Fixtures are automatically loaded via composer autoloader. Import them in your tests:

```php
use Switon\Validating\Tests\Fixtures\AlwaysPassConstraint;
```

---

*See main README.md for test structure and running instructions.*
