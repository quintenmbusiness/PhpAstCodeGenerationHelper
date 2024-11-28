<?php

declare(strict_types=1);

namespace GeneratedExamples;

use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\DemoGenerations\generated_examples\ExampleClass;

class ExampleClassTest extends TestCase
{
    public function test_constructor_initializes_param1(): void
    {
        $example = new ExampleClass('test value');
        $this->assertSame('test value', $example->param1);

        $exampleDefault = new ExampleClass();
        $this->assertSame('default', $exampleDefault->param1);
    }

    public function test_get_example_property_returns_default_value(): void
    {
        $example = new ExampleClass();
        $this->assertSame('default value', $example->getExampleProperty());
    }

    public function test_set_example_property_updates_value(): void
    {
        $example = new ExampleClass();
        $example->setExampleProperty('new value');

        $this->assertSame('new value', $example->getExampleProperty());
    }
}
