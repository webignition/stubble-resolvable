<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

/**
 * @implements \IteratorAggregate<string|ResolvableInterface>
 */
class ResolvableCollection implements ResolvableInterface, \IteratorAggregate
{
    use ResolvedTemplateMutatorTrait;

    /**
     * @var array<mixed>
     */
    private array $items;
    private string $identifier;

    /**
     * @param string $identifier
     * @param array<mixed> $items
     */
    public function __construct(string $identifier, array $items)
    {
        $this->identifier = $identifier;

        $this->items = array_filter($items, function ($item) {
            return Resolvable::canResolve($item);
        });

        $this->items = $items;
    }

    public function getTemplate(): string
    {
        $components = [];

        $resolvableItemIndex = 0;
        foreach ($this->items as $item) {
            if ($item instanceof ResolvableInterface) {
                $components[] = $this->createItemTemplate(
                    $this->createItemIdentifier($resolvableItemIndex)
                );

                $resolvableItemIndex++;
            }

            if (is_string($item)) {
                $components[] = $item;
            }

            if (is_object($item) && method_exists($item, '__toString')) {
                $components[] = (string) $item;
            }
        }

        return implode('', $components);
    }

    public function getContext(): array
    {
        $context = [];

        $resolvableItemIndex = 0;
        foreach ($this->items as $item) {
            if ($item instanceof ResolvableInterface) {
                $itemIdentifier = $this->createItemIdentifier($resolvableItemIndex);
                $context[$itemIdentifier] = $item;

                $resolvableItemIndex++;
            }
        }

        return $context;
    }

    /**
     * @return \Traversable<string|ResolvableInterface>
     */
    public function getIterator(): iterable
    {
        return new \ArrayIterator($this->items);
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
