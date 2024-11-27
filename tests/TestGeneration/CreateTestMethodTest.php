<?php

declare(strict_types=1);

namespace TestGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Return_;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\TestGenerationHelper;

class CreateTestMethodTest extends TestCase
{
    private TestGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new TestGenerationHelper(new BuilderFactory());
    }

    public function test_creates_empty_test_method(): void
    {
        $method = $this->helper->createTestMethod('testExample');

        $this->assertSame('testExample', $method->getNode()->name->toString());
        $this->assertTrue($method->getNode()->isPublic());
        $this->assertEquals('void', $method->getNode()->getReturnType()->toString());
        $this->assertEmpty($method->getNode()->stmts);
    }

    public function test_creates_test_method_with_body(): void
    {
        $body = [new Return_()];
        $method = $this->helper->createTestMethod('testReturn', $body);

        $this->assertCount(1, $method->getNode()->stmts);
        $this->assertInstanceOf(Return_::class, $method->getNode()->stmts[0]);
    }
}
