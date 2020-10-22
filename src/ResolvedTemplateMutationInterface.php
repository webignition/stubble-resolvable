<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

interface ResolvedTemplateMutationInterface
{
    public function getResolvedTemplateMutator(): callable;
}
