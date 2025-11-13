# Functions

Transform data in your templates using built-in or custom functions.

See the complete [Functions API Reference](/api/functions) for all available functions.

## Quick Overview

Functions use the `@djson` prefix:

```php
'@djson functionName {​{variable}} param1 param2'
```

## Common Functions

### String Functions

```php
'@djson upper {​{name}}'        // UPPERCASE
'@djson lower {​{name}}'        // lowercase
'@djson slug {​{title}}'        // url-friendly-slug
'@djson trim {​{text}}'         // Remove whitespace
```

### Number Functions

```php
'@djson number_format {​{price}} 2'     // 1,234.56
'@djson round {​{value}} 2'             // Round to 2 decimals
'@djson ceil {​{value}}'                // Round up
```

### Date Functions

```php
'@djson date {​{timestamp}} "Y-m-d"'    // 2024-01-01
'@djson date {​{timestamp}} "F j, Y"'   // January 1, 2024
```

### Array Functions

```php
'@djson count {​{items}}'               // Count elements
'@djson join {​{tags}} ", "'            // Join with separator
'@djson first {​{items}}'               // Get first
```

## Function Chaining

Chain multiple functions:

```php
'@djson upper|trim {​{name}}'
// 1. Trim whitespace
// 2. Convert to uppercase
```

## Custom Functions

Register your own:

```php
$djson->registerFunction('reverse', function($value) {
    return strrev($value);
});

// Use it:
'@djson reverse {​{text}}'
```

See [Functions API Reference](/api/functions) for complete documentation.
