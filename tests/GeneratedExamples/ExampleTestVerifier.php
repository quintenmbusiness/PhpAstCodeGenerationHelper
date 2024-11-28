<?php

namespace GeneratedExamples;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use quintenmbusiness\PhpAstCodeGenerationHelper\DemoGenerations\generated_examples\ExampleTest;

class ExampleTestVerifier extends TestCase
{
    /**
     * Test all methods in ExampleTest and verify they pass.
     *
     * @return void
     */
    public function test_all_example_test_methods_pass(): void
    {
        // Create a test suite from the ExampleTest class
        $suite = TestSuite::fromClassName(ExampleTest::class);

        // Run the suite
        $suite->run();
    }
}