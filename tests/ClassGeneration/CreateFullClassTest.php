<?php

declare(strict_types=1);

namespace ClassGeneration;

use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\ClassGenerationHelper;

class CreateFullClassTest extends TestCase
{
    public function test_create_full_class(): void
    {
        $factory = new BuilderFactory();
        $helper = new ClassGenerationHelper($factory);

        // Create the full class with namespace
        $namespace = $helper->createFullClass(
            'MyClass',
            'App\\Generated',
            [
                'name' => ['type' => 'string', 'visibility' => 'private', 'default' => 'DefaultName'],
            ],
            [
                ['name' => 'name', 'type' => 'string', 'default' => 'DefaultName'],
            ],
            [
                [
                    'name' => 'getName',
                    'visibility' => 'public',
                    'returnType' => 'string',
                    'params' => [],
                    'body' => [],
                ],
            ],
            ['SomeTrait'],
            ['JsonSerializable'],
            ['SomeDependency']
        );

        // Convert the namespace builder to a Node
        $namespaceNode = $namespace->getNode();

        // Pretty print the Node
        $printer = new Standard();
        $code = $printer->prettyPrintFile([$namespaceNode]);

        // Assertions
        $this->assertStringContainsString('namespace App\\Generated;', $code);
        $this->assertStringContainsString('class MyClass', $code);
        $this->assertStringContainsString('private string $name = \'DefaultName\';', $code);
        $this->assertStringContainsString('use SomeTrait;', $code);
        $this->assertStringContainsString('implements JsonSerializable', $code);
    }
}