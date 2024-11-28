<?php

declare(strict_types=1);

namespace Basics\ConditionalGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\Return_;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\ConditionalGenerationHelper;

class TestCreateIfStatement extends TestCase
{
    private ConditionalGenerationHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new ConditionalGenerationHelper(new BuilderFactory());
    }

    public function test_create_if_statement(): void
    {
        $condition = new Equal(new Variable('x'), new LNumber(10));
        $ifBody = [new Return_(new String_('True case'))];
        $elseIfBlocks = [
            ['condition' => new Equal(new Variable('x'), new LNumber(20)), 'body' => [new Return_(new String_('Else if case'))]],
        ];
        $elseBody = [new Return_(new String_('Else case'))];

        $ifStmt = $this->helper->createIfStatement($condition, $ifBody, $elseIfBlocks, $elseBody);

        $this->assertCount(1, $ifStmt->stmts);
        $this->assertCount(1, $ifStmt->elseifs);
        $this->assertNotNull($ifStmt->else);
    }
}
