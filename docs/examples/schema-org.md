# Schema.org JSON-LD

Generate structured data for SEO with Schema.org JSON-LD markup using DJson templates.

## Product Schema

Perfect for e-commerce product pages:

```php
<?php

use Qoliber\DJson\DJson;

$djson = new DJson();

$template = '{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "{​{product.name}​}",
    "description": "{​{product.description}​}",
    "image": "{​{product.image}​}",
    "sku": "{​{product.sku}​}",
    "@djson if product.brand": {
        "brand": {
            "@type": "Brand",
            "name": "{​{product.brand}​}"
        }
    },
    "offers": {
        "@type": "Offer",
        "url": "{​{product.url}​}",
        "priceCurrency": "{​{product.currency}​}",
        "price": "{​{product.price}​}",
        "availability": "{​{product.inStock ? \\"https://schema.org/InStock\\" : \\"https://schema.org/OutOfStock\\"}​}",
        "@djson if product.salePrice": {
            "priceValidUntil": "{​{product.saleEndDate}​}"
        }
    },
    "@djson if product.reviews": {
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{​{product.averageRating}​}",
            "reviewCount": "{​{product.reviewCount}​}"
        }
    }
}';

$data = [
    'product' => [
        'name' => 'Gaming Laptop Pro',
        'description' => 'High-performance gaming laptop with RTX graphics',
        'image' => 'https://example.com/images/laptop.jpg',
        'sku' => 'LAPTOP-001',
        'brand' => 'TechBrand',
        'url' => 'https://example.com/products/gaming-laptop-pro',
        'currency' => 'USD',
        'price' => 1299.99,
        'inStock' => true,
        'salePrice' => 999.99,
        'saleEndDate' => '2025-12-31',
        'reviews' => true,
        'averageRating' => 4.5,
        'reviewCount' => 89
    ]
];

$json = $djson->processToJson($template, $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

echo '<script type="application/ld+json">' . "\n";
echo $json;
echo "\n" . '</script>';
```

**Output:**
```json
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "Gaming Laptop Pro",
  "description": "High-performance gaming laptop with RTX graphics",
  "image": "https://example.com/images/laptop.jpg",
  "sku": "LAPTOP-001",
  "brand": {
    "@type": "Brand",
    "name": "TechBrand"
  },
  "offers": {
    "@type": "Offer",
    "url": "https://example.com/products/gaming-laptop-pro",
    "priceCurrency": "USD",
    "price": 1299.99,
    "availability": "https://schema.org/InStock",
    "priceValidUntil": "2025-12-31"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": 4.5,
    "reviewCount": 89
  }
}
```

## Breadcrumb List

For site navigation breadcrumbs:

```php
$template = '{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": {
        "@djson for breadcrumbs as crumb": {
            "@type": "ListItem",
            "position": "{​{_index + 1}​}",
            "name": "{​{crumb.name}​}",
            "@djson if !_last": {
                "item": "{​{crumb.url}​}"
            }
        }
    }
}';

$data = [
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => 'https://example.com'],
        ['name' => 'Electronics', 'url' => 'https://example.com/electronics'],
        ['name' => 'Laptops', 'url' => 'https://example.com/electronics/laptops'],
        ['name' => 'Gaming Laptop Pro', 'url' => '']
    ]
];
```

## Organization

For your company information:

```php
$template = '{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "{​{org.name}​}",
    "url": "{​{org.url}​}",
    "logo": "{​{org.logo}​}",
    "@djson if org.socialMedia": {
        "sameAs": {
            "@djson for org.socialMedia as social": "{​{social}​}"
        }
    },
    "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "{​{org.phone}​}",
        "contactType": "customer service",
        "availableLanguage": {
            "@djson for org.languages as lang": "{​{lang}​}"
        }
    }
}';

$data = [
    'org' => [
        'name' => 'TechMart',
        'url' => 'https://example.com',
        'logo' => 'https://example.com/logo.png',
        'phone' => '+1-555-0123',
        'languages' => ['English', 'Spanish'],
        'socialMedia' => [
            'https://facebook.com/techmart',
            'https://twitter.com/techmart',
            'https://linkedin.com/company/techmart'
        ]
    ]
];
```

## Article / Blog Post

For blog content:

```php
$template = '{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "{​{article.title}​}",
    "description": "{​{article.excerpt}​}",
    "image": "{​{article.image}​}",
    "datePublished": "{​{article.publishedAt}​}",
    "dateModified": "{​{article.updatedAt}​}",
    "author": {
        "@type": "Person",
        "name": "{​{article.author.name}​}",
        "@djson if article.author.url": {
            "url": "{​{article.author.url}​}"
        }
    },
    "publisher": {
        "@type": "Organization",
        "name": "{​{site.name}​}",
        "logo": {
            "@type": "ImageObject",
            "url": "{​{site.logo}​}"
        }
    },
    "@djson if article.tags": {
        "keywords": "@djson join {​{article.tags}​} \\", \\""
    }
}';

$data = [
    'article' => [
        'title' => 'Top 10 Gaming Laptops 2025',
        'excerpt' => 'Comprehensive guide to the best gaming laptops',
        'image' => 'https://example.com/blog/gaming-laptops.jpg',
        'publishedAt' => '2025-01-15T10:00:00Z',
        'updatedAt' => '2025-01-16T14:30:00Z',
        'author' => [
            'name' => 'John Doe',
            'url' => 'https://example.com/authors/john-doe'
        ],
        'tags' => ['gaming', 'laptops', 'reviews', 'technology']
    ],
    'site' => [
        'name' => 'TechMart Blog',
        'logo' => 'https://example.com/logo.png'
    ]
];
```

## Local Business

For brick-and-mortar stores:

```php
$template = '{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "{​{business.name}​}",
    "image": "{​{business.image}​}",
    "telephone": "{​{business.phone}​}",
    "email": "{​{business.email}​}",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "{​{business.address.street}​}",
        "addressLocality": "{​{business.address.city}​}",
        "addressRegion": "{​{business.address.state}​}",
        "postalCode": "{​{business.address.zip}​}",
        "addressCountry": "{​{business.address.country}​}"
    },
    "@djson if business.hours": {
        "openingHoursSpecification": {
            "@djson for business.hours as hours": {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": "{​{hours.day}​}",
                "opens": "{​{hours.open}​}",
                "closes": "{​{hours.close}​}"
            }
        }
    },
    "@djson if business.rating": {
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{​{business.rating}​}",
            "reviewCount": "{​{business.reviewCount}​}"
        }
    }
}';

$data = [
    'business' => [
        'name' => 'TechMart Downtown',
        'image' => 'https://example.com/store.jpg',
        'phone' => '+1-555-0123',
        'email' => 'downtown@techmart.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'San Francisco',
            'state' => 'CA',
            'zip' => '94102',
            'country' => 'US'
        ],
        'hours' => [
            ['day' => 'Monday', 'open' => '09:00', 'close' => '18:00'],
            ['day' => 'Tuesday', 'open' => '09:00', 'close' => '18:00'],
            ['day' => 'Wednesday', 'open' => '09:00', 'close' => '18:00'],
            ['day' => 'Thursday', 'open' => '09:00', 'close' => '18:00'],
            ['day' => 'Friday', 'open' => '09:00', 'close' => '20:00'],
            ['day' => 'Saturday', 'open' => '10:00', 'close' => '17:00']
        ],
        'rating' => 4.7,
        'reviewCount' => 234
    ]
];
```

## FAQ Page

For frequently asked questions:

```php
$template = '{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": {
        "@djson for faqs as faq": {
            "@type": "Question",
            "name": "{​{faq.question}​}",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "{​{faq.answer}​}"
            }
        }
    }
}';

$data = [
    'faqs' => [
        [
            'question' => 'What is your return policy?',
            'answer' => 'We offer a 30-day money-back guarantee on all products.'
        ],
        [
            'question' => 'Do you offer free shipping?',
            'answer' => 'Yes, free shipping on orders over $50.'
        ],
        [
            'question' => 'How long does delivery take?',
            'answer' => 'Standard delivery takes 3-5 business days.'
        ]
    ]
];
```

## Benefits for SEO

✅ **Type Safety** - Prices and numbers stay as numbers, not strings
✅ **Conditional Fields** - Only include data when it exists
✅ **Reusable Templates** - Use same template across all product pages
✅ **Maintainable** - Easy to update schema structure
✅ **Dynamic Content** - Generate from database or CMS data
✅ **Valid JSON** - Always produces valid JSON-LD

## Best Practices

::: tip Use JSON_UNESCAPED_SLASHES
When outputting Schema.org markup, use the `JSON_UNESCAPED_SLASHES` flag to keep URLs readable:

```php
$json = $djson->processToJson($template, $data,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
);
```
:::

::: tip Validate Your Schema
Use [Google's Rich Results Test](https://search.google.com/test/rich-results) to validate your generated JSON-LD.
:::

::: warning Type Preservation
Schema.org expects specific data types. DJson preserves types correctly:
- `"price": 19.99` (number) ✓
- `"price": "19.99"` (string) ✗
:::

## See Also

- [E-commerce Example](/examples/ecommerce) - Product catalog with Schema.org
- [Conditionals](/guide/conditionals) - Control field inclusion
- [Functions](/api/functions) - Format dates, numbers, strings
