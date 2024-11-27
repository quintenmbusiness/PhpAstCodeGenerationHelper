<?php

declare(strict_types=1);

namespace BasicGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\BasicGenerationHelper;

class ConvertToAstNodeTest extends TestCase
{
    private BasicGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new BasicGenerationHelper(new BuilderFactory());
    }

    /**
     * @dataProvider validAstNodeProvider
     */
    public function test_converts_valid_values_to_ast_nodes(mixed $value, string $expectedClass, mixed $expectedValue = null): void
    {
        $result = $this->helper->convertToAstNode($value);

        $this->assertInstanceOf($expectedClass, $result);

        if ($result instanceof LNumber || $result instanceof String_) {
            $this->assertSame($expectedValue, $result->value);
        } elseif ($result instanceof ConstFetch) {
            $this->assertSame($expectedValue, $result->name->toString());
        } elseif ($result instanceof Array_) {
            $this->assertInstanceOf(Array_::class, $result);
        } elseif ($result instanceof Variable) {
            $this->assertSame($expectedValue, $result->name);
        }
    }

    /**
     * Provides valid values and their expected AST node types and values.
     *
     * @return array<int, array{mixed, string, mixed|null}>
     */
    public static function validAstNodeProvider(): array
    {
        return [
            // Integers
            [42, LNumber::class, 42],
            [-7, LNumber::class, -7],

            // Strings
            ['test', String_::class, 'test'],
            ['hello world', String_::class, 'hello world'],

            // Arrays
            [[], Array_::class],
            [['key' => 'value', 42], Array_::class],

            // Booleans
            [true, ConstFetch::class, 'true'],
            [false, ConstFetch::class, 'false'],

            // Variables
            [new Variable('testVar'), Variable::class, 'testVar'],
        ];
    }

    /**
     * @dataProvider invalidAstNodeProvider
     */
    public function test_throws_exception_for_invalid_values(mixed $value, string $expectedMessage): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->helper->convertToAstNode($value);
    }

    /**
     * Provides invalid values and their expected exception messages.
     *
     * @return array<int, array{mixed, string}>
     */
    public static function invalidAstNodeProvider(): array
    {
        return [
            // Objects
            [new \stdClass(), 'Unsupported value type for AST conversion: object'],

            // Resources
            [fopen('php://memory', 'r'), 'Unsupported value type for AST conversion: resource'],

            // Floats
            [3.14, 'Unsupported value type for AST conversion: double'],
        ];
    }
}
