# Match / Switch

Pattern matching for multiple conditions, similar to PHP's `match` expression.

## Syntax

```json
{
    "@djson match variable": {
        "@djson case value1": {
            // content for value1
        },
        "@djson case value2": {
            // content for value2
        },
        "@djson default": {
            // default content
        }
    }
}
```

## Basic Example

```json
{
    "user": "{{user.name}}",
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
}
```

## With Quoted Strings

When matching string values, you can use escaped quotes:

```json
{
    "@djson match payment.method": {
        "@djson case \"credit_card\"": {
            "processor": "Stripe"
        },
        "@djson case \"paypal\"": {
            "processor": "PayPal"
        },
        "@djson default": {
            "processor": "Unknown"
        }
    }
}
```

## Switch Directive

Both `@djson match` and `@djson switch` work identically:

```json
{
    "@djson switch product.type": {
        "@djson case electronics": {
            "category": "Tech"
        },
        "@djson case clothing": {
            "category": "Fashion"
        }
    }
}
```

See [Directives API](/api/directives#djson-match-djson-switch) for full documentation.
