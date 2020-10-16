<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;

class ResolvableTest extends TestCase
{
    public function testImplementsResolvableInterface()
    {
        $resolvable = new Resolvable('', []);
        self::assertInstanceOf(ResolvableInterface::class, $resolvable);
    }

    public function testGetTemplate()
    {
        $template = 'template content';

        $resolvable = new Resolvable($template, []);
        self::assertSame($template, $resolvable->getTemplate());
    }

    /**
     * @dataProvider getContextDataProvider
     *
     * @param ResolvableInterface $resolvable
     * @param array<string, string> $expectedContext
     */
    public function testGetContext(ResolvableInterface $resolvable, array $expectedContext)
    {
        self::assertSame($expectedContext, $resolvable->getContext());
    }

    public function getContextDataProvider(): array
    {
        $context = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ];

        $resolvableWithoutMutator = new Resolvable('template', $context);

        return [
            'without mutator' => [
                'resolvable' => $resolvableWithoutMutator,
                'expectedContext' => $context,
            ],
            'non-mutating mutator' => [
                'resolvable' => $resolvableWithoutMutator->withContextMutator(function (string $item) {
                    return $item;
                }),
                'expectedContext' => $context,
            ],
            'non-selective mutator' => [
                'resolvable' => $resolvableWithoutMutator->withContextMutator(function (string $item) {
                    return $item . '!';
                }),
                'expectedContext' => [
                    'key1' => 'value1!',
                    'key2' => 'value2!',
                    'key3' => 'value3!',
                ],
            ],
            'selective mutator' => [
                'resolvable' => $resolvableWithoutMutator->withContextMutator(function (string $item) {
                    return $item === 'value2'
                        ? $item . '!'
                        : $item;
                }),
                'expectedContext' => [
                    'key1' => 'value1',
                    'key2' => 'value2!',
                    'key3' => 'value3',
                ],
            ],
        ];
    }

    public function testContextValuesCanBeResolvable()
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
}
