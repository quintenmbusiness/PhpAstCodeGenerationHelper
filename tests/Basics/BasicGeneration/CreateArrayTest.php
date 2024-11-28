<?php

declare(strict_types=1);

namespace Basics\BasicGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\BasicGenerationHelper;

class CreateArrayTest extends TestCase
{
    private BasicGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new BasicGenerationHelper(new BuilderFactory());
    }

    #[Test]
    public function it_creates_array_node_from_indexed_array(): void
    {
        $array = [42, 'string'];

        $result = $this->helper->createArray($array);

        $this->assertInstanceOf(Array_::class, $result);
        $this->assertCount(2, $result->items);

        $this->assertInstanceOf(ArrayItem::class, $result->items[0]);
        $this->assertInstanceOf(LNumber::class, $result->items[0]->value);
        $this->assertEquals(42, $result->items[0]->value->value);

        $this->assertInstanceOf(ArrayItem::class, $result->items[1]);
        $this->assertInstanceOf(String_::class, $result->items[1]->value);
        $this->assertEquals('string', $result->items[1]->value->value);
    }

    #[Test]
    public function it_creates_array_node_from_associative_array(): void
    {
        $array = ['key' => 'value', 42 => 'answer'];

        $result = $this->helper->createArray($array);

        $this->assertInstanceOf(Array_::class, $result);
        $this->assertCount(2, $result->items);

        $this->assertInstanceOf(ArrayItem::class, $result->items[0]);
        $this->assertInstanceOf(String_::class, $result->items[0]->value);
    }
}

