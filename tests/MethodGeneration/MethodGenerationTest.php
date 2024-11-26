<?php

declare(strict_types=1);

namespace MethodGeneration;

use PhpParser\Builder\Method;
use PhpParser\BuilderFactory;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Quintenm\PhpAstCodeGenerationHelper\GeneratorHelpers\MethodGenerationHelper;

class MethodGenerationTest extends TestCase
{
    private MethodGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new MethodGenerationHelper(new BuilderFactory());
    }

    #[Test]
    public function create_method_with_default_visibility(): void
    {
        $method = $this->helper->createMethod('testMethod');

        $this->assertInstanceOf(Method::class, $method);
        $this->assertSame('testMethod', $method->getNode()->name->toString());
        $this->assertTrue($method->getNode()->isPublic());
    }

    #[Test]
    public function create_method_with_protected_visibility(): void
    {
        $method = $this->helper->createMethod('testMethod', 'protected');

        $this->assertInstanceOf(Method::class, $method);
        $this->assertTrue($method->getNode()->isProtected());
    }

    #[Test]
    public function create_method_with_private_visibility(): void
    {
        $method = $this->helper->createMethod('testMethod', 'private');

        $this->assertInstanceOf(Method::class, $method);
        $this->assertTrue($method->getNode()->isPrivate());
    }

    #[Test]
    public function create_method_with_return_type_identifier(): void
    {
        $returnType = new Identifier('string');
        $method = $this->helper->createMethod('testMethod', 'public', $returnType);

        $this->assertInstanceOf(Method::class, $method);
        $this->assertSame('string', $method->getNode()->returnType->toString());
    }

    #[Test]
    public function create_method_with_return_type_name(): void
    {
        $returnType = new Name('SomeClass');
        $method = $this->helper->createMethod('testMethod', 'public', $returnType);

        $this->assertInstanceOf(Method::class, $method);
        $this->assertSame('SomeClass', $method->getNode()->returnType->toString());
    }

    #[Test]
    public function create_method_with_return_type_string(): void
    {
        $method = $this->helper->createMethod('testMethod', 'public', 'int');

        $this->assertInstanceOf(Method::class, $method);
        $this->assertSame('int', $method->getNode()->returnType->toString());
    }

    #[Test]
    public function create_method_without_return_type(): void
    {
        $method = $this->helper->createMethod('testMethod');

        $this->assertInstanceOf(Method::class, $method);
        $this->assertNull($method->getNode()->returnType);
    }
}
