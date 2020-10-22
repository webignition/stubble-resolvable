<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable;

class IdentifierGenerator
{
    public function generate(int $length): string
    {
        try {
            $identifier = bin2hex(random_bytes($length));
        } catch (\Exception $e) {
            $source = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $sourceLength = strlen($source);
            $content = str_repeat($source, (int) ceil($length / $sourceLength));

            $identifier = substr(str_shuffle($content), 0, $length);
        }

        return $identifier;
    }
}
