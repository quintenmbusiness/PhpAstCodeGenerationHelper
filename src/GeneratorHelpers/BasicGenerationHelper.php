<?php

declare(strict_types=1);

namespace quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers;

use PhpParser\Builder\Namespace_;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Builder\Method;
use PhpParser\BuilderFactory;
use PhpParser\Builder\Property;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Cast\Bool_;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Builder\Class_;
use PhpParser\Builder\Interface_;
use PhpParser\PrettyPrinter\Standard;

class BasicGenerationHelper
{
    /**
     * @var BuilderFactory
     */
    public BuilderFactory $factory;

    /**
     * @param BuilderFactory $factory
     */
    public function __construct(BuilderFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param  Expr    $expr
     * @return Return_
     */
    public function return(Expr $expr): Return_
    {
        return new Return_($expr);
    }

    /**
     * @param  Method|Property $stmt
     * @param  string          $visibility
     * @return Method|Property
     */
    public function addVisibility(Method|Property $stmt, string $visibility = 'public'): Method|Property
    {
        match ($visibility) {
            'public' => $stmt->makePublic(),
            'protected' => $stmt->makeProtected(),
            'private' => $stmt->makePrivate(),
            'static' => $stmt->makeStatic(),
            'abstract' => $stmt->makeAbstract(),
            'final' => $stmt->makeFinal(),
            default => $stmt,
        };

        return $stmt;
    }

    /**
     * Adds a PHPDoc comment to the given Method or Property.
     *
     * @param  Method|Property                   $method
     * @param  array<int, array<string, string>> $params       Array of parameters, where each element has 'type' and 'name'.
     * @param  null|string                       $returnType   The return type to document.
     * @param  null|string                       $throws       The exception type to document in @throws.
     * @param  null|string                       $deprecated   Marks the method/property as deprecated with an optional message.
     * @param  array<string, string>             $additional   Additional tags, where the key is the tag (e.g., 'author') and the value is the tag's content.
     * @return Method|Property
     */
    public function createDocComment(
        Method|Property $method,
        array $params = [],
        ?string $returnType = null,
        ?string $throws = null,
        ?string $deprecated = null,
        array $additional = []
    ): Method|Property {
        $comment = "\n" . '/**';

        // Add @param tags
        foreach ($params as $param) {
            $comment .= "\n" . ' * @param ' . $param['type'] . ' $' . $param['name'];
        }

        // Add @return tag
        if (!empty($returnType)) {
            $comment .= "\n * \n" . ' * @return ' . $returnType;
        }

        // Add @throws tag
        if (!empty($throws)) {
            $comment .= "\n" . ' * @throws ' . $throws;
        }

        // Add @deprecated tag
        if (!empty($deprecated)) {
            $comment .= "\n" . ' * @deprecated ' . $deprecated;
        }

        // Add any additional tags
        foreach ($additional as $tag => $content) {
            $comment .= "\n" . ' * @' . $tag . ' ' . $content;
        }

        $comment .= "\n" . ' */';

        $method->setDocComment($comment);

        return $method;
    }

    /**
     * Creates an array of arguments for a method call.
     *
     * @param array<int|string, mixed> $args
     * @return array<int, Arg>
     */
    public function createArguments(array $args): array
    {
        $arguments = [];
        foreach ($args as $value) {
            $arguments[] = new Arg($value instanceof Expr ? $value : $this->convertToAstNode($value));
        }
        return $arguments;
    }

    /**
     * Creates an array node from a PHP array.
     *
     * @param array<int|string, mixed> $items
     * @return Expr\Array_
     */
    public function createArray(array $items): Array_
    {
        $arrayItems = [];
        foreach ($items as $key => $value) {
            $keyNode = is_int($key) ? null : $this->convertToAstNode($key);
            $arrayItems[] = new ArrayItem($this->convertToAstNode($value), $keyNode);
        }

        return new Array_($arrayItems);
    }

    /**
     * Converts a PHP value to an AST node.
     *
     * @param mixed $value
     * @return Array_|LNumber|String_|ConstFetch|Variable
     * @throws \InvalidArgumentException
     */
    public function convertToAstNode(mixed $value): Array_|LNumber|String_|ConstFetch|Variable
    {
        return match (true) {
            is_int($value) => new LNumber($value),
            is_string($value) => new String_($value),
            is_array($value) => $this->createArray($value),
            is_bool($value) => new ConstFetch(new Name($value ? 'true' : 'false')),
            $value instanceof Variable => $value,
            $value === null => new ConstFetch(new Name('null')), // Handle null as ConstFetch
            default => throw new \InvalidArgumentException('Unsupported value type for AST conversion: ' . gettype($value)),
        };
    }

    /**
     * @param string|null $type
     * @param mixed $default
     * @return bool
     */
    public function isValidDefault(?string $type, mixed $default): bool
    {
        return match ($type) {
            'int' => is_int($default),
            'string' => is_string($default),
            'bool' => is_bool($default),
            'float' => is_float($default),
            'array' => is_array($default),
            default => false,
        };
    }

    /**
     * Generates a PHP file for a given class, interface, or namespace.
     *
     * @param Class_|Interface_|Namespace_ $definition The class, interface, or namespace to generate.
     * @param string|null $namespace The namespace of the class/interface (if $definition is not already a Namespace_).
     * @param string $outputPath The file path where the generated file should be saved.
     * @return void
     */
    public function generateFile(Class_|Interface_|Namespace_ $definition, ?string $namespace, string $outputPath): void
    {
        $prettyPrinter = new Standard();

        // Convert builder to node if necessary
        $node = $definition instanceof Namespace_
            ? $definition->getNode()
            : $this->factory->namespace($namespace)->addStmt($definition)->getNode();

        // Generate the PHP code
        $code = $prettyPrinter->prettyPrintFile([$node]);

        // Ensure the directory exists
        $directory = dirname($outputPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Save the generated code to the file
        file_put_contents($outputPath, $code);
    }
}
