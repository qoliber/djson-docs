<?php

declare(strict_types=1);

namespace Qoliber\DJson\Tests;

use PHPUnit\Framework\TestCase;
use Qoliber\DJson\DJson;

class SetDirectiveTest extends TestCase
{
    private DJson $djson;

    protected function setUp(): void
    {
        $this->djson = new DJson();
    }

    public function testSimpleMultiplication(): void
    {
        $jsonTemplate = '{
            "@djson set total = product.price * product.quantity": {
                "product": "{{product.name}}",
                "total": "{{total}}"
            }
        }';

        $data = [
            'product' => [
                'name' => 'Laptop',
                'price' => 999,
                'quantity' => 2
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('Laptop', $result['product']);
        $this->assertEquals(1998, $result['total']);
    }

    public function testAddition(): void
    {
        $jsonTemplate = '{
            "@djson set subtotal = item.price + item.tax": {
                "subtotal": "{{subtotal}}"
            }
        }';

        $data = [
            'item' => [
                'price' => 100,
                'tax' => 15
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals(115, $result['subtotal']);
    }

    public function testSubtraction(): void
    {
        $jsonTemplate = '{
            "@djson set finalPrice = product.price - product.discount": {
                "finalPrice": "{{finalPrice}}"
            }
        }';

        $data = [
            'product' => [
                'price' => 200,
                'discount' => 50
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals(150, $result['finalPrice']);
    }

    public function testDivision(): void
    {
        $jsonTemplate = '{
            "@djson set pricePerUnit = order.total / order.quantity": {
                "pricePerUnit": "{{pricePerUnit}}"
            }
        }';

        $data = [
            'order' => [
                'total' => 1000,
                'quantity' => 4
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals(250, $result['pricePerUnit']);
    }

    public function testStringConcatenation(): void
    {
        $jsonTemplate = '{
            "@djson set fullName = user.firstName + \\" \\" + user.lastName": {
                "fullName": "{{fullName}}"
            }
        }';

        $data = [
            'user' => [
                'firstName' => 'John',
                'lastName' => 'Doe'
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('John Doe', $result['fullName']);
    }

    public function testComputedValueInLoop(): void
    {
        $jsonTemplate = '{
            "items": {
                "@djson for items as item": {
                    "@djson set itemTotal = item.price * item.qty": {
                        "name": "{{item.name}}",
                        "total": "{{itemTotal}}"
                    }
                }
            }
        }';

        $data = [
            'items' => [
                ['name' => 'Item A', 'price' => 10, 'qty' => 3],
                ['name' => 'Item B', 'price' => 25, 'qty' => 2]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals(30, $result['items'][0]['total']);
        $this->assertEquals(50, $result['items'][1]['total']);
    }

    public function testComputedWithConstant(): void
    {
        $jsonTemplate = '{
            "@djson set taxAmount = price * 0.15": {
                "price": "{{price}}",
                "tax": "{{taxAmount}}"
            }
        }';

        $data = ['price' => 100];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals(100, $result['price']);
        $this->assertEquals(15, $result['tax']);
    }

    public function testMultipleOperations(): void
    {
        $jsonTemplate = '{
            "@djson set result = value1 + value2 * value3": {
                "result": "{{result}}"
            }
        }';

        $data = [
            'value1' => 10,
            'value2' => 5,
            'value3' => 3
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        // Should respect operator precedence: 10 + (5 * 3) = 25
        $this->assertEquals(25, $result['result']);
    }

    public function testSetWithTernary(): void
    {
        $jsonTemplate = '{
            "@djson set discount = user.isPremium ? 20 : 10": {
                "user": "{{user.name}}",
                "discountPercent": "{{discount}}"
            }
        }';

        $data = [
            'user' => ['name' => 'John', 'isPremium' => true]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals(20, $result['discountPercent']);

        // Test with false condition
        $data2 = [
            'user' => ['name' => 'Jane', 'isPremium' => false]
        ];

        $result2 = $this->djson->process($jsonTemplate, $data2);

        $this->assertEquals(10, $result2['discountPercent']);
    }

    public function testSetInConditional(): void
    {
        $jsonTemplate = '{
            "@djson if order.items": {
                "@djson set itemCount = order.quantity": {
                    "message": "You have {{itemCount}} items"
                }
            }
        }';

        $data = [
            'order' => ['items' => ['item1', 'item2'], 'quantity' => 2]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('You have 2 items', $result['message']);
    }

    public function testSetWithFloatingPoint(): void
    {
        $jsonTemplate = '{
            "@djson set percentage = value * 0.25": {
                "percentage": "{{percentage}}"
            }
        }';

        $data = ['value' => 80];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals(20, $result['percentage']);
    }

    public function testComplexCalculation(): void
    {
        $jsonTemplate = '{
            "products": {
                "@djson for products as product": {
                    "name": "{{product.name}}",
                    "@djson set subtotal = product.price * product.qty": {
                        "subtotal": "{{subtotal}}",
                        "shipping": "{{subtotal > 100 ? \\"Free\\" : \\"5.00\\"}}"
                    }
                }
            }
        }';

        $data = [
            'products' => [
                ['name' => 'Laptop', 'price' => 50, 'qty' => 3],
                ['name' => 'Mouse', 'price' => 20, 'qty' => 2]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        // First product: 50 * 3 = 150, > 100 so free shipping
        $this->assertEquals(150, $result['products'][0]['subtotal']);
        $this->assertEquals('Free', $result['products'][0]['shipping']);

        // Second product: 20 * 2 = 40, <= 100 so $5 shipping
        $this->assertEquals(40, $result['products'][1]['subtotal']);
        $this->assertEquals('5.00', $result['products'][1]['shipping']);
    }

    public function testDivisionByZero(): void
    {
        $jsonTemplate = '{
            "@djson set result = total / quantity": {
                "result": "{{result}}"
            }
        }';

        // Division by zero should return 0 (safe handling)
        $data = ['total' => 100, 'quantity' => 0];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals(0, $result['result']);
    }

    public function testDivisionByZeroWithVariable(): void
    {
        $jsonTemplate = '{
            "@djson set pricePerUnit = order.total / order.items": {
                "pricePerUnit": "{{pricePerUnit}}"
            }
        }';

        // Test with zero items (division by zero)
        $data1 = ['order' => ['total' => 1000, 'items' => 0]];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertEquals(0, $result1['pricePerUnit']);

        // Test with valid division
        $data2 = ['order' => ['total' => 1000, 'items' => 5]];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertEquals(200, $result2['pricePerUnit']);
    }

    public function testDivisionByZeroInLoop(): void
    {
        $jsonTemplate = '{
            "orders": {
                "@djson for orders as order": {
                    "orderId": "{{order.id}}",
                    "@djson set avgPrice = order.total / order.itemCount": {
                        "averagePrice": "{{avgPrice}}"
                    }
                }
            }
        }';

        $data = [
            'orders' => [
                ['id' => 'A', 'total' => 100, 'itemCount' => 5],
                ['id' => 'B', 'total' => 200, 'itemCount' => 0],  // Division by zero
                ['id' => 'C', 'total' => 150, 'itemCount' => 3]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals(20, $result['orders'][0]['averagePrice']);
        $this->assertEquals(0, $result['orders'][1]['averagePrice']);  // Safe zero
        $this->assertEquals(50, $result['orders'][2]['averagePrice']);
    }
}
