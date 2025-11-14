# Functions Reference

Functions transform data using the `@djson` syntax. They can be chained together for powerful transformations.

## Syntax

```
@djson functionName {​{variable}} param1 param2
```

**Function chaining:**
```
@djson upper|trim {​{variable}}
```

## String Functions

### upper

Convert string to uppercase.

```php
'@djson upper {​{name}}'
// Input: "hello" → Output: "HELLO"
```

### lower

Convert string to lowercase.

```php
'@djson lower {​{name}}'
// Input: "HELLO" → Output: "hello"
```

### capitalize

Capitalize first letter.

```php
'@djson capitalize {​{name}}'
// Input: "hello" → Output: "Hello"
```

### title

Capitalize first letter of each word.

```php
'@djson title {​{name}}'
// Input: "hello world" → Output: "Hello World"
```

### trim

Remove whitespace from both ends.

```php
'@djson trim {​{text}}'
// Input: "  hello  " → Output: "hello"
```

### escape

HTML escape special characters.

```php
'@djson escape {​{html}}'
// Input: "<script>" → Output: "&lt;script&gt;"
```

### slug

Convert to URL-friendly slug.

```php
'@djson slug {​{title}}'
// Input: "Hello World!" → Output: "hello-world"
```

### substr

Extract substring.

**Syntax:** `@djson substr {​{variable}} start [length]`

```php
'@djson substr {​{text}} 0 5'
// Input: "Hello World" → Output: "Hello"

'@djson substr {​{text}} 6'
// Input: "Hello World" → Output: "World"
```

### replace

Replace text.

**Syntax:** `@djson replace {​{variable}} search replacement`

```php
'@djson replace {​{text}} "foo" "bar"'
// Input: "foo bar foo" → Output: "bar bar bar"
```

### json_encode

Encode value as JSON string.

```php
'@djson json_encode {​{data}}'
// Input: ['a' => 1] → Output: '{"a":1}'
```

## Number Functions

### number_format

Format number with decimals and separators.

**Syntax:** `@djson number_format {​{number}} decimals [decPoint] [thousandsSep]`

```php
'@djson number_format {​{price}} 2'
// Input: 1234.5 → Output: "1,234.50"

'@djson number_format {​{price}} 2 "." " "'
// Input: 1234.5 → Output: "1 234.50"
```

### round

Round to specified precision.

**Syntax:** `@djson round {​{number}} [precision]`

```php
'@djson round {​{value}} 2'
// Input: 3.14159 → Output: 3.14

'@djson round {​{value}}'
// Input: 3.7 → Output: 4
```

### ceil

Round up to nearest integer.

```php
'@djson ceil {​{value}}'
// Input: 3.2 → Output: 4
```

### floor

Round down to nearest integer.

```php
'@djson floor {​{value}}'
// Input: 3.8 → Output: 3
```

### abs

Absolute value.

```php
'@djson abs {​{value}}'
// Input: -5 → Output: 5
```

## Date Functions

### date

Format date/timestamp.

**Syntax:** `@djson date {​{timestamp}} [format]`

```php
'@djson date {​{timestamp}} "Y-m-d"'
// Input: 1704067200 → Output: "2024-01-01"

'@djson date {​{timestamp}} "F j, Y"'
// Input: 1704067200 → Output: "January 1, 2024"

'@djson date {​{dateString}} "Y-m-d H:i:s"'
// Input: "2024-01-01" → Output: "2024-01-01 00:00:00"
```

**Default format:** `Y-m-d H:i:s`

### strtotime

Convert date string to timestamp.

```php
'@djson strtotime {​{dateString}}'
// Input: "2024-01-01" → Output: 1704067200
```

## Array Functions

### count

Count elements in array.

```php
'@djson count {​{items}}'
// Input: [1, 2, 3] → Output: 3
// Input: null → Output: 0
```

### first

Get first element.

```php
'@djson first {​{items}}'
// Input: [1, 2, 3] → Output: 1
// Input: [] → Output: null
```

### last

Get last element.

```php
'@djson last {​{items}}'
// Input: [1, 2, 3] → Output: 3
// Input: [] → Output: null
```

### join

Join array elements into string.

**Syntax:** `@djson join {​{array}} [separator]`

```php
'@djson join {​{tags}} ", "'
// Input: ["php", "json", "api"] → Output: "php, json, api"

'@djson join {​{items}}'
// Default separator: ','
// Input: [1, 2, 3] → Output: "1,2,3"
```

### sort

Sort array values.

```php
'@djson sort {​{items}}'
// Input: [3, 1, 2] → Output: [1, 2, 3]
```

### unique

Get unique values.

```php
'@djson unique {​{items}}'
// Input: [1, 2, 2, 3, 1] → Output: [1, 2, 3]
```

## Utility Functions

### default

Provide fallback for empty values.

**Syntax:** `@djson default {​{variable}} fallback`

```php
'@djson default {​{name}} "Unknown"'
// Input: "" → Output: "Unknown"
// Input: null → Output: "Unknown"
// Input: "John" → Output: "John"
```

### coalesce

Return first non-empty value.

**Syntax:** `@djson coalesce {​{var1}} alt1 alt2 ...`

```php
'@djson coalesce {​{primary}} {​{secondary}} "default"'
// Returns first non-empty value from: primary, secondary, or "default"
```

## Function Chaining

Chain multiple functions together using the pipe `|` operator:

```php
'@djson upper|trim {​{name}}'
// 1. Trim whitespace
// 2. Convert to uppercase
// Input: "  hello  " → Output: "HELLO"
```

**Complex example:**
```php
'@djson slug|lower {​{title}}'
// Input: "Hello World!" → Output: "hello-world"
```

**With parameters:**
```php
'@djson trim|substr {​{text}} 0 10'
// 1. Trim whitespace
// 2. Take first 10 characters
```

::: tip Chain Order
Functions are applied left to right. The output of each function becomes the input of the next.
:::

## Custom Functions

Register your own functions:

```php
$djson = new DJson();

$djson->registerFunction('reverse', function($value) {
    return strrev((string)$value);
});

// Use in template:
'@djson reverse {​{text}}'
```

**With parameters:**
```php
$djson->registerFunction('multiply', function($value, $factor = 1) {
    return $value * $factor;
});

// Use in template:
'@djson multiply {​{price}} 1.15'
```

See [Custom Functions](/guide/custom-functions) for detailed guide.

## Complete Example

```php
$template = '{
  "products": {
    "@djson for products as product": {
      "name": "@djson upper|trim {​{product.name}}",
      "slug": "@djson slug {​{product.name}}",
      "price": "@djson number_format {​{product.price}} 2",
      "description": "@djson substr {​{product.description}} 0 100",
      "tags": "@djson join {​{product.tags}} \\", \\"",
      "created": "@djson date {​{product.timestamp}} \\"F j, Y\\""
    }
  }
}';

$data = [
    'products' => [
        [
            'name' => '  Gaming Laptop  ',
            'price' => 1299.99,
            'description' => 'High-performance laptop for gaming...',
            'tags' => ['gaming', 'laptop', 'electronics'],
            'timestamp' => 1704067200
        ]
    ]
];

$result = $djson->process($template, $data);
```

**Output:**
```json
{
  "products": [
    {
      "name": "GAMING LAPTOP",
      "slug": "gaming-laptop",
      "price": "1,299.99",
      "description": "High-performance laptop for gaming...",
      "tags": "gaming, laptop, electronics",
      "created": "January 1, 2024"
    }
  ]
}
```

## Function Validation

Validate functions before processing:

```php
$template = [
    'name' => '@djson invalidFunction {​{name}}'
];

$errors = $djson->validate($template);
// Returns: ["Unknown function 'invalidFunction' at 'name': ..."]
```

## Best Practices

::: tip Use Functions for Transformation
Functions are great for formatting, not for complex business logic. Keep functions simple and focused.
:::

::: warning Performance
Function chaining is efficient, but avoid extremely long chains. Consider computing complex values in your data layer instead.
:::

::: info Type Safety
Functions receive and return typed values. String functions will convert input to strings, number functions to numbers, etc.
:::

## See Also

- [Directives Reference](/api/directives) - Control flow and logic
- [Custom Functions](/guide/custom-functions) - Create your own functions
- [Examples](/examples/ecommerce) - Real-world usage examples
