<?php

declare(strict_types=1);

namespace ClassGeneration;

use PhpParser\Builder\Class_;
use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Scalar\String_;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\ClassGenerationHelper;

class AddClassMethodTest extends TestCase
{
    private ClassGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new ClassGenerationHelper(new BuilderFactory());
    }

    public function test_add_public_method_with_no_params_or_body(): void
    {
        $factory = new BuilderFactory();
        $class = $factory->class('TestClass');

        $this->helper->addClassMethod($class, 'testMethod');

        $classNode = $class->getNode();
        $methodNode = $classNode->getMethods()[0];

        $this->assertSame('testMethod', $methodNode->name->toString());
        $this->assertTrue($methodNode->isPublic());
        $this->assertNull($methodNode->returnType);
        $this->assertCount(0, $methodNode->params);
        $this->assertCount(0, $methodNode->stmts);
    }

    public function test_add_protected_method_with_return_type(): void
    {
        $factory = new BuilderFactory();
        $class = $factory->class('TestClass');

        $this->helper->addClassMethod($class, 'getString', 'protected', 'string');

        $classNode = $class->getNode();
        $methodNode = $classNode->getMethods()[0];

        $this->assertSame('getString', $methodNode->name->toString());
        $this->assertTrue($methodNode->isProtected());
        $this->assertSame('string', $methodNode->returnType->toString());
    }

    public function test_add_method_with_parameters(): void
    {
        $factory = new BuilderFactory();
        $class = $factory->class('TestClass');

        $params = [
            ['name' => 'param1', 'type' => 'string'],
            ['name' => 'param2', 'type' => 'int'],
        ];

        $this->helper->addClassMethod($class, 'setValues', 'public', null, $params);

        $classNode = $class->getNode();
        $methodNode = $classNode->getMethods()[0];

        $this->assertCount(2, $methodNode->params);
        $this->assertSame('param1', $methodNode->params[0]->var->name);
        $this->assertSame('string', $methodNode->params[0]->type->toString());
        $this->assertSame('param2', $methodNode->params[1]->var->name);
        $this->assertSame('int', $methodNode->params[1]->type->toString());
    }

    public function test_add_method_with_body(): void
    {
        $factory = new BuilderFactory();
        $class = $factory->class('TestClass');

        $body = [
            new Return_(new String_('Hello, World!')),
        ];

        $this->helper->addClassMethod($class, 'sayHello', 'public', 'string', [], $body);

        $classNode = $class->getNode();
        $methodNode = $classNode->getMethods()[0];

        $this->assertCount(1, $methodNode->stmts);
        $this->assertInstanceOf(Return_::class, $methodNode->stmts[0]);
        $this->assertInstanceOf(String_::class, $methodNode->stmts[0]->expr);
        $this->assertSame('Hello, World!', $methodNode->stmts[0]->expr->value);
    }

    public function test_add_private_method_with_all_features(): void
    {
        $factory = new BuilderFactory();
        $class = $factory->class('TestClass');

        $params = [
            ['name' => 'name', 'type' => 'string'],
        ];

        $body = [
            new Return_(new String_('Hello, ')),
        ];

        $this->helper->addClassMethod($class, 'greet', 'private', 'string', $params, $body);

        $classNode = $class->getNode();
        $methodNode = $classNode->getMethods()[0];

        $this->assertSame('greet', $methodNode->name->toString());
        $this->assertTrue($methodNode->isPrivate());
        $this->assertSame('string', $methodNode->returnType->toString());
        $this->assertCount(1, $methodNode->params);
        $this->assertSame('name', $methodNode->params[0]->var->name);
        $this->assertSame('string', $methodNode->params[0]->type->toString());
        $this->assertCount(1, $methodNode->stmts);
        $this->assertInstanceOf(Return_::class, $methodNode->stmts[0]);
    }
}
