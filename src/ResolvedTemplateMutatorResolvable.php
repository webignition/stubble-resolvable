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

    public function __construct(ResolvableInterface $resolvable)
    {
        $this->resolvable = $resolvable;
        $this->mutator = function (string $resolvedTemplate) {
            return $resolvedTemplate;
        };
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

    public function withResolvedTemplateMutator(callable $mutator): ResolvableInterface
    {
        $new = clone $this;
        $new->mutator = $mutator;

        return $new;
    }
}
