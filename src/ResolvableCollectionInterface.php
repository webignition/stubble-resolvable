<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

interface ResolvableCollectionInterface extends ResolvableInterface, \Countable
{
    public function getIndexForItem(string|\Stringable|ResolvableInterface $item): ?int;
}
