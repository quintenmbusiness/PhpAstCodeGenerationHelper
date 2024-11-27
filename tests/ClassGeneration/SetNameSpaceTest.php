<?php

declare(strict_types=1);

namespace ClassGeneration;

use PhpParser\Builder\Namespace_;
use PhpParser\BuilderFactory;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\ClassGenerationHelper;

class SetNameSpaceTest extends TestCase
{
    public function test_set_namespace_with_no_imports(): void
    {
        $factory = new BuilderFactory();
        $helper = new ClassGenerationHelper($factory);

        $class = $helper->createClass('MyClass');
        $namespace = $helper->setNamespace('App\\Generated', $class);

        $this->assertInstanceOf(Namespace_::class, $namespace);
        $this->assertSame('App\\Generated', $namespace->getNode()->name->toString());
    }

    public function test_set_namespace_with_imports(): void
    {
        $factory = new BuilderFactory();
        $helper = new ClassGenerationHelper($factory);

        $class = $helper->createClass('MyClass');
        $namespace = $helper->setNamespace('App\\Generated', $class, ['App\\SomeDependency', 'App\\AnotherDependency']);

        $importNodes = $namespace->getNode()->stmts;
        $this->assertCount(3, $importNodes); // 2 imports + the class

        $this->assertSame('App\\SomeDependency', $importNodes[0]->uses[0]->name->toString());
        $this->assertSame('App\\AnotherDependency', $importNodes[1]->uses[0]->name->toString());
    }
}
