<?php

declare(strict_types=1);

namespace quintenmbusiness\PhpAstCodeGenerationHelper\DemoGenerations;

use PhpParser\Builder\Class_;
use PhpParser\Builder\Namespace_;
use PhpParser\BuilderFactory;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\TestGenerationHelper;

class DemoClassTestGenerator
{
    private TestGenerationHelper $helper;

    public function __construct()
    {
        $this->helper = new TestGenerationHelper(new BuilderFactory());
    }

    public function generate(): void
    {
        $outputDir = __DIR__ . '/generated_examples';
        $outputPath = $outputDir . '/ExampleTest.php';

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Generate test methods for all PHPUnit assertions
        $assertions = $this->getPhpUnitAssertions();
        $methods = [];

        foreach ($assertions as $assertion => $exampleArgs) {
            $methods["test_$assertion"] = $this->helper->createTestMethod(
                "test_$assertion",
                [
                    $this->helper->createAssertion($assertion, array_values($exampleArgs)), // Ensure indexed array
                ]
            );
        }

        // Generate the test class
        $testClass = $this->helper->createTestClass(
            'ExampleTest',
            methods: $methods,
        );

        // Add imports explicitly
        $namespaceNode = $this->addImports($testClass, [
            'PHPUnit\Framework\TestCase',
        ]);

        $this->helper->generateFile($namespaceNode, 'quintenmbusiness\PhpAstCodeGenerationHelper\DemoGenerations\generated_examples', $outputPath);

        echo "Test class generated at: $outputPath" . PHP_EOL;
    }

    /**
     * @param array<int, string> $imports
     */
    private function addImports(Class_ $class, array $imports): Namespace_
    {
        $factory = new BuilderFactory();
        $namespaceBuilder = $factory->namespace('quintenmbusiness\PhpAstCodeGenerationHelper\DemoGenerations\generated_examples');

        foreach ($imports as $import) {
            $namespaceBuilder->addStmt(new Use_([new UseItem(new Name($import))]));
        }

        $namespaceBuilder->addStmt($class->getNode());

        return $namespaceBuilder;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function getPhpUnitAssertions(): array
    {
        return [
            'assertTrue' => [true],
            'assertFalse' => [false],
            'assertEquals' => [42, 42],
            'assertNotEquals' => [42, 43],
            'assertSame' => ['value', 'value'],
            'assertNotSame' => ['value', 'otherValue'],
            'assertNull' => [null],
            'assertNotNull' => [true],
            'assertEmpty' => [[]],
            'assertNotEmpty' => [[1, 2, 3]],
            'assertCount' => [3, [1, 2, 3]],
            'assertGreaterThan' => [5, 10],
            'assertGreaterThanOrEqual' => [10, 10],
            'assertLessThan' => [10, 5],
            'assertLessThanOrEqual' => [5, 5],
            'assertStringContainsString' => ['example', 'example string'],
            'assertStringNotContainsString' => ['not', 'example string'],
            'assertStringStartsWith' => ['start', 'start of string'],
            'assertStringEndsWith' => ['end', 'string end'],
            'assertArrayHasKey' => ['key', ['key' => 'value']],
            'assertArrayNotHasKey' => ['missingKey', ['key' => 'value']],
        ];
    }
}
