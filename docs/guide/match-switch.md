# Match / Switch

Pattern matching for multiple conditions, similar to PHP's `match` expression.

## Syntax

```php
'@djson match variable'
'@djson case value1' => content1
'@djson case value2' => content2
'@djson default' => defaultContent
```

## Basic Example

```php
$template = [
    '@djson match user.role',
    '@djson case "admin"' => [
        'permissions' => 'full',
        'access' => 'all'
    ],
    '@djson case "editor"' => [
        'permissions' => 'edit',
        'access' => 'content'
    ],
    '@djson default' => [
        'permissions' => 'read',
        'access' => 'public'
    ]
];
```

See [Directives API](/api/directives#djson-match-djson-switch) for full documentation.
