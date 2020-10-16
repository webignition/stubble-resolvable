<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

interface ResolvableInterface
{
    public function getTemplate(): string;

    /**
     * @return array<string, string|ResolvableInterface>
     */
    public function getContext(): array;

    public function withContextMutator(callable $mutator): ResolvableInterface;
}
