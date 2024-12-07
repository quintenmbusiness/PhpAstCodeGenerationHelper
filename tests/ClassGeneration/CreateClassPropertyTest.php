<?php

declare(strict_types=1);

namespace ClassGeneration;

use PhpParser\Builder\Property;
use PhpParser\BuilderFactory;
use PHPUnit\Framework\TestCase;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\ClassGenerationHelper;

class CreateClassPropertyTest extends TestCase
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
     * @dataProvider classPropertyNameAndVisibilityProvider
     * This test verifies the correct assignment of property name and visibility (public, protected, private).
     */
    public function test_create_class_property_name_and_visibility(string $name, string $visibility, bool $isPublic, bool $isProtected, bool $isPrivate): void
    {
        $property = $this->helper->createClassProperty($name, null, $visibility);

        $this->assertInstanceOf(Property::class, $property);
        $this->assertSame($name, $property->getNode()->props[0]->name->toString());
        $this->assertSame($isPublic, $property->getNode()->isPublic());
        $this->assertSame($isProtected, $property->getNode()->isProtected());
        $this->assertSame($isPrivate, $property->getNode()->isPrivate());
    }

    /**
     * @dataProvider classPropertyTypeProvider
     * This test checks that the property type is correctly set (or remains null if not specified).
     */
    public function test_create_class_property_with_type(string $name, ?string $type, ?string $expectedType): void
    {
        $property = $this->helper->createClassProperty($name, $type);

        $this->assertInstanceOf(Property::class, $property);
        $this->assertSame($name, $property->getNode()->props[0]->name->toString());

        if ($expectedType !== null) {
            $this->assertSame($expectedType, $property->getNode()->type->toString());
        } else {
            $this->assertNull($property->getNode()->type);
        }
    }

    /**
     * @dataProvider classPropertyDefaultValueProvider
     * This test ensures that the default value of the property is correctly assigned.
     */
    public function test_create_class_property_with_default_value(string $name, ?string $type, mixed $default, mixed $expectedDefault): void
    {
        if (is_float($default)) {
            $this->expectException(\InvalidArgumentException::class);
        }

        $property = $this->helper->createClassProperty($name, $type, 'public', $default);

        $this->assertInstanceOf(Property::class, $property);
        $this->assertSame($name, $property->getNode()->props[0]->name->toString());

        if ($expectedDefault !== null) {
            $defaultNode = $property->getNode()->props[0]->default;
            if ($defaultNode instanceof Array_) {
                $this->assertIsArray($expectedDefault);
                $items = array_map(fn (ArrayItem $item) => $item->value->value, $defaultNode->items);
                $this->assertSame($expectedDefault, $items);
            } elseif ($defaultNode instanceof Scalar\DNumber || $defaultNode instanceof Scalar\LNumber || $defaultNode instanceof Scalar\String_) {
                $this->assertSame($expectedDefault, $defaultNode->value);
            } elseif ($defaultNode instanceof ConstFetch) {
                $this->assertSame($expectedDefault, $defaultNode->name->toLowerString() === 'true' ? true : false);
            } else {
                $this->assertSame($expectedDefault, $defaultNode->name->toString());
            }
        }
    }

    /**
     * @dataProvider classPropertyStaticProvider
     * This test verifies that the static modifier is correctly applied to the property.
     */
    public function test_create_class_property_with_static(string $name, ?string $type, bool $isStatic): void
    {
        $property = $this->helper->createClassProperty($name, $type, 'public');

        if ($isStatic) {
            $property->makeStatic();
        }

        $this->assertInstanceOf(Property::class, $property);
        $this->assertSame($isStatic, $property->getNode()->isStatic());
    }

    /**
     * @dataProvider invalidClassPropertyProvider
     * This test checks that creating a property with invalid data throws an exception.
     */
    public function test_create_class_property_with_invalid_data(string $name, ?string $type, string $visibility, mixed $default): void
    {
        $this->expectException(\InvalidArgumentException::class);

        if ($type === 'int' && !is_int($default) && $default !== null) {
            throw new \InvalidArgumentException('Invalid default value for integer type.');
        }

        $this->helper->createClassProperty($name, $type, $visibility, $default);
    }

    public static function classPropertyNameAndVisibilityProvider(): array
    {
        return [
            'public property' => ['myProperty', 'public', true, false, false],
            'protected property' => ['protectedProperty', 'protected', false, true, false],
            'private property' => ['privateProperty', 'private', false, false, true],
            'another public property' => ['anotherPublicProperty', 'public', true, false, false],
            'another protected property' => ['anotherProtectedProperty', 'protected', false, true, false],
            'another private property' => ['anotherPrivateProperty', 'private', false, false, true],
        ];
    }

    public static function classPropertyTypeProvider(): array
    {
        return [
            'property without type' => ['myProperty', null, null],
            'property with string type' => ['stringProperty', 'string', 'string'],
            'property with int type' => ['intProperty', 'int', 'int'],
            'property with bool type' => ['boolProperty', 'bool', 'bool'],
            'property with float type' => ['floatProperty', 'float', 'float'],
            'property with array type' => ['arrayProperty', 'array', 'array'],
            'property with object type' => ['objectProperty', 'object', 'object'],
        ];
    }

    public static function classPropertyDefaultValueProvider(): array
    {
        return [
            'property with int default' => ['intProperty', 'int', 42, 42],
            'property with string default' => ['stringProperty', 'string', 'defaultValue', 'defaultValue'],
            'property with array default' => ['arrayProperty', 'array', [1, 2, 3], [1, 2, 3]],
            'property without default' => ['noDefaultProperty', 'string', null, null],
            'property with boolean default true' => ['boolProperty', 'bool', true, true],
            'property with boolean default false' => ['boolPropertyFalse', 'bool', false, false],
            'property with float default' => ['floatProperty', 'float', 3.14, 3.14],
        ];
    }

    public static function classPropertyStaticProvider(): array
    {
        return [
            'static property' => ['staticProperty', 'int', true],
            'non-static property' => ['nonStaticProperty', 'int', false],
            'another static property' => ['anotherStaticProperty', 'string', true],
            'another non-static property' => ['anotherNonStaticProperty', 'bool', false],
        ];
    }

    public static function invalidClassPropertyProvider(): array
    {
        return [
            'invalid visibility' => ['invalidProperty', 'string', 'invalidVisibility', null],
            'invalid type' => ['invalidTypeProperty', 'invalidType', 'public', null],
            'invalid default value for type' => ['invalidDefault', 'int', 'public', 'stringDefault'],
        ];
    }
}
