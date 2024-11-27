<?php

declare(strict_types=1);

namespace ClassGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Class_ as ClassStmt;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\ClassGenerationHelper;

class AddClassPropertyTest extends TestCase
{
    private BuilderFactory $factory;
    private ClassGenerationHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new BuilderFactory();
        $this->helper = new ClassGenerationHelper($this->factory);
    }

    /**
     * @dataProvider classPropertyProvider
     * This test verifies adding a class property with different types, visibility, and default values.
     */
    public function test_add_class_property(string $name, ?string $type, string $visibility, mixed $default, string $expectedName, ?string $expectedType, string $expectedVisibility, mixed $expectedDefault): void
    {
        $class = $this->helper->createClass('MyClass');
        $this->helper->addClassProperty($class, $name, $type, $visibility, $default);

        $stmts = $class->getNode()->stmts;
        $this->assertCount(1, $stmts);
        $this->assertSame($expectedName, $stmts[0]->props[0]->name->toString());
        if ($expectedType !== null) {
            $this->assertSame($expectedType, $stmts[0]->type?->toString());
        } else {
            $this->assertNull($stmts[0]->type);
        }
        $this->assertSame($expectedVisibility, $this->getVisibilityAsString($stmts[0]));
        if ($expectedDefault !== null) {
            if (is_array($expectedDefault)) {
                $items = array_map(fn($item) => $item->value->value, $stmts[0]->props[0]->default->items);
                $this->assertSame($expectedDefault, $items);
            } else {
                $this->assertSame($expectedDefault, $stmts[0]->props[0]->default->value);
            }
        }
    }

    /**
     * @dataProvider invalidClassPropertyProvider
     * This test verifies that invalid class properties throw an exception.
     */
    public function test_add_class_property_invalid(string $name, ?string $type, string $visibility, mixed $default): void
    {
        $class = $this->helper->createClass('MyClass');
        $this->expectException(\InvalidArgumentException::class);
        $this->helper->addClassProperty($class, $name, $type, $visibility, $default);
    }

    /**
     * @dataProvider classPropertyStaticProvider
     * This test verifies adding a static class property.
     */
    public function test_add_class_property_static(string $name, ?string $type, string $visibility, mixed $default): void
    {
        $class = $this->helper->createClass('MyClass');
        $property = $this->helper->addClassProperty($class, $name, $type, $visibility, $default);
        $propertyNode = $class->getNode()->stmts[0];
        $propertyNode->flags |= ClassStmt::MODIFIER_STATIC;

        $this->assertTrue(($propertyNode->flags & ClassStmt::MODIFIER_STATIC) === ClassStmt::MODIFIER_STATIC);
    }

    private function getVisibilityAsString(Property $stmt): string
    {
        if ($stmt->isPublic()) {
            return 'public';
        } elseif ($stmt->isProtected()) {
            return 'protected';
        } elseif ($stmt->isPrivate()) {
            return 'private';
        }
        return '';
    }

    public function classPropertyProvider(): array
    {
        return [
            'public string property with default' => ['myProperty', 'string', 'public', 'defaultValue', 'myProperty', 'string', 'public', 'defaultValue'],
            'private int property with default' => ['myIntProperty', 'int', 'private', 42, 'myIntProperty', 'int', 'private', 42],
            'protected bool property without default' => ['myBoolProperty', 'bool', 'protected', null, 'myBoolProperty', 'bool', 'protected', null],
            'public property without type and default' => ['myPropertyNoType', null, 'public', null, 'myPropertyNoType', null, 'public', null],
            'protected array property with default' => ['myArrayProperty', 'array', 'protected', [1, 2, 3], 'myArrayProperty', 'array', 'protected', [1, 2, 3]],
        ];
    }

    public function invalidClassPropertyProvider(): array
    {
        return [
            'invalid visibility' => ['myProperty', 'string', 'invalidVisibility', null],
            'invalid type' => ['myProperty', 'invalidType', 'public', null],
            'invalid default value for type' => ['myIntProperty', 'int', 'public', 'stringDefault'],
        ];
    }

    public function classPropertyStaticProvider(): array
    {
        return [
            'static public property' => ['staticProperty', 'string', 'public', 'staticValue'],
            'static private property' => ['staticPrivateProperty', 'int', 'private', 10],
            'static protected property' => ['staticProtectedProperty', 'bool', 'protected', true],
        ];
    }
}
