<?php

declare(strict_types=1);

namespace Basics\BasicGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Return_;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\BasicGenerationHelper;

class ReturnStmtTest extends TestCase
{
    /**
     * @var BasicGenerationHelper
     */
    private BasicGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new BasicGenerationHelper(new BuilderFactory());
    }

    #[Test]
    public function it_creates_a_return_statement(): void
    {
        $variableExpr = new Variable('testVar');
        $returnStmt = $this->helper->return($variableExpr);

        $this->assertInstanceOf(Return_::class, $returnStmt);
        $this->assertInstanceOf(Expr::class, $returnStmt->expr);
        $this->assertSame($variableExpr, $returnStmt->expr);
    }
}
