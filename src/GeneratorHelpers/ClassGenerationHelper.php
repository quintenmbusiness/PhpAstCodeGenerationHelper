<?php

declare(strict_types=1);

namespace quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers;

use PhpParser\BuilderFactory;
use PhpParser\Builder\Property;

class ClassGenerationHelper extends MethodGenerationHelper
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
     * @param null|string $type
     * @param string $visibility
     * @return Property
     */
    public function createClassProperty(string $name, ?string $type = null, string $visibility = 'public'): Property
    {
        $property = $this->factory->property($name);

        if ($type != null) {
            $property->setType($type);
        }

        $this->addVisibility($property, $visibility);

        return $property;
    }
}
