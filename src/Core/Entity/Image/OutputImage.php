<?php

namespace Core\Entity\Image;

use Core\Exception\InvalidArgumentException;
use Core\Processor\VideoProcessor;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OutputImage
 * @package Core\Entity
 */
class OutputImage
{
    /** Extension output */
    public const EXT_INPUT = 'input';
    public const EXT_AUTO = 'auto';
    public const EXT_PNG = 'png';
    public const EXT_WEBP = 'webp';
    public const EXT_JPG = 'jpg';
    public const EXT_GIF = 'gif';
    public const EXT_AVIF = 'avif';
    public const EXT_JXL = 'jxl';

    /** @var InputImage */
    protected $inputImage;

    /** @var string */
    protected $outputImageName;

    /** @var string */
    protected $outputTmpPath;

    /** @var string */
    protected $outputImageExtension;

    /** @var string */
    protected $outputImageContent;

    /** @var array<string, mixed> */
    protected $outputRateLimitHeaders = [];

    /** @var string */
    protected $commandString;

    /** @var array list of the supported output extensions */
    protected $allowedOutExtensions = [
                                            self::EXT_PNG,
                                            self::EXT_JPG,
                                            self::EXT_GIF,
                                            self::EXT_WEBP,
                                            self::EXT_AVIF,
                                            self::EXT_JXL
                                        ];

    /**
     * OutputImage constructor.
     *
     * @param InputImage $inputImage
     *
     * @throws InvalidArgumentException
     */
    public function __construct(InputImage $inputImage)
    {
        $this->inputImage = $inputImage;
        $this->generateFilesName();
        $this->outputImageExtension = $this->generateFileExtension();
        $this->outputTmpPath .= '.' . $this->outputImageExtension;
        if ($this->inputImage->isInputPdf()) {
            $this->outputImageName .= '-' . $inputImage->optionsBag()->get('pdf-page-number');
        }

        if ($this->isInputMovie()) {
            $time = VideoProcessor::validateTime($this->inputImage->optionsBag()->get('time'));
            $this->outputImageName .= '-' . VideoProcessor::timeFileSuffix($time);
        }
        $this->outputImageName .= '.' . $this->outputImageExtension;
    }

    /**
     * Is input file a movie
     *
     * @return bool
     */
    private function isInputMovie(): bool
    {
        if (strpos($this->inputImage->sourceFileMimeType(), 'video/') === false) {
            return false;
        }

        return true;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function extractKey(string $key): string
    {
        return $this->inputImage->extractKey($key);
    }

    /**
     * @return InputImage
     */
    public function getInputImage(): InputImage
    {
        return $this->inputImage;
    }

    /**
     * @return string
     */
    public function getOutputImageName(): string
    {
        return $this->outputImageName;
    }

    /**
     * @return string
     */
    public function getOutputTmpPath(): string
    {
        return $this->outputTmpPath;
    }

    /**
     * @return string
     */
    public function getOutputGeneratedPath(): string
    {
        return sprintf("%s%s", UPLOAD_DIR, $this->getOutputImageName());
    }

    /**
     * @param string $commandStr
     */
    public function setCommandString(string $commandStr)
    {
        $this->commandString = $commandStr;
    }

    /**
     * @return string
     */
    public function getCommandString(): string
    {
        return $this->commandString;
    }

    /**
     * @return string
     */
    public function getOutputImageContent(): string
    {
        return $this->outputImageContent;
    }

    /**
     * @param string $outputImageContent
     */
    public function attachOutputContent(string $outputImageContent)
    {
        $this->outputImageContent = $outputImageContent;
    }

    /**
     * @return string
     */
    public function getOutputImageExtension(): string
    {
        return $this->outputImageExtension;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOutputRateLimitHeaders(): array
    {
        return $this->outputRateLimitHeaders;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setOutputRateLimitHeader(string $key, mixed $value)
    {
        $this->outputRateLimitHeaders[$key] = $value;
    }
    /**
     * Remove Temporary file
     */
    public function removeOutputImage()
    {
        if (file_exists($this->getOutputTmpPath())) {
            unlink($this->getOutputTmpPath());
        }
    }

    /**
     * Remove Generated Image
     */
    public function removeGeneratedImage()
    {
        $generatedImage = $this->getOutputGeneratedPath();
        if (file_exists($generatedImage)) {
            unlink($generatedImage);
        }
    }

    /**
     * Generate files name + files path
     */
    protected function generateFilesName()
    {
        $hashedOptions = $this->inputImage->optionsBag();
        $this->outputImageName = $hashedOptions->hashedOptionsAsString($this->inputImage->sourceImagePath());
        $this->outputTmpPath = sprintf("%s%s", TMP_DIR, $this->outputImageName);

        if ($hashedOptions->get('refresh')) {
            $this->outputTmpPath .= uniqid("-", true);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function generateFileExtension()
    {
        $requestedOutput = $this->extractKey('output');

        if ($requestedOutput == self::EXT_AUTO && $this->isAvifBrowserSupported()) {
            return self::EXT_AVIF;
        }

        if ($requestedOutput == self::EXT_AUTO && $this->isJxlBrowserSupported()) {
            return self::EXT_JXL;
        }

        if ($requestedOutput == self::EXT_AUTO && $this->isWebPBrowserSupported()) {
            return self::EXT_WEBP;
        }

        if ($requestedOutput == self::EXT_INPUT) {
            return $this->extensionByMimeType($this->inputImage->sourceImageMimeType());
        }

        if ($requestedOutput == self::EXT_AUTO) {
            return self::EXT_JPG;
        }

        if (!in_array($requestedOutput, $this->allowedOutExtensions)) {
            throw new InvalidArgumentException("Invalid file output requested : " . $requestedOutput);
        }

        return $requestedOutput;
    }

    /**
     * given a mime-type this returns the extension associated to it
     *
     * @param string $mimeType mime-type
     *
     * @return string extension OR jpeg as default
     */
    protected function extensionByMimeType(string $mimeType): string
    {
        $mimeToExtensions = [
            InputImage::PNG_MIME_TYPE => self::EXT_PNG,
            InputImage::WEBP_MIME_TYPE => self::EXT_WEBP,
            InputImage::JPEG_MIME_TYPE => self::EXT_JPG,
            InputImage::GIF_MIME_TYPE => self::EXT_GIF,
            InputImage::AVIF_MIME_TYPE => self::EXT_AVIF,
            InputImage::JXL_MIME_TYPE => self::EXT_JXL,
            InputImage::PDF_MIME_TYPE => self::EXT_JPG,
        ];

        return array_key_exists($mimeType, $mimeToExtensions) ? $mimeToExtensions[$mimeType] : self::EXT_JPG;
    }

    /**
     * Return boolean stating if WebP image format is supported; following these conditions:
     *  - The request is specifically expecting a webP response, independent of the browser's capabilities
     *  OR:
     *  - The browser sent headers explicitly stating it supports webp (absolute requirement)
     *
     * @return bool
     */
    public function isOutputWebP(): bool
    {
        return $this->outputImageExtension == self::EXT_WEBP;
    }

    /**
     * @return bool
     */
    public function isOutputAvif(): bool
    {
        return $this->outputImageExtension == self::EXT_AVIF;
    }

    /**
     * @return bool
     */
    public function isOutputJxl(): bool
    {
        return $this->outputImageExtension == self::EXT_JXL;
    }

    /**
     * @return bool
     */
    public function isOutputGif(): bool
    {
        return $this->outputImageExtension == self::EXT_GIF;
    }

    /**
     * @return bool
     */
    public function isOutputPng(): bool
    {
        return $this->outputImageExtension == self::EXT_PNG;
    }

    /**
     * @return bool
     */
    public function isOutputMozJpeg(): bool
    {
        return (!$this->isOutputPng() || $this->outputImageExtension == self::EXT_JPG) &&
            (!$this->isOutputGif());
    }

    /**
     * This just checks if the browser requesting the asset explicitly supports WebP
     * @return boolean
     */
    public function isWebPBrowserSupported(): bool
    {
        return in_array(InputImage::WEBP_MIME_TYPE, Request::createFromGlobals()->getAcceptableContentTypes())
            &&
            $this->inputImage->optionsBag()->appParameters()->parameterByKey('enable_webp');
    }

    /**
     * This just checks if the browser requesting the asset explicitly supports Avif
     * @return boolean
     */
    public function isAvifBrowserSupported(): bool
    {
        return in_array(InputImage::AVIF_MIME_TYPE, Request::createFromGlobals()->getAcceptableContentTypes())
            &&
            $this->inputImage->optionsBag()->appParameters()->parameterByKey('enable_avif');
    }

    /**
     * This checks if the browser supports JPEG XL
     * @return boolean
     */
    public function isJxlBrowserSupported(): bool
    {
        return in_array(InputImage::JXL_MIME_TYPE, Request::createFromGlobals()->getAcceptableContentTypes())
            &&
            $this->inputImage->optionsBag()->appParameters()->parameterByKey('enable_jxl');
    }
}
