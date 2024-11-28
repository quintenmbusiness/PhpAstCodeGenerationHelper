<?php

declare(strict_types=1);

namespace Basics\BasicGeneration;

use PhpParser\BuilderFactory;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\BasicGenerationHelper;

class IsValidDefaultTest extends TestCase
{
    private BasicGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new BasicGenerationHelper(new BuilderFactory());
    }

    /**
     * @dataProvider valid_default_provider
     */
    public function test_valid_defaults(?string $type, mixed $default, bool $expected): void
    {
        $result = $this->helper->isValidDefault($type, $default);

        $this->assertSame($expected, $result);
    }

    /**
     * Provides test cases for valid defaults.
     *
     * @return array<int, array{string|null, mixed, bool}>
     */
    public static function valid_default_provider(): array
    {
        return [
            // Integers
            ['int', 42, true],
            ['int', -1, true],
            ['int', 'not an int', false],

            // Strings
            ['string', 'hello', true],
            ['string', '', true],
            ['string', 123, false],

            // Booleans
            ['bool', true, true],
            ['bool', false, true],
            ['bool', 'not a bool', false],

            // Floats
            ['float', 3.14, true],
            ['float', -0.01, true],
            ['float', 'not a float', false],

            // Arrays
            ['array', [], true],
            ['array', ['key' => 'value'], true],
            ['array', 'not an array', false],

            // Null type
            [null, 42, false],
            [null, 'hello', false],
        ];
    }

    /**
     * @dataProvider invalid_type_provider
     */
    public function test_invalid_type_returns_false(?string $type, mixed $default): void
    {
        $result = $this->helper->isValidDefault($type, $default);

        $this->assertFalse($result);
    }

    /**
     * Provides test cases for invalid or unsupported types.
     *
     * @return array<int, array{string|null, mixed}>
     */
    public static function invalid_type_provider(): array
    {
        return [
            ['unsupported_type', 'value'],
            ['another_invalid_type', 123],
            ['unknown', true],
            [null, 3.14],
        ];
    }
}
