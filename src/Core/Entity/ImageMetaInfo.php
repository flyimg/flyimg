<?php

namespace Core\Entity;

use Core\Exception\ExecFailedException;
use Core\Processor\Processor;

/**
 * Fetches and stores image properties like mimetype, bits depth, weight and dimensions.
 */
class ImageMetaInfo
{
    public const IMAGE_PROP_FILE_FORMAT = 'format';
    public const IMAGE_PROP_DIMENSIONS = 'dimensions';
    public const IMAGE_PROP_CANVAS_COORDS = 'canvas';
    public const IMAGE_PROP_COLOR_DEPTH = 'colorDepth';
    public const IMAGE_PROP_COLOR_PROFILE = 'colorProfile';
    public const IMAGE_PROP_FILE_WEIGHT = 'weight';

    /** @var string */
    protected string $imagePath;

    /** @var string */
    protected string $imageMimeType;

    /** @var array Associative array that holds basic image info. Consider in the future create an imageInfo class. */
    protected array $imageInfo;

    /**
     * ImageMetaInfo constructor.
     *
     * @param string $imagePath
     */
    public function __construct(string $imagePath)
    {
        $this->imagePath = $imagePath;
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return $this->imagePath;
    }

    /**
     * Returns the mime-type (not the extension) of a file or image i.e: image/png
     * @return string
     */
    public function mimeType(): string
    {
        if (isset($this->imageMimeType)) {
            return $this->imageMimeType;
        }

        $this->imageMimeType = finfo_file(
            finfo_open(FILEINFO_MIME_TYPE),
            $this->imagePath
        );

        return $this->imageMimeType;
    }


    /**
     * @return string Image type as identified by IM like PNG, JPEG, GIF ...
     */
    public function format(): string
    {
        return $this->info()[self::IMAGE_PROP_FILE_FORMAT];
    }

    /**
     * @return string Image Canvas as identified by IM like: 70x46+0+0
     */
    public function canvas(): string
    {
        return $this->info()[self::IMAGE_PROP_CANVAS_COORDS];
    }

    /**
     * @return string Image bit depth as identified by IM like: 8-bit
     */
    public function colorBitDepth(): string
    {
        return $this->info()[self::IMAGE_PROP_COLOR_DEPTH];
    }

    /**
     * @return string Image color profile as identified by IM like: sRGB, aRGB
     */
    public function colorProfile(): string
    {
        return $this->info()[self::IMAGE_PROP_COLOR_PROFILE];
    }

    /**
     * @return string Image File weight as identified by IM like: 2.36KB
     */
    public function fileWeight(): string
    {
        return $this->info()[self::IMAGE_PROP_FILE_WEIGHT];
    }

    /**
     * Gets parsed image dimensions into width and height
     * @return array Associative array with `width` and `height` values in pixels
     */
    public function dimensions(): array
    {
        $dimensions = $this->info()[self::IMAGE_PROP_DIMENSIONS];
        $dimensions = explode('x', $dimensions);

        return [
            'width' => $dimensions[0],
            'height' => $dimensions[1],
        ];
    }

    /**
     * get stored ImageInfo or fetch it and store it
     * @return array Associative array with basic information od the image
     */
    public function info(): array
    {
        if (!empty($this->imageInfo)) {
            return $this->imageInfo;
        }

        $this->imageInfo = $this->imageImIdentify();

        return $this->imageInfo;
    }

    /**
     * Returns an associative array with the info of this image's path.
     * In the future exec functionality to an \Core\Processor\Execution class
     * To figure out: What does the ` 2>&1` part do. Without it, WebP identification just breaks.
     * @return array
     * @throws \Exception
     */
    protected function imageImIdentify(): array
    {
        $commandStr = Processor::IM_IDENTITY_COMMAND . ' ' . escapeshellarg($this->path()) . ' 2>&1';
        exec($commandStr, $output, $code);
        if (count($output) === 0) {
            $outputError = $code;
        } else {
            $outputError = implode(PHP_EOL, $output);
        }

        if (0 !== $code) {
            throw new ExecFailedException(
                "Command failed. The exit code: " .
                $outputError . "<br>The last line of output: " .
                $commandStr
            );
        }

        $output = $this->sanitizeWebPOutput($output);

        return $this->parseImageInfoResponse($output);
    }

    /**
     * Imagemagick identify will first parse and store an internal copy of WebP files, outputting that process first.
     * For example:
     *     * Decoded /tmp/magick-19757aUG67rpqXgFy. Dimensions: 100 x 100 . Format: lossy. Now saving...
     *     * Saved file /tmp/magick-19757d1r0Kuo4UPCV
     *     * tests/testImages/square.webp PAM 100x100 100x100+0+0 8-bit TrueColor sRGB 40.1KB 0.000u 0:00.000
     * This method takes care of the extra output
     *
     * @param  array $output CLI exec output array
     *
     * @return array         The expected array with the identify string at position 0.
     */
    protected function sanitizeWebPOutput(array $output): array
    {
        if (strpos($output[0], 'Decoded /tmp/magick-') !== 0) {
            return $output;
        }

        return [$output[2]];
    }

    /**
     * Parses the default output of imagemagick identify command
     * To fix in the near future: currently the bit depth is incomplete for WebP and color profile for GIF
     *
     * @param  array $output the STDOUT from executing an identify command
     *
     * @return array         associative array with the info in there
     */
    protected function parseImageInfoResponse($output): array
    {
        $output = explode(' ', $output[0]);

        return [
            self::IMAGE_PROP_FILE_FORMAT => $output[1],
            self::IMAGE_PROP_DIMENSIONS => $output[2],
            self::IMAGE_PROP_CANVAS_COORDS => $output[3],
            self::IMAGE_PROP_COLOR_DEPTH => $output[4],
            self::IMAGE_PROP_COLOR_PROFILE => $output[5],
            self::IMAGE_PROP_FILE_WEIGHT => $output[6],
        ];
    }
}
