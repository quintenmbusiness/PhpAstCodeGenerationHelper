<?php

declare(strict_types=1);

namespace Basics\LoopGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\Return_;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\LoopGenerationHelper;

class TestCreateWhileLoop extends TestCase
{
    private LoopGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new LoopGenerationHelper(new BuilderFactory());
    }

    public function test_create_while_loop(): void
    {
        $condition = new Smaller(new Variable('i'), new LNumber(5));
        $body = [new Return_(new Variable('i'))];

        $whileLoop = $this->helper->createWhileLoop($condition, $body);

        $this->assertSame($condition, $whileLoop->cond);
        $this->assertCount(1, $whileLoop->stmts);
    }
}
