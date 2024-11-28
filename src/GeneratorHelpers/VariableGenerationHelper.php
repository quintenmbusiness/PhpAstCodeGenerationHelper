<?php

declare(strict_types=1);

namespace quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\PropertyFetch;

class VariableGenerationHelper extends ConditionalGenerationHelper
{
    /**
     * @param BuilderFactory $factory
     */
    public function __construct(BuilderFactory $factory)
    {
        parent::__construct($factory);
    }

    /**
     * @param  string $thisVar
     * @param  string $var
     * @return Assign
     */
    public function assignThisVarToVar(string $thisVar, string $var): Assign
    {
        return $this->assign($this->thisVar($thisVar), $this->var($var));
    }

    /**
     * @param string $thisVarToBeAssigned
     * @param string $thisVarToBeAssignedTo
     * @return Assign
     */
    public function assignThisVarToThisVar(string $thisVarToBeAssigned, string $thisVarToBeAssignedTo): Assign
    {
        return $this->assign($this->thisVar($thisVarToBeAssigned), $this->thisVar($thisVarToBeAssignedTo));
    }

    /**
     * @param  string $name
     * @return New_
     */
    public function new(string $name): New_
    {
        return new New_(new Name($name));
    }

    /**
     * @param  string $thisVar
     * @param  string $var
     * @return Assign
     */
    public function assignVarToVar(string $thisVar, string $var): Assign
    {
        return $this->assign($this->var($thisVar), $this->var($var));
    }

    /**
     * @param  Expr   $target
     * @param  Expr   $newValue
     * @return Assign
     */
    public function assign(Expr $target, Expr $newValue): Assign
    {
        return new Assign($target, $newValue);
    }

    /**
     * @param  string   $name
     * @return Variable
     */
    public function var(string $name): Variable
    {
        return new Variable($name);
    }

    /**
     * @param  string        $property
     * @return PropertyFetch
     */
    public function thisVar(string $property): PropertyFetch
    {
        return new PropertyFetch(new Variable('this'), $property);
    }
}
