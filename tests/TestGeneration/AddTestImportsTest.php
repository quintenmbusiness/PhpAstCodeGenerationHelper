<?php

declare(strict_types=1);

namespace TestGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Use_;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\TestGenerationHelper;

class AddTestImportsTest extends TestCase
{
    private TestGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new TestGenerationHelper(new BuilderFactory());
    }

    public function test_adds_default_imports(): void
    {
        $class = $this->helper->createClass('ExampleTest');
        $namespace = $this->helper->addTestImports($class);

        $namespaceNode = $namespace->getNode();

        // Filter only Use_ statements
        $importStatements = array_values(
            array_filter($namespaceNode->stmts, fn($stmt) => $stmt instanceof Use_)
        );

        // Assertions
        $this->assertCount(2, $importStatements);
        $this->assertSame('PHPUnit\Framework\TestCase', $importStatements[0]->uses[0]->name->toString());
        $this->assertSame('PhpParser\BuilderFactory', $importStatements[1]->uses[0]->name->toString());
    }

    public function test_adds_custom_imports(): void
    {
        $class = $this->helper->createClass('ExampleTest');
        $namespace = $this->helper->addTestImports($class, ['MyNamespace\\MyClass']);

        $namespaceNode = $namespace->getNode();

        // Filter only Use_ statements
        $importStatements = array_values(
            array_filter($namespaceNode->stmts, fn($stmt) => $stmt instanceof Use_)
        );

        // Assertions
        $this->assertCount(3, $importStatements);
        $this->assertSame('PHPUnit\Framework\TestCase', $importStatements[0]->uses[0]->name->toString());
        $this->assertSame('PhpParser\BuilderFactory', $importStatements[1]->uses[0]->name->toString());
        $this->assertSame('MyNamespace\MyClass', $importStatements[2]->uses[0]->name->toString());
    }
}