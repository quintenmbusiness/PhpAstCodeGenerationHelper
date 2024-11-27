<?php

declare(strict_types=1);

namespace ClassGeneration;

use PhpParser\Builder\Property;
use PhpParser\BuilderFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\ClassGenerationHelper;

class ClassPropertyGenerationTest extends TestCase
{
    private ClassGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new ClassGenerationHelper(new BuilderFactory());
    }

    #[Test]
    public function create_class_property_with_default_visibility(): void
    {
        $property = $this->helper->createClassProperty('testProperty');

        $this->assertInstanceOf(Property::class, $property);
        $this->assertSame('testProperty', $property->getNode()->props[0]->name->toString());
        $this->assertTrue($property->getNode()->isPublic());
        $this->assertNull($property->getNode()->type);
    }

    #[Test]
    public function create_class_property_with_protected_visibility(): void
    {
        $property = $this->helper->createClassProperty('testProperty', null, 'protected');

        $this->assertInstanceOf(Property::class, $property);
        $this->assertTrue($property->getNode()->isProtected());
        $this->assertNull($property->getNode()->type);
    }

    #[Test]
    public function create_class_property_with_private_visibility(): void
    {
        $property = $this->helper->createClassProperty('testProperty', null, 'private');

        $this->assertInstanceOf(Property::class, $property);
        $this->assertTrue($property->getNode()->isPrivate());
        $this->assertNull($property->getNode()->type);
    }

    #[Test]
    public function create_class_property_with_type(): void
    {
        $property = $this->helper->createClassProperty('testProperty', 'string');

        $this->assertInstanceOf(Property::class, $property);
        $this->assertSame('string', $property->getNode()->type->toString());
        $this->assertTrue($property->getNode()->isPublic());
    }

    #[Test]
    public function create_class_property_with_type_and_visibility(): void
    {
        $property = $this->helper->createClassProperty('testProperty', 'int', 'protected');

        $this->assertInstanceOf(Property::class, $property);
        $this->assertSame('int', $property->getNode()->type->toString());
        $this->assertTrue($property->getNode()->isProtected());
    }
}
