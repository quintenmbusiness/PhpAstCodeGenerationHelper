<?php

declare(strict_types=1);

namespace Basics\ConditionalGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\Return_;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\ConditionalGenerationHelper;

class TestCreateSwitchStatement extends TestCase
{
    private ConditionalGenerationHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new ConditionalGenerationHelper(new BuilderFactory());
    }

    public function test_create_switch_statement(): void
    {
        $expression = new Variable('x');
        $cases = [
            ['case' => new LNumber(1), 'body' => [new Return_(new String_('Case 1'))]],
            ['case' => null, 'body' => [new Return_(new String_('Default case'))]],
        ];

        $switchStmt = $this->helper->createSwitchStatement($expression, $cases);

        $this->assertCount(2, $switchStmt->cases);
        $this->assertNull($switchStmt->cases[1]->cond); // Default case
    }
}
