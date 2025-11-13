# Why DJson?

Learn why DJson might be the right choice for your JSON generation needs.

## The JSON Generation Problem

When building APIs or generating JSON data, you typically face these challenges:

### Challenge 1: Mixed Concerns

```php
// Traditional approach - logic mixed with structure
$products = [];
foreach ($data as $product) {
    $item = [
        'name' => $product['name'],
        'price' => $product['price']
    ];

    if ($product['onSale']) {
        $item['discount'] = [
            'amount' => $product['discount'],
            'price' => $product['salePrice']
        ];
    }

    $products[] = $item;
}

return json_encode(['products' => $products]);
```

**Problems:**
- Structure hidden in procedural code
- Hard to visualize final JSON
- Difficult to reuse
- Testing requires full execution

### Challenge 2: Repetitive Code

```php
// Writing similar code over and over
if (isset($data['email'])) {
    $result['email'] = $data['email'];
}

if (isset($data['phone'])) {
    $result['phone'] = $data['phone'];
}

if (isset($data['address'])) {
    $result['address'] = [
        'street' => $data['address']['street'] ?? null,
        'city' => $data['address']['city'] ?? null,
    ];
}
```

### Challenge 3: Type Juggling Issues

```php
// Unexpected type conversions
$json = json_encode([
    'id' => "123",  // Should be number
    'active' => "true",  // Should be boolean
    'price' => "99.99"  // Should be float
]);

// Result: everything is a string
```

## The DJson Solution

### Separation of Concerns

```php
// Define structure once, reuse everywhere
$template = [
    'products' => [
        '@djson for products as product',
        'name' => '{​{product.name}}',
        'price' => '{​{product.price}}',
        'discount' => [
            '@djson if product.onSale',
            'amount' => '{​{product.discount}}',
            'price' => '{​{product.salePrice}}'
        ]
    ]
];

// Use anywhere
$api = $djson->process($template, $apiData);
$export = $djson->process($template, $exportData);
$test = $djson->process($template, $mockData);
```

**Benefits:**
- ✅ Clear structure
- ✅ Reusable across contexts
- ✅ Testable independently
- ✅ Easy to validate

### DRY Principle

```php
// One template definition
$template = [
    '@djson if user.email',
    'email' => '{​{user.email}}',
    '@djson if user.phone',
    'phone' => '{​{user.phone}}',
    '@djson if user.address',
    'address' => [
        'street' => '{​{user.address.street}}',
        'city' => '{​{user.address.city}}'
    ]
];

// No repetitive if-else blocks
```

### Type Safety

```php
$template = [
    'id' => '{​{id}}',        // Number stays number
    'active' => '{​{active}}', // Boolean stays boolean
    'price' => '{​{price}}'   // Float stays float
];

$data = [
    'id' => 123,
    'active' => true,
    'price' => 99.99
];

// Types are preserved automatically
```

## Comparison with Alternatives

### vs. Plain PHP

| Aspect | DJson | Plain PHP |
|--------|-------|-----------|
| Structure clarity | ✅ Visible | ❌ Hidden in code |
| Reusability | ✅ High | ⚠️ Medium |
| Type safety | ✅ Automatic | ❌ Manual |
| Testing | ✅ Template + Data | ❌ Full execution |
| Learning curve | Low | None |

### vs. Twig/Blade

| Aspect | DJson | Twig/Blade |
|--------|-------|------------|
| JSON-focused | ✅ Yes | ❌ HTML-focused |
| Type preservation | ✅ Automatic | ⚠️ Manual |
| Syntax for JSON | ✅ Natural | ⚠️ Awkward |
| Dependencies | ✅ None | ❌ Heavy |
| Setup | ✅ Simple | ⚠️ Complex |

### vs. GraphQL

| Aspect | DJson | GraphQL |
|--------|-------|---------|
| Setup complexity | ✅ Low | ❌ High |
| Learning curve | ✅ Low | ❌ High |
| Schema definition | ⚠️ Manual | ✅ Automatic |
| Flexibility | ✅ High | ⚠️ Schema-bound |
| Tooling | ⚠️ Basic | ✅ Excellent |

### vs. JMESPath/JSONPath

| Aspect | DJson | JMESPath |
|--------|-------|----------|
| Direction | ✅ Generate | ❌ Query only |
| Loops/Conditions | ✅ Built-in | ⚠️ Limited |
| Functions | ✅ 25+ | ⚠️ Few |
| Computed values | ✅ Yes | ❌ No |

## When to Use DJson

DJson excels in these scenarios:

### ✅ Perfect For

**API Responses**
```php
// Dynamic REST/GraphQL responses
// Different shapes for different roles
// Computed fields, formatting
```

**Configuration Generation**
```php
// Environment-based configs
// Feature flags
// Dynamic settings
```

**Structured Data**
```php
// Schema.org JSON-LD
// Open Graph
// RSS/Atom feeds as JSON
```

**Data Transformation**
```php
// ETL pipelines
// Data exports
// Report generation
```

### ⚠️ Consider Alternatives

**Simple Static JSON**
```php
// Just use json_encode()
$json = json_encode(['status' => 'ok']);
```

**HTML Templating**
```php
// Use Twig, Blade, or similar
// DJson is for JSON, not HTML
```

**Very Simple Mapping**
```php
// Direct array mapping might be simpler
$result = ['id' => $data['id'], 'name' => $data['name']];
```

## Real-World Benefits

### Developer Experience

**Before DJson:**
```php
// 50 lines of procedural code
// Hidden structure
// Hard to modify
// Difficult to test
```

**With DJson:**
```php
// 15 lines of declarative template
// Clear structure
// Easy to modify
// Simple to test
```

### Maintenance

**Before:**
- Change requires finding logic scattered across files
- Risk of breaking unrelated features
- Testing requires full integration tests

**After:**
- Change template in one place
- Clear impact scope
- Unit test templates separately

### Team Collaboration

**Before:**
- Backend devs write JSON generation code
- Frontend devs request changes
- Back-and-forth iterations

**After:**
- Share template between teams
- Frontend can prototype with mock data
- Backend implements data layer separately

## Performance Considerations

DJson is designed for clarity, not maximum performance. However:

**✅ Good performance for:**
- API responses (< 1000 items)
- Configuration files
- Moderate data sets

**⚠️ Consider alternatives for:**
- Millions of records
- Real-time streaming
- Microsecond latency requirements

**Optimization tips:**
- Cache parsed templates
- Precompute heavy calculations
- Use simple directives when possible

## Conclusion

Choose DJson when:
- ✅ JSON structure is important
- ✅ Reusability matters
- ✅ Type safety is critical
- ✅ Team collaboration is key
- ✅ Maintainability is priority

Skip DJson when:
- ❌ Maximum performance is critical
- ❌ JSON is trivially simple
- ❌ One-off quick scripts
- ❌ HTML templating needed

## Next Steps

Ready to try DJson? Start with the [Getting Started](/guide/getting-started) guide.
