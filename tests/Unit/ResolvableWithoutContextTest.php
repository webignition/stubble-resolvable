<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvableWithoutContext;

class ResolvableWithoutContextTest extends TestCase
{
    public function testImplementsResolvableInterface()
    {
        $resolvable = new ResolvableWithoutContext('');
        self::assertInstanceOf(ResolvableInterface::class, $resolvable);
    }

    public function testGetTemplate()
    {
        $content = 'pre-resolved content';

        $resolvable = new ResolvableWithoutContext($content);
        self::assertSame($content, $resolvable->getTemplate());
    }

    public function testGetContext()
    {
        $resolvable = new ResolvableWithoutContext('');
        self::assertSame([], $resolvable->getContext());
    }
}
