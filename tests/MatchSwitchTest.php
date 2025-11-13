<?php

declare(strict_types=1);

namespace Qoliber\DJson\Tests;

use PHPUnit\Framework\TestCase;
use Qoliber\DJson\DJson;

class MatchSwitchTest extends TestCase
{
    private DJson $djson;

    protected function setUp(): void
    {
        $this->djson = new DJson();
    }

    // ====================
    // MATCH DIRECTIVE TESTS
    // ====================

    public function testSimpleMatch(): void
    {
        $jsonTemplate = '{
            "user": "{{user.name}}",
            "@djson match user.role": {
                "@djson case admin": {
                    "permissions": "all"
                },
                "@djson case moderator": {
                    "permissions": "moderate"
                },
                "@djson case user": {
                    "permissions": "basic"
                }
            }
        }';

        // Test admin
        $data1 = ['user' => ['name' => 'John', 'role' => 'admin']];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertEquals('all', $result1['permissions']);

        // Test moderator
        $data2 = ['user' => ['name' => 'Jane', 'role' => 'moderator']];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertEquals('moderate', $result2['permissions']);

        // Test user
        $data3 = ['user' => ['name' => 'Bob', 'role' => 'user']];
        $result3 = $this->djson->process($jsonTemplate, $data3);
        $this->assertEquals('basic', $result3['permissions']);
    }

    public function testMatchWithDefault(): void
    {
        $jsonTemplate = '{
            "@djson match status": {
                "@djson case active": {
                    "message": "Active"
                },
                "@djson case pending": {
                    "message": "Pending"
                },
                "@djson default": {
                    "message": "Unknown"
                }
            }
        }';

        $data1 = ['status' => 'active'];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertEquals('Active', $result1['message']);

        $data2 = ['status' => 'archived'];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertEquals('Unknown', $result2['message']);
    }

    public function testMatchWithQuotedStrings(): void
    {
        $jsonTemplate = '{
            "@djson match payment.method": {
                "@djson case \\"credit_card\\"": {
                    "processor": "Stripe"
                },
                "@djson case \\"paypal\\"": {
                    "processor": "PayPal"
                },
                "@djson case \\"bank_transfer\\"": {
                    "processor": "Bank"
                },
                "@djson default": {
                    "processor": "Unknown"
                }
            }
        }';

        $data1 = ['payment' => ['method' => 'credit_card']];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertEquals('Stripe', $result1['processor']);

        $data2 = ['payment' => ['method' => 'paypal']];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertEquals('PayPal', $result2['processor']);

        $data3 = ['payment' => ['method' => 'cash']];
        $result3 = $this->djson->process($jsonTemplate, $data3);
        $this->assertEquals('Unknown', $result3['processor']);
    }

    // ====================
    // SWITCH DIRECTIVE TESTS (alias for match)
    // ====================

    public function testSimpleSwitch(): void
    {
        $jsonTemplate = '{
            "product": "{{product.name}}",
            "@djson switch product.type": {
                "@djson case electronics": {
                    "category": "Tech"
                },
                "@djson case clothing": {
                    "category": "Fashion"
                },
                "@djson case food": {
                    "category": "Grocery"
                }
            }
        }';

        $data1 = ['product' => ['name' => 'Laptop', 'type' => 'electronics']];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertEquals('Tech', $result1['category']);

        $data2 = ['product' => ['name' => 'Shirt', 'type' => 'clothing']];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertEquals('Fashion', $result2['category']);
    }

    public function testSwitchWithDefault(): void
    {
        $jsonTemplate = '{
            "@djson switch order.status": {
                "@djson case completed": {
                    "badge": "Success",
                    "color": "green"
                },
                "@djson case processing": {
                    "badge": "In Progress",
                    "color": "blue"
                },
                "@djson case failed": {
                    "badge": "Failed",
                    "color": "red"
                },
                "@djson default": {
                    "badge": "Unknown",
                    "color": "gray"
                }
            }
        }';

        $data1 = ['order' => ['status' => 'completed']];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertEquals('Success', $result1['badge']);
        $this->assertEquals('green', $result1['color']);

        $data2 = ['order' => ['status' => 'cancelled']];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertEquals('Unknown', $result2['badge']);
        $this->assertEquals('gray', $result2['color']);
    }

    // ====================
    // MATCH WITH NUMBERS
    // ====================

    public function testMatchWithNumbers(): void
    {
        $jsonTemplate = '{
            "@djson match user.level": {
                "@djson case 1": {
                    "tier": "Bronze"
                },
                "@djson case 2": {
                    "tier": "Silver"
                },
                "@djson case 3": {
                    "tier": "Gold"
                },
                "@djson default": {
                    "tier": "None"
                }
            }
        }';

        $data1 = ['user' => ['level' => 1]];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertEquals('Bronze', $result1['tier']);

        $data2 = ['user' => ['level' => 3]];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertEquals('Gold', $result2['tier']);

        $data3 = ['user' => ['level' => 99]];
        $result3 = $this->djson->process($jsonTemplate, $data3);
        $this->assertEquals('None', $result3['tier']);
    }

    // ====================
    // MATCH IN LOOPS
    // ====================

    public function testMatchInLoop(): void
    {
        $jsonTemplate = '{
            "users": {
                "@djson for users as user": {
                    "name": "{{user.name}}",
                    "@djson match user.status": {
                        "@djson case active": {
                            "badge": "Active",
                            "canLogin": true
                        },
                        "@djson case suspended": {
                            "badge": "Suspended",
                            "canLogin": false
                        },
                        "@djson default": {
                            "badge": "Unknown",
                            "canLogin": false
                        }
                    }
                }
            }
        }';

        $data = [
            'users' => [
                ['name' => 'John', 'status' => 'active'],
                ['name' => 'Jane', 'status' => 'suspended'],
                ['name' => 'Bob', 'status' => 'pending']
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('Active', $result['users'][0]['badge']);
        $this->assertTrue($result['users'][0]['canLogin']);

        $this->assertEquals('Suspended', $result['users'][1]['badge']);
        $this->assertFalse($result['users'][1]['canLogin']);

        $this->assertEquals('Unknown', $result['users'][2]['badge']);
        $this->assertFalse($result['users'][2]['canLogin']);
    }

    // ====================
    // NESTED MATCH
    // ====================

    public function testNestedMatch(): void
    {
        $jsonTemplate = '{
            "@djson match user.type": {
                "@djson case customer": {
                    "userType": "Customer",
                    "@djson match user.tier": {
                        "@djson case premium": {
                            "discount": 20
                        },
                        "@djson case regular": {
                            "discount": 10
                        },
                        "@djson default": {
                            "discount": 0
                        }
                    }
                },
                "@djson case admin": {
                    "userType": "Admin",
                    "discount": 100
                }
            }
        }';

        $data1 = ['user' => ['type' => 'customer', 'tier' => 'premium']];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertEquals('Customer', $result1['userType']);
        $this->assertEquals(20, $result1['discount']);

        $data2 = ['user' => ['type' => 'customer', 'tier' => 'regular']];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertEquals('Customer', $result2['userType']);
        $this->assertEquals(10, $result2['discount']);

        $data3 = ['user' => ['type' => 'admin']];
        $result3 = $this->djson->process($jsonTemplate, $data3);
        $this->assertEquals('Admin', $result3['userType']);
        $this->assertEquals(100, $result3['discount']);
    }

    // ====================
    // MATCH WITHOUT DEFAULT
    // ====================

    public function testMatchWithoutDefault(): void
    {
        $jsonTemplate = '{
            "name": "{{product.name}}",
            "@djson match product.availability": {
                "@djson case in_stock": {
                    "status": "Available"
                },
                "@djson case pre_order": {
                    "status": "Pre-order"
                }
            }
        }';

        $data1 = ['product' => ['name' => 'Laptop', 'availability' => 'in_stock']];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertEquals('Available', $result1['status']);

        // No match and no default - status key should not exist
        $data2 = ['product' => ['name' => 'Monitor', 'availability' => 'discontinued']];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertArrayNotHasKey('status', $result2);
        $this->assertEquals('Monitor', $result2['name']);
    }

    // ====================
    // REAL WORLD SCENARIOS
    // ====================

    public function testShippingCalculation(): void
    {
        $jsonTemplate = '{
            "order": "{{order.id}}",
            "@djson match order.country": {
                "@djson case US": {
                    "shipping": 5.00,
                    "currency": "USD"
                },
                "@djson case UK": {
                    "shipping": 8.00,
                    "currency": "GBP"
                },
                "@djson case EU": {
                    "shipping": 10.00,
                    "currency": "EUR"
                },
                "@djson default": {
                    "shipping": 15.00,
                    "currency": "USD"
                }
            }
        }';

        $data1 = ['order' => ['id' => 'ORD-123', 'country' => 'US']];
        $result1 = $this->djson->process($jsonTemplate, $data1);
        $this->assertEquals(5.00, $result1['shipping']);
        $this->assertEquals('USD', $result1['currency']);

        $data2 = ['order' => ['id' => 'ORD-456', 'country' => 'UK']];
        $result2 = $this->djson->process($jsonTemplate, $data2);
        $this->assertEquals(8.00, $result2['shipping']);
        $this->assertEquals('GBP', $result2['currency']);

        $data3 = ['order' => ['id' => 'ORD-789', 'country' => 'JP']];
        $result3 = $this->djson->process($jsonTemplate, $data3);
        $this->assertEquals(15.00, $result3['shipping']);
        $this->assertEquals('USD', $result3['currency']);
    }

    public function testUserPermissions(): void
    {
        $jsonTemplate = '{
            "user": "{{user.name}}",
            "@djson match user.role": {
                "@djson case owner": {
                    "canRead": true,
                    "canWrite": true,
                    "canDelete": true,
                    "canManageUsers": true
                },
                "@djson case admin": {
                    "canRead": true,
                    "canWrite": true,
                    "canDelete": true,
                    "canManageUsers": false
                },
                "@djson case editor": {
                    "canRead": true,
                    "canWrite": true,
                    "canDelete": false,
                    "canManageUsers": false
                },
                "@djson case viewer": {
                    "canRead": true,
                    "canWrite": false,
                    "canDelete": false,
                    "canManageUsers": false
                },
                "@djson default": {
                    "canRead": false,
                    "canWrite": false,
                    "canDelete": false,
                    "canManageUsers": false
                }
            }
        }';

        $data = ['user' => ['name' => 'John', 'role' => 'admin']];
        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertTrue($result['canRead']);
        $this->assertTrue($result['canWrite']);
        $this->assertTrue($result['canDelete']);
        $this->assertFalse($result['canManageUsers']);
    }

    public function testProductCategorization(): void
    {
        $jsonTemplate = '{
            "products": {
                "@djson for products as product": {
                    "name": "{{product.name}}",
                    "@djson switch product.type": {
                        "@djson case digital": {
                            "delivery": "Instant Download",
                            "shippingRequired": false
                        },
                        "@djson case physical": {
                            "delivery": "Standard Shipping",
                            "shippingRequired": true
                        },
                        "@djson case service": {
                            "delivery": "Scheduled",
                            "shippingRequired": false
                        },
                        "@djson default": {
                            "delivery": "Contact Support",
                            "shippingRequired": false
                        }
                    }
                }
            }
        }';

        $data = [
            'products' => [
                ['name' => 'E-book', 'type' => 'digital'],
                ['name' => 'T-shirt', 'type' => 'physical'],
                ['name' => 'Consulting', 'type' => 'service']
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('Instant Download', $result['products'][0]['delivery']);
        $this->assertFalse($result['products'][0]['shippingRequired']);

        $this->assertEquals('Standard Shipping', $result['products'][1]['delivery']);
        $this->assertTrue($result['products'][1]['shippingRequired']);

        $this->assertEquals('Scheduled', $result['products'][2]['delivery']);
        $this->assertFalse($result['products'][2]['shippingRequired']);
    }
}
