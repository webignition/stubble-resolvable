<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\Tests\Model\Stringable;

class ResolvableTest extends TestCase
{
    public function testImplementsResolvableInterface(): void
    {
        $resolvable = new Resolvable('', []);
        self::assertInstanceOf(ResolvableInterface::class, $resolvable);
    }

    public function testGetTemplate(): void
    {
        $template = 'template content';

        $resolvable = new Resolvable($template, []);
        self::assertSame($template, $resolvable->getTemplate());
    }

    public function testGetContext(): void
    {
        $context = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $resolvable = new Resolvable('', $context);
        self::assertSame($context, $resolvable->getContext());
    }

    public function testContextValuesCanBeResolvable(): void
    {
        $context = [
            'key1' => 'value1',
            'key2' => new Resolvable('key2 template', [
                'key2key1' => 'key2value2'
            ]),
        ];

        $resolvable = new Resolvable('', $context);
        self::assertSame($context, $resolvable->getContext());
    }

    public function testContextValuesCanBeStringable(): void
    {
        $context = [
            'key1' => 'value1',
            'key2' => new Resolvable('key2 template', [
                'key2key1' => new Stringable('key2value2')
            ]),
        ];

        $resolvable = new Resolvable('', $context);
        self::assertSame($context, $resolvable->getContext());
    }
}
