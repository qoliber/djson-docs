# Template Validation

Validate your DJson templates before processing to catch errors early. The validation system checks JSON syntax, directive validity, and function names.

## Basic Validation

Use the `validate()` method to check templates:

```php
use Qoliber\DJson\DJson;

$djson = new DJson();

$template = '{
    "user": "{​{user.name}​}",
    "@djson if user.active": {
        "status": "Active"
    }
}';

$errors = $djson->validate($template);

if (empty($errors)) {
    // Template is valid, safe to process
    $result = $djson->process($template, $data);
} else {
    // Handle validation errors
    foreach ($errors as $error) {
        echo $error . "\n";
    }
}
```

## What Gets Validated

The validation system checks:

1. **JSON Syntax** - Valid JSON structure
2. **Directive Syntax** - Valid `@djson` directives
3. **Function Names** - Registered function names
4. **Chained Functions** - Each function in a chain exists

## Validation Examples

### Valid Template

```php
$template = '{
    "users": {
        "@djson for users as user": {
            "name": "@djson upper {​{user.name}​}",
            "@djson if user.active": {
                "status": "Active"
            },
            "@djson else": {
                "status": "Inactive"
            }
        }
    }
}';

$errors = $djson->validate($template);
// $errors = [] (empty - template is valid)
```

### Invalid JSON Syntax

```php
$template = '{
    "user": "{​{user.name}​}",
    "status": "Active"
'; // Missing closing brace

$errors = $djson->validate($template);
// $errors = ["Invalid JSON syntax: Syntax error"]
```

### Invalid Directive

```php
$template = '{
    "user": "{​{user.name}​}",
    "@djson invalidDirective user.active": {
        "status": "Active"
    }
}';

$errors = $djson->validate($template);
// $errors = ["Invalid directive at '@djson invalidDirective user.active': ..."]
```

### Invalid Function

```php
$template = '{
    "user": "@djson nonExistentFunction {​{user.name}​}"
}';

$errors = $djson->validate($template);
// $errors = ["Unknown function 'nonExistentFunction' at path: user"]
```

## Validating Complex Templates

### Nested Structures

Validation works recursively through nested templates:

```php
$template = '{
    "@djson for categories as category": {
        "name": "{​{category.name}​}",
        "@djson if category.featured": {
            "badge": "Featured",
            "@djson for category.products as product": {
                "productName": "@djson upper {​{product.name}​}"
            }
        }
    }
}';

$errors = $djson->validate($template);
// $errors = [] (empty - all nested directives are valid)
```

### Multiple Errors

The validator collects all errors in a single pass:

```php
$template = '{
    "name": "@djson invalidFunc {​{user.name}​}",
    "@djson wrongDirective": {
        "test": "value"
    },
    "upper": "@djson anotherBadFunc {​{test}​}"
}';

$errors = $djson->validate($template);
// $errors contains 3 errors - one for each invalid element
```

## Validating Directives

### Set Directive

```php
// Valid
$template = '{
    "@djson set total = product.price * product.qty": {
        "total": "{​{total}​}"
    }
}';

$errors = $djson->validate($template);
// $errors = [] (valid)

// Invalid - missing expression
$template = '{
    "@djson set": {
        "total": "{​{total}​}"
    }
}';

$errors = $djson->validate($template);
// $errors = ["Invalid directive at '@djson set': ..."]
```

### All Directives

Validation supports all built-in directives:

```php
$template = '{
    "@djson if user.active": {
        "status": "active"
    },
    "@djson unless user.banned": {
        "access": "allowed"
    },
    "@djson exists user.email": {
        "hasEmail": true
    },
    "@djson for items as item": {
        "name": "{​{item.name}​}"
    },
    "@djson set total = 100": {
        "total": "{​{total}​}"
    }
}';

$errors = $djson->validate($template);
// $errors = [] (all directives are valid)
```

## Validating Functions

### Single Function

```php
$template = '{
    "user": "@djson upper {​{user.name}​}"
}';

$errors = $djson->validate($template);
// $errors = [] (upper is a valid function)
```

### Chained Functions

Each function in the chain is validated:

```php
// Valid chain
$template = '{
    "name": "@djson upper|trim {​{user.name}​}"
}';

$errors = $djson->validate($template);
// $errors = [] (both functions exist)

// Invalid chain
$template = '{
    "name": "@djson upper|nonexistent {​{user.name}​}"
}';

$errors = $djson->validate($template);
// $errors = ["Unknown function 'nonexistent' at path: name"]
```

## Validating Advanced Features

### Ternary Operators

```php
$template = '{
    "status": "{​{user.active ? \\"Online\\" : \\"Offline\\"}​}"
}';

$errors = $djson->validate($template);
// $errors = [] (ternary syntax is valid)
```

### Logical Operators

```php
$template = '{
    "@djson if user.active && user.verified": {
        "access": "granted"
    }
}';

$errors = $djson->validate($template);
// $errors = [] (logical operators are valid)
```

## Input Formats

Validation accepts both JSON strings and PHP arrays:

### JSON String

```php
$template = '{
    "name": "@djson upper {​{user.name}​}",
    "@djson if user.active": {
        "status": "Active"
    }
}';

$errors = $djson->validate($template);
```

### PHP Array

```php
$template = [
    'name' => '@djson upper {​{user.name}​}',
    '@djson if user.active' => [
        'status' => 'Active'
    ]
];

$errors = $djson->validate($template);
```

## Error Handling

### Empty Array = Valid

```php
$errors = $djson->validate($template);

if (empty($errors)) {
    echo "Template is valid!";
}
```

### Array of Strings = Errors

```php
$errors = $djson->validate($template);

if (!empty($errors)) {
    echo "Validation errors:\n";
    foreach ($errors as $error) {
        echo "- $error\n";
    }
}
```

## Real-World Usage

### Pre-Processing Validation

```php
class TemplateRenderer
{
    public function __construct(
        private \Qoliber\DJson\DJson $djson
    ) {}

    public function render(string $template, array $data): string
    {
        // Validate before processing
        $errors = $this->djson->validate($template);

        if (!empty($errors)) {
            throw new \InvalidArgumentException(
                'Invalid template: ' . implode(', ', $errors)
            );
        }

        return $this->djson->processToJson($template, $data, JSON_PRETTY_PRINT);
    }
}
```

### Template Storage System

```php
class TemplateManager
{
    public function __construct(
        private \Qoliber\DJson\DJson $djson,
        private TemplateRepository $repository
    ) {}

    public function save(string $name, string $template): void
    {
        // Validate before saving
        $errors = $this->djson->validate($template);

        if (!empty($errors)) {
            throw new ValidationException(
                "Cannot save invalid template: " . implode('; ', $errors)
            );
        }

        $this->repository->save($name, $template);
    }

    public function load(string $name): string
    {
        return $this->repository->load($name);
    }
}
```

### User-Provided Templates

```php
class ApiController
{
    public function __construct(
        private \Qoliber\DJson\DJson $djson
    ) {}

    public function createTemplate(Request $request): JsonResponse
    {
        $template = $request->input('template');

        // Validate user-provided template
        $errors = $this->djson->validate($template);

        if (!empty($errors)) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => $errors
            ], 400);
        }

        // Store and use the template
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Template is valid'
        ]);
    }
}
```

## Best Practices

::: tip Validate Early
Always validate templates before processing, especially for user-provided or dynamically generated templates.
:::

::: tip Cache Valid Templates
If templates don't change often, validate once and cache the result to avoid repeated validation overhead.
:::

::: warning Production Validation
In production, consider validating templates during deployment or build time rather than at runtime for better performance.
:::

::: tip Custom Functions
If you register custom functions, validate templates after registration to ensure all functions are recognized:

```php
$djson = new DJson();
$djson->registerFunction('myCustomFunc', fn($value) => strtoupper($value));

// Now validate - custom function will be recognized
$errors = $djson->validate($template);
```
:::

## Performance Notes

- Validation is fast but not free - it parses JSON and walks the entire tree
- For static templates, validate once at load/build time
- For dynamic templates, validation is worth the overhead to catch errors early
- Validation is much cheaper than failed processing at runtime

## See Also

- [Custom Directives](/guide/custom-directives) - Validate custom directive syntax
- [Custom Functions](/guide/custom-functions) - Register functions before validation
