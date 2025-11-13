<?php

declare(strict_types=1);

namespace Qoliber\DJson\Tests;

use PHPUnit\Framework\TestCase;
use Qoliber\DJson\DJson;

class NestedConditionalsTest extends TestCase
{
    private DJson $djson;

    protected function setUp(): void
    {
        $this->djson = new DJson();
    }

    public function testSimpleNestedIf(): void
    {
        $jsonTemplate = '{
            "user": "{{user.name}}",
            "@djson if user.age >= 18": {
                "status": "adult",
                "@djson if user.premium": {
                    "badge": "Premium Adult"
                },
                "@djson else": {
                    "badge": "Regular Adult"
                }
            },
            "@djson else": {
                "status": "minor"
            }
        }';

        // Test: Adult with premium
        $data1 = ['user' => ['name' => 'John', 'age' => 25, 'premium' => true]];
        $result1 = $this->djson->process($jsonTemplate, $data1);

        $this->assertEquals('adult', $result1['status']);
        $this->assertEquals('Premium Adult', $result1['badge']);

        // Test: Adult without premium
        $data2 = ['user' => ['name' => 'Alice', 'age' => 25, 'premium' => false]];
        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertEquals('adult', $result2['status']);
        $this->assertEquals('Regular Adult', $result2['badge']);

        // Test: Minor
        $data3 = ['user' => ['name' => 'Bob', 'age' => 15]];
        $result3 = $this->djson->process($jsonTemplate, $data3);

        $this->assertEquals('minor', $result3['status']);
        $this->assertArrayNotHasKey('badge', $result3);
    }

    public function testTripleNestedIf(): void
    {
        $jsonTemplate = '{
            "@djson if user.active": {
                "status": "active",
                "@djson if user.verified": {
                    "verification": "verified",
                    "@djson if user.premium": {
                        "tier": "premium"
                    },
                    "@djson else": {
                        "tier": "free"
                    }
                }
            }
        }';

        // Test: All conditions true
        $data1 = ['user' => ['active' => true, 'verified' => true, 'premium' => true]];
        $result1 = $this->djson->process($jsonTemplate, $data1);

        $this->assertEquals('active', $result1['status']);
        $this->assertEquals('verified', $result1['verification']);
        $this->assertEquals('premium', $result1['tier']);

        // Test: Active and verified, but not premium
        $data2 = ['user' => ['active' => true, 'verified' => true, 'premium' => false]];
        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertEquals('active', $result2['status']);
        $this->assertEquals('verified', $result2['verification']);
        $this->assertEquals('free', $result2['tier']);

        // Test: Active but not verified
        $data3 = ['user' => ['active' => true, 'verified' => false]];
        $result3 = $this->djson->process($jsonTemplate, $data3);

        $this->assertEquals('active', $result3['status']);
        $this->assertArrayNotHasKey('verification', $result3);
        $this->assertArrayNotHasKey('tier', $result3);
    }

    public function testNestedIfInLoop(): void
    {
        $jsonTemplate = '{
            "products": {
                "@djson for products as product": {
                    "name": "{{product.name}}",
                    "@djson if product.inStock": {
                        "availability": "In Stock",
                        "@djson if product.price > 100": {
                            "category": "Premium"
                        },
                        "@djson else": {
                            "category": "Standard"
                        }
                    },
                    "@djson else": {
                        "availability": "Out of Stock"
                    }
                }
            }
        }';

        $data = [
            'products' => [
                ['name' => 'Laptop', 'inStock' => true, 'price' => 999],
                ['name' => 'Mouse', 'inStock' => true, 'price' => 29],
                ['name' => 'Monitor', 'inStock' => false, 'price' => 299]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertCount(3, $result['products']);

        // Product 1: In stock, premium
        $this->assertEquals('In Stock', $result['products'][0]['availability']);
        $this->assertEquals('Premium', $result['products'][0]['category']);

        // Product 2: In stock, standard
        $this->assertEquals('In Stock', $result['products'][1]['availability']);
        $this->assertEquals('Standard', $result['products'][1]['category']);

        // Product 3: Out of stock
        $this->assertEquals('Out of Stock', $result['products'][2]['availability']);
        $this->assertArrayNotHasKey('category', $result['products'][2]);
    }

    public function testNestedIfWithStringComparison(): void
    {
        $jsonTemplate = '{
            "user": "{{user.name}}",
            "@djson if user.role == \\"admin\\"": {
                "access": "admin",
                "@djson if user.department == \\"IT\\"": {
                    "permissions": "full"
                },
                "@djson else": {
                    "permissions": "limited"
                }
            },
            "@djson else": {
                "access": "user"
            }
        }';

        // Test: IT Admin
        $data1 = ['user' => ['name' => 'John', 'role' => 'admin', 'department' => 'IT']];
        $result1 = $this->djson->process($jsonTemplate, $data1);

        $this->assertEquals('admin', $result1['access']);
        $this->assertEquals('full', $result1['permissions']);

        // Test: Non-IT Admin
        $data2 = ['user' => ['name' => 'Alice', 'role' => 'admin', 'department' => 'Sales']];
        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertEquals('admin', $result2['access']);
        $this->assertEquals('limited', $result2['permissions']);

        // Test: Regular User
        $data3 = ['user' => ['name' => 'Bob', 'role' => 'user']];
        $result3 = $this->djson->process($jsonTemplate, $data3);

        $this->assertEquals('user', $result3['access']);
        $this->assertArrayNotHasKey('permissions', $result3);
    }

    public function testDeepNesting(): void
    {
        $jsonTemplate = '{
            "@djson if level1": {
                "l1": true,
                "@djson if level2": {
                    "l2": true,
                    "@djson if level3": {
                        "l3": true,
                        "@djson if level4": {
                            "l4": true
                        }
                    }
                }
            }
        }';

        // All levels true
        $data1 = ['level1' => true, 'level2' => true, 'level3' => true, 'level4' => true];
        $result1 = $this->djson->process($jsonTemplate, $data1);

        $this->assertTrue($result1['l1']);
        $this->assertTrue($result1['l2']);
        $this->assertTrue($result1['l3']);
        $this->assertTrue($result1['l4']);

        // Only first 2 levels true
        $data2 = ['level1' => true, 'level2' => true, 'level3' => false];
        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertTrue($result2['l1']);
        $this->assertTrue($result2['l2']);
        $this->assertArrayNotHasKey('l3', $result2);
        $this->assertArrayNotHasKey('l4', $result2);
    }

    public function testNestedUnlessWithIf(): void
    {
        $jsonTemplate = '{
            "@djson unless user.banned": {
                "canAccess": true,
                "@djson if user.verified": {
                    "status": "verified-active"
                },
                "@djson else": {
                    "status": "unverified-active"
                }
            },
            "@djson else": {
                "canAccess": false,
                "status": "banned"
            }
        }';

        // Not banned, verified
        $data1 = ['user' => ['banned' => false, 'verified' => true]];
        $result1 = $this->djson->process($jsonTemplate, $data1);

        $this->assertTrue($result1['canAccess']);
        $this->assertEquals('verified-active', $result1['status']);

        // Not banned, unverified
        $data2 = ['user' => ['banned' => false, 'verified' => false]];
        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertTrue($result2['canAccess']);
        $this->assertEquals('unverified-active', $result2['status']);

        // Banned
        $data3 = ['user' => ['banned' => true]];
        $result3 = $this->djson->process($jsonTemplate, $data3);

        $this->assertFalse($result3['canAccess']);
        $this->assertEquals('banned', $result3['status']);
    }

    public function testNestedExistsWithIf(): void
    {
        $jsonTemplate = '{
            "user": "{{user.name}}",
            "@djson exists user.subscription": {
                "hasSubscription": true,
                "@djson if user.subscription.active": {
                    "status": "active-subscriber"
                },
                "@djson else": {
                    "status": "inactive-subscriber"
                }
            }
        }';

        // Has active subscription
        $data1 = ['user' => ['name' => 'John', 'subscription' => ['active' => true]]];
        $result1 = $this->djson->process($jsonTemplate, $data1);

        $this->assertTrue($result1['hasSubscription']);
        $this->assertEquals('active-subscriber', $result1['status']);

        // Has inactive subscription
        $data2 = ['user' => ['name' => 'Alice', 'subscription' => ['active' => false]]];
        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertTrue($result2['hasSubscription']);
        $this->assertEquals('inactive-subscriber', $result2['status']);

        // No subscription
        $data3 = ['user' => ['name' => 'Bob']];
        $result3 = $this->djson->process($jsonTemplate, $data3);

        $this->assertArrayNotHasKey('hasSubscription', $result3);
        $this->assertArrayNotHasKey('status', $result3);
    }

    public function testComplexNestedScenario(): void
    {
        $jsonTemplate = '{
            "products": {
                "@djson for products as product": {
                    "name": "{{product.name}}",
                    "@djson if product.stock > 0": {
                        "inStock": true,
                        "@djson if product.discount": {
                            "hasDiscount": true,
                            "@djson if product.discount > 20": {
                                "badge": "Big Sale"
                            },
                            "@djson else": {
                                "badge": "On Sale"
                            }
                        },
                        "@djson else": {
                            "hasDiscount": false
                        }
                    },
                    "@djson else": {
                        "inStock": false
                    }
                }
            }
        }';

        $data = [
            'products' => [
                ['name' => 'Laptop', 'stock' => 10, 'discount' => 25],
                ['name' => 'Mouse', 'stock' => 5, 'discount' => 10],
                ['name' => 'Keyboard', 'stock' => 3, 'discount' => 0],
                ['name' => 'Monitor', 'stock' => 0, 'discount' => 15]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        // Laptop: In stock, big discount
        $this->assertTrue($result['products'][0]['inStock']);
        $this->assertTrue($result['products'][0]['hasDiscount']);
        $this->assertEquals('Big Sale', $result['products'][0]['badge']);

        // Mouse: In stock, small discount
        $this->assertTrue($result['products'][1]['inStock']);
        $this->assertTrue($result['products'][1]['hasDiscount']);
        $this->assertEquals('On Sale', $result['products'][1]['badge']);

        // Keyboard: In stock, no discount
        $this->assertTrue($result['products'][2]['inStock']);
        $this->assertFalse($result['products'][2]['hasDiscount']);
        $this->assertArrayNotHasKey('badge', $result['products'][2]);

        // Monitor: Out of stock
        $this->assertFalse($result['products'][3]['inStock']);
        $this->assertArrayNotHasKey('hasDiscount', $result['products'][3]);
    }
}
