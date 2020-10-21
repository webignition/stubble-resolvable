<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

trait ResolvedTemplateMutatorTrait
{
    /**
     * @var callable|null
     */
    private $resolvedContentMutator = null;

    public function getResolvedTemplateMutator(): ?callable
    {
        return $this->resolvedContentMutator;
    }

    public function withResolvedTemplateMutator(?callable $mutator): ResolvableInterface
    {
        $new = clone $this;
        $new->resolvedContentMutator = $mutator;

        return $new;
    }
}
