<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

/**
 * @implements \IteratorAggregate<string|\Stringable|ResolvableInterface>
 */
class ResolvableCollection implements ResolvableCollectionInterface, \IteratorAggregate
{
    public const GENERATED_IDENTIFIER_LENGTH = 16;

    /**
     * @var array<int, ResolvableInterface|string|\Stringable>
     */
    private array $items = [];
    private string $identifier;

    /**
     * @param array<mixed> $items
     */
    public function __construct(array $items, string $identifier)
    {
        foreach ($items as $item) {
            if (is_string($item) || $item instanceof \Stringable) {
                $this->items[] = (string) $item;
            } elseif ($item instanceof ResolvableInterface) {
                $this->items[] = $item;
            }
        }

        $this->identifier = $identifier;
    }

    /**
     * @param array<mixed> $items
     * @param int<1, max>  $length
     */
    public static function create(
        array $items,
        int $length = self::GENERATED_IDENTIFIER_LENGTH,
        ?IdentifierGenerator $identifierGenerator = null
    ): self {
        $identifierGenerator = $identifierGenerator instanceof IdentifierGenerator
            ? $identifierGenerator
            : new IdentifierGenerator();

        return new ResolvableCollection($items, $identifierGenerator->generate($length));
    }

    public function getTemplate(): string
    {
        $components = [];

        $resolvableItemIndex = 0;
        foreach ($this->items as $item) {
            if (is_string($item) || is_object($item) && method_exists($item, '__toString')) {
                $components[] = (string) $item;
            } elseif ($item instanceof ResolvableInterface) {
                $components[] = $this->createItemTemplate(
                    $this->createItemIdentifier($resolvableItemIndex)
                );

                ++$resolvableItemIndex;
            }
        }

        return implode('', $components);
    }

    public function getContext(): array
    {
        $context = [];

        $resolvableItemIndex = 0;
        foreach ($this->items as $item) {
            $isStringable = is_string($item) || is_object($item) && method_exists($item, '__toString');

            if (false === $isStringable && $item instanceof ResolvableInterface) {
                $itemIdentifier = $this->createItemIdentifier($resolvableItemIndex);
                $context[$itemIdentifier] = $item;

                ++$resolvableItemIndex;
            }
        }

        return $context;
    }

    /**
     * @return \Traversable<int, ResolvableInterface|string|\Stringable>
     */
    public function getIterator(): iterable
    {
        return new \ArrayIterator($this->items);
    }

    public function getIndexForItem(string|\Stringable|ResolvableInterface $item): ?int
    {
        $position = array_search($item, $this->items);

        return false === $position ? null : $position;
    }

    public function count(): int
    {
        return count($this->items);
    }

    private function createItemTemplate(string $identifier): string
    {
        return '{{ ' . $identifier . ' }}';
    }

    private function createItemIdentifier(int $index): string
    {
        return $this->identifier . ((string) $index);
    }
}
