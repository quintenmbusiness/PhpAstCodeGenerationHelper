<?php

namespace GeneratedExamples;

use GeneratedExamples\ExampleTrait;
use GeneratedExamples\ExampleInterface;
class ExampleClass implements GeneratedExamples\ExampleInterface
{
    use GeneratedExamples\ExampleTrait;
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