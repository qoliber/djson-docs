---
layout: home

hero:
  name: DJson
  text: Dynamic JSON Templating
  tagline: A lightweight PHP library for creating dynamic JSON with loops, conditionals, and variables
  image:
    src: /logo.svg
    alt: DJson
  actions:
    - theme: brand
      text: Get Started
      link: /guide/getting-started
    - theme: alt
      text: View on GitHub
      link: https://github.com/qoliber/djson
    - theme: alt
      text: Reference
      link: /api/directives

features:
  - icon: 🔄
    title: Loops & Iteration
    details: Iterate over arrays with @djson for directive. Support for nested loops, loop variables (_index, _first, _last), and complex data structures.

  - icon: ⚡
    title: Conditionals
    details: Dynamic content with @djson if, @djson unless, @djson exists, and @djson else. Support for logical operators (&&, ||, !) and comparisons.

  - icon: 🎯
    title: Pattern Matching
    details: Switch-case logic with @djson match directive. Clean syntax for handling multiple conditions and fallback cases.

  - icon: 🧮
    title: Computed Values
    details: Calculate values on-the-fly with @djson set. Support for arithmetic operations, ternary operators, and expression evaluation.

  - icon: 🔧
    title: 25+ Built-in Functions
    details: String manipulation, number formatting, date handling, array operations, and more. Chain functions together for powerful transformations.

  - icon: 📦
    title: Type Preservation
    details: Maintains data types - numbers stay numbers, booleans stay booleans. No unwanted string conversions.

  - icon: 🚀
    title: Zero Dependencies
    details: Lightweight library with no external dependencies. Just PHP 8.1+ required. Works with any framework or standalone.

  - icon: 🔌
    title: Extensible
    details: Register custom directives and functions. Extend DJson to fit your specific use cases with clean, simple APIs.

  - icon: ✅
    title: Template Validation
    details: Validate templates before processing. Catch errors early with built-in validation for directives, functions, and syntax.
---

## Quick Example

**JSON Template:**
```json
{
  "products": {
    "@djson for products as product": {
      "name": "{​{product.name}​}",
      "price": "{​{product.price}​}",
      "@djson if product.onSale": {
        "discount": {
          "original": "{​{product.price}​}",
          "sale": "{​{product.salePrice}​}"
        }
      }
    }
  }
}
```

**PHP Code:**
```php
use Qoliber\DJson\DJson;

$djson = new DJson();
$template = file_get_contents('template.json');

$data = [
    'products' => [
        ['name' => 'Laptop', 'price' => 999, 'onSale' => true, 'salePrice' => 799],
        ['name' => 'Mouse', 'price' => 29, 'onSale' => false]
    ]
];

echo $djson->processToJson($template, $data, JSON_PRETTY_PRINT);
```

**Output:**
```json
{
  "products": [
    {
      "name": "Laptop",
      "price": 999,
      "discount": {
        "original": 999,
        "sale": 799
      }
    },
    {
      "name": "Mouse",
      "price": 29
    }
  ]
}
```

## Installation

```bash
composer require qoliber/djson
```

## Why DJson?

**Born from E-commerce Needs**

DJson was created to solve a real problem: generating complex JSON structures for e-commerce platforms. When building product catalogs, API responses, and structured data (like Schema.org JSON-LD), we needed a way to create dynamic JSON that was:
- Easy to read and maintain
- Type-safe (prices stay numbers, not strings)
- Reusable across different contexts
- Simple enough for non-developers to understand

What started as an e-commerce solution has become a powerful library for any JSON generation needs.

**Key Benefits:**

- **Built for JSON**: Unlike general templating engines, DJson is specifically designed for JSON generation
- **Clean Syntax**: Directives use a consistent `@djson` prefix, making templates readable
- **Type Safe**: Preserves data types without unwanted conversions - critical for e-commerce pricing
- **Well Tested**: 103 tests with 385 assertions, including mutation testing
- **Production Ready**: Battle-tested in real-world e-commerce applications

## Use Cases

- **API Responses**: Generate dynamic REST/GraphQL responses
- **Configuration**: Create config files based on environment
- **Schema.org**: Build JSON-LD structured data
- **E-commerce**: Product catalogs, pricing, inventory
- **CMS**: Blog posts, comments, dynamic content
- **Reporting**: Generate JSON reports from data

## Features at a Glance

| Feature | Syntax | Description |
|---------|--------|-------------|
| Variables | `{​{variable}​}` | Dynamic value insertion |
| Loops | `@djson for items as item` | Iterate over arrays |
| Conditionals | `@djson if condition` | Show/hide content |
| Match/Switch | `@djson match variable` | Pattern matching |
| Computed | `@djson set total = a * b` | Calculate values |
| Functions | `@djson upper {​{name}​}` | Transform data |
| Ternary | `{​{condition ? yes : no}​}` | Inline conditions |
| Logical | `condition && other` | Complex logic |

## Next Steps

<div class="vp-doc" style="margin-top: 2rem;">

**New to DJson?**
Start with the [Getting Started Guide](/guide/getting-started) to learn the basics.

**Want to see examples?**
Check out [Real-World Examples](/examples/ecommerce) for common use cases.

**Need API details?**
Browse the [API Reference](/api/directives) for complete documentation.

</div>
