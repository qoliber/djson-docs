<?php

declare(strict_types=1);

namespace Qoliber\DJson\Tests;

use PHPUnit\Framework\TestCase;
use Qoliber\DJson\DJson;

class ValidationTest extends TestCase
{
    private DJson $djson;

    protected function setUp(): void
    {
        $this->djson = new DJson();
    }

    public function testValidTemplatePassesValidation(): void
    {
        $jsonTemplate = '{
            "user": "{{user.name}}",
            "@djson if user.active": {
                "status": "Active"
            }
        }';

        $errors = $this->djson->validate($jsonTemplate);

        $this->assertEmpty($errors);
    }

    public function testInvalidJsonFailsValidation(): void
    {
        $invalidJson = '{
            "user": "{{user.name}}",
            "status": "Active"
        '; // Missing closing brace

        $errors = $this->djson->validate($invalidJson);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Invalid JSON syntax', $errors[0]);
    }

    public function testInvalidDirectiveFailsValidation(): void
    {
        $jsonTemplate = '{
            "user": "{{user.name}}",
            "@djson invalidDirective user.active": {
                "status": "Active"
            }
        }';

        $errors = $this->djson->validate($jsonTemplate);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Invalid directive', $errors[0]);
    }

    public function testInvalidFunctionFailsValidation(): void
    {
        $jsonTemplate = '{
            "user": "@djson nonExistentFunction {{user.name}}"
        }';

        $errors = $this->djson->validate($jsonTemplate);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Unknown function', $errors[0]);
        $this->assertStringContainsString('nonExistentFunction', $errors[0]);
    }

    public function testValidFunctionPassesValidation(): void
    {
        $jsonTemplate = '{
            "user": "@djson upper {{user.name}}"
        }';

        $errors = $this->djson->validate($jsonTemplate);

        $this->assertEmpty($errors);
    }

    public function testValidateComplexTemplate(): void
    {
        $jsonTemplate = '{
            "users": {
                "@djson for users as user": {
                    "name": "@djson upper {{user.name}}",
                    "@djson if user.active": {
                        "status": "Active"
                    },
                    "@djson else": {
                        "status": "Inactive"
                    }
                }
            }
        }';

        $errors = $this->djson->validate($jsonTemplate);

        $this->assertEmpty($errors);
    }

    public function testValidateMultipleErrors(): void
    {
        $jsonTemplate = '{
            "name": "@djson invalidFunc {{user.name}}",
            "@djson wrongDirective": {
                "test": "value"
            },
            "upper": "@djson anotherBadFunc {{test}}"
        }';

        $errors = $this->djson->validate($jsonTemplate);

        $this->assertCount(3, $errors);
    }

    public function testValidateSetDirective(): void
    {
        $jsonTemplate = '{
            "@djson set total = product.price * product.qty": {
                "total": "{{total}}"
            }
        }';

        $errors = $this->djson->validate($jsonTemplate);

        $this->assertEmpty($errors);
    }

    public function testValidateInvalidSetDirective(): void
    {
        $jsonTemplate = '{
            "@djson set": {
                "total": "{{total}}"
            }
        }';

        $errors = $this->djson->validate($jsonTemplate);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Invalid directive', $errors[0]);
    }

    public function testValidateChainedFunctions(): void
    {
        $jsonTemplate = '{
            "name": "@djson upper|trim {{user.name}}"
        }';

        $errors = $this->djson->validate($jsonTemplate);

        $this->assertEmpty($errors);
    }

    public function testValidateInvalidChainedFunctions(): void
    {
        $jsonTemplate = '{
            "name": "@djson upper|nonexistent {{user.name}}"
        }';

        $errors = $this->djson->validate($jsonTemplate);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Unknown function', $errors[0]);
    }

    public function testValidateNestedStructures(): void
    {
        $jsonTemplate = '{
            "@djson for categories as category": {
                "name": "{{category.name}}",
                "@djson if category.featured": {
                    "badge": "Featured",
                    "@djson for category.products as product": {
                        "productName": "@djson upper {{product.name}}"
                    }
                }
            }
        }';

        $errors = $this->djson->validate($jsonTemplate);

        $this->assertEmpty($errors);
    }

    public function testValidateAllDirectives(): void
    {
        $jsonTemplate = '{
            "@djson if user.active": {
                "status": "active"
            },
            "@djson unless user.banned": {
                "access": "allowed"
            },
            "@djson exists user.email": {
                "hasEmail": true
            },
            "@djson for items as item": {
                "name": "{{item.name}}"
            },
            "@djson set total = 100": {
                "total": "{{total}}"
            }
        }';

        $errors = $this->djson->validate($jsonTemplate);

        $this->assertEmpty($errors);
    }

    public function testValidateWithTernaryOperators(): void
    {
        $jsonTemplate = '{
            "status": "{{user.active ? \\"Online\\" : \\"Offline\\"}}"
        }';

        $errors = $this->djson->validate($jsonTemplate);

        $this->assertEmpty($errors);
    }

    public function testValidateArrayFromString(): void
    {
        $template = [
            'name' => '@djson upper {{user.name}}',
            '@djson if user.active' => [
                'status' => 'Active'
            ]
        ];

        $errors = $this->djson->validate($template);

        $this->assertEmpty($errors);
    }

    public function testValidateReturnsEmptyForSimpleTemplate(): void
    {
        $jsonTemplate = '{
            "user": "{{user.name}}",
            "email": "{{user.email}}"
        }';

        $errors = $this->djson->validate($jsonTemplate);

        $this->assertEmpty($errors);
    }
}
