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
     * @var array<string|ResolvableInterface>
     */
    private array $items;
    private string $identifier;

    /**
     * @param string $identifier
     * @param array<string|ResolvableInterface> $items
     */
    public function __construct(string $identifier, array $items)
    {
        $this->identifier = $identifier;
        $this->items = $items;
    }

    public function getTemplate(): string
    {
        return implode('', $this->createItemTemplates());
    }

    public function getContext(): array
    {
        $context = [];
        $itemIndex = 0;

        foreach ($this->createItemIdentifiers() as $itemIdentifier) {
            $context[$itemIdentifier] = $this->items[$itemIndex];
            $itemIndex++;
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

    /**
     * @return string[]
     */
    private function createItemTemplates(): array
    {
        $templates = [];

        foreach ($this->createItemIdentifiers() as $itemIdentifier) {
            $templates[] = '{{ ' . $itemIdentifier . ' }}';
        }

        return $templates;
    }

    /**
     * @return string[]
     */
    private function createItemIdentifiers(): array
    {
        $identifiers = [];
        $itemCount = count($this->items);

        for ($itemIndex = 0; $itemIndex < $itemCount; $itemIndex++) {
            $identifiers[] = $this->createItemIdentifier($itemIndex);
        }

        return $identifiers;
    }

    private function createItemIdentifier(int $index): string
    {
        return $this->identifier . ((string) $index);
    }
}
