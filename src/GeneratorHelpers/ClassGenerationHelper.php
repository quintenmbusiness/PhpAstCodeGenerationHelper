<?php

declare(strict_types=1);

namespace Quintenm\PhpAstCodeGenerationHelper\GeneratorHelpers;

use PhpParser\BuilderFactory;

class ClassGenerationHelper extends MethodGenerationHelper
{
    /**
     * @param BuilderFactory $factory
     */
    public function __construct(BuilderFactory $factory)
    {
        parent::__construct($factory);
    }
}
