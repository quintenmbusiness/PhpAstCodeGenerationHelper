<?php

declare(strict_types=1);

namespace BasicGeneration;

use PHPUnit\Framework\TestCase;
use PhpParser\BuilderFactory;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\BasicGenerationHelper;
use PhpParser\Builder\Class_;
use PhpParser\Builder\Interface_;

class GenerateFileTest extends TestCase
{
    private BasicGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new BasicGenerationHelper(new BuilderFactory());
    }

    public function test_generates_class_file(): void
    {
        $class = (new BuilderFactory())->class('ExampleClass');
        $namespace = 'TestNamespace';
        $outputPath = sys_get_temp_dir() . '/ExampleClass.php';

        $this->helper->generateFile($class, $namespace, $outputPath);

        $this->assertFileExists($outputPath);

        $content = file_get_contents($outputPath);
        $this->assertStringContainsString('namespace TestNamespace;', $content);
        $this->assertStringContainsString('class ExampleClass', $content);

        unlink($outputPath);
    }

    public function test_generates_interface_file(): void
    {
        $interface = (new BuilderFactory())->interface('ExampleInterface');
        $namespace = 'TestNamespace';
        $outputPath = sys_get_temp_dir() . '/ExampleInterface.php';

        $this->helper->generateFile($interface, $namespace, $outputPath);

        $this->assertFileExists($outputPath);

        $content = file_get_contents($outputPath);
        $this->assertStringContainsString('namespace TestNamespace;', $content);
        $this->assertStringContainsString('interface ExampleInterface', $content);

        unlink($outputPath);
    }

    public function test_creates_directory_if_not_exists(): void
    {
        $class = (new BuilderFactory())->class('ExampleClass');
        $namespace = 'TestNamespace';
        $outputDir = sys_get_temp_dir() . '/test_dir';
        $outputPath = $outputDir . '/ExampleClass.php';

        $this->helper->generateFile($class, $namespace, $outputPath);

        $this->assertFileExists($outputPath);
        $this->assertDirectoryExists($outputDir);

        unlink($outputPath);
        rmdir($outputDir);
    }
}
