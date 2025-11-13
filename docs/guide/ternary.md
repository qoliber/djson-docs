# Ternary Operators

Ternary operators provide inline conditional logic: `condition ? trueValue : falseValue`

## Basic Syntax

```json
{
  "status": "{​{user.active ? \"Online\" : \"Offline\"}​}"
}
```

**PHP Code:**
```php
$data = ['user' => ['active' => true]];
$result = $djson->process($template, $data);
// Result: ['status' => 'Online']
```

## With Comparisons

### Greater Than

```json
{
  "product": {
    "name": "{​{product.name}​}",
    "badge": "{​{product.price > 100 ? \"Premium\" : \"Standard\"}​}"
  }
}
```

**Data:**
```php
$data = ['product' => ['name' => 'Laptop', 'price' => 999]];
// Result: badge = 'Premium'
```

### Age Check

```json
{
  "canVote": "{​{user.age >= 18 ? \"Yes\" : \"No\"}​}"
}
```

### Equality

```json
{
  "message": "{​{status == \"approved\" ? \"Welcome!\" : \"Pending\"}​}"
}
```

### Inequality

```json
{
  "status": "{​{order.status != \"cancelled\" ? \"Active\" : \"Cancelled\"}​}"
}
```

## Using Variables as Values

Return different variables based on condition:

```json
{
  "user": {
    "role": "{​{user.isAdmin ? user.adminRole : user.userRole}​}"
  }
}
```

**Data:**
```php
$data = [
    'user' => [
        'isAdmin' => true,
        'adminRole' => 'Administrator',
        'userRole' => 'Guest'
    ]
];
// Result: role = 'Administrator'
```

## With Numbers

Ternary operators preserve number types:

```json
{
  "discount": "{​{user.isPremium ? 20 : 10}​}"
}
```

**Data:**
```php
$data = ['user' => ['isPremium' => true]];
// Result: ['discount' => 20]  // Integer, not string!
```

## In Loops

Use ternary operators inside loops:

```json
{
  "products": {
    "@djson for products as product": {
      "name": "{​{product.name}​}",
      "availability": "{​{product.inStock ? \"Available\" : \"Out of Stock\"}​}"
    }
  }
}
```

**Data:**
```php
$data = [
    'products' => [
        ['name' => 'Laptop', 'inStock' => true],
        ['name' => 'Mouse', 'inStock' => false]
    ]
];
```

**Output:**
```json
{
  "products": [
    {"name": "Laptop", "availability": "Available"},
    {"name": "Mouse", "availability": "Out of Stock"}
  ]
}
```

## Combined with Directives

Use ternary inside conditional blocks:

```json
{
  "user": "{​{user.name}​}",
  "@djson if user.active": {
    "status": "Active",
    "tier": "{​{user.isPremium ? \"Premium\" : \"Free\"}​}"
  }
}
```

## Comparison Operators

All comparison operators work in ternary:

| Operator | Description | Example |
|----------|-------------|---------|
| `>`      | Greater than | `{​{price > 100 ? "High" : "Low"}​}` |
| `<`      | Less than | `{​{age < 18 ? "Minor" : "Adult"}​}` |
| `>=`     | Greater or equal | `{​{score >= 90 ? "A" : "B"}​}` |
| `<=`     | Less or equal | `{​{weight <= 5 ? "Light" : "Heavy"}​}` |
| `==`     | Equal | `{​{status == "new" ? "New" : "Used"}​}` |
| `!=`     | Not equal | `{​{type != "draft" ? "Live" : "Draft"}​}` |

## Real-World Examples

### Shipping Method

```json
{
  "order": {
    "shipping": "{​{order.weight <= 5 ? \"Standard\" : \"Heavy\"}​}"
  }
}
```

### Product Status

```json
{
  "products": {
    "@djson for products as product": {
      "name": "{​{product.name}​}",
      "status": "{​{product.stock > 0 ? \"In Stock\" : \"Out of Stock\"}​}",
      "shippingTime": "{​{product.stock > 10 ? \"Ships Today\" : \"Ships in 3-5 days\"}​}"
    }
  }
}
```

### User Tier

```json
{
  "user": {
    "name": "{​{user.name}​}",
    "tier": "{​{user.points >= 1000 ? \"Gold\" : user.points >= 500 ? \"Silver\" : \"Bronze\"}​}"
  }
}
```

## Best Practices

::: tip Keep It Simple
Use ternary for simple conditions. For complex logic, use `@djson if/else` directives.
:::

::: tip Quote Strings
Always quote string values: `\"Online\"` not `Online`
:::

::: warning Type Preservation
- Numbers: `{​{isPremium ? 20 : 10}​}` → preserves integer type
- Strings: `{​{active ? \"Yes\" : \"No\"}​}` → returns string
:::

## See Also

- [Conditionals](/guide/conditionals) - `@djson if/else` directives
- [Logical Operators](/guide/logical-operators) - AND, OR, NOT
- [Match/Switch](/guide/match-switch) - Multiple conditions
