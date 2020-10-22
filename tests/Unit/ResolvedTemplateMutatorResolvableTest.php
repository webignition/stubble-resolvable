<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutationInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutatorResolvable;

class ResolvedTemplateMutatorResolvableTest extends TestCase
{
    public function testImplementsResolvableInterface()
    {
        $resolvable = new ResolvedTemplateMutatorResolvable(
            new Resolvable('', []),
            function () {
            }
        );

        self::assertInstanceOf(ResolvableInterface::class, $resolvable);
    }

    public function testImplementsResolvedTemplateMutationInterface()
    {
        $resolvable = new ResolvedTemplateMutatorResolvable(
            new Resolvable('', []),
            function () {
            }
        );

        self::assertInstanceOf(ResolvedTemplateMutationInterface::class, $resolvable);
    }

    public function testGetTemplate()
    {
        $template = 'template content';
        $resolvable = new ResolvedTemplateMutatorResolvable(
            new Resolvable($template, []),
            function () {
            }
        );

        self::assertSame($template, $resolvable->getTemplate());
    }

    public function testGetContext()
    {
        $context = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $resolvable = new ResolvedTemplateMutatorResolvable(
            new Resolvable('', $context),
            function () {
            }
        );

        self::assertSame($context, $resolvable->getContext());
    }

    public function testResolvedTemplateMutator()
    {
        $resolvable = new ResolvedTemplateMutatorResolvable(
            new Resolvable('template content', []),
            function (string $resolvedTemplate) {
                return $resolvedTemplate . '!';
            }
        );

        self::assertSame(
            'template content!',
            ($resolvable->getResolvedTemplateMutator())($resolvable->getTemplate())
        );
    }
}
