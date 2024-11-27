<?php

declare(strict_types=1);

namespace TestGeneration;

use PhpParser\BuilderFactory;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\TestGenerationHelper;

class CreateTestClassTest extends TestCase
{
    private TestGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new TestGenerationHelper(new BuilderFactory());
    }

    public function test_creates_test_class_with_methods_and_setup(): void
    {
        $methods = [
            'testExample' => $this->helper->createTestMethod('testExample'),
            'testAnother' => $this->helper->createTestMethod('testAnother'),
        ];

        $setupBody = [$this->helper->createAssertion('assertNotNull', [new \PhpParser\Node\Expr\Variable('this')])];

        $testClass = $this->helper->createTestClass(
            'ExampleTest',
            $methods,
            ['MyNamespace\\MyClass'],
            $setupBody
        );

        $classNode = $testClass->getNode();
        $this->assertSame('ExampleTest', $classNode->name->toString());
        $this->assertCount(3, $classNode->stmts); // setUp + 2 test methods
    }
}
