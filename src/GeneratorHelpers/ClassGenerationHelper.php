<?php

declare(strict_types=1);

namespace quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers;

use InvalidArgumentException;
use PhpParser\Builder\Class_;
use PhpParser\Builder\Namespace_;
use PhpParser\Builder\Property;
use PhpParser\BuilderFactory;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\TraitUse;

class ClassGenerationHelper extends MethodGenerationHelper
{
    /**
     * @param BuilderFactory $factory
     */
    public function __construct(BuilderFactory $factory)
    {
        parent::__construct($factory);
    }

    /**
     * Creates a new class.
     *
     * @param string $name
     * @return Class_
     */
    public function createClass(string $name): Class_
    {
        return $this->factory->class($name);
    }

    /**
     * Sets the namespace for a class.
     *
     * @param string $namespace
     * @param Class_ $class
     * @param string[] $imports
     * @return Namespace_
     */
    public function setNamespace(string $namespace, Class_ $class, array $imports = []): Namespace_
    {
        $namespaceNode = $this->factory->namespace($namespace);

        foreach ($imports as $import) {
            $namespaceNode->addStmt($this->factory->use($import));
        }

        $namespaceNode->addStmt($class);

        return $namespaceNode;
    }

    /**
     * Creates a class property.
     *
     * @param string $name
     * @param string|null $type
     * @param string $visibility
     * @param mixed|null $default
     * @return Property
     * @throws InvalidArgumentException if the visibility, type, or default value is invalid.
     */
    public function createClassProperty(string $name, ?string $type = null, string $visibility = 'public', mixed $default = null): Property
    {
        if (!in_array($visibility, ['public', 'protected', 'private'])) {
            throw new InvalidArgumentException('Invalid visibility provided: ' . $visibility);
        }

        if ($type !== null && !in_array($type, ['string', 'int', 'bool', 'float', 'array', 'object'])) {
            throw new InvalidArgumentException('Invalid type provided: ' . $type);
        }

        if ($default !== null && !$this->isValidDefault($type, $default)) {
            throw new InvalidArgumentException('Invalid default value for type: ' . $type);
        }

        $property = $this->factory->property($name);

        if ($type !== null) {
            $property->setType($type);
        }

        if ($default !== null) {
            $property->setDefault($this->convertToAstNode($default));
        }

        $this->addVisibility($property, $visibility);

        return $property;
    }

    /**
     * Adds properties to a class.
     *
     * @param Class_ $class
     * @param string $name
     * @param string|null $type
     * @param string $visibility
     * @param mixed|null $default
     * @return Class_
     */
    public function addClassProperty(Class_ $class, string $name, ?string $type = null, string $visibility = 'public', mixed $default = null): Class_
    {
        $property = $this->createClassProperty($name, $type, $visibility, $default);
        $class->addStmt($property);

        return $class;
    }

    /**
     * Adds methods to a class.
     *
     * @param Class_ $class
     * @param string $name
     * @param string $visibility
     * @param string|null $returnType
     * @param array<int, array{name: string, type: string|null}> $params
     * @param array<int, Stmt> $body
     * @return Class_
     */
    public function addClassMethod(
        Class_ $class,
        string $name,
        string $visibility = 'public',
        ?string $returnType = null,
        array $params = [],
        array $body = []
    ): Class_ {
        $method = $this->createMethod($name, $visibility, $returnType);

        foreach ($params as $param) {
            $methodParam = $this->factory->param($param['name']);
            if ($param['type'] !== null) {
                $methodParam->setType($param['type']);
            }
            $method->addParam($methodParam);
        }

        foreach ($body as $stmt) {
            $method->addStmt($stmt);
        }

        $class->addStmt($method);

        return $class;
    }

    /**
     * Sets the parent class for a class.
     *
     * @param Class_ $class
     * @param string $parentClassName
     * @return Class_
     */
    public function extendClass(Class_ $class, string $parentClassName): Class_
    {
        $class->extend($parentClassName);
        return $class;
    }

    /**
     * Adds interfaces to a class.
     *
     * @param Class_ $class
     * @param string[] $interfaces
     * @return Class_
     */
    public function implementInterfaces(Class_ $class, array $interfaces): Class_
    {
        foreach ($interfaces as $interface) {
            $class->implement($interface);
        }

        return $class;
    }

    /**
     * Adds traits to a class.
     *
     * @param Class_ $class
     * @param string[] $traits
     * @return Class_
     */
    public function addTraitsToClass(Class_ $class, array $traits): Class_
    {
        foreach ($traits as $trait) {
            $class->addStmt(new TraitUse([new Name($trait)]));
        }

        return $class;
    }

    /**
     * Adds a constructor to a class.
     *
     * @param Class_ $class
     * @param array<int, array{name: string, type: string|null, default: mixed|null}> $params
     * @return Class_
     */
    public function addConstructorToClass(Class_ $class, array $params = []): Class_
    {
        $constructor = $this->factory->method('__construct')
            ->makePublic();

        foreach ($params as $param) {
            $methodParam = $this->factory->param($param['name']);
            if ($param['type'] !== null) {
                $methodParam->setType($param['type']);
            }
            if ($param['default'] !== null) { // No need for array_key_exists
                $methodParam->setDefault($this->convertToAstNode($param['default']));
            }
            $constructor->addParam($methodParam);

            $constructor->addStmt($this->assignThisVarToVar($param['name'], $param['name']));
        }

        $class->addStmt($constructor);

        return $class;
    }

    /**
     * Combines multiple class creation methods into one.
     *
     * @param string $className
     * @param string $namespace
     * @param array<string, array{type: string|null, visibility: string, default: mixed|null}> $properties
     * @param array<int, array{name: string, type: string|null, default: mixed|null}> $constructorParams
     * @param array<int, array{name: string, visibility: string, returnType: string|null, params: array<int, array{name: string, type: string|null}>, body: array<int, Stmt>}> $methods
     * @param string[] $traits
     * @param string[] $implements
     * @param string[] $imports
     * @return Namespace_
     */
    public function createFullClass(
        string $className,
        string $namespace,
        array $properties = [],
        array $constructorParams = [],
        array $methods = [],
        array $traits = [],
        array $implements = [],
        array $imports = []
    ): Namespace_ {
        $class = $this->createClass($className);

        foreach ($properties as $name => $details) {
            $this->addClassProperty($class, $name, $details['type'], $details['visibility'], $details['default']);
        }

        if (!empty($constructorParams)) {
            $this->addConstructorToClass($class, $constructorParams);
        }

        foreach ($methods as $method) {
            $this->addClassMethod(
                $class,
                $method['name'],
                $method['visibility'],
                $method['returnType'],
                $method['params'],
                $method['body']
            );
        }

        $this->addTraitsToClass($class, $traits);
        $this->implementInterfaces($class, $implements);

        return $this->setNamespace($namespace, $class, $imports);
    }
}
