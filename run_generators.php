<?php

declare(strict_types=1);

use quintenmbusiness\PhpAstCodeGenerationHelper\DemoGenerations\DemoClassGenerator;
use quintenmbusiness\PhpAstCodeGenerationHelper\DemoGenerations\DemoClassTestGenerator;

require_once __DIR__ . '/vendor/autoload.php';

// Run the normal class generator
echo "Generating ExampleClass...\n";
$classGenerator = new DemoClassGenerator();
$classGenerator->generate();

// Run the test class generator
echo "Generating ExampleTest...\n";
$testGenerator = new DemoClassTestGenerator();
$testGenerator->generate();

echo "\nClasses generated successfully";
