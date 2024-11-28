<?php

declare(strict_types=1);

namespace Basics\LoopGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Expr\PostInc;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\Return_;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\LoopGenerationHelper;

class TestCreateForLoop extends TestCase
{
    private LoopGenerationHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new LoopGenerationHelper(new BuilderFactory());
    }

    public function test_create_for_loop(): void
    {
        $init = [new Variable('i')];
        $condition = new Smaller(new Variable('i'), new LNumber(10));
        $update = [new PostInc(new Variable('i'))];
        $body = [new Return_(new Variable('i'))];

        $forLoop = $this->helper->createForLoop($init, $condition, $update, $body);

        $this->assertCount(1, $forLoop->init);
        $this->assertCount(1, $forLoop->cond);
        $this->assertCount(1, $forLoop->loop);
        $this->assertCount(1, $forLoop->stmts);
    }
}
