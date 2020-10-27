<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

class ResolvedTemplateMutatorResolvable implements
    ResolvableCollectionInterface,
    ResolvableProviderInterface,
    ResolvedTemplateMutationInterface
{
    private ResolvableInterface $resolvable;

    /**
     * @var callable
     */
    private $mutator;

    public function __construct(ResolvableInterface $resolvable, callable $mutator)
    {
        $this->resolvable = $resolvable;
        $this->mutator = $mutator;
    }

    public function getTemplate(): string
    {
        return $this->resolvable->getTemplate();
    }

    public function getContext(): array
    {
        return $this->resolvable->getContext();
    }

    public function getResolvedTemplateMutator(): callable
    {
        return $this->mutator;
    }

    public function getResolvable(): ResolvableInterface
    {
        return $this->resolvable;
    }

    public function count(): int
    {
        if ($this->resolvable instanceof ResolvableCollectionInterface) {
            return $this->resolvable->count();
        }

        return 1;
    }

    public function getIndexForItem($item): ?int
    {
        if ($this->resolvable instanceof ResolvableCollectionInterface) {
            return $this->resolvable->getIndexForItem($item);
        }

        return null;
    }
}
