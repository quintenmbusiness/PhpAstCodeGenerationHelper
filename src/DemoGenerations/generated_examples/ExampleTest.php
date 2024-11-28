<?php

namespace quintenmbusiness\PhpAstCodeGenerationHelper\DemoGenerations\generated_examples;

use PHPUnit\Framework\TestCase;
class ExampleTest extends TestCase
{
    
    /**
     * 
     * @return void
     */
    public function test_assertTrue(): void
    {
        $this->assertTrue(true);
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertFalse(): void
    {
        $this->assertFalse(false);
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertEquals(): void
    {
        $this->assertEquals(42, 42);
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertNotEquals(): void
    {
        $this->assertNotEquals(42, 43);
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertSame(): void
    {
        $this->assertSame('value', 'value');
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertNotSame(): void
    {
        $this->assertNotSame('value', 'otherValue');
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertNull(): void
    {
        $this->assertNull(null);
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertNotNull(): void
    {
        $this->assertNotNull(true);
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertEmpty(): void
    {
        $this->assertEmpty([]);
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertNotEmpty(): void
    {
        $this->assertNotEmpty([1, 2, 3]);
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertCount(): void
    {
        $this->assertCount(3, [1, 2, 3]);
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertGreaterThan(): void
    {
        $this->assertGreaterThan(5, 10);
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertGreaterThanOrEqual(): void
    {
        $this->assertGreaterThanOrEqual(10, 10);
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertLessThan(): void
    {
        $this->assertLessThan(10, 5);
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertLessThanOrEqual(): void
    {
        $this->assertLessThanOrEqual(5, 5);
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertStringContainsString(): void
    {
        $this->assertStringContainsString('example', 'example string');
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertStringNotContainsString(): void
    {
        $this->assertStringNotContainsString('not', 'example string');
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertStringStartsWith(): void
    {
        $this->assertStringStartsWith('start', 'start of string');
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertStringEndsWith(): void
    {
        $this->assertStringEndsWith('end', 'string end');
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertArrayHasKey(): void
    {
        $this->assertArrayHasKey('key', ['key' => 'value']);
    }
    
    /**
     * 
     * @return void
     */
    public function test_assertArrayNotHasKey(): void
    {
        $this->assertArrayNotHasKey('missingKey', ['key' => 'value']);
    }
}