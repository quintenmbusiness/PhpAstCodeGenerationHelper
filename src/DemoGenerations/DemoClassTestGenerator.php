<?php

declare(strict_types=1);

namespace quintenmbusiness\PhpAstCodeGenerationHelper\DemoGenerations;

use PhpParser\BuilderFactory;
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

        // Generate the test class
        $testClass = $this->helper->createTestClass(
            'ExampleTest',
            methods: [
                'test_example_property' => $this->helper->createTestMethod(
                    'test_example_property',
                    [
                        $this->helper->createAssertion('assertTrue', [true]),
                        $this->helper->createAssertion(
                            'assertInstanceOf',
                            ['ExampleClass::class', new \PhpParser\Node\Expr\New_(new \PhpParser\Node\Name('ExampleClass'))]
                        ),
                    ]
                ),
            ],
            setupBody: [
                $this->helper->createAssertion(
                    'assertInstanceOf',
                    ['ExampleClass::class', new \PhpParser\Node\Expr\New_(new \PhpParser\Node\Name('ExampleClass'))]
                ),
            ]
        );

        $this->helper->addTestImports($testClass);

        $this->helper->generateFile($testClass, 'GeneratedExamples', $outputPath);

        echo "Test class generated at: $outputPath" . PHP_EOL;
    }
}