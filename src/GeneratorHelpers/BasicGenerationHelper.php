<?php

declare(strict_types=1);

namespace quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Builder\Method;
use PhpParser\BuilderFactory;
use PhpParser\Builder\Property;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Cast\Bool_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\Return_;

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
            $arguments[] = new Arg($this->convertToAstNode($value));
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
            $arrayItems[] = new ArrayItem($this->convertToAstNode($value));
        }

        return new Array_($arrayItems);
    }

    /**
     * Converts a PHP value to an AST node.
     *
     * @param mixed $value
     * @return Array_|LNumber|String_
     */
    public function convertToAstNode(mixed $value): Array_|LNumber|String_
    {
        return match (true) {
            is_int($value) => new LNumber($value),
            is_string($value) => new String_($value),
            is_array($value) => $this->createArray($value),
            default => throw new \InvalidArgumentException('Unsupported value type for AST conversion.'),
        };
    }
}
