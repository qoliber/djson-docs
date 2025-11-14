# Directives Reference

Directives are special keys that control the flow and logic of your templates. All directives use the `@djson` prefix.

## Loop Directives

### @djson for ... as ...

Iterates over an array or collection.

**Syntax:**
```
@djson for <path> as <variable>
```

**Example:**
```php
$template = '{
  "users": {
    "@djson for users as user": {
      "name": "{​{user.name}}",
      "email": "{​{user.email}}"
    }
  }
}';

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

**Loop Variables:**

Inside a loop, you have access to special variables:

| Variable | Description | Type |
|----------|-------------|------|
| `{​{_index}}` | Current index (0-based) | Integer |
| `{​{_key}}` | Current key (same as _index) | Integer/String |
| `{​{_first}}` | True if first item | Boolean |
| `{​{_last}}` | True if last item | Boolean |

**Example with loop variables:**
```php
$template = '{
  "items": {
    "@djson for items as item": {
      "position": "{​{_index}}",
      "value": "{​{item}}",
      "isFirst": "{​{_first}}",
      "isLast": "{​{_last}}"
    }
  }
}';
```

## Conditional Directives

### @djson if

Include content only if condition is truthy.

**Syntax:**
```
@djson if <condition>
```

**Example:**
```php
$template = '{
  "@djson if user.isPremium": {
    "status": "Premium Member",
    "features": "All unlocked"
  }
}';

$data = ['user' => ['isPremium' => true]];
```

**With comparison operators:**
```php
'@djson if age >= 18'
'@djson if status == "active"'
'@djson if count > 0'
```

**With logical operators:**
```php
'@djson if isActive && isPremium'
'@djson if status == "active" || role == "admin"'
'@djson if !isExpired'
```

**Supported Operators:**
- Comparison: `==`, `!=`, `>`, `<`, `>=`, `<=`
- Logical: `&&` (and), `||` (or), `!` (not)

### @djson unless

Include content only if condition is falsy (opposite of `if`).

**Syntax:**
```
@djson unless <condition>
```

**Example:**
```php
$template = '{
  "@djson unless isValid": {
    "message": "Validation failed"
  }
}';

$data = ['isValid' => false];
// Shows error message
```

### @djson exists

Include content only if variable exists and is not null.

**Syntax:**
```
@djson exists <path>
```

**Example:**
```php
$template = '{
  "@djson exists user.bio": {
    "bio": "{​{user.bio}}"
  }
}';

$data = ['user' => ['name' => 'John']];
// Content is not included because 'bio' doesn't exist
```

### @djson else

Fallback content when previous condition fails.

**Syntax:**
```
@djson else
```

**Example:**
```php
$template = '{
  "@djson if user.isPremium": {
    "status": "Premium"
  },
  "@djson else": {
    "status": "Free"
  }
}';

$data = ['user' => ['isPremium' => false]];
// Result: {"status": "Free"}
```

**With @djson unless:**
```php
$template = '{
  "@djson unless errors": {
    "success": true
  },
  "@djson else": {
    "errors": "{​{errors}}"
  }
}';
```

## Pattern Matching

### @djson match / @djson switch

Pattern matching similar to PHP's match expression.

**Syntax:**
```
@djson match <variable>
  @djson case <value1>: content1
  @djson case <value2>: content2
  @djson default: defaultContent
```

**Example:**
```php
$template = '{
  "@djson match user.role": {
    "@djson case admin": {
      "permissions": "full",
      "access": "all"
    },
    "@djson case editor": {
      "permissions": "edit",
      "access": "content"
    },
    "@djson default": {
      "permissions": "read",
      "access": "public"
    }
  }
}';

$data = ['user' => ['role' => 'admin']];
```

**With quoted strings:**
```json
{
  "@djson match status": {
    "@djson case \"active\"": {"color": "green"},
    "@djson case \"pending\"": {"color": "yellow"},
    "@djson default": {"color": "gray"}
  }
}
```

**Both `match` and `switch` are supported:**
```json
{
  "@djson switch priority": {
    "@djson case high": {"color": "red"}
  }
}
```

## Computed Values

### @djson set

Create computed variables from expressions.

**Syntax:**
```
@djson set <variable> = <expression>
```

**Arithmetic operations:**
```php
$template = '{
  "@djson set total = price * quantity": {},
  "price": "{​{price}}",
  "quantity": "{​{quantity}}",
  "total": "{​{total}}"
}';

$data = ['price' => 10, 'quantity' => 5];
// Result: {"price": 10, "quantity": 5, "total": 50}
```

**Supported operators:**
- `*` - Multiplication
- `/` - Division
- `+` - Addition (or string concatenation)
- `-` - Subtraction

**String concatenation:**
```php
'@djson set fullName = firstName + " " + lastName'
```

**With ternary operator:**
```php
'@djson set discount = isPremium ? 20 : 0'
```

**With constants:**
```php
'@djson set taxAmount = subtotal * 0.15'
```

## Nesting Directives

Directives can be nested for complex logic:

**Nested conditions:**
```php
$template = '{
  "@djson if hasAccess": {
    "@djson if isPremium": {
      "premiumContent": "{​{content.premium}}"
    }
  }
}';
```

**Loops with conditions:**
```php
$template = '{
  "items": {
    "@djson for items as item": {
      "@djson if item.isApproved": {
        "name": "{​{item.name}}"
      }
    }
  }
}';
```

**Match in loop:**
```php
$template = '{
  "orders": {
    "@djson for orders as order": {
      "@djson match order.status": {
        "@djson case shipped": {"color": "green"},
        "@djson case pending": {"color": "yellow"}
      }
    }
  }
}';
```

## Best Practices

::: tip Directive Order
When using multiple directives, order matters:
1. `@djson set` - Compute values first
2. `@djson if/unless/exists` - Check conditions
3. `@djson for` - Loop over data
4. `@djson match` - Pattern matching
:::

::: warning Directive Results
- `@djson if/unless/exists` return `null` when condition fails
- `@djson for` returns empty array for empty/null collections
- `@djson match` returns `null` when no case matches and no default
:::

::: info Performance
Directives are processed recursively. Deep nesting works but may impact performance with very large datasets.
:::

## Error Handling

**Invalid directive:**
```php
'@djson invalidDirective' => 'value'
// No error, treated as regular key
```

**Missing data in loop:**
```php
'@djson for missing as item'
// Returns empty array []
```

**Failed condition:**
```php
'@djson if missing'
// Returns null, content excluded
```

## See Also

- [Functions Reference](/api/functions) - Transform data with functions
- [Validation](/guide/validation) - Validate templates before processing
- [Custom Directives](/guide/custom-directives) - Create your own directives
