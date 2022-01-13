<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

interface ResolvableInterface
{
    public function getTemplate(): string;

    /**
     * @return array<string, ResolvableInterface|string|\Stringable>
     */
    public function getContext(): array;
}
