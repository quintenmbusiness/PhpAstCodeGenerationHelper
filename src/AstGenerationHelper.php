<?php

declare(strict_types=1);

namespace Quintenm\PhpAstCodeGenerationHelper;

use PhpParser\BuilderFactory;
use Quintenm\PhpAstCodeGenerationHelper\GeneratorHelpers\ClassGenerationHelper;

class AstGenerationHelper extends ClassGenerationHelper
{
    public function __construct()
    {
        parent::__construct(new BuilderFactory());
    }
}
