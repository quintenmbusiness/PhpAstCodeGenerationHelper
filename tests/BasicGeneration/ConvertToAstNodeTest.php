<?php

declare(strict_types=1);

namespace BasicGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\BasicGenerationHelper;

class ConvertToAstNodeTest extends TestCase
{
    private BasicGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new BasicGenerationHelper(new BuilderFactory());
    }

    #[Test]
    public function it_converts_integer_to_lnumber(): void
    {
        $result = $this->helper->convertToAstNode(42);

        $this->assertInstanceOf(LNumber::class, $result);
        $this->assertEquals(42, $result->value);
    }

    #[Test]
    public function it_converts_string_to_string_node(): void
    {
        $result = $this->helper->convertToAstNode('string');

        $this->assertInstanceOf(String_::class, $result);
        $this->assertEquals('string', $result->value);
    }

    #[Test]
    public function it_converts_array_to_array_node(): void
    {
        $array = ['key' => 'value', 42];
        $result = $this->helper->convertToAstNode($array);

        $this->assertInstanceOf(Array_::class, $result);
    }

    #[Test]
    public function it_throws_exception_for_unsupported_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported value type for AST conversion.');

        $this->helper->convertToAstNode(new \stdClass());
    }
}