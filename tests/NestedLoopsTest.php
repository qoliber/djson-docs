<?php

declare(strict_types=1);

namespace Qoliber\DJson\Tests;

use PHPUnit\Framework\TestCase;
use Qoliber\DJson\DJson;

class NestedLoopsTest extends TestCase
{
    private DJson $djson;

    protected function setUp(): void
    {
        $this->djson = new DJson();
    }

    public function testTwoLevelNestedLoops(): void
    {
        $jsonTemplate = '{
            "categories": {
                "@djson for categories as category": {
                    "name": "{{category.name}}",
                    "id": "{{category.id}}",
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
            'categories' => [
                [
                    'id' => 'cat1',
                    'name' => 'Electronics',
                    'products' => [
                        ['name' => 'Laptop', 'price' => 999],
                        ['name' => 'Mouse', 'price' => 29]
                    ]
                ],
                [
                    'id' => 'cat2',
                    'name' => 'Books',
                    'products' => [
                        ['name' => 'PHP Guide', 'price' => 45]
                    ]
                ]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertCount(2, $result['categories']);

        // First category
        $this->assertEquals('Electronics', $result['categories'][0]['name']);
        $this->assertEquals('cat1', $result['categories'][0]['id']);
        $this->assertCount(2, $result['categories'][0]['products']);
        $this->assertEquals('Laptop', $result['categories'][0]['products'][0]['name']);
        $this->assertEquals(999, $result['categories'][0]['products'][0]['price']);

        // Second category
        $this->assertEquals('Books', $result['categories'][1]['name']);
        $this->assertCount(1, $result['categories'][1]['products']);
        $this->assertEquals('PHP Guide', $result['categories'][1]['products'][0]['name']);
    }

    public function testThreeLevelNestedLoops(): void
    {
        $jsonTemplate = '{
            "departments": {
                "@djson for departments as dept": {
                    "deptName": "{{dept.name}}",
                    "deptCode": "{{dept.code}}",
                    "categories": {
                        "@djson for dept.categories as category": {
                            "categoryName": "{{category.name}}",
                            "products": {
                                "@djson for category.products as product": {
                                    "productName": "{{product.name}}",
                                    "productPrice": "{{product.price}}"
                                }
                            }
                        }
                    }
                }
            }
        }';

        $data = [
            'departments' => [
                [
                    'name' => 'Technology',
                    'code' => 'TECH',
                    'categories' => [
                        [
                            'name' => 'Computers',
                            'products' => [
                                ['name' => 'Laptop Pro', 'price' => 1299],
                                ['name' => 'Desktop', 'price' => 899]
                            ]
                        ],
                        [
                            'name' => 'Accessories',
                            'products' => [
                                ['name' => 'Keyboard', 'price' => 79]
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Home',
                    'code' => 'HOME',
                    'categories' => [
                        [
                            'name' => 'Furniture',
                            'products' => [
                                ['name' => 'Desk', 'price' => 299]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        // Verify department level
        $this->assertCount(2, $result['departments']);
        $this->assertEquals('Technology', $result['departments'][0]['deptName']);
        $this->assertEquals('TECH', $result['departments'][0]['deptCode']);

        // Verify category level (first department)
        $this->assertCount(2, $result['departments'][0]['categories']);
        $this->assertEquals('Computers', $result['departments'][0]['categories'][0]['categoryName']);

        // Verify product level (first category of first department)
        $this->assertCount(2, $result['departments'][0]['categories'][0]['products']);
        $this->assertEquals('Laptop Pro', $result['departments'][0]['categories'][0]['products'][0]['productName']);
        $this->assertEquals(1299, $result['departments'][0]['categories'][0]['products'][0]['productPrice']);

        // Verify second department with single category and product
        $this->assertEquals('Home', $result['departments'][1]['deptName']);
        $this->assertCount(1, $result['departments'][1]['categories']);
        $this->assertEquals('Furniture', $result['departments'][1]['categories'][0]['categoryName']);
        $this->assertCount(1, $result['departments'][1]['categories'][0]['products']);
    }

    public function testNestedLoopsWithLoopVariables(): void
    {
        $jsonTemplate = '{
            "regions": {
                "@djson for regions as region": {
                    "regionName": "{{region.name}}",
                    "regionIndex": "{{_index}}",
                    "isFirstRegion": "{{_first}}",
                    "isLastRegion": "{{_last}}",
                    "stores": {
                        "@djson for region.stores as store": {
                            "storeName": "{{store.name}}",
                            "storeIndex": "{{_index}}",
                            "isFirstStore": "{{_first}}",
                            "items": {
                                "@djson for store.items as item": {
                                    "itemName": "{{item.name}}",
                                    "itemIndex": "{{_index}}"
                                }
                            }
                        }
                    }
                }
            }
        }';

        $data = [
            'regions' => [
                [
                    'name' => 'North',
                    'stores' => [
                        [
                            'name' => 'Store A',
                            'items' => [
                                ['name' => 'Item 1'],
                                ['name' => 'Item 2']
                            ]
                        ],
                        [
                            'name' => 'Store B',
                            'items' => [
                                ['name' => 'Item 3']
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'South',
                    'stores' => [
                        [
                            'name' => 'Store C',
                            'items' => [
                                ['name' => 'Item 4']
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        // Verify region loop variables
        $this->assertEquals(0, $result['regions'][0]['regionIndex']);
        $this->assertTrue($result['regions'][0]['isFirstRegion']);
        $this->assertFalse($result['regions'][0]['isLastRegion']);

        $this->assertEquals(1, $result['regions'][1]['regionIndex']);
        $this->assertFalse($result['regions'][1]['isFirstRegion']);
        $this->assertTrue($result['regions'][1]['isLastRegion']);

        // Verify store loop variables
        $this->assertEquals(0, $result['regions'][0]['stores'][0]['storeIndex']);
        $this->assertTrue($result['regions'][0]['stores'][0]['isFirstStore']);

        $this->assertEquals(1, $result['regions'][0]['stores'][1]['storeIndex']);
        $this->assertFalse($result['regions'][0]['stores'][1]['isFirstStore']);

        // Verify item loop variables
        $this->assertEquals(0, $result['regions'][0]['stores'][0]['items'][0]['itemIndex']);
        $this->assertEquals(1, $result['regions'][0]['stores'][0]['items'][1]['itemIndex']);
    }

    public function testNestedLoopsWithConditionsAtEachLevel(): void
    {
        $jsonTemplate = '{
            "departments": {
                "@djson for departments as dept": {
                    "name": "{{dept.name}}",
                    "@djson if dept.active": {
                        "status": "Active",
                        "categories": {
                            "@djson for dept.categories as category": {
                                "categoryName": "{{category.name}}",
                                "@djson if category.featured": {
                                    "badge": "Featured",
                                    "products": {
                                        "@djson for category.products as product": {
                                            "name": "{{product.name}}",
                                            "@djson if product.inStock": {
                                                "availability": "Available",
                                                "price": "{{product.price}}"
                                            },
                                            "@djson else": {
                                                "availability": "Out of Stock"
                                            }
                                        }
                                    }
                                },
                                "@djson else": {
                                    "badge": "Standard"
                                }
                            }
                        }
                    },
                    "@djson else": {
                        "status": "Inactive"
                    }
                }
            }
        }';

        $data = [
            'departments' => [
                [
                    'name' => 'Electronics',
                    'active' => true,
                    'categories' => [
                        [
                            'name' => 'Laptops',
                            'featured' => true,
                            'products' => [
                                ['name' => 'Gaming Laptop', 'price' => 1499, 'inStock' => true],
                                ['name' => 'Business Laptop', 'price' => 999, 'inStock' => false]
                            ]
                        ],
                        [
                            'name' => 'Tablets',
                            'featured' => false,
                            'products' => [
                                ['name' => 'iPad', 'price' => 599, 'inStock' => true]
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Discontinued',
                    'active' => false,
                    'categories' => []
                ]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        // Active department with featured category
        $this->assertEquals('Active', $result['departments'][0]['status']);
        $this->assertEquals('Featured', $result['departments'][0]['categories'][0]['badge']);
        $this->assertEquals('Available', $result['departments'][0]['categories'][0]['products'][0]['availability']);
        $this->assertEquals(1499, $result['departments'][0]['categories'][0]['products'][0]['price']);
        $this->assertEquals('Out of Stock', $result['departments'][0]['categories'][0]['products'][1]['availability']);
        $this->assertArrayNotHasKey('price', $result['departments'][0]['categories'][0]['products'][1]);

        // Active department with non-featured category
        $this->assertEquals('Standard', $result['departments'][0]['categories'][1]['badge']);
        $this->assertArrayNotHasKey('products', $result['departments'][0]['categories'][1]);

        // Inactive department
        $this->assertEquals('Inactive', $result['departments'][1]['status']);
        $this->assertArrayNotHasKey('categories', $result['departments'][1]);
    }

    public function testNestedLoopsWithMixedData(): void
    {
        $jsonTemplate = '{
            "organizations": {
                "@djson for organizations as org": {
                    "orgName": "{{org.name}}",
                    "teams": {
                        "@djson for org.teams as team": {
                            "teamName": "{{team.name}}",
                            "teamSize": "{{team.size}}",
                            "members": {
                                "@djson for team.members as member": {
                                    "memberName": "{{member.name}}",
                                    "role": "{{member.role}}",
                                    "projects": {
                                        "@djson for member.projects as project": {
                                            "projectName": "{{project.name}}",
                                            "status": "{{project.status}}"
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }';

        $data = [
            'organizations' => [
                [
                    'name' => 'Tech Corp',
                    'teams' => [
                        [
                            'name' => 'Backend Team',
                            'size' => 5,
                            'members' => [
                                [
                                    'name' => 'John',
                                    'role' => 'Developer',
                                    'projects' => [
                                        ['name' => 'API', 'status' => 'active'],
                                        ['name' => 'Database', 'status' => 'completed']
                                    ]
                                ],
                                [
                                    'name' => 'Alice',
                                    'role' => 'Lead',
                                    'projects' => [
                                        ['name' => 'Architecture', 'status' => 'active']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        // Verify 4-level deep nesting
        $this->assertEquals('Tech Corp', $result['organizations'][0]['orgName']);
        $this->assertEquals('Backend Team', $result['organizations'][0]['teams'][0]['teamName']);
        $this->assertEquals(5, $result['organizations'][0]['teams'][0]['teamSize']);
        $this->assertCount(2, $result['organizations'][0]['teams'][0]['members']);

        // First member
        $this->assertEquals('John', $result['organizations'][0]['teams'][0]['members'][0]['memberName']);
        $this->assertEquals('Developer', $result['organizations'][0]['teams'][0]['members'][0]['role']);
        $this->assertCount(2, $result['organizations'][0]['teams'][0]['members'][0]['projects']);
        $this->assertEquals('API', $result['organizations'][0]['teams'][0]['members'][0]['projects'][0]['projectName']);
        $this->assertEquals('active', $result['organizations'][0]['teams'][0]['members'][0]['projects'][0]['status']);

        // Second member
        $this->assertEquals('Alice', $result['organizations'][0]['teams'][0]['members'][1]['memberName']);
        $this->assertCount(1, $result['organizations'][0]['teams'][0]['members'][1]['projects']);
    }

    public function testNestedLoopsWithEmptyCollections(): void
    {
        $jsonTemplate = '{
            "categories": {
                "@djson for categories as category": {
                    "name": "{{category.name}}",
                    "products": {
                        "@djson for category.products as product": {
                            "name": "{{product.name}}"
                        }
                    }
                }
            }
        }';

        $data = [
            'categories' => [
                [
                    'name' => 'Electronics',
                    'products' => [
                        ['name' => 'Laptop']
                    ]
                ],
                [
                    'name' => 'Empty Category',
                    'products' => []
                ],
                [
                    'name' => 'Books',
                    'products' => [
                        ['name' => 'PHP Guide']
                    ]
                ]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertCount(3, $result['categories']);
        $this->assertCount(1, $result['categories'][0]['products']);
        $this->assertCount(0, $result['categories'][1]['products']); // Empty array
        $this->assertCount(1, $result['categories'][2]['products']);
    }

    public function testNestedLoopsWithFunctions(): void
    {
        $jsonTemplate = '{
            "departments": {
                "@djson for departments as dept": {
                    "name": "@djson upper {{dept.name}}",
                    "categories": {
                        "@djson for dept.categories as category": {
                            "slug": "@djson slug {{category.name}}",
                            "products": {
                                "@djson for category.products as product": {
                                    "name": "{{product.name}}",
                                    "formattedPrice": "@djson number_format {{product.price}} 2 . ,",
                                    "upperName": "@djson upper {{product.name}}"
                                }
                            }
                        }
                    }
                }
            }
        }';

        $data = [
            'departments' => [
                [
                    'name' => 'Electronics',
                    'categories' => [
                        [
                            'name' => 'Laptop Computers',
                            'products' => [
                                ['name' => 'Gaming Laptop', 'price' => 1499.99],
                                ['name' => 'Business Laptop', 'price' => 999.50]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        $this->assertEquals('ELECTRONICS', $result['departments'][0]['name']);
        $this->assertEquals('laptop-computers', $result['departments'][0]['categories'][0]['slug']);
        $this->assertEquals('1,499.99', $result['departments'][0]['categories'][0]['products'][0]['formattedPrice']);
        $this->assertEquals('GAMING LAPTOP', $result['departments'][0]['categories'][0]['products'][0]['upperName']);
        $this->assertEquals('999.50', $result['departments'][0]['categories'][0]['products'][1]['formattedPrice']);
    }

    public function testComplexNestedLoopsScenario(): void
    {
        $jsonTemplate = '{
            "ecommerce": {
                "stores": {
                    "@djson for stores as store": {
                        "storeName": "{{store.name}}",
                        "storeIndex": "{{_index}}",
                        "@djson if store.active": {
                            "status": "Open",
                            "departments": {
                                "@djson for store.departments as dept": {
                                    "deptName": "{{dept.name}}",
                                    "deptIndex": "{{_index}}",
                                    "@djson if dept.visible": {
                                        "visibility": "Public",
                                        "categories": {
                                            "@djson for dept.categories as cat": {
                                                "categoryName": "{{cat.name}}",
                                                "categorySlug": "@djson slug {{cat.name}}",
                                                "@djson if cat.featured": {
                                                    "badge": "Featured",
                                                    "products": {
                                                        "@djson for cat.products as product": {
                                                            "name": "{{product.name}}",
                                                            "productIndex": "{{_index}}",
                                                            "@djson if product.price > 100": {
                                                                "tier": "Premium",
                                                                "shipping": "Free"
                                                            },
                                                            "@djson else": {
                                                                "tier": "Standard",
                                                                "shipping": "5.00"
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        "@djson else": {
                            "status": "Closed"
                        }
                    }
                }
            }
        }';

        $data = [
            'stores' => [
                [
                    'name' => 'Main Store',
                    'active' => true,
                    'departments' => [
                        [
                            'name' => 'Electronics',
                            'visible' => true,
                            'categories' => [
                                [
                                    'name' => 'High-End Laptops',
                                    'featured' => true,
                                    'products' => [
                                        ['name' => 'Gaming Laptop', 'price' => 1499],
                                        ['name' => 'Budget Laptop', 'price' => 99]
                                    ]
                                ],
                                [
                                    'name' => 'Accessories',
                                    'featured' => false,
                                    'products' => []
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Branch Store',
                    'active' => false,
                    'departments' => []
                ]
            ]
        ];

        $result = $this->djson->process($jsonTemplate, $data);

        // Store level
        $this->assertCount(2, $result['ecommerce']['stores']);
        $this->assertEquals('Main Store', $result['ecommerce']['stores'][0]['storeName']);
        $this->assertEquals(0, $result['ecommerce']['stores'][0]['storeIndex']);
        $this->assertEquals('Open', $result['ecommerce']['stores'][0]['status']);

        // Department level
        $this->assertCount(1, $result['ecommerce']['stores'][0]['departments']);
        $this->assertEquals('Electronics', $result['ecommerce']['stores'][0]['departments'][0]['deptName']);
        $this->assertEquals('Public', $result['ecommerce']['stores'][0]['departments'][0]['visibility']);

        // Category level
        $categories = $result['ecommerce']['stores'][0]['departments'][0]['categories'];
        $this->assertCount(2, $categories);
        $this->assertEquals('High-End Laptops', $categories[0]['categoryName']);
        $this->assertEquals('high-end-laptops', $categories[0]['categorySlug']);
        $this->assertEquals('Featured', $categories[0]['badge']);

        // Product level
        $products = $categories[0]['products'];
        $this->assertCount(2, $products);
        $this->assertEquals('Gaming Laptop', $products[0]['name']);
        $this->assertEquals('Premium', $products[0]['tier']);
        $this->assertEquals('Free', $products[0]['shipping']);

        $this->assertEquals('Budget Laptop', $products[1]['name']);
        $this->assertEquals('Standard', $products[1]['tier']);
        $this->assertEquals('5.00', $products[1]['shipping']);

        // Non-featured category should not have products
        $this->assertArrayNotHasKey('badge', $categories[1]);
        $this->assertArrayNotHasKey('products', $categories[1]);

        // Closed store
        $this->assertEquals('Branch Store', $result['ecommerce']['stores'][1]['storeName']);
        $this->assertEquals('Closed', $result['ecommerce']['stores'][1]['status']);
        $this->assertArrayNotHasKey('departments', $result['ecommerce']['stores'][1]);
    }
}
