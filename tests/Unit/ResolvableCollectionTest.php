<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable\Tests\Unit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use webignition\ObjectReflector\ObjectReflector;
use webignition\StubbleResolvable\IdentifierGenerator;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\Tests\Model\Stringable;
use webignition\StubbleResolvable\Tests\Model\StringableResolvable;

class ResolvableCollectionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testImplementsResolvableInterface()
    {
        $collection = new ResolvableCollection([], '');
        self::assertInstanceOf(ResolvableInterface::class, $collection);
    }

    public function testCreate()
    {
        $items = [
            'item1',
            'item2',
            'item3',
        ];

        $length = ResolvableCollection::GENERATED_IDENTIFIER_LENGTH;
        $generatedIdentifier = 'generated identifier';

        $identifierGenerator = \Mockery::mock(IdentifierGenerator::class);
        $identifierGenerator
            ->shouldReceive('generate')
            ->with($length)
            ->andReturn($generatedIdentifier);

        $collection = ResolvableCollection::create($items, $length, $identifierGenerator);
        self::assertInstanceOf(ResolvableCollection::class, $collection);

        self::assertSame(
            $generatedIdentifier,
            ObjectReflector::getProperty($collection, 'identifier')
        );

        self::assertSame(
            $items,
            ObjectReflector::getProperty($collection, 'items')
        );
    }

    /**
     * @dataProvider getTemplateDataProvider
     *
     * @param ResolvableCollection $collection
     * @param string $expectedTemplate
     */
    public function testGetTemplate(ResolvableCollection $collection, string $expectedTemplate)
    {
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
            new StringableResolvable('item5'),
        ];

        return [
            'empty identifier, no items' => [
                'collection' => new ResolvableCollection([], ''),
                'expectedTemplate' => '',
            ],
            'has identifier, no items' => [
                'collection' => new ResolvableCollection([], 'collection_item'),
                'expectedTemplate' => '',
            ],
            'empty identifier, has items' => [
                'collection' => new ResolvableCollection($items, ''),
                'expectedTemplate' => '{{ 0 }}item2item3{{ 1 }}item5',
            ],
            'has identifier, has items' => [
                'collection' => new ResolvableCollection($items, 'collection_item'),
                'expectedTemplate' => '{{ collection_item0 }}item2item3{{ collection_item1 }}item5',
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
                'collection' => new ResolvableCollection([], ''),
                'expectedContext' => [],
            ],
            'has identifier, no items' => [
                'collection' => new ResolvableCollection([], 'collection_item'),
                'expectedContext' => [],
            ],
            'empty identifier, has items' => [
                'collection' => new ResolvableCollection($items, ''),
                'expectedContext' => [
                    '0' => $resolvableItem1,
                    '1' => $resolvableItem2,
                ],
            ],
            'has identifier, has items' => [
                'collection' => new ResolvableCollection($items, 'collection_item'),
                'expectedContext' => [
                    'collection_item0' => $resolvableItem1,
                    'collection_item1' => $resolvableItem2,
                ],
            ],
        ];
    }

    public function testIterable()
    {
        $items = [
            'item1',
            'item2',
            'item3',
        ];

        $collection = ResolvableCollection::create($items);
        self::assertTrue(is_iterable($collection));

        $iteratedItems = [];
        foreach ($collection as $item) {
            $iteratedItems[] = $item;
        }

        self::assertSame($items, $iteratedItems);
    }
}
