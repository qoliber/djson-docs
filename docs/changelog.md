---
title: Changelog
description: Release history and version changes for DJson
---

# Changelog

All notable changes to DJson will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2025-11-16

### 🚀 Object Support Release

DJson now supports PHP objects in addition to arrays, making it work seamlessly with object-oriented codebases, ORMs, and entity classes.

### Added

#### Full PHP Object Support
- **Smart Property Access** - Automatic resolution through multiple strategies:
  - Getter methods: `getName()` for property `name`
  - Boolean methods: `isActive()` for property `active`
  - Has methods: `hasPermission()` for property `permission`
  - Public properties: Direct access fallback
- **Nested Object Access** - Deep navigation with dot notation: `{{user.profile.address.city}}`
- **Object Collections** - Loop over arrays of objects with `@djson for`
- **Mixed Data Support** - Seamlessly mix arrays and objects in the same template

#### Enhanced Core
- **DJson.php** - New `getObjectProperty()` method for intelligent property resolution
- **FunctionProcessor.php** - Improved parameter parsing for quoted strings with spaces
- **Better Function Arguments** - Enhanced handling of escaped quotes and complex parameters

#### Testing
- **531 New Tests** - Comprehensive object support test suite (`ObjectSupportTest.php`)
  - Public and private property access
  - All getter method types (`get*()`, `is*()`, `has*()`)
  - Nested object structures (4+ levels deep)
  - Objects in loops and conditionals
  - Functions applied to object properties
  - Mixed array/object scenarios

### Example

```php
class Product {
    public string $name;
    private float $price;
    private bool $active;

    public function getPrice(): float {
        return $this->price;
    }

    public function isActive(): bool {
        return $this->active;
    }
}

$product = new Product('Laptop', 999.99, true);

$template = '{
  "name": "{{product.name}}",
  "price": "{{product.price}}",
  "active": "{{product.active}}"
}';

$result = $djson->process($template, ['product' => $product]);
// Works seamlessly with both public properties and getter methods
```

### Use Cases

Now perfect for:
- **Doctrine/Eloquent Entities** - Work directly with ORM objects
- **Domain Models** - Use your business objects in templates
- **DTOs** - Process Data Transfer Objects without conversion
- **API Models** - Transform API response objects to JSON
- **Legacy Code** - Integrate with existing object-oriented codebases

### Compatibility

- ✅ **100% Backward Compatible** - All array-based code works unchanged
- ✅ **No Breaking Changes** - Existing templates continue to work
- ✅ **Transparent Operation** - Automatic detection of arrays vs objects

### Technical Details

**Property Resolution Order:**
1. Try `getName()` getter method
2. Try `isActive()` boolean method
3. Try `hasPermission()` has method
4. Try direct public property access
5. Return `null` if property not found

**Files Changed:**
- `src/DJson.php` (+42 lines)
- `src/FunctionProcessor.php` (+106 lines)
- `tests/ObjectSupportTest.php` (+531 lines)

---

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

**[1.1.0]:** Object support release - 2025-11-16
**[1.0.0]:** Initial stable release - 2025-11-13
