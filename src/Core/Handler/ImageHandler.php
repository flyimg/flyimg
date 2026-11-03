<?php

namespace Core\Handler;

use Core\Entity\AppParameters;
use Core\Entity\Image\InputImage;
use Core\Entity\Image\OutputImage;
use Core\Entity\OptionsBag;
use Core\Processor\ExtractProcessor;
use Core\Processor\FaceDetectProcessor;
use Core\Processor\ImageProcessor;
use Core\Processor\SmartCropProcessor;
use League\Flysystem\Filesystem;
use Core\Handler\RateLimitHandler;
use Core\RateLimiter\RateLimiterFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ImageHandler
 * @package Core\Service
 */
class ImageHandler
{
    /** @var ImageProcessor */
    protected $imageProcessor;

    /** @var FaceDetectProcessor */
    protected $faceDetectProcessor;

    /** @var ExtractProcessor */
    protected $extractProcessor;

    /** @var SmartCropProcessor */
    protected $smartCropProcessor;

    /** @var SecurityHandler */
    protected $securityHandler;

    /** @var RateLimitHandler */
    protected $rateLimitHandler;

    /** @var Filesystem */
    protected $filesystem;

    /** @var AppParameters */
    protected $appParameters;

    /**
     * ImageHandler constructor.
     *
     * @param Filesystem $filesystem
     * @param AppParameters $appParameters
     */
    public function __construct(Filesystem $filesystem, AppParameters $appParameters)
    {
        $this->filesystem = $filesystem;
        $this->appParameters = $appParameters;

        $this->imageProcessor = new ImageProcessor();
        $this->faceDetectProcessor = new FaceDetectProcessor();
        $this->extractProcessor = new ExtractProcessor();
        $this->smartCropProcessor = new SmartCropProcessor();
        $this->securityHandler = new SecurityHandler($appParameters);

        if ($this->appParameters->parameterByKey('rate_limit_enabled')) {
            $rateLimiter = RateLimiterFactory::create($appParameters);
            $this->rateLimitHandler = new RateLimitHandler($rateLimiter, $appParameters);
        }
    }

    /**
     * @return ImageProcessor
     */
    public function imageProcessor(): ImageProcessor
    {
        return $this->imageProcessor;
    }

    /**
     * @return AppParameters
     */
    public function appParameters(): AppParameters
    {
        return $this->appParameters;
    }

    /**
     * @return SecurityHandler
     */
    public function securityHandler(): SecurityHandler
    {
        return $this->securityHandler;
    }


    /**
     * @param string $options
     * @param string $imageSrc
     *
     * @return OutputImage
     * @throws \Exception
     */
    public function processImage(string $options, string $imageSrc): OutputImage
    {
        $imageSrc = preg_replace('#^(https?):/([^/])#', '$1://$2', $imageSrc);
        [$options, $imageSrc] = $this->securityHandler->checkSecurityHash($options, $imageSrc);
        $this->securityHandler->checkRestrictedDomains($imageSrc);

        $request = Request::createFromGlobals();
        $optionsBag = new OptionsBag($this->appParameters, $options);
        $inputImage = new InputImage($optionsBag, $imageSrc);
        $outputImage = new OutputImage($inputImage);

        try {
            // Refresh overwrites in place (processNewImage -> filesystem->write)
            // so concurrent readers keep seeing the previous file until replace.
            if ($optionsBag->get('refresh') || !$this->filesystem->has($outputImage->getOutputImageName())) {
                // Check if rate limiting is enabled
                if ($this->appParameters->parameterByKey('rate_limit_enabled')) {
                    $headers = $this->rateLimitHandler->checkRateLimit($request);
                    // Attach the rate limit headers to the output image
                    foreach ($headers as $key => $value) {
                        $outputImage->setOutputRateLimitHeader($key, $value);
                    }
                }
                // Process the image
                $outputImage = $this->processNewImage($outputImage);
            }
            // Attach the output content to the output image
            $outputImage->attachOutputContent($this->filesystem->read($outputImage->getOutputImageName()));
        } catch (\Exception $e) {
            $outputImage->removeOutputImage();
            throw $e;
        }

        return $outputImage;
    }

    /**
     * @param OutputImage $outputImage
     *
     * @throws \Exception
     */
    protected function smartCropProcess(OutputImage $outputImage): void
    {
        $smartCrop = $outputImage->extractKey('smart-crop');

        if ($smartCrop && !$outputImage->isOutputGif()) {
            $this->smartCropProcessor->smartCrop($outputImage);
        }
    }

    /**
     * @param OutputImage $outputImage
     *
     * @throws \Exception
     */
    protected function faceDetectionProcess(OutputImage $outputImage): void
    {
        $faceCrop = $outputImage->extractKey('face-crop');
        $faceCropPosition = $outputImage->extractKey('face-crop-position');
        $faceBlur = $outputImage->extractKey('face-blur');

        if ($faceBlur && !$outputImage->isOutputGif()) {
            $this->faceDetectProcessor->blurFaces($outputImage);
        }

        if ($faceCrop && !$outputImage->isOutputGif()) {
            $this->faceDetectProcessor->cropFaces($outputImage, $faceCropPosition);
        }
    }

    /**
     * @param OutputImage $outputImage
     *
     * @return OutputImage
     * @throws \Exception
     */
    protected function processNewImage(OutputImage $outputImage): OutputImage
    {
        //Check Extract options
        if ($outputImage->extractKey('extract')) {
            $this->extractProcess($outputImage);
        }

        $outputImage = $this->imageProcessor()->processNewImage($outputImage);

        // Check if Smart Crop enabled
        $this->smartCropProcess($outputImage);

        //Check Face Detection options
        $this->faceDetectionProcess($outputImage);

        $tmpPath = $outputImage->getOutputTmpPath();

        // Retry mechanism for concurrent access
        $maxRetries = 3;
        $retryDelay = 100000; // 100ms in microseconds

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            if (!file_exists($tmpPath)) {
                if ($attempt < $maxRetries) {
                    usleep($retryDelay);
                    continue;
                }
                throw new \Exception("Temporary file not found after $maxRetries attempts: " . $tmpPath);
            }

            $handle = fopen($tmpPath, 'r');
            if ($handle === false) {
                if ($attempt < $maxRetries) {
                    usleep($retryDelay);
                    continue;
                }
                throw new \Exception("Failed to open temporary file after $maxRetries attempts: " . $tmpPath);
            }

            $content = stream_get_contents($handle);
            fclose($handle);

            if ($content === false) {
                if ($attempt < $maxRetries) {
                    usleep($retryDelay);
                    continue;
                }
                throw new \Exception("Failed to read from temporary file after $maxRetries attempts: " . $tmpPath);
            }

            // Success - break out of retry loop
            break;
        }

        $this->filesystem->write(
            $outputImage->getOutputImageName(),
            $content
        );

        return $outputImage;
    }

    /**
     * @param OutputImage $outputImage
     *
     * @throws \Exception
     */
    protected function extractProcess(OutputImage $outputImage): void
    {
        $this->extractProcessor->extract($outputImage);
    }

    /**
     * @param OutputImage $outputImage
     *
     * @return string
     */
    public function responseContentType(OutputImage $outputImage): string
    {
        if ($outputImage->getOutputImageExtension() == OutputImage::EXT_JXL) {
            return InputImage::JXL_MIME_TYPE;
        }
        if ($outputImage->getOutputImageExtension() == OutputImage::EXT_AVIF) {
            return InputImage::AVIF_MIME_TYPE;
        }
        if ($outputImage->getOutputImageExtension() == OutputImage::EXT_WEBP) {
            return InputImage::WEBP_MIME_TYPE;
        }
        if ($outputImage->getOutputImageExtension() == OutputImage::EXT_PNG) {
            return InputImage::PNG_MIME_TYPE;
        }
        if ($outputImage->getOutputImageExtension() == OutputImage::EXT_GIF) {
            return InputImage::GIF_MIME_TYPE;
        }

        return InputImage::JPEG_MIME_TYPE;
    }
}
