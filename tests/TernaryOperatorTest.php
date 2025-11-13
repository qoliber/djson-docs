<?php

declare(strict_types=1);

namespace Qoliber\DJson\Tests;

use PHPUnit\Framework\TestCase;
use Qoliber\DJson\DJson;

class TernaryOperatorTest extends TestCase
{
    private DJson $djson;

    protected function setUp(): void
    {
        $this->djson = new DJson();
    }

    public function testSimpleTernaryOperator(): void
    {
        $jsonTemplate = '{
            "user": {
                "name": "{{user.name}}",
                "status": "{{user.active ? \\"Online\\" : \\"Offline\\"}}"
            }
        }';

        $data = ['user' => ['name' => 'John', 'active' => true]];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('Online', $result['user']['status']);

        $data2 = ['user' => ['name' => 'Jane', 'active' => false]];
        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertEquals('Offline', $result2['user']['status']);
    }

    public function testTernaryWithComparison(): void
    {
        $jsonTemplate = '{
            "product": {
                "name": "{{product.name}}",
                "badge": "{{product.price > 100 ? \\"Premium\\" : \\"Standard\\"}}"
            }
        }';

        $data = ['product' => ['name' => 'Laptop', 'price' => 999]];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('Premium', $result['product']['badge']);

        $data2 = ['product' => ['name' => 'Mouse', 'price' => 29]];
        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertEquals('Standard', $result2['product']['badge']);
    }

    public function testTernaryWithVariables(): void
    {
        $jsonTemplate = '{
            "user": {
                "role": "{{user.isAdmin ? user.adminRole : user.userRole}}"
            }
        }';

        $data = [
            'user' => [
                'isAdmin' => true,
                'adminRole' => 'Administrator',
                'userRole' => 'Guest'
            ]
        ];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('Administrator', $result['user']['role']);

        $data2 = [
            'user' => [
                'isAdmin' => false,
                'adminRole' => 'Administrator',
                'userRole' => 'Guest'
            ]
        ];
        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertEquals('Guest', $result2['user']['role']);
    }

    public function testTernaryInLoop(): void
    {
        $jsonTemplate = '{
            "products": {
                "@djson for products as product": {
                    "name": "{{product.name}}",
                    "availability": "{{product.inStock ? \\"Available\\" : \\"Out of Stock\\"}}"
                }
            }
        }';

        $data = [
            'products' => [
                ['name' => 'Laptop', 'inStock' => true],
                ['name' => 'Mouse', 'inStock' => false],
                ['name' => 'Keyboard', 'inStock' => true]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('Available', $result['products'][0]['availability']);
        $this->assertEquals('Out of Stock', $result['products'][1]['availability']);
        $this->assertEquals('Available', $result['products'][2]['availability']);
    }

    public function testTernaryWithNumbers(): void
    {
        $jsonTemplate = '{
            "discount": "{{user.isPremium ? 20 : 10}}"
        }';

        $data = ['user' => ['isPremium' => true]];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals(20, $result['discount']);

        $data2 = ['user' => ['isPremium' => false]];
        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertEquals(10, $result2['discount']);
    }

    public function testTernaryWithGreaterThanOrEqual(): void
    {
        $jsonTemplate = '{
            "canVote": "{{user.age >= 18 ? \\"Yes\\" : \\"No\\"}}"
        }';

        $data = ['user' => ['age' => 18]];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('Yes', $result['canVote']);

        $data2 = ['user' => ['age' => 17]];
        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertEquals('No', $result2['canVote']);
    }

    public function testTernaryWithLessThanOrEqual(): void
    {
        $jsonTemplate = '{
            "shipping": "{{order.weight <= 5 ? \\"Standard\\" : \\"Heavy\\"}}"
        }';

        $data = ['order' => ['weight' => 5]];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('Standard', $result['shipping']);

        $data2 = ['order' => ['weight' => 10]];
        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertEquals('Heavy', $result2['shipping']);
    }

    public function testTernaryWithEquality(): void
    {
        $jsonTemplate = '{
            "message": "{{status == \\"approved\\" ? \\"Welcome!\\" : \\"Pending\\"}}"
        }';

        $data = ['status' => 'approved'];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('Welcome!', $result['message']);

        $data2 = ['status' => 'pending'];
        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertEquals('Pending', $result2['message']);
    }

    public function testTernaryWithInequality(): void
    {
        $jsonTemplate = '{
            "status": "{{order.status != \\"cancelled\\" ? \\"Active\\" : \\"Cancelled\\"}}"
        }';

        $data = ['order' => ['status' => 'processing']];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('Active', $result['status']);

        $data2 = ['order' => ['status' => 'cancelled']];
        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertEquals('Cancelled', $result2['status']);
    }

    public function testNestedTernaryInConditional(): void
    {
        $jsonTemplate = '{
            "user": "{{user.name}}",
            "@djson if user.active": {
                "status": "Active",
                "tier": "{{user.isPremium ? \\"Premium\\" : \\"Free\\"}}"
            }
        }';

        $data = [
            'user' => ['name' => 'John', 'active' => true, 'isPremium' => true]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('Active', $result['status']);
        $this->assertEquals('Premium', $result['tier']);
    }
}
