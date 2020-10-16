<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

interface ResolvableInterface
{
    public function getTemplate(): string;

    /**
     * @return array<string, string>
     */
    public function getContext(): array;
}
