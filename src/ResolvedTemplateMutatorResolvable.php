<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

class ResolvedTemplateMutatorResolvable implements ResolvableInterface, ResolvedTemplateMutationInterface
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
}
