# Conditionals

Control what content appears in your JSON based on conditions using conditional directives.

## @djson if

Include content only when a condition is true.

### Basic Usage

```json
{
  "user": {
    "name": "{​{user.name}​}",
    "@djson if user.isPremium": {
      "status": "Premium Member",
      "benefits": ["feature1", "feature2"]
    }
  }
}
```

**PHP Code:**
```php
$data = ['user' => ['name' => 'John', 'isPremium' => true]];
$result = $djson->process($template, $data);
```

**Output when true:**
```json
{
  "user": {
    "name": "John",
    "status": "Premium Member",
    "benefits": ["feature1", "feature2"]
  }
}
```

### With Comparisons

```json
{
  "@djson if age >= 65": {
    "discount": {
      "type": "Senior Discount",
      "amount": 20
    }
  }
}
```

**Supported operators:**
- `==` - Equal
- `!=` - Not equal
- `>` - Greater than
- `<` - Less than
- `>=` - Greater than or equal
- `<=` - Less than or equal

## @djson unless

Include content only when a condition is false:

```json
{
  "@djson unless isValid": {
    "error": {
      "message": "Please fix the errors"
    }
  }
}
```

## @djson else

Provide fallback content when condition fails:

```json
{
  "user": {
    "@djson if user.isPremium": {
      "status": "Premium",
      "features": ["all"]
    },
    "@djson else": {
      "status": "Free",
      "features": ["basic"]
    }
  }
}
```

## Logical Operators

### AND (&&)

```json
{
  "@djson if age >= 18 && country == \"USA\"": {
    "specialOffer": {
      "message": "Special offer available!"
    }
  }
}
```

### OR (||)

```json
{
  "@djson if role == \"admin\" || role == \"editor\"": {
    "canEdit": {
      "permissions": ["edit", "publish"]
    }
  }
}
```

### NOT (!)

```json
{
  "@djson if !isExpired": {
    "activeSubscription": {
      "status": "active"
    }
  }
}
```

## Nested Conditionals

```json
{
  "@djson if hasAccount": {
    "access": {
      "basic": true,
      "@djson if isPremium": {
        "premium": {
          "features": ["feature1", "feature2"]
        }
      }
    }
  }
}
```

## Real-World Examples

### Discount Badge

```json
{
  "product": {
    "name": "{​{product.name}​}",
    "price": "{​{product.price}​}",
    "@djson if product.discount > 20": {
      "badge": {
        "text": "Hot Deal!",
        "color": "red"
      }
    }
  }
}
```

### User Status

```json
{
  "user": {
    "name": "{​{user.name}​}",
    "@djson if user.isOnline": {
      "status": "online",
      "lastSeen": "now"
    },
    "@djson else": {
      "status": "offline",
      "lastSeen": "{​{user.lastSeenAt}​}"
    }
  }
}
```

## Best Practices

::: tip Keep It Simple
Prefer simple conditions over complex nested logic.
:::

::: warning Null Handling
Conditions treat null as falsy. Use `@djson exists` if you need to distinguish null from false.
:::

## See Also

- [Logical Operators](/guide/logical-operators) - AND, OR, NOT details
- [Match/Switch](/guide/match-switch) - Multiple conditions
