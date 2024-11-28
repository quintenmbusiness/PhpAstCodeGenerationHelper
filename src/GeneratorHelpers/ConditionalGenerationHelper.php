<?php

declare(strict_types=1);

namespace quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\Node\Stmt\Case_;

class ConditionalGenerationHelper extends BasicGenerationHelper
{
    /**
     * @param BuilderFactory $factory
     */
    public function __construct(BuilderFactory $factory)
    {
        parent::__construct($factory);
    }

    /**
     * Creates an `if` statement with optional `else if` and `else` blocks.
     *
     * @param Expr $condition The condition for the `if`.
     * @param array<int, Stmt> $ifBody Statements for the `if` block.
     * @param array<int, array{condition: Expr, body: array<int, Stmt>}> $elseIfBlocks `else if` conditions and bodies.
     * @param array<int, Stmt>|null $elseBody Statements for the `else` block.
     * @return If_
     */
    public function createIfStatement(
        Expr $condition,
        array $ifBody,
        array $elseIfBlocks = [],
        ?array $elseBody = null
    ): If_ {
        $ifStmt = new If_($condition, ['stmts' => $ifBody]);

        foreach ($elseIfBlocks as $elseIf) {
            // Flatten the body to ensure it's an array of Stmt
            $ifStmt->elseifs[] = new ElseIf_($elseIf['condition'], $elseIf['body']);
        }

        if ($elseBody !== null) {
            // Ensure else body is an array of Stmt
            $ifStmt->else = new Else_($elseBody);
        }

        return $ifStmt;
    }

    /**
     * Creates a `switch` statement.
     *
     * @param Expr $expression The expression to evaluate.
     * @param array<int, array{case: Expr|null, body: array<int, Stmt>}> $cases Cases and their bodies.
     * @return Switch_
     */
    public function createSwitchStatement(Expr $expression, array $cases): Switch_
    {
        $caseStatements = [];

        foreach ($cases as $case) {
            $caseStatements[] = new Case_(
                $case['case'], // null for default
                $case['body']
            );
        }

        return new Switch_($expression, $caseStatements);
    }
}
