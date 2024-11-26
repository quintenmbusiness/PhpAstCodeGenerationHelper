<?php

declare(strict_types=1);

namespace Quintenm\PhpAstCodeGenerationHelper\GeneratorHelpers;

use PhpParser\Builder\Method;
use PhpParser\BuilderFactory;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;

class MethodGenerationHelper extends VariableGenerationHelper
{
    /**
     * @param BuilderFactory $factory
     */
    public function __construct(BuilderFactory $factory)
    {
        parent::__construct($factory);
    }

    /**
     * @param string $name
     * @param string $visibility
     * @param ComplexType|Identifier|Name|string|null $type
     * @return Method
     */
    public function createMethod(string $name, string $visibility = 'public', ComplexType|Identifier|Name|string $type = null): Method {
        $method = new Method($name);

        $this->addVisibility($method, $visibility);

        if($type !== null) {
            $method->setReturnType($type);
        }

        return $method;
    }
}
