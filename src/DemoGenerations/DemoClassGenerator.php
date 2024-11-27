<?php

declare(strict_types=1);

namespace quintenmbusiness\PhpAstCodeGenerationHelper\DemoGenerations;

use PhpParser\BuilderFactory;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\BasicGenerationHelper;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\ClassGenerationHelper;

class DemoClassGenerator
{
    private BasicGenerationHelper $helper;

    public function __construct()
    {
        $this->helper = new ClassGenerationHelper(new BuilderFactory());
    }

    public function generate(): void
    {
        $outputDir = __DIR__ . '/generated_examples';
        $outputPath = $outputDir . '/ExampleClass.php';

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Generate the class
        $class = $this->helper->createFullClass(
            'ExampleClass',
            'GeneratedExamples',
            properties: [
                'exampleProperty' => [
                    'type' => 'string',
                    'visibility' => 'public',
                    'default' => 'default value',
                ],
            ],
            constructorParams: [
                ['name' => 'param1', 'type' => 'string', 'default' => 'default'],
            ],
            methods: [
                [
                    'name' => 'getExampleProperty',
                    'visibility' => 'public',
                    'returnType' => 'string',
                    'params' => [],
                    'body' => [
                        $this->helper->return($this->helper->thisVar('exampleProperty')),
                    ],
                ],
                [
                    'name' => 'setExampleProperty',
                    'visibility' => 'public',
                    'returnType' => 'void',
                    'params' => [['name' => 'value', 'type' => 'string']],
                    'body' => [
                        $this->helper->assignThisVarToVar('exampleProperty', 'value'),
                    ],
                ],
            ],
            traits: ['GeneratedExamples\ExampleTrait'],
            implements: ['GeneratedExamples\ExampleInterface'],
            imports: ['GeneratedExamples\ExampleTrait', 'GeneratedExamples\ExampleInterface']
        );

        $this->helper->generateFile($class, 'GeneratedExamples', $outputPath);

        echo "Class generated at: $outputPath" . PHP_EOL;
    }
}