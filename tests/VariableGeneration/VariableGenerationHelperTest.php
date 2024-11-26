<?php

declare(strict_types=1);

namespace VariableGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Quintenm\PhpAstCodeGenerationHelper\GeneratorHelpers\VariableGenerationHelper;

class VariableGenerationHelperTest extends TestCase
{
    private VariableGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new VariableGenerationHelper(new BuilderFactory());
    }

    #[Test]
    public function assign_this_var_to_var(): void
    {
        $result = $this->helper->assignThisVarToVar('property', 'varName');

        $this->assertInstanceOf(Assign::class, $result);
        $this->assertInstanceOf(PropertyFetch::class, $result->var);
        $this->assertInstanceOf(Variable::class, $result->expr);
        $this->assertSame('property', $result->var->name->toString());
        $this->assertSame('varName', $result->expr->name);
    }

    #[Test]
    public function assign_this_var_to_this_var(): void
    {
        $result = $this->helper->assignThisVarToThisVar('propertyOne', 'propertyTwo');

        $this->assertInstanceOf(Assign::class, $result);
        $this->assertInstanceOf(PropertyFetch::class, $result->var);
        $this->assertInstanceOf(PropertyFetch::class, $result->expr);
        $this->assertSame('propertyOne', $result->var->name->toString());
        $this->assertSame('propertyTwo', $result->expr->name->toString());
    }

    #[Test]
    public function new(): void
    {
        $result = $this->helper->new('SomeClass');

        $this->assertInstanceOf(New_::class, $result);
        $this->assertSame('SomeClass', $result->class->toString());
    }

    #[Test]
    public function assign_var_to_var(): void
    {
        $result = $this->helper->assignVarToVar('varOne', 'varTwo');

        $this->assertInstanceOf(Assign::class, $result);
        $this->assertInstanceOf(Variable::class, $result->var);
        $this->assertInstanceOf(Variable::class, $result->expr);
        $this->assertSame('varOne', $result->var->name);
        $this->assertSame('varTwo', $result->expr->name);
    }

    #[Test]
    public function assign(): void
    {
        $target = new Variable('target');
        $newValue = new Variable('newValue');

        $result = $this->helper->assign($target, $newValue);

        $this->assertInstanceOf(Assign::class, $result);
        $this->assertSame($target, $result->var);
        $this->assertSame($newValue, $result->expr);
    }

    #[Test]
    public function var_creation(): void
    {
        $result = $this->helper->var('testVar');

        $this->assertInstanceOf(Variable::class, $result);
        $this->assertSame('testVar', $result->name);
    }

    #[Test]
    public function this_var(): void
    {
        $result = $this->helper->thisVar('testProperty');

        $this->assertInstanceOf(PropertyFetch::class, $result);
        $this->assertInstanceOf(Variable::class, $result->var);
        $this->assertSame('this', $result->var->name);
        $this->assertSame('testProperty', $result->name->toString());
    }
}
