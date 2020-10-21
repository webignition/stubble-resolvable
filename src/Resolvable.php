<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

class Resolvable implements ResolvableInterface
{
    use ResolvedTemplateMutatorTrait;

    private string $template;

    /**
     * @var array<string, string|ResolvableInterface>
     */
    private array $context;

    /**
     * @param string $template
     * @param array<string, string|ResolvableInterface> $context
     */
    public function __construct(string $template, array $context)
    {
        $this->template = $template;
        $this->context = $context;
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
