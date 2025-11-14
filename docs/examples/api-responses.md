# API Responses

Examples of using DJson to generate REST API responses with pagination, filtering, and error handling.

## Basic API Response

```php
use Qoliber\DJson\DJson;

$djson = new DJson();

$template = [
    'success' => true,
    'data' => [
        '@djson for items as item',
        'id' => '{​{item.id}}',
        'name' => '{​{item.name}}',
        'createdAt' => '@djson date {​{item.timestamp}} "c"'
    ],
    'meta' => [
        'count' => '@djson count {​{items}}',
        'timestamp' => '{​{requestTime}}'
    ]
];

$data = [
    'items' => [
        ['id' => 1, 'name' => 'Item 1', 'timestamp' => 1704067200],
        ['id' => 2, 'name' => 'Item 2', 'timestamp' => 1704153600]
    ],
    'requestTime' => date('Y-m-d H:i:s')
];

$json = $djson->processToJson($template, $data, JSON_PRETTY_PRINT);
```

**Output:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Item 1",
      "createdAt": "2024-01-01T00:00:00+00:00"
    },
    {
      "id": 2,
      "name": "Item 2",
      "createdAt": "2024-01-02T00:00:00+00:00"
    }
  ],
  "meta": {
    "count": 2,
    "timestamp": "2025-01-13 10:30:00"
  }
}
```

## Paginated Response

```php
$template = [
    'success' => true,
    'data' => [
        '@djson for items as item',
        'id' => '{​{item.id}}',
        'title' => '{​{item.title}}'
    ],
    'pagination' => [
        'currentPage' => '{​{page}}',
        'perPage' => '{​{perPage}}',
        'total' => '{​{totalItems}}',
        '@djson set totalPages = totalItems / perPage',
        'totalPages' => '@djson ceil {​{totalPages}}',
        'hasNextPage' => '{​{page < totalPages}}',
        'hasPreviousPage' => '{​{page > 1}}'
    ]
];

$data = [
    'items' => [
        ['id' => 1, 'title' => 'First'],
        ['id' => 2, 'title' => 'Second']
    ],
    'page' => 1,
    'perPage' => 10,
    'totalItems' => 25
];
```

## Error Response

```php
$template = [
    'success' => false,
    'error' => [
        'code' => '{​{error.code}}',
        'message' => '{​{error.message}}',
        'details' => [
            '@djson if error.details',
            '@djson for error.details as detail',
            'field' => '{​{detail.field}}',
            'message' => '{​{detail.message}}'
        ]
    ],
    'meta' => [
        'timestamp' => '{​{timestamp}}',
        'requestId' => '{​{requestId}}'
    ]
];

$data = [
    'error' => [
        'code' => 'VALIDATION_ERROR',
        'message' => 'The request data is invalid',
        'details' => [
            ['field' => 'email', 'message' => 'Invalid email format'],
            ['field' => 'age', 'message' => 'Must be at least 18']
        ]
    ],
    'timestamp' => date('c'),
    'requestId' => uniqid()
];
```

## Resource with Relationships

```php
$template = [
    'success' => true,
    'data' => [
        'id' => '{​{post.id}}',
        'title' => '{​{post.title}}',
        'content' => '{​{post.content}}',
        'author' => [
            '@djson if post.author',
            'id' => '{​{post.author.id}}',
            'name' => '{​{post.author.name}}',
            'avatar' => '{​{post.author.avatar}}'
        ],
        'comments' => [
            '@djson if post.comments',
            'count' => '@djson count {​{post.comments}}',
            'items' => [
                '@djson for post.comments as comment',
                'id' => '{​{comment.id}}',
                'text' => '{​{comment.text}}',
                'author' => '{​{comment.authorName}}'
            ]
        ],
        'tags' => [
            '@djson if post.tags',
            '@djson for post.tags as tag',
            '{​{tag}}'
        ]
    ]
];
```

## Conditional Fields Based on Auth

```php
$template = [
    'success' => true,
    'data' => [
        '@djson for users as user',
        'id' => '{​{user.id}}',
        'name' => '{​{user.name}}',
        'email' => [
            '@djson if isAuthenticated',
            '{​{user.email}}'
        ],
        'phone' => [
            '@djson if isAdmin',
            '{​{user.phone}}'
        ],
        'permissions' => [
            '@djson if isAdmin',
            '@djson for user.permissions as perm',
            '{​{perm}}'
        ]
    ]
];
```

## Search Results with Highlights

```php
$template = [
    'success' => true,
    'query' => '{​{searchQuery}}',
    'results' => [
        '@djson for results as result',
        'id' => '{​{result.id}}',
        'title' => '{​{result.title}}',
        'snippet' => '@djson substr {​{result.content}} 0 200',
        'score' => '@djson round {​{result.score}} 2',
        'type' => '{​{result.type}}',
        'url' => '{​{result.url}}',
        'highlights' => [
            '@djson if result.highlights',
            '@djson for result.highlights as highlight',
            '{​{highlight}}'
        ]
    ],
    'meta' => [
        'total' => '{​{totalResults}}',
        'took' => '{​{searchTime}}',
        'page' => '{​{page}}'
    ]
];
```

## Status Endpoint

```php
$template = '{
    "status": "{{status}}",
    "version": "{{version}}",
    "services": {
        "@djson for services as service": {
            "name": "{{service.name}}",
            "@djson match service.status": {
                "@djson case \\"healthy\\"": {
                    "code": "OK",
                    "color": "green"
                },
                "@djson case \\"degraded\\"": {
                    "code": "WARNING",
                    "color": "yellow"
                },
                "@djson default": {
                    "code": "ERROR",
                    "color": "red"
                }
            },
            "responseTime": "{{service.responseTime}}",
            "lastChecked": "@djson date {{service.lastCheck}} \\"c\\""
        }
    },
    "timestamp": "{{timestamp}}"
}';
```

## Bulk Operation Response

```php
$template = [
    'success' => true,
    'summary' => [
        'total' => '@djson count {​{results}}',
        '@djson set successCount = 0',
        '@djson for results as result',
        '@djson if result.success',
        '@djson set successCount = successCount + 1',
        'succeeded' => '{​{successCount}}',
        '@djson set failedCount = total - successCount',
        'failed' => '{​{failedCount}}'
    ],
    'results' => [
        '@djson for results as result',
        'id' => '{​{result.id}}',
        'success' => '{​{result.success}}',
        'message' => '{​{result.success ? "OK" : result.error}}',
        'error' => [
            '@djson unless result.success',
            'code' => '{​{result.errorCode}}',
            'details' => '{​{result.errorDetails}}'
        ]
    ]
];
```

## Rate Limit Headers

```php
$template = [
    'data' => '{​{responseData}}',
    'rateLimit' => [
        'limit' => '{​{rateLimit.limit}}',
        'remaining' => '{​{rateLimit.remaining}}',
        '@djson set resetTime = rateLimit.resetAt',
        'reset' => '@djson date {​{resetTime}} "c"',
        'resetIn' => '{​{rateLimit.resetIn}}'
    ]
];
```

## HATEOAS Links

```php
$template = [
    'data' => [
        'id' => '{​{item.id}}',
        'name' => '{​{item.name}}'
    ],
    'links' => [
        'self' => [
            'href' => '/api/items/{​{item.id}}',
            'method' => 'GET'
        ],
        'update' => [
            '@djson if canEdit',
            'href' => '/api/items/{​{item.id}}',
            'method' => 'PUT'
        ],
        'delete' => [
            '@djson if canDelete',
            'href' => '/api/items/{​{item.id}}',
            'method' => 'DELETE'
        ],
        'related' => [
            '@djson if item.categoryId',
            'href' => '/api/categories/{​{item.categoryId}}',
            'method' => 'GET'
        ]
    ]
];
```

## Benefits

✅ **Consistent** - Same structure across endpoints
✅ **Maintainable** - Change response format in one place
✅ **Testable** - Mock data easily for tests
✅ **Documented** - Structure is self-documenting
✅ **Flexible** - Adapt to different client needs

## See Also

- [E-commerce Catalog](/examples/ecommerce) - Product listings
- [Functions](/api/functions) - Format response data
- [Conditionals](/guide/conditionals) - Conditional fields
