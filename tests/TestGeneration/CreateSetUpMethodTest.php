<?php

declare(strict_types=1);

namespace TestGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\TestGenerationHelper;

class CreateSetUpMethodTest extends TestCase
{
    private TestGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new TestGenerationHelper(new BuilderFactory());
    }

    public function test_creates_setup_method_with_parent_call(): void
    {
        $method = $this->helper->createSetUpMethod();

        $this->assertSame('setUp', $method->getNode()->name->toString());
        $this->assertTrue($method->getNode()->isProtected());
        $this->assertEquals('void', $method->getNode()->getReturnType()->toString());

        $firstStatement = $method->getNode()->stmts[0];
        $this->assertInstanceOf(Expression::class, $firstStatement);
        $this->assertInstanceOf(MethodCall::class, $firstStatement->expr);
        $this->assertSame('parent::setUp', $firstStatement->expr->name->toString());
    }

    public function test_creates_setup_method_with_body(): void
    {
        $body = [new Expression(new MethodCall(new Variable('this'), 'initializeSomething'))];
        $method = $this->helper->createSetUpMethod($body);

        $this->assertCount(2, $method->getNode()->stmts); // parent::setUp + initializeSomething
        $this->assertSame('initializeSomething', $method->getNode()->stmts[1]->expr->name->toString());
    }
}
