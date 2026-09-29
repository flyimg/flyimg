<?php

namespace Tests\Core\Processor;

use Core\Processor\ImageProcessor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * WebP define sanitization does not need ImageMagick.
 */
class WebpEncodeOptionsTest extends TestCase
{
    /**
     * @param mixed $value
     */
    #[DataProvider('threadProvider')]
    public function testNormalizeWebpThreads($value, int $expected): void
    {
        $this->assertSame($expected, ImageProcessor::normalizeWebpThreads($value));
    }

    /**
     * @return array<string, array{0: mixed, 1: int}>
     */
    public static function threadProvider(): array
    {
        return [
            'missing null' => [null, 1],
            'missing empty' => ['', 1],
            'non numeric' => ['yes', 1],
            'disabled' => [0, 0],
            'disabled string' => ['0', 0],
            'enabled' => [1, 1],
            'enabled string' => ['1', 1],
            'any positive enables' => ['2', 1],
        ];
    }

    /**
     * @param mixed $value
     */
    #[DataProvider('methodProvider')]
    public function testNormalizeWebpMethod($value, int $expected): void
    {
        $this->assertSame($expected, ImageProcessor::normalizeWebpMethod($value));
    }

    public function testBuildWebpEncoderCommandDoesNotPassWebpDefinesToImageMagick(): void
    {
        $command = ImageProcessor::buildWebpEncoderCommand(80, '0', null, '', '/tmp/out.webp');

        $this->assertStringStartsWith('png:- | ', $command);
        $this->assertStringContainsString("'/usr/bin/cwebp'", $command);
        $this->assertStringContainsString("-q '80'", $command);
        $this->assertStringContainsString("-m '4'", $command);
        $this->assertStringContainsString(' -mt ', $command);
        $this->assertStringNotContainsString('-lossless', $command);
        $this->assertStringEndsWith(" -o '/tmp/out.webp' -- -", $command);
        $this->assertStringNotContainsString('webp:thread-level', $command);
        $this->assertStringNotContainsString('webp:method', $command);
    }

    public function testBuildWebpEncoderCommandClampsHostileMethod(): void
    {
        $command = ImageProcessor::buildWebpEncoderCommand(80, '1', 0, '4;id', '/tmp/out.webp');

        $this->assertStringContainsString("-m '4'", $command);
        $this->assertStringContainsString(' -lossless ', $command);
        $this->assertStringNotContainsString(' -mt ', $command);
        $this->assertStringNotContainsString(';id', $command);
    }

    /**
     * @return array<string, array{0: mixed, 1: int}>
     */
    public static function methodProvider(): array
    {
        return [
            'missing null uses default' => [null, 4],
            'missing empty uses default' => ['', 4],
            'shell payload uses default' => ['4;id', 4],
            'explicit zero' => ['0', 0],
            'explicit zero int' => [0, 0],
            'default four' => [4, 4],
            'default four string' => ['4', 4],
            'above range clamps' => [9, 6],
            'below range clamps' => [-3, 0],
        ];
    }
}
