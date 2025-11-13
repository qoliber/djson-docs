# Getting Started

Get up and running with DJson in just a few minutes.

## Installation

Install DJson via Composer:

```bash
composer require qoliber/djson
```

### Requirements

- PHP 8.1 or higher
- ext-json (JSON extension)

## Your First Template

Let's create a simple greeting template using **JSON string format**:

```php
<?php

require_once 'vendor/autoload.php';

use Qoliber\DJson\DJson;

$djson = new DJson();

// JSON template as string
$template = '{
    "greeting": "Hello {​{name}​}!",
    "timestamp": "{​{timestamp}​}"
}';

$data = [
    'name' => 'World',
    'timestamp' => date('Y-m-d H:i:s')
];

$result = $djson->process($template, $data);
print_r($result);
```

**Output:**
```php
Array
(
    [greeting] => Hello World!
    [timestamp] => 2025-01-13 10:30:00
)
```

## Processing Options

DJson offers multiple ways to work with templates:

### 1. JSON String → PHP Array

```php
$jsonTemplate = '{
    "name": "{​{userName}​}"
}';

$data = ['userName' => 'Alice'];

$result = $djson->process($jsonTemplate, $data);
// Returns: ['name' => 'Alice']
```

### 2. JSON String → JSON String

```php
$jsonTemplate = '{
    "name": "{​{userName}​}"
}';

$json = $djson->processToJson($jsonTemplate, $data);
// Returns: {"name":"Alice"}

// With formatting:
$json = $djson->processToJson($jsonTemplate, $data, JSON_PRETTY_PRINT);
```

### 3. JSON File → PHP Array

```php
// templates/user.json:
// {
//   "name": "{​{user.name}​}",
//   "email": "{​{user.email}​}"
// }

$data = [
    'user' => [
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ]
];

$result = $djson->processFile('templates/user.json', $data);
```

### 4. JSON File → JSON String

```php
$json = $djson->processFileToJson('templates/user.json', $data, JSON_PRETTY_PRINT);
```

## Basic Syntax

### Variables

Use double curly braces to insert values:

```json
{
    "firstName": "{​{firstName}​}",
    "lastName": "{​{lastName}​}"
}
```

### Nested Data

Access nested data with dot notation:

```json
{
    "city": "{​{user.address.city}​}"
}
```

**Data:**
```php
$data = [
    'user' => [
        'address' => [
            'city' => 'New York'
        ]
    ]
];
```

### Multiple Variables

Combine multiple variables in one string:

```json
{
    "fullName": "{​{firstName}​} {​{lastName}​}"
}
```

## Type Preservation

DJson preserves data types:

```json
{
    "age": "{​{age}​}",
    "active": "{​{active}​}",
    "price": "{​{price}​}",
    "name": "{​{name}​}"
}
```

```php
$data = [
    'age' => 30,           // Integer
    'active' => true,      // Boolean
    'price' => 99.99,      // Float
    'name' => 'Product'    // String
];

$result = $djson->process($template, $data);
// Types are preserved: age=30 (int), active=true (bool), etc.
```

::: tip Type Safety
When a variable is the only content in a value, its type is preserved. When combined with other text, it's converted to a string.
:::

## Error Handling

DJson handles missing variables gracefully:

```json
{
    "name": "{​{missing}​}"
}
```

```php
$data = [];
$result = $djson->process($template, $data);
// Result: ['name' => null]
```

For string interpolation, missing values become empty strings:

```json
{
    "greeting": "Hello {​{missing}​}!"
}
```

```php
// Result: ['greeting' => 'Hello !']
```

## JSON Template Files

Create reusable templates in JSON files:

**templates/product.json:**
```json
{
  "id": "{​{product.id}​}",
  "name": "{​{product.name}​}",
  "price": "{​{product.price}​}"
}
```

**Process the template:**
```php
$data = [
    'product' => [
        'id' => 123,
        'name' => 'Laptop',
        'price' => 999.99
    ]
];

$result = $djson->processFile('templates/product.json', $data);
```

## Next Steps

Now that you understand the basics:

- Learn about [Loops](/guide/loops) to iterate over arrays
- Explore [Conditionals](/guide/conditionals) for dynamic content
- Check out [Functions](/guide/functions) for data transformation
- See [Real Examples](/examples/ecommerce) for practical use cases
