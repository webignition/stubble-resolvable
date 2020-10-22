<?php

declare(strict_types=1);

namespace webignition\StubbleResolvable\Tests\Unit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use phpmock\mockery\PHPMockery;
use PHPUnit\Framework\TestCase;
use webignition\StubbleResolvable\IdentifierGenerator;

class IdentifierGeneratorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testGenerateWithRandomBytes()
    {
        $length = 16;
        $mockedRandomBytes = 'mocked random_bytes';
        $mockedBin2Hex = 'mocked bin2hex';

        $generator = new IdentifierGenerator();

        PHPMockery::mock('webignition\StubbleResolvable', 'random_bytes')
            ->with($length)
            ->andReturn($mockedRandomBytes);

        PHPMockery::mock('webignition\StubbleResolvable', 'bin2hex')
            ->with($mockedRandomBytes)
            ->andReturn($mockedBin2Hex);

        $identifier = $generator->generate($length);

        self::assertSame($mockedBin2Hex, $identifier);
    }

    public function testGenerateWithRandomBytesThrowsException()
    {
        $length = 16;
        $mockedStrShuffle = 'mocked str_shuffle';

        $generator = new IdentifierGenerator();

        PHPMockery::mock('webignition\StubbleResolvable', 'random_bytes')
            ->with($length)
            ->andThrow(new \Exception('message', 123));

        PHPMockery::mock('webignition\StubbleResolvable', 'str_shuffle')
            ->andReturn($mockedStrShuffle);

        $identifier = $generator->generate($length);

        self::assertSame(
            substr($mockedStrShuffle, 0, $length),
            $identifier
        );
    }
}
