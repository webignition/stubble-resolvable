<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableCollectionInterface;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvableProviderInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutationInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutatorResolvable;

class ResolvedTemplateMutatorResolvableTest extends TestCase
{
    public function testImplementsInterfaces(): void
    {
        $resolvable = new ResolvedTemplateMutatorResolvable(
            new Resolvable('', []),
            function () {
            }
        );

        self::assertInstanceOf(ResolvableInterface::class, $resolvable);
        self::assertInstanceOf(ResolvedTemplateMutationInterface::class, $resolvable);
        self::assertInstanceOf(ResolvableCollectionInterface::class, $resolvable);
    }

    public function testGetTemplate(): void
    {
        $template = 'template content';
        $resolvable = new ResolvedTemplateMutatorResolvable(
            new Resolvable($template, []),
            function () {
            }
        );

        self::assertSame($template, $resolvable->getTemplate());
    }

    public function testGetContext(): void
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

    /**
     * @dataProvider resolvedTemplateMutatorsDataProvider
     */
    public function testResolvedTemplateMutators(
        string $resolvedTemplate,
        ResolvedTemplateMutatorResolvable $resolvable,
        string $expectedMutatedResolvedTemplate
    ): void {
        $mutators = $resolvable->getResolvedTemplateMutators();
        foreach ($mutators as $mutator) {
            if (is_callable($mutator)) {
                $resolvedTemplate = $mutator($resolvedTemplate);
            }
        }

        self::assertSame($expectedMutatedResolvedTemplate, $resolvedTemplate);
    }

    /**
     * @return array<mixed>
     */
    public function resolvedTemplateMutatorsDataProvider(): array
    {
        return [
            'non-mutating inner resolvable' => [
                'resolvedTemplate' => 'content',
                'resolvable' => new ResolvedTemplateMutatorResolvable(
                    new Resolvable('content', []),
                    function (string $resolvedTemplate) {
                        return $resolvedTemplate . ' append 1';
                    }
                ),
                'expectedMutatedResolvedTemplate' => 'content append 1',
            ],
            'mutating inner resolvable' => [
                'resolvedTemplate' => 'content',
                'resolvable' => new ResolvedTemplateMutatorResolvable(
                    new ResolvedTemplateMutatorResolvable(
                        new Resolvable('content', []),
                        function (string $resolvedTemplate) {
                            return $resolvedTemplate . ' append 1';
                        }
                    ),
                    function (string $resolvedTemplate) {
                        return $resolvedTemplate . ' append 2';
                    }
                ),
                'expectedMutatedResolvedTemplate' => 'content append 1 append 2',
            ],
            'mutating inner resolvable inside mutating inner resolvable' => [
                'resolvedTemplate' => 'content',
                'resolvable' => new ResolvedTemplateMutatorResolvable(
                    new ResolvedTemplateMutatorResolvable(
                        new ResolvedTemplateMutatorResolvable(
                            new Resolvable('content', []),
                            function (string $resolvedTemplate) {
                                return $resolvedTemplate . ' append 1';
                            }
                        ),
                        function (string $resolvedTemplate) {
                            return $resolvedTemplate . ' append 2';
                        }
                    ),
                    function (string $resolvedTemplate) {
                        return $resolvedTemplate . ' append 3';
                    }
                ),
                'expectedMutatedResolvedTemplate' => 'content append 1 append 2 append 3',
            ],
        ];
    }

    public function testGetResolvable(): void
    {
        $encapsulatedResolvable = new Resolvable('', []);

        $resolvable = new ResolvedTemplateMutatorResolvable(
            $encapsulatedResolvable,
            function () {
            }
        );

        self::assertInstanceOf(ResolvableProviderInterface::class, $resolvable);
        self::assertSame($encapsulatedResolvable, $resolvable->getResolvable());
    }

    /**
     * @dataProvider countDataProvider
     */
    public function testCount(ResolvedTemplateMutatorResolvable $resolvable, int $expectedCount): void
    {
        self::assertSame($expectedCount, count($resolvable));
    }

    /**
     * @return array<mixed>
     */
    public function countDataProvider(): array
    {
        return [
            'non-iterable inner resolvable' => [
                'resolvable' => new ResolvedTemplateMutatorResolvable(
                    new Resolvable('', []),
                    function () {
                    },
                ),
                'expectedCount' => 1,
            ],
            'single-item inner resolvable' => [
                'resolvable' => new ResolvedTemplateMutatorResolvable(
                    new ResolvableCollection(['item1'], ''),
                    function () {
                    },
                ),
                'expectedCount' => 1,
            ],
            'triple-item inner resolvable' => [
                'resolvable' => new ResolvedTemplateMutatorResolvable(
                    new ResolvableCollection(['item1', 'item2', 'item3'], ''),
                    function () {
                    },
                ),
                'expectedCount' => 3,
            ],
        ];
    }

    /**
     * @dataProvider getItemForIndexDataProvider
     */
    public function testGetIndexForItem(
        ResolvedTemplateMutatorResolvable $collection,
        string|\Stringable|ResolvableInterface $item,
        ?int $expectedIndex
    ): void {
        self::assertSame($expectedIndex, $collection->getIndexForItem($item));
    }

    /**
     * @return array<mixed>
     */
    public function getItemForIndexDataProvider(): array
    {
        $resolvable = new Resolvable('', []);
        $collection = new ResolvableCollection(
            [
                'item1',
                'item2',
                $resolvable,
            ],
            ''
        );
        $encapsulatedCollection = new ResolvedTemplateMutatorResolvable(
            $collection,
            function () {
            }
        );

        return [
            'non-iterable inner resolvable' => [
                'resolvable' => new ResolvedTemplateMutatorResolvable(
                    new Resolvable('', []),
                    function () {
                    },
                ),
                'item' => 'item',
                'expectedIndex' => null,
            ],
            'empty collection' => [
                'collection' => new ResolvedTemplateMutatorResolvable(
                    new ResolvableCollection([], ''),
                    function () {
                    }
                ),
                'item' => 'item',
                'expectedIndex' => null,
            ],
            'item not present' => [
                'collection' => $encapsulatedCollection,
                'item' => 'item',
                'expectedIndex' => null,
            ],
            'first item' => [
                'collection' => $encapsulatedCollection,
                'item' => 'item1',
                'expectedIndex' => 0,
            ],
            'last item' => [
                'collection' => $encapsulatedCollection,
                'item' => $resolvable,
                'expectedIndex' => 2,
            ],
        ];
    }
}
