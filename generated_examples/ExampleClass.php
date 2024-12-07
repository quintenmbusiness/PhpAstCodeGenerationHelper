<?php

namespace quintenmbusiness\PhpAstCodeGenerationHelper\DemoGenerations\generated_examples;

class ExampleClass
{
    public string $exampleProperty = 'default value';
    public function __construct(string $param1 = 'default')
    {
        $this->param1 = $param1;
    }
    public function getExampleProperty(): string
    {
        return $this->exampleProperty;
    }
    public function setExampleProperty(string $value): void
    {
        $this->exampleProperty = $value;
    }
}
