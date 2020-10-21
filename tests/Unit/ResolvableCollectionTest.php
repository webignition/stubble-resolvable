<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableInterface;

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
                'items' => [
                    'item1',
                    'item2',
                    'item3',
                ],
                'expectedTemplate' => '{{ 0 }}{{ 1 }}{{ 2 }}',
            ],
            'has identifier, has items' => [
                'identifier' => 'collection_item',
                'items' => [
                    'item1',
                    'item2',
                    'item3',
                ],
                'expectedTemplate' => '{{ collection_item0 }}{{ collection_item1 }}{{ collection_item2 }}',
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
                'collection' => new ResolvableCollection('', [
                    'item1',
                    'item2',
                    'item3',
                ]),
                'expectedContext' => [
                    '0' => 'item1',
                    '1' => 'item2',
                    '2' => 'item3',
                ],
            ],
            'has identifier, has items' => [
                'collection' => new ResolvableCollection('collection_item', [
                    'item1',
                    'item2',
                    'item3',
                ]),
                'expectedContext' => [
                    'collection_item0' => 'item1',
                    'collection_item1' => 'item2',
                    'collection_item2' => 'item3',
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
