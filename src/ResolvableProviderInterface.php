<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

interface ResolvableProviderInterface
{
    public function getResolvable(): ResolvableInterface;
}
