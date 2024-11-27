<?php

declare(strict_types=1);

namespace quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers;

use PhpParser\Builder\Class_;
use PhpParser\Builder\Method;
use PhpParser\Builder\Namespace_;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;

class TestGenerationHelper extends ClassGenerationHelper
{
    /**
     * @param BuilderFactory $factory
     */
    public function __construct(BuilderFactory $factory)
    {
        parent::__construct($factory);
    }

    /**
     * Creates a `setUp` method for initializing test dependencies.
     *
     * @param array<int, Expression> $body Statements to include in the `setUp` method body.
     * @return Method
     */
    public function createSetUpMethod(array $body = []): Method
    {
        $method = $this->factory->method('setUp')
            ->makeProtected()
            ->setReturnType('void');

        $method->addStmt(new Expression(new MethodCall(new Variable('this'), 'parent::setUp')));

        foreach ($body as $stmt) {
            $method->addStmt($stmt);
        }

        $this->createDocComment($method, returnType: 'void');

        return $method;
    }

    /**
     * Creates a generic test method with optional body statements.
     *
     * @param string $methodName The name of the test method.
     * @param array<int, Stmt> $body Statements to include in the method body.
     * @return Method
     */
    public function createTestMethod(string $methodName, array $body = []): Method
    {
        $method = $this->factory->method($methodName)
            ->makePublic()
            ->setReturnType('void');

        foreach ($body as $stmt) {
            $method->addStmt($stmt);
        }

        $this->createDocComment($method, returnType: 'void');

        return $method;
    }

    /**
     * Creates an assertion statement.
     *
     * @param string $assertMethod The PHPUnit assertion method (e.g., assertEquals, assertTrue).
     * @param array<int, mixed> $arguments Arguments for the assertion.
     * @return Expression
     */
    public function createAssertion(string $assertMethod, array $arguments = []): Expression
    {
        return new Expression(
            new MethodCall(new Variable('this'), $assertMethod, $this->createArguments($arguments))
        );
    }

    /**
     * Adds common PHPUnit imports to a test class.
     *
     * @param Class_ $class
     * @param array<string> $imports List of classes to import.
     * @return Namespace_
     */
    public function addTestImports(Class_ $class, array $imports = []): Namespace_
    {
        // Create a namespace and add imports
        return $this->setNamespace('Tests\\Generated', $class, array_merge([
            'PHPUnit\\Framework\\TestCase',
            'PhpParser\\BuilderFactory',
        ], $imports));
    }

    /**
     * Creates a fully functional test class.
     *
     * @param string $className The name of the test class.
     * @param array<string, Method> $methods An array of test methods.
     * @param array<string> $imports List of additional classes to import.
     * @param array<int, Expression> $setupBody Optional body for the `setUp` method.
     * @return Class_
     */
    public function createTestClass(
        string $className,
        array $methods,
        array $imports = [],
        array $setupBody = []
    ): Class_ {
        $class = $this->createClass($className);

        // Extend TestCase
        $this->extendClass($class, 'TestCase');

        // Add the setUp method
        if (!empty($setupBody)) {
            $class->addStmt($this->createSetUpMethod($setupBody));
        }

        // Add all test methods
        foreach ($methods as $methodName => $method) {
            $class->addStmt($method);
        }

        // Add imports
        $this->addTestImports($class, $imports);

        return $class;
    }
}