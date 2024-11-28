<?php

declare(strict_types=1);

namespace quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\While_;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class LoopGenerationHelper extends ConditionalGenerationHelper
{
    /**
     * @param BuilderFactory $factory
     */
    public function __construct(BuilderFactory $factory)
    {
        parent::__construct($factory);
    }

    /**
     * Creates a `for` loop.
     *
     * @param array<int, Expr> $init Initialization expressions.
     * @param Expr $condition Loop condition.
     * @param array<int, Expr> $update Update expressions.
     * @param array<int, Stmt> $body Loop body statements.
     * @return For_
     */
    public function createForLoop(array $init, Expr $condition, array $update, array $body): For_
    {
        return new For_([
            'init' => $init,
            'cond' => [$condition],
            'loop' => $update,
            'stmts' => $body,
        ]);
    }

    /**
     * Creates a `foreach` loop.
     *
     * @param Expr $iterable The iterable expression.
     * @param Expr $valueVar The variable to hold each value.
     * @param array<int, Stmt> $body Loop body statements.
     * @param Expr|null $keyVar Optional key variable.
     * @return Foreach_
     */
    public function createForeachLoop(Expr $iterable, Expr $valueVar, array $body, ?Expr $keyVar = null): Foreach_
    {
        return new Foreach_($iterable, $valueVar, [
            'keyVar' => $keyVar,
            'stmts' => $body,
        ]);
    }

    /**
     * Creates a `while` loop.
     *
     * @param Expr $condition Loop condition.
     * @param array<int, Stmt> $body Loop body statements.
     * @return While_
     */
    public function createWhileLoop(Expr $condition, array $body): While_
    {
        return new While_($condition, $body); // Pass $condition directly, not as an array.
    }
}
