<?php

namespace Core\Processor;

use Core\Entity\Command;
use Core\Entity\Image\OutputImage;
use Core\Exception\ExecFailedException;
use Monolog\Registry;

/**
 * Class Processor
 * @package Core\Processor
 */
class Processor
{
    /** MozJPEG bin path (make install places the binary in bin/) */
    public const MOZJPEG_COMMAND = '/opt/mozjpeg/bin/cjpeg';

    /** ImageMagick bin path*/
    public const IM_CONVERT_COMMAND = '/usr/local/bin/magick';
    public const IM_IDENTITY_COMMAND = '/usr/local/bin/identify';
    public const IM_MOGRIFY_COMMAND = '/usr/local/bin/mogrify';

    /** CWEBP bin path */
    public const CWEBP_COMMAND = '/usr/bin/cwebp';

    /** FaceDetect bin path */
    public const FACEDETECT_COMMAND = '/usr/bin/python3 /var/www/html/python/facedetect.py';

    /** Smart Crop script */
    public const SMARTCROP_COMMAND = '/usr/bin/python3 /var/www/html/python/smartcrop.py';

    /** Ffmpeg bin path */
    public const FFMPEG_COMMAND = '/usr/bin/ffmpeg';

    /** OutputImage options excluded from IM command */
    public const EXCLUDED_IM_OPTIONS = ['quality', 'mozjpeg', 'refresh', 'webp-lossless', 'webp-method', 'jxl-effort'];

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param Logger $logger
     */
    public function __construct()
    {
        $this->logger = Registry::getInstance('flyimg');
    }

    /**
     * @param Command $command
     *
     * @return array
     * @throws \Exception
     */
    public function execute(Command $command): array
    {
        $descriptorspec = [
            0 => ["pipe", "r"],  // stdin
            1 => ["pipe", "w"],  // stdout
            2 => ["pipe", "w"]   // stderr
        ];

        $process = proc_open($command, $descriptorspec, $pipes);

        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            $errorOutput = stream_get_contents($pipes[2]);

            fclose($pipes[1]);
            fclose($pipes[2]);

            $return_value = proc_close($process);

            if ($return_value !== 0) {
                throw new ExecFailedException(
                    "Error output: $errorOutput\n" .
                    "Command: $command"
                );
            }
        } else {
            throw new ExecFailedException("Failed to initiate the process.");
        }

        return explode(PHP_EOL, $output);
    }

    /**
     * Get the image Identity information
     *
     * @param OutputImage $image
     *
     * @return string
     */
    public function imageIdentityInformation(OutputImage $image): string
    {
        $identityCmd = new Command(self::IM_IDENTITY_COMMAND);
        $identityCmd->addArgument($image->getOutputTmpPath());
        $output = $this->execute($identityCmd);

        return !empty($output[0]) ? $output[0] : "";
    }
}
