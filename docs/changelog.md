---
title: Changelog
description: Release history and version changes for DJson
---

# Changelog

All notable changes to DJson will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.5.0] - 2025-11-18

### 🔒 Enhanced Security Release

Version 1.5.0 adds advanced security protections with **database function blocking** and **reflection-based code inspection** to prevent security bypasses.

### Added

#### Database Function Blocking
- **Comprehensive database pattern blocking** - Prevents template access to database systems
  - MySQL: `mysqli`, `mysql_`
  - PostgreSQL: `pg_`
  - SQLite: `sqlite`
  - PDO: `pdo`
  - ODBC: `odbc_`
  - SQL Server: `sqlsrv_`
  - Oracle: `oci_`

**Example - Database functions blocked:**
```php
// BLOCKED - function name contains database pattern
$djson->registerFunction('mysqli_query', fn($q) => "SAFE: $q");
// Error: "dangerous pattern 'mysqli'"

$djson->registerFunction('pg_query', fn($q) => "SAFE: $q");
// Error: "dangerous pattern 'pg_'"
```

#### Reflection-Based Code Inspection
- **Deep callable validation** - Inspects actual source code of registered functions
  - Uses PHP Reflection API to analyze callable internals
  - Detects dangerous function calls hidden inside safe-looking functions
  - Prevents security bypasses by wrapping malicious code
  - Validates closures, methods, and callable objects
  - Scans for all dangerous patterns (execution, system, database, filesystem)

**Example - No security bypass possible:**
```php
// BLOCKED - Even with safe name, eval() detected inside!
$djson->registerFunction('process_data', function ($input) {
    return eval($input);  // Detected by reflection!
});
// Error: "callable's source code contains prohibited function pattern 'eval'"

// BLOCKED - Database access detected
$djson->registerFunction('get_user', function ($id) {
    return mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
});
// Error: "callable's source code contains prohibited pattern 'mysqli'"

// BLOCKED - System commands detected
$djson->registerFunction('run_task', function ($cmd) {
    return shell_exec($cmd);
});
// Error: "callable's source code contains prohibited pattern 'exec'"
```

### Changed
- **Removed obsolete `create_function` pattern**
  - `create_function()` was removed in PHP 8.0
  - DJson requires PHP 8.1+, making this pattern irrelevant
  - Cleaner, more accurate security validation

### Testing
- **Enhanced Security Test Suite** (`tests/SecurityTest.php`)
  - 46 total security tests (+17 from v1.4.0)
  - Database function blocking tests (8 new)
  - Reflection-based code inspection tests (7 new)
  - Removed obsolete `create_function` test

### Statistics
- **229 total tests** (+17 from v1.4.0)
- **664 total assertions** (+34 from v1.4.0)

### Files Changed
- `src/FunctionProcessor.php` - Database patterns, reflection validation, removed obsolete pattern
- `tests/SecurityTest.php` - Enhanced test coverage
- `README.md` - Updated security documentation

### Backward Compatibility
- ✅ Fully backward compatible for safe functions
- ⚠️ BREAKING: Database function names now blocked (intentional security)
- ⚠️ BREAKING: Callables containing dangerous code now blocked (intentional security)

---

## [1.4.0] - 2025-11-18

### 🛡️ Security Protection Release

Version 1.4.0 introduces **mandatory security protection** against dangerous function registration. No backdoors, no compromises.

### Added

#### Mandatory Security Validation
- **Dangerous function name blocking** when registering custom functions
  - Code execution: `eval`, `assert`, `call_user_func`
  - System commands: `exec`, `shell_exec`, `system`, `passthru`, `popen`, `proc_open`
  - Filesystem: `file_get_contents`, `file_put_contents`, `fopen`, `unlink`, `chmod`, `rename`
  - Includes: `include`, `require`, `include_once`, `require_once`
  - Serialization: `unserialize`
  - Reflection: `reflection`

- **No Escape Hatch** - Security is mandatory
  - `registerUnsafeFunction()` method exists but ALSO throws exception
  - No backdoors, no compromises
  - Security cannot be disabled

**Example - Security by default:**
```php
// BLOCKED - throws InvalidArgumentException
$djson->registerFunction('exec', fn($cmd) => shell_exec($cmd));
// Error suggests trying registerUnsafeFunction()...

// BUT PLOT TWIST: That ALSO throws!
$djson->registerUnsafeFunction('exec', fn($cmd) => shell_exec($cmd));
// Error: "Sorry matey, no unsafe functions allowed!"

// These work fine - safe functions only
$djson->registerFunction('currency', fn($v) => '$' . $v);
$djson->registerFunction('md5hash', fn($v) => md5($v));  // Safe!
```

### Testing
- **Security Test Suite** (`tests/SecurityTest.php`)
  - 29 comprehensive security tests
  - Tests all dangerous pattern categories
  - Tests case-insensitive pattern detection
  - Tests that safe functions work correctly
  - Tests that `registerUnsafeFunction()` also throws

### Statistics
- **212 total tests** (+29 from v1.3.0)
- **630 total assertions** (+58 from v1.3.0)

### Files Changed
- `src/FunctionProcessor.php` - Security validation with pattern detection
- `src/DJson.php` - Added `registerUnsafeFunction()` method (troll mode)
- `tests/SecurityTest.php` - Comprehensive security test coverage

### Backward Compatibility
- ✅ Fully backward compatible for safe functions
- ⚠️ BREAKING: Dangerous function names now blocked (intentional security feature)

---

## [1.3.0] - 2025-11-18

### 🐛 Critical Bug Fix Release

Fixed a critical bug that prevented custom functions with digits in their names from working correctly.

### Fixed

#### Custom Function Name Parsing
- **Regex Pattern Bug** - Function names can now contain digits (e.g., `base64`, `md5`, `sha256`)
  - Previously, functions like `base64_encode` would be ignored and rendered as literal text
  - Fixed in `FunctionProcessor.php` by updating regex from `/^([a-z_|]+)/i` to `/^([a-z0-9_|]+)/i`
  - Affects both `validateFunction()` and `apply()` methods

**Before (broken):**
```php
$djson->registerFunction('base64', fn($v) => base64_encode($v));
// Output: "@djson base64 {​{email}}" (literal string)
```

**After (fixed):**
```php
$djson->registerFunction('base64', fn($v) => base64_encode($v));
// Output: "dGVzdEBleGFtcGxlLmNvbQ==" (encoded value)
```

### Added

#### Examples & Documentation
- **11 Comprehensive Examples** - Complete working examples in `lib/examples/` directory
  - Basic variables, loops, conditionals
  - Functions, calculations, pattern matching
  - Object support, JSON-LD, real-world scenarios
  - Custom function registration
  - See [Code Examples](/examples/code-examples) for details

- **Roadmap Document** - Future features and development priorities

#### Testing
- **CustomFunctionsWithNumbersTest.php** - 13 new tests for numbered function names
  - Tests: `base64`, `md5hash`, `sha256`, `gravatar200`, `format2decimals`
  - Validates function chaining, parameters, and loop integration

### Statistics
- **183 total tests** (+13 from v1.2.0)
- **572 total assertions** (+18 from v1.2.0)

### Backward Compatibility
- ✅ Fully backward compatible
- ✅ No breaking changes
- ✅ Fix only enables previously broken functionality

---

## [1.2.0] - 2025-11-18

### 🎨 Render Modes & Real-World Examples

Added debug/production output modes, comprehensive examples covering all features, and extensive JSON-LD test coverage.

### Added

#### Render Mode Feature
- **Output Modes** - Choose between debug and production JSON formatting
  - `RENDER_MODE_DEBUG` - Pretty-printed JSON with indentation
  - `RENDER_MODE_COMPACT` - Single-line compact JSON (default)
  - `setRenderMode(string $mode): self` - Fluent interface
  - `getRenderMode(): string` - Get current mode
  - Constructor accepts optional `$renderMode` parameter

**Usage:**
```php
// Debug mode - formatted output
$djson = new DJson(DJson::RENDER_MODE_DEBUG);
$json = $djson->processToJson($template, $data);

// Compact mode - production (default)
$djson = new DJson(DJson::RENDER_MODE_COMPACT);
$json = $djson->processToJson($template, $data);

// Fluent interface
$json = (new DJson())
    ->setRenderMode(DJson::RENDER_MODE_DEBUG)
    ->processToJson($template, $data);
```

#### Testing
- **RenderModeTest.php** - 16 tests for render mode functionality
- **JsonStringAndScalarArrayTest.php** - 18 tests for JSON string processing
  - Validates JSON string → Array → Process → Array → JSON string workflow
  - Tests scalar arrays (strings, numbers, booleans)
  - Loop index support for arrays

- **StructuredDataTest.php** - 11 real-world JSON-LD tests
  - Schema.org breadcrumbs (including Sportano.pl example with Polish characters)
  - Product schema with offers
  - Organization schema
  - Article schema
  - E-commerce product lists
  - Unicode character handling

### Statistics
- **170 total tests** (+45 from v1.1.0)
- **554 total assertions** (+130 from v1.1.0)

### Backward Compatibility
- ✅ Default mode is COMPACT (same as before)
- ✅ No breaking changes
- ✅ Optional render mode parameter

---

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
- **Nested Object Access** - Deep navigation with dot notation: `{​{user.profile.address.city}​}`
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
  "name": "{​{product.name}}",
  "price": "{​{product.price}}",
  "active": "{​{product.active}}"
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

**[1.5.0]:** Enhanced security with reflection inspection - 2025-11-18
**[1.4.0]:** Mandatory security protection - 2025-11-18
**[1.3.0]:** Bug fix and examples release - 2025-11-18
**[1.2.0]:** Render modes and testing release - 2025-11-18
**[1.1.0]:** Object support release - 2025-11-16
**[1.0.0]:** Initial stable release - 2025-11-13
