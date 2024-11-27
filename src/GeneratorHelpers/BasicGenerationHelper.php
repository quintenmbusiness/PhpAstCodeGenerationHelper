<?php

declare(strict_types=1);

namespace quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers;

use PhpParser\Node\Expr;
use PhpParser\Builder\Method;
use PhpParser\BuilderFactory;
use PhpParser\Builder\Property;
use PhpParser\Node\Stmt\Return_;

class BasicGenerationHelper
{
    /**
     * @var BuilderFactory
     */
    public BuilderFactory $factory;

    /**
     * @param BuilderFactory $factory
     */
    public function __construct(BuilderFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param  Expr    $expr
     * @return Return_
     */
    public function return(Expr $expr): Return_
    {
        return new Return_($expr);
    }

    /**
     * @param  Method|Property $stmt
     * @param  string          $visibility
     * @return Method|Property
     */
    public function addVisibility(Method|Property $stmt, string $visibility = 'public'): Method|Property
    {
        match ($visibility) {
            'public' => $stmt->makePublic(),
            'protected' => $stmt->makeProtected(),
            'private' => $stmt->makePrivate(),
            'static' => $stmt->makeStatic(),
            'abstract' => $stmt->makeAbstract(),
            'final' => $stmt->makeFinal(),
            default => $stmt,
        };

        return $stmt;
    }
}
