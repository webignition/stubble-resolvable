<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable\Tests\Model;

use webignition\StubbleResolvable\ResolvableInterface;

class StringableResolvable implements ResolvableInterface
{
    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function __toString(): string
    {
        return $this->content;
    }

    public function getTemplate(): string
    {
        return (string) $this;
    }

    public function getContext(): array
    {
        return [];
    }
}
