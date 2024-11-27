<?php

declare(strict_types=1);

namespace TestGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Name;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\TestGenerationHelper;

class CreateAssertionTest extends TestCase
{
    private TestGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new TestGenerationHelper(new BuilderFactory());
    }

    public function test_creates_assertion_statement(): void
    {
        $assertion = $this->helper->createAssertion('assertTrue', [true]);

        $this->assertInstanceOf(Expression::class, $assertion);
        $this->assertInstanceOf(MethodCall::class, $assertion->expr);
        $this->assertSame('assertTrue', $assertion->expr->name->toString());
        $this->assertCount(1, $assertion->expr->args);

        // Validate the argument node
        $arg = $assertion->expr->args[0]->value;
        $this->assertInstanceOf(ConstFetch::class, $arg);
        $this->assertSame('true', $arg->name->toString());
    }

    /**
     * @dataProvider assertionDataProvider
     */
    public function test_creates_various_assertions(string $method, array $arguments, array $expectedArgs): void
    {
        $assertion = $this->helper->createAssertion($method, $arguments);

        $this->assertInstanceOf(Expression::class, $assertion);
        $this->assertInstanceOf(MethodCall::class, $assertion->expr);
        $this->assertSame($method, $assertion->expr->name->toString());
        $this->assertCount(count($expectedArgs), $assertion->expr->args);

        foreach ($assertion->expr->args as $index => $arg) {
            $value = $arg->value;

            if ($value instanceof ConstFetch) {
                $this->assertSame($expectedArgs[$index], $value->name->toString());
            } elseif ($value instanceof LNumber || $value instanceof String_) {
                $this->assertSame($expectedArgs[$index], $value->value);
            } elseif ($value instanceof Array_) {
                $this->assertInstanceOf(Array_::class, $value);
            } elseif ($value instanceof Variable) {
                $this->assertSame($expectedArgs[$index], $value->name);
            }
        }
    }

    public static function assertionDataProvider(): array
    {
        return [
            'assertTrue' => [
                'method' => 'assertTrue',
                'arguments' => [true],
                'expectedArgs' => ['true'],
            ],
            'assertFalse' => [
                'method' => 'assertFalse',
                'arguments' => [false],
                'expectedArgs' => ['false'],
            ],
            'assertEquals' => [
                'method' => 'assertEquals',
                'arguments' => [42, 42],
                'expectedArgs' => [42, 42],
            ],
            'assertSame' => [
                'method' => 'assertSame',
                'arguments' => ['string', 'string'],
                'expectedArgs' => ['string', 'string'],
            ],
            'assertNotEquals' => [
                'method' => 'assertNotEquals',
                'arguments' => [10, 20],
                'expectedArgs' => [10, 20],
            ],
            'assertNull' => [
                'method' => 'assertNull',
                'arguments' => [null],
                'expectedArgs' => ['null'], // ConstFetch for null
            ],
            'assertCount' => [
                'method' => 'assertCount',
                'arguments' => [2, [1, 2]],
                'expectedArgs' => [2, [1, 2]],
            ],
        ];
    }

    public function test_creates_assertion_with_variable_argument(): void
    {
        $variable = new Variable('result');
        $assertion = $this->helper->createAssertion('assertNotNull', [$variable]);

        $this->assertInstanceOf(Expression::class, $assertion);
        $this->assertInstanceOf(MethodCall::class, $assertion->expr);
        $this->assertSame('assertNotNull', $assertion->expr->name->toString());

        $arg = $assertion->expr->args[0]->value;
        $this->assertInstanceOf(Variable::class, $arg);
        $this->assertSame('result', $arg->name);
    }
}
