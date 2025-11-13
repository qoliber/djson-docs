<?php

declare(strict_types=1);

namespace Qoliber\DJson\Tests;

use PHPUnit\Framework\TestCase;
use Qoliber\DJson\DJson;

class LogicalOperatorsTest extends TestCase
{
    private DJson $djson;

    protected function setUp(): void
    {
        $this->djson = new DJson();
    }

    // ====================
    // AND OPERATOR (&&) TESTS
    // ====================

    public function testSimpleAndOperator(): void
    {
        $jsonTemplate = '{
            "user": "{{user.name}}",
            "@djson if user.active && user.verified": {
                "status": "Active and Verified"
            }
        }';

        // Both true
        $data1 = ['user' => ['name' => 'John', 'active' => true, 'verified' => true]];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertEquals('Active and Verified', $result1['status']);

        // First true, second false
        $data2 = ['user' => ['name' => 'Jane', 'active' => true, 'verified' => false]];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertArrayNotHasKey('status', $result2);

        // Both false
        $data3 = ['user' => ['name' => 'Bob', 'active' => false, 'verified' => false]];
        $result3 = $this->djson->process($jsonTemplate, $data3);
        $this->assertArrayNotHasKey('status', $result3);
    }

    public function testAndWithComparison(): void
    {
        $jsonTemplate = '{
            "@djson if user.age >= 18 && user.country == \\"US\\"": {
                "canVote": true
            }
        }';

        $data1 = ['user' => ['age' => 25, 'country' => 'US']];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertTrue($result1['canVote']);

        $data2 = ['user' => ['age' => 17, 'country' => 'US']];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertArrayNotHasKey('canVote', $result2);

        $data3 = ['user' => ['age' => 25, 'country' => 'CA']];
        $result3 = $this->djson->process($jsonTemplate, $data3);
        $this->assertArrayNotHasKey('canVote', $result3);
    }

    public function testMultipleAndOperators(): void
    {
        $jsonTemplate = '{
            "@djson if user.active && user.verified && user.premium": {
                "access": "Full Premium Access"
            }
        }';

        // All true
        $data1 = ['user' => ['active' => true, 'verified' => true, 'premium' => true]];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertEquals('Full Premium Access', $result1['access']);

        // One false
        $data2 = ['user' => ['active' => true, 'verified' => true, 'premium' => false]];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertArrayNotHasKey('access', $result2);
    }

    // ====================
    // OR OPERATOR (||) TESTS
    // ====================

    public function testSimpleOrOperator(): void
    {
        $jsonTemplate = '{
            "@djson if product.inStock || product.preorder": {
                "available": true
            }
        }';

        // First true
        $data1 = ['product' => ['inStock' => true, 'preorder' => false]];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertTrue($result1['available']);

        // Second true
        $data2 = ['product' => ['inStock' => false, 'preorder' => true]];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertTrue($result2['available']);

        // Both true
        $data3 = ['product' => ['inStock' => true, 'preorder' => true]];
        $result3 = $this->djson->process($jsonTemplate, $data3);
        $this->assertTrue($result3['available']);

        // Both false
        $data4 = ['product' => ['inStock' => false, 'preorder' => false]];
        $result4 = $this->djson->process($jsonTemplate, $data4);
        $this->assertArrayNotHasKey('available', $result4);
    }

    public function testOrWithComparison(): void
    {
        $jsonTemplate = '{
            "@djson if user.role == \\"admin\\" || user.role == \\"moderator\\"": {
                "canModerate": true
            }
        }';

        $data1 = ['user' => ['role' => 'admin']];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertTrue($result1['canModerate']);

        $data2 = ['user' => ['role' => 'moderator']];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertTrue($result2['canModerate']);

        $data3 = ['user' => ['role' => 'user']];
        $result3 = $this->djson->process($jsonTemplate, $data3);
        $this->assertArrayNotHasKey('canModerate', $result3);
    }

    public function testMultipleOrOperators(): void
    {
        $jsonTemplate = '{
            "@djson if status == \\"new\\" || status == \\"pending\\" || status == \\"processing\\"": {
                "isActive": true
            }
        }';

        $data1 = ['status' => 'new'];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertTrue($result1['isActive']);

        $data2 = ['status' => 'pending'];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertTrue($result2['isActive']);

        $data3 = ['status' => 'completed'];
        $result3 = $this->djson->process($jsonTemplate, $data3);
        $this->assertArrayNotHasKey('isActive', $result3);
    }

    // ====================
    // NOT OPERATOR (!) TESTS
    // ====================

    public function testSimpleNotOperator(): void
    {
        $jsonTemplate = '{
            "@djson if !user.banned": {
                "access": "allowed"
            }
        }';

        $data1 = ['user' => ['banned' => false]];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertEquals('allowed', $result1['access']);

        $data2 = ['user' => ['banned' => true]];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertArrayNotHasKey('access', $result2);
    }

    public function testNotWithComparison(): void
    {
        $jsonTemplate = '{
            "@djson if !user.age < 18": {
                "isAdult": true
            }
        }';

        $data1 = ['user' => ['age' => 25]];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertTrue($result1['isAdult']);

        $data2 = ['user' => ['age' => 15]];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertArrayNotHasKey('isAdult', $result2);
    }

    // ====================
    // COMBINED OPERATORS TESTS
    // ====================

    public function testAndWithOr(): void
    {
        // Due to precedence: AND is evaluated before OR
        // user.active && user.admin || user.moderator
        // means: (user.active && user.admin) || user.moderator
        // So we need to test a different scenario
        $jsonTemplate = '{
            "@djson if user.admin || user.moderator && user.active": {
                "canManage": true
            }
        }';

        // Admin (regardless of active)
        $data1 = ['user' => ['admin' => true, 'moderator' => false, 'active' => false]];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertTrue($result1['canManage']);

        // Active moderator
        $data2 = ['user' => ['admin' => false, 'moderator' => true, 'active' => true]];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertTrue($result2['canManage']);

        // Inactive moderator (should fail)
        $data3 = ['user' => ['admin' => false, 'moderator' => true, 'active' => false]];
        $result3 = $this->djson->process($jsonTemplate, $data3);
        $this->assertArrayNotHasKey('canManage', $result3);

        // Neither admin nor moderator
        $data4 = ['user' => ['admin' => false, 'moderator' => false, 'active' => true]];
        $result4 = $this->djson->process($jsonTemplate, $data4);
        $this->assertArrayNotHasKey('canManage', $result4);
    }

    public function testNotWithAnd(): void
    {
        $jsonTemplate = '{
            "@djson if !user.banned && user.verified": {
                "status": "Good Standing"
            }
        }';

        $data1 = ['user' => ['banned' => false, 'verified' => true]];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertEquals('Good Standing', $result1['status']);

        $data2 = ['user' => ['banned' => true, 'verified' => true]];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertArrayNotHasKey('status', $result2);

        $data3 = ['user' => ['banned' => false, 'verified' => false]];
        $result3 = $this->djson->process($jsonTemplate, $data3);
        $this->assertArrayNotHasKey('status', $result3);
    }

    public function testNotWithOr(): void
    {
        $jsonTemplate = '{
            "@djson if !user.deleted || user.archived": {
                "visible": true
            }
        }';

        $data1 = ['user' => ['deleted' => false, 'archived' => false]];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertTrue($result1['visible']);

        $data2 = ['user' => ['deleted' => true, 'archived' => true]];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertTrue($result2['visible']);

        $data3 = ['user' => ['deleted' => true, 'archived' => false]];
        $result3 = $this->djson->process($jsonTemplate, $data3);
        $this->assertArrayNotHasKey('visible', $result3);
    }

    public function testComplexCondition(): void
    {
        $jsonTemplate = '{
            "@djson if user.age >= 18 && user.country == \\"US\\" && !user.banned": {
                "eligibleToVote": true
            }
        }';

        // All conditions met
        $data1 = ['user' => ['age' => 25, 'country' => 'US', 'banned' => false]];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertTrue($result1['eligibleToVote']);

        // Banned
        $data2 = ['user' => ['age' => 25, 'country' => 'US', 'banned' => true]];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertArrayNotHasKey('eligibleToVote', $result2);

        // Wrong country
        $data3 = ['user' => ['age' => 25, 'country' => 'CA', 'banned' => false]];
        $result3 = $this->djson->process($jsonTemplate, $data3);
        $this->assertArrayNotHasKey('eligibleToVote', $result3);

        // Too young
        $data4 = ['user' => ['age' => 17, 'country' => 'US', 'banned' => false]];
        $result4 = $this->djson->process($jsonTemplate, $data4);
        $this->assertArrayNotHasKey('eligibleToVote', $result4);
    }

    // ====================
    // OPERATORS IN LOOPS
    // ====================

    public function testLogicalOperatorsInLoop(): void
    {
        $jsonTemplate = '{
            "products": {
                "@djson for products as product": {
                    "name": "{{product.name}}",
                    "@djson if product.inStock && product.price > 50": {
                        "badge": "Premium In Stock"
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

        $this->assertEquals('Premium In Stock', $result['products'][0]['badge']);
        $this->assertArrayNotHasKey('badge', $result['products'][1]);
        $this->assertArrayNotHasKey('badge', $result['products'][2]);
    }

    // ====================
    // OPERATORS WITH ELSE
    // ====================

    public function testLogicalOperatorWithElse(): void
    {
        $jsonTemplate = '{
            "@djson if user.premium && user.active": {
                "tier": "Premium Active"
            },
            "@djson else": {
                "tier": "Standard"
            }
        }';

        $data1 = ['user' => ['premium' => true, 'active' => true]];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertEquals('Premium Active', $result1['tier']);

        $data2 = ['user' => ['premium' => true, 'active' => false]];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertEquals('Standard', $result2['tier']);

        $data3 = ['user' => ['premium' => false, 'active' => true]];
        $result3 = $this->djson->process($jsonTemplate, $data3);
        $this->assertEquals('Standard', $result3['tier']);
    }

    // ====================
    // OPERATORS IN TERNARY
    // ====================

    public function testLogicalOperatorInTernary(): void
    {
        $jsonTemplate = '{
            "status": "{{user.active && user.verified ? \\"Verified\\" : \\"Unverified\\"}}"
        }';

        $data1 = ['user' => ['active' => true, 'verified' => true]];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertEquals('Verified', $result1['status']);

        $data2 = ['user' => ['active' => true, 'verified' => false]];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertEquals('Unverified', $result2['status']);
    }

    // ====================
    // REAL WORLD SCENARIOS
    // ====================

    public function testAccessControlScenario(): void
    {
        $jsonTemplate = '{
            "user": "{{user.name}}",
            "@djson if user.role == \\"admin\\" && !user.suspended": {
                "canModerate": true,
                "canDeletePosts": true
            },
            "@djson if user.verified && !user.banned": {
                "canPost": true
            }
        }';

        $data = [
            'user' => [
                'name' => 'John',
                'role' => 'admin',
                'suspended' => false,
                'verified' => true,
                'banned' => false
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertTrue($result['canModerate']);
        $this->assertTrue($result['canDeletePosts']);
        $this->assertTrue($result['canPost']);
    }

    public function testEcommerceScenario(): void
    {
        $jsonTemplate = '{
            "products": {
                "@djson for products as product": {
                    "name": "{{product.name}}",
                    "@djson if product.stock > 0 && product.price > 0": {
                        "available": true,
                        "@djson if product.featured || product.discount > 0": {
                            "promoted": true
                        }
                    }
                }
            }
        }';

        $data = [
            'products' => [
                ['name' => 'Laptop', 'stock' => 10, 'price' => 999, 'featured' => true, 'discount' => 0],
                ['name' => 'Mouse', 'stock' => 5, 'price' => 29, 'featured' => false, 'discount' => 5],
                ['name' => 'Cable', 'stock' => 0, 'price' => 10, 'featured' => false, 'discount' => 0]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertTrue($result['products'][0]['available']);
        $this->assertTrue($result['products'][0]['promoted']);

        $this->assertTrue($result['products'][1]['available']);
        $this->assertTrue($result['products'][1]['promoted']);

        $this->assertArrayNotHasKey('available', $result['products'][2]);
    }
}
