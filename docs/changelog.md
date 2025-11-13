---
title: Changelog
description: Release history and version changes for DJson
---

# Changelog

All notable changes to DJson will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-11-13

### 🎉 Module Release

First stable release of DJson - a production-ready, fully-tested dynamic JSON templating library for PHP. This release is covered with comprehensive unit tests and mutation testing to ensure code quality and reliability.

### Added

#### Core Features
- **Template Processing** - Process JSON templates with dynamic data
- **Type Preservation** - Maintains data types (numbers stay numbers, booleans stay booleans)
- **Variable Interpolation** - `{​{variable.path}​}` syntax for data access
- **Dot Notation** - Deep nested data access with `parent.child.value`

#### Directives (7)
- `@djson for items as item` - Loop through arrays and collections
- `@djson if condition` - Conditional content inclusion
- `@djson unless condition` - Inverse conditional
- `@djson else` - Fallback for if/unless
- `@djson exists variable` - Check variable existence
- `@djson set variable = expression` - Computed values with arithmetic
- `@djson match variable` - Pattern matching (switch/case logic)

#### Built-in Functions (25)

**String Functions:**
- `upper`, `lower`, `capitalize`, `title` - Case transformations
- `trim` - Remove whitespace
- `escape` - HTML entity encoding
- `slug` - URL-friendly slugs
- `substr` - String extraction
- `replace` - String replacement
- `json_encode` - JSON encoding

**Number Functions:**
- `number_format` - Format with decimals and separators
- `round`, `ceil`, `floor` - Rounding operations
- `abs` - Absolute value

**Date Functions:**
- `date` - Format timestamps and date strings
- `strtotime` - Parse date strings to timestamps

**Array Functions:**
- `count` - Count array elements
- `first`, `last` - Get first/last element
- `join` - Join array into string
- `sort` - Sort array values
- `unique` - Remove duplicates

**Utility Functions:**
- `default` - Provide fallback values
- `coalesce` - Return first non-empty value

#### Advanced Features
- **Ternary Operators** - `{​{condition ? "yes" : "no"}​}` inline conditionals
- **Logical Operators** - AND (`&&`), OR (`||`), NOT (`!`)
- **Comparison Operators** - `==`, `!=`, `>`, `<`, `>=`, `<=`
- **Function Chaining** - `@djson upper|trim {​{value}​}`
- **Loop Variables** - `_index`, `_first`, `_last` in loops
- **Nested Loops** - Unlimited nesting depth
- **Template Validation** - Validate before processing

#### Extensibility
- **Custom Directives** - Register your own directives via `DirectiveInterface`
- **Custom Functions** - Register custom transformation functions
- **PSR-4 Autoloading** - Modern PHP namespace structure

#### Development
- **103 Unit Tests** - Comprehensive test coverage with 385 assertions
- **Mutation Testing** - Infection framework integration
- **PHPUnit 10** - Modern testing framework
- **PHP 8.1+** - Constructor property promotion, enums, readonly properties

#### Output Formats
- `process()` - Returns PHP array
- `processToJson()` - Returns JSON string with flags support
- `processFile()` - Process from file
- `processFileToJson()` - Process file to JSON string

#### Validation
- `validate()` - Check template syntax before processing
- Validates JSON syntax, directive syntax, and function names
- Returns array of error messages

### Technical Details

- **Requirements:** PHP 8.1+, ext-json
- **License:** MIT
- **Namespace:** `Qoliber\DJson`
- **Package:** `qoliber/djson`
- **Zero Dependencies** - No external library dependencies

### Use Cases

- E-commerce product catalogs and pricing
- REST/GraphQL API responses
- Schema.org JSON-LD structured data
- Configuration file generation
- CMS content rendering
- Report generation

### Documentation

Complete documentation including:
- Getting Started guide
- Comprehensive examples (e-commerce, API responses, Schema.org)
- Function and directive reference
- Custom directive/function guides
- Real-world use cases

### Quality Assurance

- 103 passing tests
- Mutation testing enabled
- Type-safe code with PHP 8.1+ features
- Strict types declared throughout

---

## Upgrade Guide

### From Pre-release to 1.0.0

This is the first stable release. If you were using pre-release versions:

1. Update composer.json:
   ```json
   {
       "require": {
           "qoliber/djson": "^1.0"
       }
   }
   ```

2. Run composer update:
   ```bash
   composer update qoliber/djson
   ```

3. No breaking changes - all existing code should work as-is

---

## Future Plans

Planned features for future releases:

- Additional built-in functions (e.g., `md5`, `sha1`, `base64_encode`)
- Performance optimizations for large datasets
- Template caching mechanisms
- More directive types (e.g., `@djson switch`)
- Enhanced error messages with line numbers

---

## Contributing

See [CONTRIBUTING.md](/contributing) for guidelines on submitting issues and pull requests.

## Links

- **GitHub:** [https://github.com/qoliber/djson](https://github.com/qoliber/djson)
- **Packagist:** [https://packagist.org/packages/qoliber/djson](https://packagist.org/packages/qoliber/djson)
- **Documentation:** [https://djson.qoliber.com](https://djson.qoliber.com) (or local docs)
- **Issues:** [https://github.com/qoliber/djson/issues](https://github.com/qoliber/djson/issues)

---

**[1.0.0]:** Initial stable release - 2025-11-13
