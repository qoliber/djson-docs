# Logical Operators

Combine conditions with logical operators: AND (`&&`), OR (`||`), and NOT (`!`).

## AND Operator (&&)

Both conditions must be true.

### Basic Usage

```json
{
  "user": "{​{user.name}​}",
  "@djson if user.active && user.verified": {
    "status": "Active and Verified"
  }
}
```

**Data:**
```php
// Both true - shows status
$data = ['user' => ['name' => 'John', 'active' => true, 'verified' => true]];

// One false - no status
$data = ['user' => ['name' => 'Jane', 'active' => true, 'verified' => false]];
```

### With Comparisons

```json
{
  "@djson if user.age >= 18 && user.country == \"US\"": {
    "canVote": true
  }
}
```

### Multiple AND Conditions

Chain multiple conditions:

```json
{
  "@djson if user.active && user.verified && user.premium": {
    "access": "Full Premium Access"
  }
}
```

## OR Operator (||)

At least one condition must be true.

### Basic Usage

```json
{
  "@djson if product.inStock || product.preorder": {
    "available": true
  }
}
```

**Results:**
- `inStock=true, preorder=false` → available ✓
- `inStock=false, preorder=true` → available ✓
- `inStock=true, preorder=true` → available ✓
- `inStock=false, preorder=false` → not available ✗

### Role-Based Access

```json
{
  "@djson if user.role == \"admin\" || user.role == \"moderator\"": {
    "canModerate": true
  }
}
```

### Multiple OR Conditions

```json
{
  "@djson if status == \"new\" || status == \"pending\" || status == \"processing\"": {
    "isActive": true
  }
}
```

## NOT Operator (!)

Negates a condition.

### Basic Usage

```json
{
  "@djson if !user.banned": {
    "access": "allowed"
  }
}
```

### With Comparisons

```json
{
  "@djson if !user.age < 18": {
    "isAdult": true
  }
}
```

## Combining Operators

### AND with OR

```json
{
  "@djson if user.admin || user.moderator && user.active": {
    "canManage": true
  }
}
```

**Precedence:** AND (`&&`) is evaluated before OR (`||`)
- Reads as: `user.admin || (user.moderator && user.active)`

### NOT with AND

```json
{
  "@djson if !user.banned && user.verified": {
    "status": "Good Standing"
  }
}
```

### NOT with OR

```json
{
  "@djson if !user.deleted || user.archived": {
    "visible": true
  }
}
```

### Complex Conditions

```json
{
  "@djson if user.age >= 18 && user.country == \"US\" && !user.banned": {
    "eligibleToVote": true
  }
}
```

## In Loops

Use logical operators inside loops:

```json
{
  "products": {
    "@djson for products as product": {
      "name": "{​{product.name}​}",
      "@djson if product.inStock && product.price > 50": {
        "badge": "Premium In Stock"
      }
    }
  }
}
```

**Data:**
```php
$data = [
    'products' => [
        ['name' => 'Laptop', 'inStock' => true, 'price' => 999],   // Gets badge
        ['name' => 'Mouse', 'inStock' => true, 'price' => 29],     // No badge (price)
        ['name' => 'Monitor', 'inStock' => false, 'price' => 299]  // No badge (stock)
    ]
];
```

## With @djson else

```json
{
  "@djson if user.premium && user.active": {
    "tier": "Premium Active"
  },
  "@djson else": {
    "tier": "Standard"
  }
}
```

## In Ternary Operators

```json
{
  "status": "{​{user.active && user.verified ? \"Verified\" : \"Unverified\"}​}"
}
```

## Real-World Examples

### Access Control

```json
{
  "user": "{​{user.name}​}",
  "@djson if user.role == \"admin\" && !user.suspended": {
    "canModerate": true,
    "canDeletePosts": true
  },
  "@djson if user.verified && !user.banned": {
    "canPost": true
  }
}
```

### E-commerce Product

```json
{
  "products": {
    "@djson for products as product": {
      "name": "{​{product.name}​}",
      "@djson if product.stock > 0 && product.price > 0": {
        "available": true,
        "@djson if product.featured || product.discount > 0": {
          "promoted": true
        }
      }
    }
  }
}
```

**Data:**
```php
$data = [
    'products' => [
        ['name' => 'Laptop', 'stock' => 10, 'price' => 999, 'featured' => true, 'discount' => 0],
        ['name' => 'Mouse', 'stock' => 5, 'price' => 29, 'featured' => false, 'discount' => 5],
        ['name' => 'Cable', 'stock' => 0, 'price' => 10, 'featured' => false, 'discount' => 0]
    ]
];
```

### Eligibility Check

```json
{
  "@djson if user.age >= 18 && user.country == \"US\" && !user.convicted": {
    "voting": {
      "eligible": true,
      "registrationUrl": "https://vote.gov"
    }
  }
}
```

### Content Moderation

```json
{
  "posts": {
    "@djson for posts as post": {
      "title": "{​{post.title}​}",
      "@djson if !post.flagged && (post.approved || post.author.trusted)": {
        "visible": true,
        "content": "{​{post.content}​}"
      }
    }
  }
}
```

## Operator Precedence

Operators are evaluated in this order:

1. **NOT (`!`)** - Highest precedence
2. **AND (`&&`)** 
3. **OR (`||`)** - Lowest precedence

**Example:**
```
!a && b || c
```
Evaluates as: `((!a) && b) || c`

## Best Practices

::: tip Use Parentheses Mentally
While DJson doesn't support parentheses in expressions, understand precedence:
- `a && b || c` means `(a && b) || c`
- `a || b && c` means `a || (b && c)`
:::

::: warning Keep It Readable
Complex conditions with many operators can be hard to read. Consider using nested `@djson if` directives instead.
:::

::: tip Quote String Comparisons
Always quote strings in comparisons:
```json
"@djson if status == \"active\""  // ✓ Correct
"@djson if status == active"      // ✗ Wrong
```
:::

## Comparison Table

| Expression | When True |
|------------|-----------|
| `a && b` | Both a AND b are true |
| `a \|\| b` | Either a OR b is true (or both) |
| `!a` | a is false or falsy |
| `a && b && c` | All three are true |
| `a \|\| b \|\| c` | At least one is true |
| `!a && b` | a is false AND b is true |
| `a && (b \|\| c)` | a is true AND at least one of b or c is true |

## See Also

- [Conditionals](/guide/conditionals) - `@djson if/else` directives
- [Ternary Operators](/guide/ternary) - Inline conditions
- [API Reference](/api/directives) - Complete directive documentation
