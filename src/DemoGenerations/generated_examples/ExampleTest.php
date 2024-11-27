<?php

namespace GeneratedExamples;

class ExampleTest extends TestCase
{
    
    /**
     * 
     * @return void
     */
    protected function setUp(): void
    {
        $this->parent::setUp();
        $this->assertInstanceOf('ExampleClass::class', new ExampleClass());
    }
    
    /**
     * 
     * @return void
     */
    public function test_example_property(): void
    {
        $this->assertTrue(true);
        $this->assertInstanceOf('ExampleClass::class', new ExampleClass());
    }
}