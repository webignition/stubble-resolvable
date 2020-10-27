<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

interface ResolvedTemplateMutationInterface
{
    /**
     * @return callable[]
     */
    public function getResolvedTemplateMutators(): array;
}
