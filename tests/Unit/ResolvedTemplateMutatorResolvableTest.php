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
            new Resolvable('', [])
        );

        self::assertInstanceOf(ResolvableInterface::class, $resolvable);
    }

    public function testImplementsResolvedTemplateMutationInterface()
    {
        $resolvable = new ResolvedTemplateMutatorResolvable(
            new Resolvable('', [])
        );

        self::assertInstanceOf(ResolvedTemplateMutationInterface::class, $resolvable);
    }

    public function testGetTemplate()
    {
        $template = 'template content';
        $resolvable = new ResolvedTemplateMutatorResolvable(
            new Resolvable($template, [])
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
            new Resolvable('', $context)
        );

        self::assertSame($context, $resolvable->getContext());
    }

    public function testResolvedTemplateMutator()
    {
        $mutator = function (string $resolvedTemplate) {
            return $resolvedTemplate . '!';
        };

        $resolvable = new ResolvedTemplateMutatorResolvable(
            new Resolvable('template content', [])
        );

        self::assertSame(
            'template content',
            ($resolvable->getResolvedTemplateMutator())($resolvable->getTemplate())
        );

        $resolvableWithMutator = $resolvable->withResolvedTemplateMutator($mutator);

        self::assertInstanceOf(ResolvableInterface::class, $resolvableWithMutator);
        self::assertInstanceOf(ResolvedTemplateMutationInterface::class, $resolvableWithMutator);
        self::assertNotSame($resolvable, $resolvableWithMutator);

        if ($resolvableWithMutator instanceof ResolvedTemplateMutatorResolvable) {
            self::assertSame(
                'template content!',
                ($resolvableWithMutator->getResolvedTemplateMutator())($resolvableWithMutator->getTemplate())
            );
        }
    }
}
