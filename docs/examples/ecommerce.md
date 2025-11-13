# E-commerce Catalog

This example shows how to build a complete e-commerce product catalog with categories, products, pricing, discounts, and stock management.

## Full Example

```php
<?php

use Qoliber\DJson\DJson;

$djson = new DJson();

$template = '{
    "store": {
        "name": "{​{store.name}​}",
        "currency": "{​{store.currency}​}"
    },
    "categories": {
        "@djson for categories as category": {
            "id": "{​{category.id}​}",
            "name": "{​{category.name}​}",
            "slug": "@djson slug {​{category.name}​}",
            "products": {
                "@djson for category.products as product": {
                    "id": "{​{product.id}​}",
                    "name": "{​{product.name}​}",
                    "slug": "@djson slug {​{product.name}​}",
                    "@djson set discount = product.price - product.salePrice": {
                        "@djson set discountPercent = (discount / product.price) * 100": {
                            "pricing": {
                                "regular": "@djson number_format {​{product.price}​} 2",
                                "@djson if product.onSale": {
                                    "sale": {
                                        "price": "@djson number_format {​{product.salePrice}​} 2",
                                        "savings": "@djson number_format {​{discount}​} 2",
                                        "percent": "@djson round {​{discountPercent}​} 0"
                                    }
                                }
                            },
                            "@djson match product.stock": {
                                "@djson case 0": {
                                    "stock": {
                                        "status": "out_of_stock",
                                        "message": "Out of Stock",
                                        "available": false
                                    }
                                },
                                "@djson default": {
                                    "@djson if product.stock <= 5": {
                                        "stock": {
                                            "status": "low_stock",
                                            "message": "Only {​{product.stock}​} left!",
                                            "available": true
                                        }
                                    },
                                    "@djson else": {
                                        "stock": {
                                            "status": "in_stock",
                                            "message": "In Stock",
                                            "available": true
                                        }
                                    }
                                }
                            },
                            "@djson if product.features": {
                                "features": {
                                    "highlights": {
                                        "@djson for product.features as feature": {
                                            "name": "{​{feature.name}​}",
                                            "value": "{​{feature.value}​}"
                                        }
                                    }
                                }
                            },
                            "@djson if product.reviews": {
                                "rating": {
                                    "average": "@djson round {​{product.averageRating}​} 1",
                                    "count": "{​{product.reviewCount}​}",
                                    "stars": "{​{product.averageRating >= 4 ? \\"⭐⭐⭐⭐⭐\\" : \\"⭐⭐⭐\\"}​}"
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}';

$data = [
    'store' => [
        'name' => 'TechMart',
        'currency' => 'USD'
    ],
    'categories' => [
        [
            'id' => 1,
            'name' => 'Laptops & Computers',
            'products' => [
                [
                    'id' => 101,
                    'name' => 'Gaming Laptop Pro',
                    'price' => 1299.99,
                    'onSale' => true,
                    'salePrice' => 999.99,
                    'stock' => 12,
                    'features' => [
                        ['name' => 'CPU', 'value' => 'Intel i7'],
                        ['name' => 'RAM', 'value' => '16GB'],
                        ['name' => 'Storage', 'value' => '512GB SSD']
                    ],
                    'reviews' => true,
                    'averageRating' => 4.5,
                    'reviewCount' => 89
                ],
                [
                    'id' => 102,
                    'name' => 'Budget Laptop',
                    'price' => 499.99,
                    'onSale' => false,
                    'stock' => 0,
                    'features' => null,
                    'reviews' => true,
                    'averageRating' => 3.8,
                    'reviewCount' => 23
                ]
            ]
        ],
        [
            'id' => 2,
            'name' => 'Accessories',
            'products' => [
                [
                    'id' => 201,
                    'name' => 'Wireless Mouse',
                    'price' => 29.99,
                    'onSale' => false,
                    'stock' => 3,
                    'features' => [
                        ['name' => 'DPI', 'value' => '1600'],
                        ['name' => 'Battery', 'value' => '6 months']
                    ],
                    'reviews' => false
                ]
            ]
        ]
    ]
];

$json = $djson->processToJson($template, $data, JSON_PRETTY_PRINT);

echo $json;
```

## Output

```json
{
  "store": {
    "name": "TechMart",
    "currency": "USD"
  },
  "categories": [
    {
      "id": 1,
      "name": "Laptops & Computers",
      "slug": "laptops-computers",
      "products": [
        {
          "id": 101,
          "name": "Gaming Laptop Pro",
          "slug": "gaming-laptop-pro",
          "pricing": {
            "regular": "1,299.99",
            "sale": {
              "price": "999.99",
              "savings": "300.00",
              "percent": 23
            }
          },
          "stock": {
            "status": "in_stock",
            "message": "In Stock",
            "available": true
          },
          "features": {
            "highlights": [
              {"name": "CPU", "value": "Intel i7"},
              {"name": "RAM", "value": "16GB"},
              {"name": "Storage", "value": "512GB SSD"}
            ]
          },
          "rating": {
            "average": 4.5,
            "count": 89,
            "stars": "⭐⭐⭐⭐⭐"
          }
        },
        {
          "id": 102,
          "name": "Budget Laptop",
          "slug": "budget-laptop",
          "pricing": {
            "regular": "499.99"
          },
          "stock": {
            "status": "out_of_stock",
            "message": "Out of Stock",
            "available": false
          },
          "rating": {
            "average": 3.8,
            "count": 23,
            "stars": "⭐⭐⭐"
          }
        }
      ]
    },
    {
      "id": 2,
      "name": "Accessories",
      "slug": "accessories",
      "products": [
        {
          "id": 201,
          "name": "Wireless Mouse",
          "slug": "wireless-mouse",
          "pricing": {
            "regular": "29.99"
          },
          "stock": {
            "status": "low_stock",
            "message": "Only 3 left!",
            "available": true
          },
          "features": {
            "highlights": [
              {"name": "DPI", "value": "1600"},
              {"name": "Battery", "value": "6 months"}
            ]
          }
        }
      ]
    }
  ]
}
```

## Key Features Demonstrated

### 1. **Nested Loops**
```json
{
    "categories": {
        "@djson for categories as category": {
            "products": {
                "@djson for category.products as product": {
                    ...
                }
            }
        }
    }
}
```

### 2. **Computed Discounts**
```json
{
    "@djson set discount = product.price - product.salePrice": {
        "@djson set discountPercent = (discount / product.price) * 100": {
            "savings": "{​{discount}​}",
            "percent": "{​{discountPercent}​}"
        }
    }
}
```

### 3. **Dynamic Stock Status**
```json
{
    "@djson match product.stock": {
        "@djson case 0": {
            "stock": {"status": "out_of_stock"}
        },
        "@djson default": {
            "@djson if product.stock <= 5": {
                "stock": {"status": "low_stock"}
            },
            "@djson else": {
                "stock": {"status": "in_stock"}
            }
        }
    }
}
```

### 4. **Conditional Features**
```json
{
    "@djson if product.features": {
        "features": {
            "highlights": {
                "@djson for product.features as feature": {
                    "name": "{​{feature.name}​}",
                    "value": "{​{feature.value}​}"
                }
            }
        }
    }
}
```

### 5. **Price Formatting**
```json
{
    "regular": "@djson number_format {​{product.price}​} 2"
}
// Output: "1,299.99"
```

### 6. **URL Slugs**
```json
{
    "slug": "@djson slug {​{product.name}​}"
}
// "Gaming Laptop Pro" → "gaming-laptop-pro"
```

### 7. **Ternary Operators**
```json
{
    "stars": "{​{product.averageRating >= 4 ? \\"⭐⭐⭐⭐⭐\\" : \\"⭐⭐⭐\\"}​}"
}
```

## Variations

### REST API Response

```php
$template = '{
    "success": true,
    "data": {
        "products": {
            "@djson for products as product": {
                "id": "{​{product.id}​}",
                "name": "{​{product.name}​}",
                "@djson set finalPrice = product.onSale ? product.salePrice : product.price": {
                    "price": {
                        "amount": "{​{finalPrice}​}",
                        "currency": "USD",
                        "formatted": "@djson number_format {​{finalPrice}​} 2"
                    }
                },
                "availability": "{​{product.stock > 0 ? \\"available\\" : \\"unavailable\\"}​}"
            }
        }
    },
    "meta": {
        "total": "@djson count {​{products}​}",
        "page": "{​{page}​}",
        "perPage": "{​{perPage}​}"
    }
}';
```

### Shopping Cart

```php
$template = '{
    "cart": {
        "items": {
            "@djson for cart.items as item": {
                "@djson set lineTotal = item.price * item.quantity": {
                    "product": "{​{item.name}​}",
                    "quantity": "{​{item.quantity}​}",
                    "unitPrice": "@djson number_format {​{item.price}​} 2",
                    "total": "@djson number_format {​{lineTotal}​} 2"
                }
            }
        },
        "@djson set subtotal = cart.subtotal": {
            "@djson set tax = subtotal * 0.15": {
                "@djson set grandTotal = subtotal + tax": {
                    "summary": {
                        "subtotal": "@djson number_format {​{subtotal}​} 2",
                        "tax": "@djson number_format {​{tax}​} 2",
                        "total": "@djson number_format {​{grandTotal}​} 2"
                    }
                }
            }
        }
    }
}';
```

## Benefits

✅ **Reusable** - Same template for API, frontend, exports
✅ **Maintainable** - Structure is clear and documented
✅ **Testable** - Can test templates independently
✅ **Type-safe** - Numbers stay numbers, no string conversion issues
✅ **Flexible** - Easy to add/remove fields

## See Also

- [API Responses](/examples/api-responses) - REST API examples
- [Functions](/api/functions) - Formatting functions
- [Computed Values](/guide/computed-values) - Calculations
