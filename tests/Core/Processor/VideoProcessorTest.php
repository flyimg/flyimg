<?php

namespace Tests\Core\Processor;

use Core\Exception\InvalidArgumentException;
use Core\Processor\VideoProcessor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Time validation and ffmpeg command building do not need ffmpeg.
 */
class VideoProcessorTest extends TestCase
{
    #[DataProvider('validTimeProvider')]
    public function testValidateTimeAcceptsDurations(string $time, string $suffix): void
    {
        $this->assertSame($time, VideoProcessor::validateTime($time));
        $this->assertSame($suffix, VideoProcessor::timeFileSuffix($time));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function validTimeProvider(): array
    {
        return [
            'default' => ['00:00:01', '000001'],
            'seconds' => ['5', '5'],
            'fractional seconds' => ['1.5', '15'],
            'minutes and seconds' => ['01:30', '0130'],
            'hours with millis' => ['01:02:03.250', '010203250'],
        ];
    }

    /**
     * @param mixed $time
     */
    #[DataProvider('invalidTimeProvider')]
    public function testValidateTimeRejectsInvalidInput($time): void
    {
        $this->expectException(InvalidArgumentException::class);
        VideoProcessor::validateTime($time);
    }

    /**
     * @return array<string, array{0: mixed}>
     */
    public static function invalidTimeProvider(): array
    {
        return [
            'advisory payload' => ['1;sleep 8;'],
            'command substitution' => ['$(id)'],
            'backticks' => ['`id`'],
            'pipe' => ['1|id'],
            'ampersand' => ['1&id'],
            'path traversal' => ['1/../../x'],
            'trailing newline' => ["1\n"],
            'negative' => ['-1'],
            'empty' => [''],
            'null' => [null],
            'too many segments' => ['1:2:3:4'],
        ];
    }

    public function testFrameExtractCommandQuotesOutputPath(): void
    {
        $command = (string) VideoProcessor::buildFrameExtractCommand(
            '/tmp/original-abc',
            '00:00:01',
            "/tmp/original-abc-1;id;'x"
        );

        $this->assertStringContainsString("-i '/tmp/original-abc'", $command);
        $this->assertStringContainsString("-ss '00:00:01'", $command);
        $this->assertStringContainsString(
            ' ' . escapeshellarg("/tmp/original-abc-1;id;'x") . ' -hide_banner',
            $command
        );
    }
}
