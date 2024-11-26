<?php

declare(strict_types=1);

namespace BasicGeneration;

use PhpParser\BuilderFactory;
use PhpParser\Builder\Method;
use PhpParser\Builder\Property;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Quintenm\PhpAstCodeGenerationHelper\GeneratorHelpers\BasicGenerationHelper;

class VisibilityTest extends TestCase
{
    /**
     * @var BasicGenerationHelper
     */
    private BasicGenerationHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new BasicGenerationHelper(new BuilderFactory());
    }

    /**
     * @var string[]
     */
    private $visibilities = [
        'public',
        'protected',
        'private',
        'static',
        'abstract',
        'final',
    ];

    #[Test]
    public function can_add_visibility_to_method(): void
    {
        foreach ($this->visibilities as $visibility) {
            $method = (new BuilderFactory())->method('testMethod');
            $result = $this->helper->addVisibility($method, $visibility);

            $this->assertInstanceOf(Method::class, $result);

            $checkMethod = 'is' . ucfirst($visibility);

            $this->assertTrue(method_exists($result->getNode(), $checkMethod));
            $this->assertTrue($result->getNode()->{$checkMethod}());
        }
    }

    #[Test]
    public function can_add_visibility_to_property(): void
    {
        $property = (new BuilderFactory())->property('testProperty');

        $result = $this->helper->addVisibility($property, 'protected');

        $this->assertInstanceOf(Property::class, $result);
        $this->assertTrue($result->getNode()->isProtected());
    }

    #[Test]
    public function invalid_visiblity_is_ignored(): void
    {
        $method = (new BuilderFactory())->method('testMethod');
        $result = $this->helper->addVisibility($method, 'private');

        $this->assertInstanceOf(Method::class, $result);
        $this->assertTrue($result->getNode()->isPrivate());

        $result = $this->helper->addVisibility($method, 'invalidVisibility');

        $this->assertInstanceOf(Method::class, $result);
        $this->assertTrue($result->getNode()->isPrivate());
    }
}
