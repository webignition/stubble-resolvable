<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

class Resolvable implements ResolvableInterface
{
    private string $template;

    /**
     * @var array<string, string|ResolvableInterface>
     */
    private array $context;

    /**
     * @var ?callable
     */
    private $contextMutator = null;

    /**
     * @param string $template
     * @param array<string, string|ResolvableInterface> $context
     */
    public function __construct(string $template, array $context)
    {
        $this->template = $template;
        $this->context = $context;
    }

    public function withContextMutator(callable $mutator): self
    {
        $new = clone $this;
        $new->contextMutator = $mutator;

        return $new;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getContext(): array
    {
        $context = $this->context;

        if (is_callable($this->contextMutator)) {
            foreach ($context as $key => $value) {
                $context[$key] = ($this->contextMutator)($value);
            }
        }

        return $context;
    }
}
