<?php

declare(strict_types=1);

namespace ClassGeneration;

use PhpParser\Builder\Class_;
use PhpParser\BuilderFactory;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\ClassGenerationHelper;

class ExtendClassTest extends TestCase
{
    public function test_extend_class_with_parent_class(): void
    {
        $factory = new BuilderFactory();
        $helper = new ClassGenerationHelper($factory);

        // Create the class
        $class = $helper->createClass('ChildClass');

        // Extend the class with a parent class
        $helper->extendClass($class, 'ParentClass');

        // Get the generated node
        $classNode = $class->getNode();

        // Assertions
        $this->assertNotNull($classNode->extends);
        $this->assertSame('ParentClass', $classNode->extends->toString());
    }

    public function test_extend_class_without_parent_class_does_nothing(): void
    {
        $factory = new BuilderFactory();
        $helper = new ClassGenerationHelper($factory);

        // Create the class without extending
        $class = $helper->createClass('StandaloneClass');

        // Get the generated node
        $classNode = $class->getNode();

        // Assertions
        $this->assertNull($classNode->extends);
    }
}
