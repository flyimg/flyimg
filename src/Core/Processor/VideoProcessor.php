<?php

namespace Core\Processor;

use Core\Entity\Command;
use Core\Entity\OptionsBag;
use Core\Exception\ExecFailedException;
use Core\Exception\InvalidArgumentException;

/**
 * Class VideoProcessor
 */
class VideoProcessor extends Processor
{
    /**
     * Generate video source image
     *
     * Generate a full resolution image at the requested (or default) time
     * duration to be used as the source image.
     *
     * @param OptionsBag $optionsBag      Options bag
     * @param string     $sourceImagePath Source image path (movie)
     *
     * @return string
     */
    public function generateVideoSourceImage(OptionsBag $optionsBag, string $sourceImagePath)
    {
        $time = self::validateTime($optionsBag->get('time'));
        $videoSourceImagePath = $sourceImagePath . '-' . self::timeFileSuffix($time);

        if (file_exists($videoSourceImagePath) && !$optionsBag->get('refresh')) {
            return $videoSourceImagePath;
        }

        $cmd = self::buildFrameExtractCommand($sourceImagePath, $time, $videoSourceImagePath);
        $this->logger->info('VideoProcessorCommand: ' . $cmd);

        $this->execute($cmd);

        // For some reason in ffmpeg the error:-
        // 'Output file is empty, nothing was encoded (check -ss / -t / -frames parameters if used)'
        // is in warning level but if we turn that on we get other ignorable errors show up ('deprecated pixel format
        // used, make sure you did set range correctly using ffmpeg'). So just check the output file
        // exists and if not it's likely that the user set a duration greater than the movie length.
        if (!file_exists($videoSourceImagePath)) {
            $msg = 'Output file is empty, possibly the time parameter is greater than the movie length.';
            throw new ExecFailedException($msg);
        }

        return $videoSourceImagePath;
    }

    /**
     * Ensure the time option is an ffmpeg duration: `[[HH:]MM:]SS[.mmm]`.
     *
     * @param mixed $time
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public static function validateTime($time): string
    {
        $time = (string) $time;
        if (!preg_match('/^(?:(?:\d{1,2}:)?\d{1,2}:)?\d{1,6}(?:\.\d{1,3})?$/D', $time)) {
            throw new InvalidArgumentException('Invalid time option. Expected format: HH:MM:SS or SS');
        }

        return $time;
    }

    /**
     * Filename-safe representation of a validated time option.
     *
     * @param string $time
     *
     * @return string
     */
    public static function timeFileSuffix(string $time): string
    {
        return str_replace(['.', ':'], '', $time);
    }

    /**
     * @param string $sourceImagePath
     * @param string $time
     * @param string $outputPath
     *
     * @return Command
     */
    public static function buildFrameExtractCommand(string $sourceImagePath, string $time, string $outputPath): Command
    {
        $cmd = new Command(self::FFMPEG_COMMAND);

        $cmd->addArgument('-y');
        $cmd->addArgument('-i', $sourceImagePath);
        $cmd->addArgument("-vf scale='iw:ih'");
        $cmd->addArgument('-ss', $time);
        $cmd->addArgument('-f', 'image2');
        $cmd->addArgument('-vframes', 1);
        $cmd->addArgument(escapeshellarg($outputPath));
        $cmd->addArgument('-hide_banner');
        $cmd->addArgument('-loglevel', 'error');

        return $cmd;
    }
}
