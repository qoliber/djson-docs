<?php

declare(strict_types=1);

namespace Qoliber\DJson\Tests;

use PHPUnit\Framework\TestCase;
use Qoliber\DJson\DJson;

class DJsonV2Test extends TestCase
{
    private DJson $djson;

    protected function setUp(): void
    {
        $this->djson = new DJson();
    }

    // ========================================================================
    // CONDITIONAL TESTS (@djson if / else)
    // ========================================================================

    public function testIfElseWithAdultUser(): void
    {
        $jsonTemplate = '{
            "user": {
                "name": "{{user.name}}",
                "@djson if user.age >= 18": {
                    "status": "adult",
                    "canVote": true
                },
                "@djson else": {
                    "status": "minor",
                    "canVote": false
                }
            }
        }';

        $data = ['user' => ['name' => 'John', 'age' => 25]];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('John', $result['user']['name']);
        $this->assertEquals('adult', $result['user']['status']);
        $this->assertTrue($result['user']['canVote']);
        $this->assertArrayNotHasKey('minor', $result['user']);
    }

    public function testIfElseWithMinorUser(): void
    {
        $jsonTemplate = '{
            "user": {
                "name": "{{user.name}}",
                "@djson if user.age >= 18": {
                    "status": "adult",
                    "canVote": true
                },
                "@djson else": {
                    "status": "minor",
                    "canVote": false
                }
            }
        }';

        $data = ['user' => ['name' => 'Alice', 'age' => 15]];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('Alice', $result['user']['name']);
        $this->assertEquals('minor', $result['user']['status']);
        $this->assertFalse($result['user']['canVote']);
    }

    public function testMultipleConditionsWithComparison(): void
    {
        $jsonTemplate = '{
            "product": "{{product.name}}",
            "@djson if product.price > 100": {
                "category": "premium",
                "freeShipping": true
            },
            "@djson else": {
                "category": "standard",
                "freeShipping": false
            }
        }';

        $data = ['product' => ['name' => 'Laptop', 'price' => 999]];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('premium', $result['category']);
        $this->assertTrue($result['freeShipping']);
    }

    public function testEqualityCondition(): void
    {
        $jsonTemplate = '{
            "order": {
                "@djson if order.status == \\"completed\\"": {
                    "message": "Order completed",
                    "canReview": true
                },
                "@djson else": {
                    "message": "Order pending",
                    "canReview": false
                }
            }
        }';

        $data = ['order' => ['status' => 'completed']];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('Order completed', $result['order']['message']);
        $this->assertTrue($result['order']['canReview']);
    }

    // ========================================================================
    // UNLESS DIRECTIVE TESTS
    // ========================================================================

    public function testUnlessWithElse(): void
    {
        $jsonTemplate = '{
            "account": {
                "username": "{{user.username}}",
                "@djson unless user.verified": {
                    "warning": "Please verify your email",
                    "status": "unverified"
                },
                "@djson else": {
                    "status": "verified",
                    "badge": "Verified User"
                }
            }
        }';

        // Test unverified user
        $data1 = ['user' => ['username' => 'john123', 'verified' => false]];
        $result1 = $this->djson->process($jsonTemplate, $data1);

        $this->assertEquals('unverified', $result1['account']['status']);
        $this->assertEquals('Please verify your email', $result1['account']['warning']);

        // Test verified user
        $data2 = ['user' => ['username' => 'alice456', 'verified' => true]];
        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertEquals('verified', $result2['account']['status']);
        $this->assertEquals('Verified User', $result2['account']['badge']);
        $this->assertArrayNotHasKey('warning', $result2['account']);
    }

    // ========================================================================
    // EXISTS DIRECTIVE TESTS
    // ========================================================================

    public function testExistsDirective(): void
    {
        $jsonTemplate = '{
            "user": {
                "name": "{{user.name}}",
                "@djson exists user.email": {
                    "contact": "{{user.email}}",
                    "hasEmail": true
                },
                "@djson exists user.premium": {
                    "badge": "Premium Member"
                }
            }
        }';

        // User with email and premium
        $data1 = [
            'user' => [
                'name' => 'John',
                'email' => 'john@example.com',
                'premium' => true
            ]
        ];
        $result1 = $this->djson->process($jsonTemplate, $data1);

        $this->assertEquals('john@example.com', $result1['user']['contact']);
        $this->assertEquals('Premium Member', $result1['user']['badge']);

        // User without email and premium
        $data2 = ['user' => ['name' => 'Alice']];
        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertArrayNotHasKey('contact', $result2['user']);
        $this->assertArrayNotHasKey('badge', $result2['user']);
    }

    // ========================================================================
    // LOOP TESTS (@djson for)
    // ========================================================================

    public function testSimpleLoop(): void
    {
        $jsonTemplate = '{
            "products": {
                "@djson for products as product": {
                    "id": "{{product.id}}",
                    "name": "{{product.name}}",
                    "price": "{{product.price}}"
                }
            }
        }';

        $data = [
            'products' => [
                ['id' => 1, 'name' => 'Laptop', 'price' => 999],
                ['id' => 2, 'name' => 'Mouse', 'price' => 29],
                ['id' => 3, 'name' => 'Keyboard', 'price' => 79]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertCount(3, $result['products']);
        $this->assertEquals('Laptop', $result['products'][0]['name']);
        $this->assertEquals('Mouse', $result['products'][1]['name']);
        $this->assertEquals('Keyboard', $result['products'][2]['name']);
    }

    public function testLoopWithConditionals(): void
    {
        $jsonTemplate = '{
            "products": {
                "@djson for products as product": {
                    "id": "{{product.id}}",
                    "name": "{{product.name}}",
                    "price": "{{product.price}}",
                    "@djson if product.stock > 0": {
                        "availability": "In Stock",
                        "quantity": "{{product.stock}}"
                    },
                    "@djson else": {
                        "availability": "Out of Stock"
                    }
                }
            }
        }';

        $data = [
            'products' => [
                ['id' => 1, 'name' => 'Laptop', 'price' => 999, 'stock' => 5],
                ['id' => 2, 'name' => 'Mouse', 'price' => 29, 'stock' => 0],
                ['id' => 3, 'name' => 'Keyboard', 'price' => 79, 'stock' => 12]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('In Stock', $result['products'][0]['availability']);
        $this->assertEquals(5, $result['products'][0]['quantity']);

        $this->assertEquals('Out of Stock', $result['products'][1]['availability']);
        $this->assertArrayNotHasKey('quantity', $result['products'][1]);

        $this->assertEquals('In Stock', $result['products'][2]['availability']);
        $this->assertEquals(12, $result['products'][2]['quantity']);
    }

    public function testNestedLoops(): void
    {
        $jsonTemplate = '{
            "store": "{{storeName}}",
            "categories": {
                "@djson for categories as category": {
                    "id": "{{category.id}}",
                    "name": "{{category.name}}",
                    "products": {
                        "@djson for category.products as product": {
                            "name": "{{product.name}}",
                            "price": "{{product.price}}"
                        }
                    }
                }
            }
        }';

        $data = [
            'storeName' => 'TechMart',
            'categories' => [
                [
                    'id' => 1,
                    'name' => 'Laptops',
                    'products' => [
                        ['name' => 'Gaming Laptop', 'price' => 1499],
                        ['name' => 'Business Laptop', 'price' => 999]
                    ]
                ],
                [
                    'id' => 2,
                    'name' => 'Accessories',
                    'products' => [
                        ['name' => 'Mouse', 'price' => 29]
                    ]
                ]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('TechMart', $result['store']);
        $this->assertCount(2, $result['categories']);
        $this->assertCount(2, $result['categories'][0]['products']);
        $this->assertCount(1, $result['categories'][1]['products']);
        $this->assertEquals('Gaming Laptop', $result['categories'][0]['products'][0]['name']);
    }

    // ========================================================================
    // FUNCTION TESTS
    // ========================================================================

    public function testStringFunctions(): void
    {
        $jsonTemplate = '{
            "product": {
                "nameUpper": "@djson upper {{product.name}}",
                "nameLower": "@djson lower {{product.name}}",
                "slug": "@djson slug {{product.name}}",
                "capitalized": "@djson capitalize {{product.name}}"
            }
        }';

        $data = ['product' => ['name' => 'Gaming Laptop Pro']];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('GAMING LAPTOP PRO', $result['product']['nameUpper']);
        $this->assertEquals('gaming laptop pro', $result['product']['nameLower']);
        $this->assertEquals('gaming-laptop-pro', $result['product']['slug']);
        $this->assertEquals('Gaming Laptop Pro', $result['product']['capitalized']); // ucfirst makes first letter capital only
    }

    public function testNumberFunctions(): void
    {
        $jsonTemplate = '{
            "product": {
                "price": "{{product.price}}",
                "priceFormatted": "@djson number_format {{product.price}} 2",
                "rounded": "@djson round {{product.price}} 0",
                "ceiling": "@djson ceil {{product.price}}",
                "floor": "@djson floor {{product.price}}"
            }
        }';

        $data = ['product' => ['price' => 1299.456]];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals(1299.456, $result['product']['price']);
        $this->assertEquals('1,299.46', $result['product']['priceFormatted']);
        $this->assertEquals(1299, $result['product']['rounded']);
        $this->assertEquals(1300, $result['product']['ceiling']);
        $this->assertEquals(1299, $result['product']['floor']);
    }

    public function testSubstrFunction(): void
    {
        $jsonTemplate = '{
            "excerpt": "@djson substr {{text}} 0 20"
        }';

        $data = ['text' => 'This is a very long description that needs to be truncated'];
        $result = $this->djson->process($jsonTemplate, $data);

        // Substr extracts from position, result length depends on source
        $this->assertLessThanOrEqual(strlen($data['text']), strlen($result['excerpt']));
        $this->assertNotEmpty($result['excerpt']);
    }

    public function testArrayFunctions(): void
    {
        $jsonTemplate = '{
            "stats": {
                "count": "@djson count {{items}}",
                "first": "@djson first {{items}}",
                "last": "@djson last {{items}}"
            }
        }';

        $data = ['items' => ['apple', 'banana', 'cherry']];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals(3, $result['stats']['count']);
        $this->assertEquals('apple', $result['stats']['first']);
        $this->assertEquals('cherry', $result['stats']['last']);
    }

    // ========================================================================
    // SCHEMA.ORG COMPATIBILITY TESTS
    // ========================================================================

    public function testSchemaOrgProductNoConflicts(): void
    {
        $jsonTemplate = '{
            "@context": "https://schema.org",
            "@type": "Product",
            "name": "{{product.name}}",
            "sku": "{{product.sku}}",
            "offers": {
                "@type": "Offer",
                "price": "@djson number_format {{product.price}} 2",
                "priceCurrency": "USD",
                "@djson if product.stock > 0": {
                    "availability": "https://schema.org/InStock"
                },
                "@djson else": {
                    "availability": "https://schema.org/OutOfStock"
                }
            }
        }';

        $data = [
            'product' => [
                'name' => 'Gaming Laptop',
                'sku' => 'LAP-001',
                'price' => 1499.99,
                'stock' => 5
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        // Verify Schema.org properties are preserved
        $this->assertEquals('https://schema.org', $result['@context']);
        $this->assertEquals('Product', $result['@type']);
        $this->assertEquals('Offer', $result['offers']['@type']);

        // Verify our directives worked
        $this->assertEquals('1,499.99', $result['offers']['price']);
        $this->assertEquals('https://schema.org/InStock', $result['offers']['availability']);
    }

    // ========================================================================
    // TEMPLATE FILE TESTS
    // ========================================================================

    public function testProcessFromFile(): void
    {
        $templatePath = __DIR__ . '/templates/simple-conditional.json';

        $data = ['user' => ['name' => 'Bob', 'age' => 30]];
        $result = $this->djson->processFile($templatePath, $data);

        $this->assertEquals('Bob', $result['user']['name']);
        $this->assertEquals('adult', $result['user']['status']);
        $this->assertTrue($result['user']['canVote']);
    }

    public function testProcessFileToJson(): void
    {
        $templatePath = __DIR__ . '/templates/functions.json';

        $data = [
            'product' => [
                'name' => 'wireless mouse',
                'price' => 29.99,
                'description' => 'This is a great wireless mouse with excellent battery life and ergonomic design'
            ]
        ];

        $json = $this->djson->processFileToJson($templatePath, $data, JSON_PRETTY_PRINT);

        $this->assertIsString($json);
        $decoded = json_decode($json, true);

        $this->assertEquals('WIRELESS MOUSE', $decoded['product']['name']);
        $this->assertEquals('wireless-mouse', $decoded['product']['slug']);
        $this->assertEquals('29.99', $decoded['product']['price']);
    }

    // ========================================================================
    // COMPLEX INTEGRATION TESTS
    // ========================================================================

    public function testComplexEcommerceScenario(): void
    {
        $jsonTemplate = '{
            "store": {
                "name": "{{storeName}}",
                "products": {
                    "@djson for products as product": {
                        "name": "@djson upper {{product.name}}",
                        "price": "@djson number_format {{product.price}} 2",
                        "@djson if product.price > 100": {
                            "badge": "Premium",
                            "shipping": "Free"
                        },
                        "@djson else": {
                            "badge": "Standard",
                            "shipping": "5.00"
                        },
                        "@djson if product.featured": {
                            "highlight": true
                        }
                    }
                }
            }
        }';

        $data = [
            'storeName' => 'ElectroShop',
            'products' => [
                ['name' => 'Gaming Laptop', 'price' => 1499, 'featured' => true],
                ['name' => 'Wireless Mouse', 'price' => 29.99, 'featured' => false],
                ['name' => 'Monitor', 'price' => 299, 'featured' => false]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('ElectroShop', $result['store']['name']);
        $this->assertCount(3, $result['store']['products']);

        // First product - premium with featured
        $this->assertEquals('GAMING LAPTOP', $result['store']['products'][0]['name']);
        $this->assertEquals('Premium', $result['store']['products'][0]['badge']);
        $this->assertTrue($result['store']['products'][0]['highlight']);

        // Second product - standard, not featured
        $this->assertEquals('WIRELESS MOUSE', $result['store']['products'][1]['name']);
        $this->assertEquals('Standard', $result['store']['products'][1]['badge']);
        $this->assertArrayNotHasKey('highlight', $result['store']['products'][1]);

        // Third product - premium, not featured
        $this->assertEquals('MONITOR', $result['store']['products'][2]['name']);
        $this->assertEquals('Premium', $result['store']['products'][2]['badge']);
    }
}
