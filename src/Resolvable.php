<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

class Resolvable implements ResolvableInterface
{
    use ResolvedTemplateMutatorTrait;

    private string $template;

    /**
     * @var array<string, mixed>
     */
    private array $context;

    /**
     * @param string $template
     * @param array<string, mixed> $context
     */
    public function __construct(string $template, array $context)
    {
        $this->template = $template;

        $this->context = array_filter($context, function ($item) {
            return self::canResolve($item);
        });

        $this->context = $context;
    }

    /**
     * @param mixed $item
     *
     * @return bool
     */
    public static function canResolve($item): bool
    {
        if (is_string($item)) {
            return true;
        }

        if (is_object($item) && method_exists($item, '__toString')) {
            return true;
        }

        return $item instanceof ResolvableInterface;
    }

    public static function createFromStringable(object $object): self
    {
        $identifier = 'object_' . (string) spl_object_id($object);

        return new Resolvable(
            '{{ ' . $identifier . ' }}',
            [
                $identifier => (string) $object,
            ]
        );
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
