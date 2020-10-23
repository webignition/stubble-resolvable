<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

/**
 * @implements \IteratorAggregate<string|ResolvableInterface>
 */
class ResolvableCollection implements ResolvableInterface, \IteratorAggregate
{
    public const GENERATED_IDENTIFIER_LENGTH = 16;

    /**
     * @var array<mixed>
     */
    private array $items;
    private string $identifier;

    /**
     * @param array<mixed> $items
     * @param string $identifier
     */
    public function __construct(array $items, string $identifier)
    {
        $this->items = array_filter($items, function ($item) {
            return Resolvable::canResolve($item);
        });

        $this->items = $items;
        $this->identifier = $identifier;
    }

    /**
     * @param array<mixed> $items
     * @param int $length
     * @param IdentifierGenerator|null $identifierGenerator
     *
     * @return self
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
            if ($this->isStringable($item)) {
                $components[] = (string) $item;
            } elseif ($item instanceof ResolvableInterface) {
                $components[] = $this->createItemTemplate(
                    $this->createItemIdentifier($resolvableItemIndex)
                );

                $resolvableItemIndex++;
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

    /**
     * @param mixed $object
     *
     * @return bool
     */
    private function isStringable($object): bool
    {
        if (is_string($object)) {
            return true;
        }

        if (is_object($object) && method_exists($object, '__toString')) {
            return true;
        }

        return false;
    }
}
