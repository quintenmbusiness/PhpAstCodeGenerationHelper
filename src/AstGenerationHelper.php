<?php

declare(strict_types=1);

namespace quintenmbusiness\PhpAstCodeGenerationHelper;

use PhpParser\BuilderFactory;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\ClassGenerationHelper;

class AstGenerationHelper extends ClassGenerationHelper
{
    public function __construct()
    {
        parent::__construct(new BuilderFactory());
    }
}
