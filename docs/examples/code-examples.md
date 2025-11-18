# Code Examples

Complete, runnable PHP examples demonstrating all DJson features. Each example is a standalone script that can be executed directly.

## 📦 Getting the Examples

All examples are available in the library repository at:
```
qoliber/djson/examples/
```

Clone or download the repository to run them locally:
```bash
git clone https://github.com/qoliber/djson.git
cd djson/examples
php 01-basic-variables.php
```

---

## 📚 Available Examples

### 01. Basic Variables
**File:** `01-basic-variables.php`

Learn the fundamentals of variable interpolation in DJson.

**Topics Covered:**
- Simple variable interpolation with `{{variable}}`
- Nested data access with dot notation (`user.profile.address.city`)
- Type preservation (strings, integers, floats, booleans, null)

**Example:**
```php
$template = '{
    "name": "{{name}}",
    "email": "{{email}}",
    "age": "{{age}}",
    "active": "{{active}}"
}';

$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 30,
    'active' => true
];

$result = $djson->process($template, $data);
```

**Best For:** Understanding the basics of variable usage in DJson

---

### 02. Loops
**File:** `02-loops.php`

Master looping and iteration with the `@djson for` directive.

**Topics Covered:**
- `@djson for` directive syntax
- Looping over scalar arrays
- Looping over arrays of objects
- Loop index with `{{_index}}`
- Nested loops (categories → products)

**Example:**
```php
$template = '{
    "users": {
        "@djson for users as user": {
            "name": "{{user.name}}",
            "email": "{{user.email}}",
            "index": "{{_index}}"
        }
    }
}';

$data = [
    'users' => [
        ['name' => 'John', 'email' => 'john@example.com'],
        ['name' => 'Jane', 'email' => 'jane@example.com']
    ]
];
```

**Best For:** Learning how to create dynamic arrays in JSON output

---

### 03. Conditionals
**File:** `03-conditionals.php`

Control flow and conditional logic in templates.

**Topics Covered:**
- `@djson if` directive
- `@djson else` directive
- `@djson unless` (inverse if)
- `@djson exists` (check if variable exists)
- Comparison operators (`>`, `<`, `>=`, `<=`, `==`, `!=`)
- Logical operators (`&&`, `||`, `!`)

**Example:**
```php
$template = '{
    "@djson if stock > 0": {
        "availability": "In Stock",
        "quantity": "{{stock}}"
    },
    "@djson else": {
        "availability": "Out of Stock"
    }
}';

$data = ['stock' => 5];
```

**Best For:** Understanding conditional logic and control flow

---

### 04. Functions
**File:** `04-functions.php`

Data transformation with 25+ built-in functions.

**Topics Covered:**
- **String functions:** `upper`, `lower`, `trim`, `ucfirst`, `ucwords`, `slug`, `substr`, `length`
- **Math functions:** `round`, `ceil`, `floor`, `abs`, `number_format`
- **Array functions:** `count`, `join`, `first`, `last`
- **Date functions:** `date`, `now`
- **Utility functions:** `default`, ternary operator
- **Function chaining** with pipes: `trim|lower|ucfirst`

**Example:**
```php
$template = '{
    "name": "@djson upper|trim {{name}}",
    "slug": "@djson slug {{name}}",
    "price": "@djson number_format {{price}} 2",
    "created": "@djson date {{timestamp}} \\"Y-m-d\\""
}';

$data = [
    'name' => '  Gaming Laptop  ',
    'price' => 1299.99,
    'timestamp' => 1704067200
];
```

**Best For:** Learning data transformation and formatting

---

### 05. Calculations and Set
**File:** `05-calculations-set.php`

Perform calculations within templates using `@djson set`.

**Topics Covered:**
- `@djson set` directive
- Arithmetic operations (`+`, `-`, `*`, `/`)
- Creating computed values
- Using calculations in loops
- Complex multi-step calculations (tax, discount, totals)

**Example:**
```php
$template = '{
    "@djson set subtotal = price * quantity": {},
    "@djson set tax = subtotal * 0.23": {},
    "@djson set total = subtotal + tax": {},
    "price": "{{price}}",
    "quantity": "{{quantity}}",
    "subtotal": "{{subtotal}}",
    "tax": "{{tax}}",
    "total": "{{total}}"
}';

$data = [
    'price' => 100,
    'quantity' => 3
];
```

**Best For:** Understanding how to perform calculations within templates

---

### 06. Match/Switch
**File:** `06-match-switch.php`

Pattern matching as an alternative to multiple if/else statements.

**Topics Covered:**
- `@djson match` directive
- `@djson case` statements
- `@djson default` fallback
- Order status handling
- Payment method selection
- User role permissions

**Example:**
```php
$template = '{
    "@djson match order.status": {
        "@djson case \\"pending\\"": {
            "color": "yellow",
            "message": "Order is being processed"
        },
        "@djson case \\"shipped\\"": {
            "color": "blue",
            "message": "Order has been shipped"
        },
        "@djson case \\"delivered\\"": {
            "color": "green",
            "message": "Order delivered"
        },
        "@djson default": {
            "color": "gray",
            "message": "Unknown status"
        }
    }
}';

$data = ['order' => ['status' => 'shipped']];
```

**Best For:** Learning pattern matching as an alternative to multiple if/else

---

### 07. Object Support
**File:** `07-object-support.php`

Work directly with PHP objects in templates.

**Topics Covered:**
- Using PHP objects in templates
- Getter methods (`getName()`, `getPrice()`)
- Boolean getters (`isActive()`, `isInStock()`)
- Public properties
- Nested objects
- Arrays of objects
- Objects with conditionals and functions

**Example:**
```php
class Product {
    public string $name;
    private float $price;

    public function getPrice(): float {
        return $this->price;
    }

    public function isActive(): bool {
        return $this->active;
    }
}

$product = new Product('Laptop', 999.99);

$template = '{
    "name": "{{product.name}}",
    "price": "{{product.price}}",
    "active": "{{product.active}}"
}';

$result = $djson->process($template, ['product' => $product]);
```

**Best For:** Understanding how DJson works with PHP objects (perfect for ORM entities)

---

### 08. JSON-LD Breadcrumbs
**File:** `08-jsonld-breadcrumbs.php`

Generate Schema.org breadcrumb structured data for SEO.

**Topics Covered:**
- Schema.org structured data
- JSON-LD breadcrumb navigation
- SEO markup for search engines
- Unicode character handling (Polish: Mężczyzna, Odzież)
- Debug vs. Compact render modes
- HTML integration with `<script type="application/ld+json">`

**Example:**
```php
$template = '{
    "@context": "https://schema.org/",
    "@type": "BreadcrumbList",
    "itemListElement": {
        "@djson for breadcrumbs as crumb": {
            "@type": "ListItem",
            "position": "{{crumb.position}}",
            "item": {
                "@id": "{{crumb.url}}",
                "name": "{{crumb.name}}"
            }
        }
    }
}';

$data = [
    'breadcrumbs' => [
        ['position' => 1, 'url' => 'https://example.com/', 'name' => 'Home'],
        ['position' => 2, 'url' => 'https://example.com/products', 'name' => 'Products'],
        ['position' => 3, 'url' => 'https://example.com/products/laptop', 'name' => 'Laptop']
    ]
];
```

**Best For:** Real-world SEO implementation for websites

---

### 09. JSON-LD Product
**File:** `09-jsonld-product.php`

Generate Schema.org Product schema for e-commerce SEO.

**Topics Covered:**
- Schema.org Product schema
- Product with offers
- Aggregate ratings and reviews
- Multiple product variants
- Conditional availability
- E-commerce structured data

**Example:**
```php
$template = '{
    "@context": "https://schema.org/",
    "@type": "Product",
    "name": "{{product.name}}",
    "description": "{{product.description}}",
    "sku": "{{product.sku}}",
    "offers": {
        "@type": "Offer",
        "price": "{{product.price}}",
        "priceCurrency": "USD",
        "availability": "{{product.inStock ? \\"InStock\\" : \\"OutOfStock\\"}}"
    },
    "@djson if product.hasReviews": {
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{product.rating}}",
            "reviewCount": "{{product.reviewCount}}"
        }
    }
}';
```

**Best For:** E-commerce product page SEO

---

### 10. Complex Real-World Scenario
**File:** `10-complex-real-world.php`

See all DJson features working together in a realistic e-commerce order receipt.

**Topics Covered:**
- Comprehensive e-commerce order receipt
- Combining loops, conditionals, calculations, and functions
- Premium vs. standard customer handling
- Tax and shipping calculations
- Discount calculations per item
- Order status with match/switch
- String formatting and date formatting

**Example:**
```php
// Complete order with:
// - Customer info (premium badge)
// - Order items loop with discounts
// - Tax and shipping calculations
// - Order status (pending/shipped/delivered)
// - Payment method
// - Formatted dates and currencies

$template = '{
    "orderId": "{{order.id}}",
    "customer": {
        "name": "{{customer.name}}",
        "@djson if customer.isPremium": {
            "badge": "Premium Member"
        }
    },
    "items": {
        "@djson for order.items as item": {
            "@djson set itemTotal = item.price * item.quantity": {},
            "@djson set discount = itemTotal * item.discountPercent": {},
            "@djson set finalPrice = itemTotal - discount": {},
            "product": "{{item.name}}",
            "quantity": "{{item.quantity}}",
            "price": "@djson number_format {{item.price}} 2",
            "total": "@djson number_format {{finalPrice}} 2"
        }
    },
    "@djson set subtotal = order.subtotal": {},
    "@djson set tax = subtotal * 0.23": {},
    "@djson set total = subtotal + tax + order.shipping": {},
    "subtotal": "@djson number_format {{subtotal}} 2",
    "tax": "@djson number_format {{tax}} 2",
    "shipping": "@djson number_format {{order.shipping}} 2",
    "total": "@djson number_format {{total}} 2",
    "status": {
        "@djson match order.status": {
            "@djson case \\"pending\\"": {"text": "Processing"},
            "@djson case \\"shipped\\"": {"text": "On the way"},
            "@djson case \\"delivered\\"": {"text": "Delivered"},
            "@djson default": {"text": "Unknown"}
        }
    }
}';
```

**Best For:** Seeing how all DJson features work together in a realistic scenario

---

### 11. Custom Functions
**File:** `11-custom-functions.php`

Register and use your own custom transformation functions.

**Topics Covered:**
- `registerFunction()` method
- Simple custom functions
- Functions with parameters
- Functions with multiple parameters
- Using custom functions in loops
- Chaining custom functions with built-in ones
- Real-world examples: `gravatar`, `currency`, `highlight`

**Example:**
```php
// Register custom function
$djson->registerFunction('gravatar', function($email, $size = 80) {
    $hash = md5(strtolower(trim($email)));
    return "https://www.gravatar.com/avatar/{$hash}?s={$size}";
});

// Use in template
$template = '{
    "avatar": "@djson gravatar {{email}} 200",
    "thumbnailAvatar": "@djson gravatar {{email}} 50"
}';

$data = ['email' => 'john@example.com'];
```

**Best For:** Extending DJson with your own custom transformations

---

## 🎯 Learning Path

### Beginner Track
1. Start with **01-basic-variables.php** to understand variable syntax
2. Move to **02-loops.php** to learn dynamic arrays
3. Try **03-conditionals.php** for control flow

### Intermediate Track
4. Learn **04-functions.php** for data transformation
5. Master **05-calculations-set.php** for computed values
6. Understand **06-match-switch.php** for pattern matching

### Advanced Track
7. Explore **07-object-support.php** for OOP integration
8. Study **08-jsonld-breadcrumbs.php** for real-world SEO
9. Review **09-jsonld-product.php** for e-commerce
10. Analyze **10-complex-real-world.php** to see it all together
11. Extend with **11-custom-functions.php**

---

## 📖 Running the Examples

Each example is self-contained and can be run independently:

```bash
# From the examples directory
php 01-basic-variables.php
php 02-loops.php
php 03-conditionals.php
# ... etc
```

All examples output formatted JSON to demonstrate the results.

---

## 🔍 Common Use Cases by Example

### API Response Generation
Examples: **01**, **02**, **03**

### E-commerce Product Feeds
Examples: **07**, **09**

### SEO Structured Data
Examples: **08**, **09**

### Order Processing
Examples: **05**, **10**

### Dynamic Configurations
Examples: **03**, **06**

### ORM Integration
Example: **07**

### Custom Transformations
Example: **11**

---

## 📊 Features by Example

| Feature | Examples |
|---------|----------|
| Variables `{{var}}` | All examples |
| Loops `@djson for` | 02, 07, 08, 09, 10 |
| Conditionals `@djson if/else` | 03, 07, 09, 10 |
| Functions | 04, 07, 10, 11 |
| Calculations `@djson set` | 05, 10 |
| Match/Switch | 06, 10 |
| Objects | 07 |
| JSON-LD | 08, 09 |
| Render Modes | 08 |
| Custom Functions | 11 |

---

## 💡 Tips for Learning

1. **Start Simple** - Begin with basic examples and gradually move to complex ones
2. **Experiment** - Modify the data and templates to see how changes affect output
3. **Combine Features** - The real power comes from combining multiple features
4. **Check Output** - Run examples to see JSON output and understand the structure
5. **Read Comments** - Each example has detailed comments explaining the concepts
6. **Build Your Own** - After reviewing examples, create your own templates!

---

## 🚀 Next Steps

After reviewing these examples:

1. Check the [Getting Started Guide](/guide/getting-started) for installation
2. Review [API Documentation](/api/directives) for complete reference
3. Read [CHANGELOG](/changelog) to see what's new
4. Explore the test suite for more edge cases
5. Start building your own templates!

---

## 📦 Repository

All examples are available in the DJson repository:
- **GitHub:** [https://github.com/qoliber/djson/tree/main/examples](https://github.com/qoliber/djson/tree/main/examples)
- **Examples README:** Includes detailed learning path and feature matrix

---

## 🆘 Need Help?

- **GitHub Issues:** [https://github.com/qoliber/djson/issues](https://github.com/qoliber/djson/issues)
- **Documentation:** See complete [guides](/guide/getting-started) and [API reference](/api/directives)
- **Tests:** Review the `tests/` directory for more examples
