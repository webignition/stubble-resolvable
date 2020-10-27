<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

interface ResolvableCollectionInterface extends ResolvableInterface, \Countable
{
    /**
     * @param string|ResolvableInterface $item
     *
     * @return int|null
     */
    public function getIndexForItem($item): ?int;
}
