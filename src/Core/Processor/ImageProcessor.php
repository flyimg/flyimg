<?php

namespace Core\Processor;

use Core\Entity\Command;
use Core\Entity\Image\OutputImage;
use Core\Entity\ImageMetaInfo;

/**
 * Class ImageProcessor
 * @package Core\Processor
 *
 * In this class we separate requests in 3 types
 *  - Simple resize geometry, resolved with -thumbnail
 *      -- Only width or height
 *      -- Only width and height
 *  - Cropping
 *
 *  - Advanced requests
 */
class ImageProcessor extends Processor
{
    /**
     * Basic source image info parsed from IM identify command
     * @var ImageMetaInfo
     */
    protected $sourceImageInfo;

    /**
     * OptionsBag from the request
     * @var \Core\Entity\OptionsBag
     */
    protected $options;

    /**
     * stores/caches image related data like dimensions
     * @var array
     */
    protected $geometry;

    /**
     * Save new FileName based on source file and list of options
     *
     * @param OutputImage $outputImage
     *
     * @return OutputImage
     * @throws \Exception
     */
    public function processNewImage(OutputImage $outputImage): OutputImage
    {
        $this->sourceImageInfo = $outputImage->getInputImage()->sourceImageInfo();
        $this->options = $outputImage->getInputImage()->optionsBag();
        $command = $this->generateCommand($outputImage);
        $this->logger->info('sourceImageUrl: ' . $outputImage->getInputImage()->sourceImageUrl());
        $this->logger->info('ImageProcessorCommand: ' . $command);
        $this->execute($command);

        return $outputImage;
    }

    /**
     * Generate Command string bases on options
     *
     * @param OutputImage $outputImage
     *
     * @return Command
     */
    protected function generateCommand(OutputImage $outputImage): Command
    {
        $command = new Command(self::IM_CONVERT_COMMAND);
        $isPdf = $outputImage->getInputImage()->isInputPdf();

        $pdfPageNo = $isPdf ?
            '[' . $outputImage->extractKey('pdf-page-number') - 1 . ']' :
            '';

        if ($isPdf && !empty($this->options->getOption('density'))) {
            $command->addArgument('-density', $this->options->getOption('density'));
        }

        $command->addArgument($this->getSourceImagePath($outputImage) . $pdfPageNo);

        if (!empty($this->options->getOption('gravity'))) {
            $command->addArgument('-gravity', $this->options->getOption('gravity'));
        }

        $command->addArgument($this->calculateSize())
            ->addArgument('-colorspace', $outputImage->extractKey('colorspace'));

        if (!empty($outputImage->extractKey('monochrome'))) {
            $command->addArgument('-monochrome');
        }

        $command->addArgument($this->checkForwardedOptions());
        $command->addArgument($this->addTextAnnotation());

        //Strip is added internally by ImageMagick when using -thumbnail
        $withoutValueOptions = ['strip', 'auto-orient'];

        foreach ($withoutValueOptions as $option) {
            if (!empty($this->options->getOption($option))) {
                $command->addArgument("-" . $option);
            }
        }

        if (!empty($outputImage->extractKey('thread'))) {
            $command->addArgument("-limit thread", $outputImage->extractKey('thread'));
        }

        $command->addArgument($this->calculateQuality($outputImage));

        $outputImage->setCommandString($command);

        return $command;
    }

    /**
     * @return string
     */
    protected function calculateSize(): string
    {
        $width = $this->options->getOption('width');
        $height = $this->options->getOption('height');
        $crop = $this->options->getOption('crop');
        $size = '';

        // if width AND height AND crop are defined we need check further to define the type of operation we will do
        if ($width && $height && $crop) {
            $size = $this->generateCropSize();
        } elseif ($width || $height) {
            $size = $this->generateSimpleSize();
        }

        return $size;
    }

    /**
     * IF we crop we need to know if the source image is bigger or smaller than the target size.
     * @return string command section for the resizing.
     *
     * note: The shorthand version of resize to fill space will always fill the space even if image is bigger
     */
    protected function generateCropSize(): string
    {
        $this->updateTargetDimensions();
        $command = [];
        $command[] = $this->getResizeOperator();
        $command[] = $this->getDimensions() . '^';
        $command[] = '-background none';
        $command[] = '-extent ' . $this->getDimensions();

        return implode(' ', $command);
    }

    /**
     * IF we simply resize we let IM deal with the calculations
     * @return string command section for the resizing.
     */
    protected function generateSimpleSize(): string
    {
        $command = [];
        $command[] = $this->getResizeOperator();

        $dimensions = $this->getDimensions();
        $preserveAspectRatio = $this->options->getOption('preserve-aspect-ratio');
        $preserveNaturalSize = $this->options->getOption('preserve-natural-size');

        if (!$preserveAspectRatio || $preserveAspectRatio === '0' || $preserveAspectRatio === 0) {
            // par=0 → stretch: ignore aspect ratio and fill exactly w×h
            $dimensions .= '!';
        } elseif ($preserveNaturalSize && ($preserveNaturalSize === '1' || $preserveNaturalSize === 1)) {
            // default behavior: don't upscale small images
            $dimensions .= escapeshellarg('>');
        }

        $command[] = $dimensions;

        return implode(' ', $command);
    }

    /**
     * Gets the source image path and adds any extra modifiers to the string
     *
     * @param OutputImage $outputImage
     *
     * @return string                   Path of the source file to be used in the conversion command
     */
    protected function getSourceImagePath(OutputImage $outputImage): string
    {
        $tmpFileName = $this->sourceImageInfo->path();

        //Check the source image is gif
        if ($outputImage->getInputImage()->isInputGif()) {
            $frame = $this->options->getOption('gif-frame');

            // set the frame if the output image is not gif (to get ony one  frame)
            if ($outputImage->getOutputImageExtension() !== OutputImage::EXT_GIF) {
                $tmpFileName .= '[' . escapeshellarg($frame) . ']';
            }
        }

        return $tmpFileName;
    }

    /**
     * Apply the Quality processor based on options
     *
     * @param OutputImage $outputImage
     *
     * @return string
     */
    protected function calculateQuality(OutputImage $outputImage): string
    {
        $quality = $outputImage->extractKey('quality');

        if ($outputImage->isOutputAvif()) {
            $heicSpeed = $outputImage->getInputImage()->optionsBag()->appParameters()->parameterByKey('heic_speed');
            $parameter = "-define heic:speed=" . $heicSpeed . " -quality " . escapeshellarg($quality) .
                " " . escapeshellarg($outputImage->getOutputTmpPath());
        } elseif ($outputImage->isOutputJxl()) {
            // Native JPEG XL via ImageMagick
            $jxlEffort = $outputImage->extractKey('jxl-effort');
            $jxlDecodingSpeed = $outputImage->extractKey('jxl-decoding-speed');
            $jxlDecodingSpeedCmd = $jxlDecodingSpeed ? " -define jxl:decoding-speed=" . (int)$jxlDecodingSpeed : "";
            // map quality 0-100 into jxl distance if IM expects it; IM supports -quality for JXL as of recent versions
            $parameter = "-define jxl:effort=" . (int)$jxlEffort .
                $jxlDecodingSpeedCmd .
                " -quality " . escapeshellarg($quality) .
                " " . escapeshellarg($outputImage->getOutputTmpPath());
        } elseif (is_executable(self::CWEBP_COMMAND) && $outputImage->isOutputWebP()) {
            $lossLess = $outputImage->extractKey('webp-lossless') ? 'true' : 'false';
            $webpThreads = $outputImage->getInputImage()->optionsBag()->appParameters()->parameterByKey('webp_threads');
            $webpMethod = max(0, min(6, (int) $outputImage->extractKey('webp-method')));
            $parameter = "-quality " . escapeshellarg($quality) .
                " -define webp:thread-level=" . $webpThreads .
                " -define webp:method=" . escapeshellarg((string) $webpMethod) .
                " -define webp:lossless=" . $lossLess .
                " " . escapeshellarg($outputImage->getOutputTmpPath());
        } elseif (is_executable(self::MOZJPEG_COMMAND) && $outputImage->isOutputMozJpeg()) {
            /** MozJpeg compression */
            // Force truecolor TGA: MozJPEG rejects paletted TGA with 32-bit colormap (PNG8 + tRNS).
            $parameter = "-type TrueColor TGA:- | " . escapeshellarg(self::MOZJPEG_COMMAND)
                . " -quality " . escapeshellarg($quality)
                . " -outfile " . escapeshellarg($outputImage->getOutputTmpPath())
                . " -targa";
        } else {
            /** default ImageMagick compression */
            $parameter = "-quality " . escapeshellarg($quality) .
                " " . escapeshellarg($outputImage->getOutputTmpPath());
        }

        return $parameter;
    }

    /**
     * This works as a cache for calculations
     *
     * @param string $key the key with wich we store a calculated value
     * @param callable $calculate function that returns a calculated value
     *
     * @return string|mixed
     */
    protected function getGeometry($key, $calculate): string
    {
        if (isset($this->geometry[$key])) {
            return $this->geometry[$key];
        }
        $this->geometry[$key] = call_user_func($calculate);

        return $this->geometry[$key];
    }

    /**
     * @return string
     */
    protected function getDimensions(): string
    {
        return $this->getGeometry(
            'dimensions',
            function () {
                $targetWidth = $this->options->getOption('width');
                $targetHeight = $this->options->getOption('height');

                $dimensions = '';
                if ($targetWidth) {
                    $dimensions .= (string)escapeshellarg($targetWidth);
                }
                if ($targetHeight) {
                    $dimensions .= (string)'x' . escapeshellarg($targetHeight);
                }

                return $dimensions;
            }
        );
    }

    /**
     * @return string
     */
    protected function getResizeOperator(): string
    {
        return $this->getGeometry(
            'resizeOperator',
            function () {
                return $this->options->getOption('resize') ? '-resize' : '-thumbnail';
            }
        );
    }

    /**
     *
     */
    protected function updateTargetDimensions(): void
    {
        if (!$this->options->getOption('preserve-natural-size')) {
            return;
        }

        $targetWidth = $this->options->getOption('width');
        $targetHeight = $this->options->getOption('height');
        $originalWidth = $this->sourceImageInfo->dimensions()['width'];
        $originalHeight = $this->sourceImageInfo->dimensions()['height'];

        if ($originalWidth < $targetWidth) {
            $this->options->setOption('width', $originalWidth);
        }

        if ($originalHeight < $targetHeight) {
            $this->options->setOption('height', $originalHeight);
        }
    }

    /**
     * Check if one of the defined options are passed via the URL
     * And apply the value of it
     *
     * @return string
     */
    private function checkForwardedOptions(): string
    {
        $command = new Command("");
        $forwardedOptions = ['background', 'rotate', 'unsharp', 'sharpen', 'blur', 'filter'];

        foreach ($forwardedOptions as $option) {
            if (!empty($this->options->getOption($option))) {
                $command->addArgument("-" . $option, $this->options->getOption($option));
                if ($option == 'background') {
                    $command->addArgument("-alpha remove -alpha off");
                }
            }
        }

        return $command;
    }

    /**
     * Add text annotation to the image
     *
     * @return string
     */
    private function addTextAnnotation(): string
    {
        $command = new Command("");

        if (!empty($this->options->getOption('text'))) {
            if (!empty($this->options->getOption('text-bg'))) {
                $command->addArgument("-undercolor", $this->options->getOption('text-bg'));
            }
            $command->addArgument("-fill", $this->options->getOption('text-color'))
                ->addArgument("-pointsize", $this->options->getOption('text-size'))
                ->addArgument("-annotate +0+0 ", $this->options->getOption('text'));
        }

        return $command;
    }
}
