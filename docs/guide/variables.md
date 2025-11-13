# Variables

Variables allow you to dynamically insert data into your templates.

## Basic Syntax

Use double curly braces around variable names to insert dynamic values.

## Examples

See the [Getting Started Guide](/guide/getting-started) for complete variable documentation including:

- Simple variables
- Nested data access with dot notation  
- Type preservation
- Multiple variables in strings
- Missing variable handling

## Quick Reference

| Syntax | Description |
|--------|-------------|
| `name` | Simple variable |
| `user.email` | Nested property |
| `items.0` | Array index |

Variables preserve their original types (numbers, booleans, arrays) when used alone.
