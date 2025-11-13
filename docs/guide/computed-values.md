# Computed Values

Calculate values dynamically using the `@djson set` directive.

## Syntax

```php
'@djson set variableName = expression'
```

## Arithmetic

```php
'@djson set total = price * quantity'
'@djson set discount = total * 0.15'
'@djson set finalPrice = total - discount'
```

## Example

```php
$template = [
    '@djson set subtotal = price * quantity',
    '@djson set tax = subtotal * 0.15',
    '@djson set total = subtotal + tax',
    'price' => '{​{price}}',
    'quantity' => '{​{quantity}}',
    'subtotal' => '@djson number_format {​{subtotal}} 2',
    'tax' => '@djson number_format {​{tax}} 2',
    'total' => '@djson number_format {​{total}} 2'
];
```

See [Directives API](/api/directives#djson-set) for more details.
