<?php

declare(strict_types=1);

namespace ClassGeneration;

use PhpParser\Builder\Class_;
use PhpParser\BuilderFactory;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\ClassGenerationHelper;

class CreateClassTest extends TestCase
{
    public function test_create_class(): void
    {
        $factory = new BuilderFactory();
        $helper = new ClassGenerationHelper($factory);

        $class = $helper->createClass('MyClass');

        $this->assertInstanceOf(Class_::class, $class);
        $this->assertSame('MyClass', $class->getNode()->name->toString());
    }
}
