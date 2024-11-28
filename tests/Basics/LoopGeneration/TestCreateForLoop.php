<?php

declare(strict_types=1);

namespace Basics\LoopGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PostInc;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\Expression;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\LoopGenerationHelper;

class TestCreateForLoop extends TestCase
{
    private LoopGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new LoopGenerationHelper(new BuilderFactory());
    }

    public function test_create_for_loop(): void
    {
        // Define loop components
        $init = [new Assign(new Variable('i'), new LNumber(0))];
        $condition = new Smaller(new Variable('i'), new LNumber(10));
        $update = [new PostInc(new Variable('i'))];
        $body = [new Expression(new Assign(new Variable('result'), new Variable('i')))];

        // Generate the for loop
        $forLoop = $this->helper->createForLoop($init, $condition, $update, $body);

        // Validate structure and components
        $this->assertCount(1, $forLoop->init, 'Initialization part must have one statement');
        $this->assertCount(1, $forLoop->cond, 'Condition part must have one statement');
        $this->assertCount(1, $forLoop->loop, 'Update part must have one statement');
        $this->assertCount(1, $forLoop->stmts, 'Body must have one statement');

        // Validate initialization
        $this->assertInstanceOf(Assign::class, $forLoop->init[0], 'Initialization must be an assignment');
        $this->assertSame('i', $forLoop->init[0]->var->name, 'Variable in initialization must be "i"');
        $this->assertEquals(0, $forLoop->init[0]->expr->value, 'Initialization value must be 0');

        // Validate condition
        $this->assertInstanceOf(Smaller::class, $forLoop->cond[0], 'Condition must be a comparison');
        $this->assertSame('i', $forLoop->cond[0]->left->name, 'Condition must compare "i"');
        $this->assertEquals(10, $forLoop->cond[0]->right->value, 'Condition must compare "i" to 10');

        // Validate update
        $this->assertInstanceOf(PostInc::class, $forLoop->loop[0], 'Update must increment the variable');
        $this->assertSame('i', $forLoop->loop[0]->var->name, 'Update must increment "i"');

        // Validate body
        $this->assertInstanceOf(Expression::class, $forLoop->stmts[0], 'Body must contain an expression');
        $this->assertInstanceOf(Assign::class, $forLoop->stmts[0]->expr, 'Body must assign a value');
        $this->assertSame('result', $forLoop->stmts[0]->expr->var->name, 'Assignment must target "result"');
        $this->assertSame('i', $forLoop->stmts[0]->expr->expr->name, 'Assignment must assign "i" to "result"');
    }
}
