# Loops

Learn how to iterate over arrays and collections with the `@djson for` directive using JSON templates.

## Basic Syntax

In your JSON template, use the directive as a **key**:

```json
{
  "users": {
    "@djson for users as user": {
      "name": "{​{user.name}​}",
      "email": "{​{user.email}​}"
    }
  }
}
```

**PHP Code:**
```php
$data = [
    'users' => [
        ['name' => 'Alice', 'email' => 'alice@example.com'],
        ['name' => 'Bob', 'email' => 'bob@example.com']
    ]
];

$result = $djson->process($template, $data);
```

**Output:**
```json
{
  "users": [
    {"name": "Alice", "email": "alice@example.com"},
    {"name": "Bob", "email": "bob@example.com"}
  ]
}
```

## Loop Variables

Inside a loop, you have access to special variables:

### Using _index

```json
{
  "items": {
    "@djson for items as item": {
      "position": "{​{_index}​}",
      "value": "{​{item}​}"
    }
  }
}
```

**Data:**
```php
$data = ['items' => ['first', 'second', 'third']];
```

**Output:**
```json
{
  "items": [
    {"position": 0, "value": "first"},
    {"position": 1, "value": "second"},
    {"position": 2, "value": "third"}
  ]
}
```

### Using _first and _last

```json
{
  "items": {
    "@djson for items as item": {
      "value": "{​{item}​}",
      "isFirst": "{​{_first}​}",
      "isLast": "{​{_last}​}"
    }
  }
}
```

## Nested Loops

Loop inside loop for hierarchical data:

```json
{
  "categories": {
    "@djson for categories as category": {
      "name": "{​{category.name}​}",
      "products": {
        "@djson for category.products as product": {
          "id": "{​{product.id}​}",
          "name": "{​{product.name}​}"
        }
      }
    }
  }
}
```

**Data:**
```php
$data = [
    'categories' => [
        [
            'name' => 'Electronics',
            'products' => [
                ['id' => 1, 'name' => 'Laptop'],
                ['id' => 2, 'name' => 'Mouse']
            ]
        ]
    ]
];
```

## Loops with Conditionals

Filter items within loops:

```json
{
  "products": {
    "@djson for products as product": {
      "name": "{​{product.name}​}",
      "@djson if product.isApproved": {
        "status": "approved",
        "price": "{​{product.price}​}"
      }
    }
  }
}
```

## Empty Arrays

Loops handle empty/null collections gracefully:

```json
{
  "items": {
    "@djson for items as item": {
      "value": "{​{item}​}"
    }
  }
}
```

```php
$data = ['items' => []];
// Output: {"items": []}

$data = ['items' => null];
// Output: {"items": []}
```

## Complex Example

```json
{
  "orders": {
    "@djson for orders as order": {
      "orderId": "{{order.id}}",
      "items": {
        "@djson for order.items as item": {
          "@djson set lineTotal = item.price * item.quantity": {},
          "product": "{{item.name}}",
          "quantity": "{{item.quantity}}",
          "total": "{{lineTotal}}"
        }
      }
    }
  }
}
```

## Best Practices

::: tip Naming Conventions
Use descriptive variable names: `product`, `user`, `item` instead of generic `x`, `i`.
:::

::: warning Performance
Deep nesting works but impacts performance with large datasets. Consider flattening data when possible.
:::

## See Also

- [Conditionals](/guide/conditionals) - Filter loop results
- [API Reference](/api/directives#djson-for-as) - Complete directive documentation
