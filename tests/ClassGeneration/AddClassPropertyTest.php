<?php

declare(strict_types=1);

namespace ClassGeneration;

use PhpParser\Builder\Class_;
use PhpParser\BuilderFactory;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\ClassGenerationHelper;

class AddClassPropertyTest extends TestCase
{
    public function test_add_class_property(): void
    {
        $factory = new BuilderFactory();
        $helper = new ClassGenerationHelper($factory);

        $class = $helper->createClass('MyClass');
        $helper->addClassProperty($class, 'myProperty', 'string', 'private', 'defaultValue');

        $stmts = $class->getNode()->stmts;
        $this->assertCount(1, $stmts);
        $this->assertSame('myProperty', $stmts[0]->props[0]->name->toString());
    }
}
