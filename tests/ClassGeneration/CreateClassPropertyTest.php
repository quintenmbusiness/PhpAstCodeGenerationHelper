<?php

declare(strict_types=1);

namespace ClassGeneration;

use PhpParser\Builder\Property;
use PhpParser\BuilderFactory;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\ClassGenerationHelper;

class CreateClassPropertyTest extends TestCase
{
    public function test_create_class_property_with_default_values(): void
    {
        $factory = new BuilderFactory();
        $helper = new ClassGenerationHelper($factory);

        $property = $helper->createClassProperty('myProperty');

        $this->assertInstanceOf(Property::class, $property);
        $this->assertSame('myProperty', $property->getNode()->props[0]->name->toString());
    }

    public function test_create_class_property_with_type_and_visibility(): void
    {
        $factory = new BuilderFactory();
        $helper = new ClassGenerationHelper($factory);

        $property = $helper->createClassProperty('myProperty', 'string', 'private');

        $this->assertInstanceOf(Property::class, $property);
        $this->assertSame('string', $property->getNode()->type->toString());
        $this->assertTrue($property->getNode()->isPrivate());
    }

    public function test_create_class_property_with_default_value(): void
    {
        $factory = new BuilderFactory();
        $helper = new ClassGenerationHelper($factory);

        $property = $helper->createClassProperty('myProperty', 'int', 'protected', 42);

        $this->assertInstanceOf(Property::class, $property);
        $this->assertSame(42, $property->getNode()->props[0]->default->value);
    }
}
