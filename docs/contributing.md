---
title: Contributing
description: Guidelines for contributing to DJson
---

# Contributing to DJson

Thank you for your interest in contributing to DJson! We welcome contributions from the community.

## Getting Started

1. Fork the repository
2. Clone your fork: `git clone https://github.com/your-username/djson.git`
3. Install dependencies: `composer install`
4. Run tests: `vendor/bin/phpunit`

## Development Workflow

### Running Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run with coverage
vendor/bin/phpunit --coverage-html coverage

# Run mutation testing
vendor/bin/infection
```

### Code Standards

- PHP 8.1+ features (constructor property promotion, enums, etc.)
- Strict types declared in all files
- PSR-4 autoloading
- Comprehensive test coverage

## Making Changes

1. Create a new branch: `git checkout -b feature/your-feature-name`
2. Make your changes
3. Add tests for new functionality
4. Ensure all tests pass
5. Commit your changes with clear messages
6. Push to your fork
7. Open a Pull Request

## Pull Request Guidelines

- **Title**: Clear and descriptive
- **Description**: Explain what changes you made and why
- **Tests**: Include tests for new features or bug fixes
- **Documentation**: Update docs if needed
- **One feature per PR**: Keep PRs focused

## Code Style

Follow existing code style in the project:

```php
<?php

declare(strict_types=1);

namespace Qoliber\DJson;

/**
 * Example class demonstrating DJson code style
 */
class Example
{
    /**
     * @param string $value The value to process
     */
    public function __construct(
        private string $value
    ) {}

    /**
     * Process the value by converting to uppercase
     *
     * @return string The processed uppercase value
     */
    public function process(): string
    {
        return strtoupper($this->value);
    }
}
```

## Adding New Features

### Custom Directives

When adding a new directive:

1. Create a class implementing `DirectiveInterface`
2. Add tests in `tests/`
3. Document in `docs/api/directives.md`
4. Add example usage

### Custom Functions

When adding a new function:

1. Register in `FunctionProcessor::registerBuiltInFunctions()`
2. Add tests
3. Document in `docs/api/functions.md`
4. Add real-world examples

## Testing

We maintain high test coverage:

- **103 tests** with 385 assertions
- Mutation testing with Infection
- All new code must include tests

## Documentation

Update documentation when:

- Adding new features
- Changing existing behavior
- Adding examples
- Fixing bugs that affect usage

Documentation is in the `docs/` directory using VitePress.

## Reporting Issues

When reporting bugs:

1. Check if the issue already exists
2. Provide a clear description
3. Include code examples
4. Specify PHP version
5. Include error messages

## Questions?

- Open a [GitHub Discussion](https://github.com/qoliber/djson/discussions)
- Check existing [Issues](https://github.com/qoliber/djson/issues)

## License

By contributing, you agree that your contributions will be licensed under the MIT License.
