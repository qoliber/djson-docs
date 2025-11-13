# What is DJson?

DJson is a lightweight PHP library that enables you to create dynamic JSON structures using a template-based approach. Think of it as a templating engine specifically designed for JSON generation.

## The Problem

When building APIs, you often need to generate JSON responses that vary based on data. Traditional approaches require verbose PHP code:

```php
$response = ['products' => []];

foreach ($products as $product) {
    $item = [
        'name' => $product['name'],
        'price' => $product['price']
    ];

    if ($product['onSale']) {
        $item['discount'] = [
            'original' => $product['price'],
            'sale' => $product['salePrice']
        ];
    }

    $response['products'][] = $item;
}

return json_encode($response);
```

This approach:
- Mixes logic with data structure
- Becomes hard to maintain as complexity grows
- Difficult to reuse across different contexts
- Hard to validate before runtime

## The DJson Solution

DJson separates your JSON structure from the logic:

```php
$template = [
    'products' => [
        '@djson for products as product',
        'name' => '{​{product.name}}',
        'price' => '{​{product.price}}',
        'discount' => [
            '@djson if product.onSale',
            'original' => '{​{product.price}}',
            'sale' => '{​{product.salePrice}}'
        ]
    ]
];

$djson = new DJson();
$result = $djson->process($template, $data);
```

This approach:
- ✅ Separates structure from logic
- ✅ Readable and maintainable
- ✅ Reusable templates
- ✅ Validatable before processing
- ✅ Testable independently

## Key Concepts

### Templates
Templates define the structure of your JSON output. They can be:
- PHP arrays
- JSON strings
- JSON files

### Directives
Special keys that control flow and logic:
- `@djson for` - Loops
- `@djson if` - Conditionals
- `@djson match` - Pattern matching
- `@djson set` - Computed values

### Variables
Dynamic placeholders using `{​{variable}}` syntax:
- `{​{name}}` - Simple variable
- `{​{user.address.city}}` - Nested access
- `{​{_index}}` - Loop variables

### Functions
Built-in transformations:
- `@djson upper {​{name}}` - String functions
- `@djson number_format {​{price}} 2` - Number formatting
- `@djson date {​{timestamp}} Y-m-d` - Date formatting

## When to Use DJson

DJson is perfect for:

- **API Responses**: REST, GraphQL, or any JSON API
- **Configuration Files**: Generate configs based on environment
- **Structured Data**: Schema.org, Open Graph, JSON-LD
- **Reports**: Transform data into JSON reports
- **CMS**: Dynamic content rendering
- **E-commerce**: Product catalogs, pricing

## When NOT to Use DJson

DJson might not be the best choice for:

- Simple static JSON (just use `json_encode()`)
- HTML templating (use Twig, Blade, etc.)
- Very simple data mapping (arrays might be simpler)
- Performance-critical hot paths (native PHP might be faster)

## Philosophy

DJson follows these principles:

1. **Explicit over Implicit**: Directives use clear `@djson` prefix
2. **Type Preservation**: Numbers stay numbers, booleans stay booleans
3. **Zero Magic**: No hidden behaviors or surprises
4. **Composable**: Directives can be nested and combined
5. **Extensible**: Add custom directives and functions easily

## Comparison

| Feature | DJson | Twig | json_encode() | GraphQL |
|---------|-------|------|---------------|---------|
| JSON-focused | ✅ | ❌ | ✅ | ✅ |
| Template reuse | ✅ | ✅ | ❌ | ✅ |
| Type preservation | ✅ | ⚠️ | ✅ | ✅ |
| Learning curve | Low | Medium | None | High |
| Setup complexity | Low | Low | None | High |
| Loops & conditions | ✅ | ✅ | ❌ | ✅ |
| Functions | ✅ | ✅ | ❌ | ✅ |
| Validation | ✅ | ⚠️ | ❌ | ✅ |

## Next Steps

Ready to get started? Head over to the [Getting Started](/guide/getting-started) guide to install DJson and create your first template.
