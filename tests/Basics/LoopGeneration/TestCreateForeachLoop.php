<?php

declare(strict_types=1);

namespace Basics\LoopGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Return_;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\LoopGenerationHelper;

class TestCreateForeachLoop extends TestCase
{
    private LoopGenerationHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new LoopGenerationHelper(new BuilderFactory());
    }

    public function test_create_foreach_loop(): void
    {
        $iterable = new Variable('items');
        $valueVar = new Variable('item');
        $body = [new Return_(new Variable('item'))];
        $keyVar = new Variable('key');

        $foreachLoop = $this->helper->createForeachLoop($iterable, $valueVar, $body, $keyVar);

        $this->assertSame($iterable, $foreachLoop->expr);
        $this->assertSame($valueVar, $foreachLoop->valueVar);
        $this->assertSame($keyVar, $foreachLoop->keyVar);
        $this->assertCount(1, $foreachLoop->stmts);
    }
}
