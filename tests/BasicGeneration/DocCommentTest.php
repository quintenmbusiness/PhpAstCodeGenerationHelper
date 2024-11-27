<?php

declare(strict_types=1);

namespace BasicGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Builder\Method;
use PhpParser\Builder\Property;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use quintenmbusiness\PhpAstCodeGenerationHelper\GeneratorHelpers\BasicGenerationHelper;

class DocCommentTest extends TestCase
{
    private BasicGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new BasicGenerationHelper(new BuilderFactory());
    }

    #[Test]
    public function it_creates_doc_comment_with_params_and_return(): void
    {
        $method = new Method('exampleMethod');
        $params = [
            ['type' => 'int', 'name' => 'id'],
            ['type' => 'string', 'name' => 'name']
        ];
        $returnType = 'bool';

        $result = $this->helper->createDocComment($method, $params, $returnType);

        $this->assertInstanceOf(Method::class, $result);

        $expected = <<<DOC
/**
 * @param int \$id
 * @param string \$name
 * 
 * @return bool
 */
DOC;

        $this->assertSame(
            $this->normalizeDocComment($expected),
            $this->normalizeDocComment($result->getNode()->getDocComment()->getText())
        );
    }

    #[Test]
    public function it_creates_doc_comment_with_throws_and_deprecated(): void
    {
        $method = new Method('exampleMethod');
        $throws = 'InvalidArgumentException';
        $deprecated = 'Use the newMethod() instead.';

        $result = $this->helper->createDocComment($method, [], null, $throws, $deprecated);

        $this->assertInstanceOf(Method::class, $result);

        $expected = <<<DOC
/**
 * @throws InvalidArgumentException
 * @deprecated Use the newMethod() instead.
 */
DOC;

        $this->assertSame(
            $this->normalizeDocComment($expected),
            $this->normalizeDocComment($result->getNode()->getDocComment()->getText())
        );
    }

    #[Test]
    public function it_creates_doc_comment_with_additional_tags(): void
    {
        $property = new Property('exampleProperty');
        $additional = [
            'author' => 'Quinten Muijser',
            'since' => '1.0.0'
        ];

        $result = $this->helper->createDocComment($property, [], null, null, null, $additional);

        $this->assertInstanceOf(Property::class, $result);

        $expected = <<<DOC
/**
 * @author Quinten Muijser
 * @since 1.0.0
 */
DOC;

        $this->assertSame(
            $this->normalizeDocComment($expected),
            $this->normalizeDocComment($result->getNode()->getDocComment()->getText())
        );
    }

    #[Test]
    public function it_creates_empty_doc_comment(): void
    {
        $method = new Method('exampleMethod');

        $result = $this->helper->createDocComment($method);

        $this->assertInstanceOf(Method::class, $result);

        $expected = <<<DOC
/**
 */
DOC;

        $this->assertSame(
            $this->normalizeDocComment($expected),
            $this->normalizeDocComment($result->getNode()->getDocComment()->getText())
        );
    }

    private function normalizeDocComment(string $docComment): string
    {
        // Normalize line endings and trim extra whitespace
        return str_replace(["\r\n", "\r"], "\n", trim($docComment));
    }
}