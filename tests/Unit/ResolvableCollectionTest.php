<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\Tests\Model\Stringable;

class ResolvableCollectionTest extends TestCase
{
    public function testImplementsResolvableInterface()
    {
        $collection = new ResolvableCollection('', []);
        self::assertInstanceOf(ResolvableInterface::class, $collection);
    }

    /**
     * @dataProvider getTemplateDataProvider
     *
     * @param string $identifier
     * @param string[]|ResolvableInterface[] $items
     * @param string $expectedTemplate
     */
    public function testGetTemplate(string $identifier, array $items, string $expectedTemplate)
    {
        $collection = new ResolvableCollection($identifier, $items);
        self::assertSame($expectedTemplate, $collection->getTemplate());
    }

    public function getTemplateDataProvider(): array
    {
        $items = [
            new Resolvable('{{ self }}', [
                'self' => 'item1',
            ]),
            'item2',
            new Stringable('item3'),
            new Resolvable('{{ self }}', [
                'self' => 'item4',
            ]),
        ];

        return [
            'empty identifier, no items' => [
                'identifier' => '',
                'items' => [],
                'expectedTemplate' => '',
            ],
            'has identifier, no items' => [
                'identifier' => 'collection_item',
                'items' => [],
                'expectedTemplate' => '',
            ],
            'empty identifier, has items' => [
                'identifier' => '',
                'items' => $items,
                'expectedTemplate' => '{{ 0 }}item2item3{{ 1 }}',
            ],
            'has identifier, has items' => [
                'identifier' => 'collection_item',
                'items' => $items,
                'expectedTemplate' => '{{ collection_item0 }}item2item3{{ collection_item1 }}',
            ],
        ];
    }

    /**
     * @dataProvider getContextDataProvider
     *
     * @param ResolvableCollection $collection
     * @param string[]|ResolvableCollection[] $expectedContext
     */
    public function testGetContext(ResolvableCollection $collection, array $expectedContext)
    {
        self::assertEquals($expectedContext, $collection->getContext());
    }

    public function getContextDataProvider(): array
    {
        $resolvableItem1 = new Resolvable('{{ self }}', [
            'self' => 'item1',
        ]);

        $resolvableItem2 = new Resolvable('{{ self }}', [
            'self' => 'item4',
        ]);

        $items = [
            $resolvableItem1,
            'item2',
            new Stringable('item3'),
            $resolvableItem2,
        ];

        return [
            'empty identifier, no items' => [
                'collection' => new ResolvableCollection('', []),
                'expectedContext' => [],
            ],
            'has identifier, no items' => [
                'collection' => new ResolvableCollection('collection_item', []),
                'expectedContext' => [],
            ],
            'empty identifier, has items' => [
                'collection' => new ResolvableCollection('', $items),
                'expectedContext' => [
                    '0' => $resolvableItem1,
                    '1' => $resolvableItem2,
                ],
            ],
            'has identifier, has items' => [
                'collection' => new ResolvableCollection('collection_item', $items),
                'expectedContext' => [
                    'collection_item0' => $resolvableItem1,
                    'collection_item1' => $resolvableItem2,
                ],
            ],
        ];
    }

    public function testResolvedTemplateMutator()
    {
        $mutator = function () {
        };

        $collection = new ResolvableCollection('', []);
        self::assertNull($collection->getResolvedTemplateMutator());
        $resolvableWithMutator = $collection->withResolvedTemplateMutator($mutator);

        self::assertNotSame($collection, $resolvableWithMutator);
        self::assertSame($mutator, $resolvableWithMutator->getResolvedTemplateMutator());
    }

    public function testIterable()
    {
        $items = [
            'item1',
            'item2',
            'item3',
        ];

        $collection = new ResolvableCollection('collection_item', $items);
        self::assertTrue(is_iterable($collection));

        $iteratedItems = [];
        foreach ($collection as $item) {
            $iteratedItems[] = $item;
        }

        self::assertSame($items, $iteratedItems);
    }
}
