<?php

declare(strict_types=1);

namespace Basics\BasicGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\BasicGenerationHelper;

class CreateArgumentsTest extends TestCase
{
    private BasicGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new BasicGenerationHelper(new BuilderFactory());
    }

    #[Test]
    public function it_creates_arguments_from_simple_values(): void
    {
        $args = [42, 'string'];

        $result = $this->helper->createArguments($args);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Arg::class, $result[0]);
        $this->assertInstanceOf(LNumber::class, $result[0]->value);
        $this->assertEquals(42, $result[0]->value->value);

        $this->assertInstanceOf(Arg::class, $result[1]);
        $this->assertInstanceOf(String_::class, $result[1]->value);
        $this->assertEquals('string', $result[1]->value->value);
    }

    #[Test]
    public function it_creates_arguments_from_nested_array(): void
    {
        $args = [['key' => 'value', 42]];

        $result = $this->helper->createArguments($args);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(Arg::class, $result[0]);
        $this->assertInstanceOf(Array_::class, $result[0]->value);
    }
}