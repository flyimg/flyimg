<?php

namespace Core\Entity\Image;

use Core\Entity\ImageMetaInfo;
use Core\Entity\OptionsBag;
use Core\Exception\AccessDeniedException;
use Core\Exception\AppException;
use Core\Exception\ServiceUnavailableException;
use Core\Exception\UnauthorizedException;
use Core\Exception\InvalidArgumentException;
use Core\Exception\ReadFileException;
use Core\Processor\VideoProcessor;
use Symfony\Component\HttpFoundation\Request;
use Core\Exception\FileNotFoundException;

/**
 * Class InputImage
 * @package Core\Entity
 */
class InputImage
{
    /**
     * Content TYPE constants.
     */
    public const WEBP_MIME_TYPE = 'image/webp';
    public const JPEG_MIME_TYPE = 'image/jpeg';
    public const PNG_MIME_TYPE = 'image/png';
    public const GIF_MIME_TYPE = 'image/gif';
    public const AVIF_MIME_TYPE = 'image/avif';
    public const PDF_MIME_TYPE = 'application/pdf';

    /**
     * Options selected for processing.
     *
     * @var OptionsBag
     */
    protected $optionsBag;

    /**
     * Original source image spec (URL, path, data URI, s3 URI).
     *
     * @var string
     */
    protected $sourceImageUrl;

    /**
     * Temporary local path to the downloaded source image.
     *
     * @var string
     */
    protected $sourceImagePath;

    /**
     * Detected MIME type of the source image.
     *
     * @var string
     */
    protected $sourceImageMimeType;

    /**
     * Parsed metadata for the source image.
     *
     * @var ImageMetaInfo
     */
    protected $sourceImageInfo;

    /**
     * Source file MIME type (videos handled specially).
     *
     * @var string
     */
    protected $sourceFileMimeType;

    /**
     * InputImage constructor.
     *
     * @param OptionsBag $optionsBag     Options for processing.
     * @param string     $sourceImageUrl Source image spec.
     *
     * @throws \Exception
     */
    public function __construct(OptionsBag $optionsBag, string $sourceImageUrl)
    {
        $this->optionsBag = $optionsBag;
        $this->sourceImageUrl = $sourceImageUrl;

        $this->sourceImagePath =  TMP_DIR . 'original-' . md5($this->sourceImageUrl);
        $this->saveToTemporaryFile();

        $this->sourceImageInfo = new ImageMetaInfo($this->sourceImagePath);

        // Store the source file mime type.
        $this->sourceFileMimeType = $this->sourceImageInfo->mimeType();

        // If the source file has a video mime type, generate a full resolution
        // image at the requested (or default) time duration and use that as
        // the source image.
        if (strpos($this->sourceFileMimeType, 'video/') !== false) {
            $videoProcessor = new VideoProcessor();
            $this->sourceImagePath = $videoProcessor->generateVideoSourceImage(
                $this->optionsBag,
                $this->sourceImagePath
            );
            $this->sourceImageInfo = new ImageMetaInfo($this->sourceImagePath);
        }
    }

    /**
     * Properly encode URL.
     *
     * @param string $url URL to encode.
     *
     * @return string
     */
    private function encodeUrl($url)
    {
        // URL scheme was not always set as the input scheme
        if (preg_match("!https?:/[a-zA-Z]!", $url)) {
            $url = preg_replace("!http(s?):/!", 'http$1://', $url);
        }
        $parsedUrl = parse_url($url);

        if (empty($parsedUrl['scheme']) || empty($parsedUrl['host'])) {
            return $url; // invalid URL
        }

        $user = isset($parsedUrl['user']) ? rawurlencode($parsedUrl['user']) : '';
        $pass = isset($parsedUrl['pass']) ? ':' . rawurlencode($parsedUrl['pass'])  : '';
        $auth = $user || $pass ? "$user$pass@" : '';

        $host = $parsedUrl['host'];
        $port = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';

        $path = '';
        if (isset($parsedUrl['path'])) {
            $path = implode('/', array_map('rawurlencode', explode('/', $parsedUrl['path'])));
        }
        $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';

        $result = $parsedUrl['scheme'] . '://'
            . $auth
            . $host
            . $port
            . $path
            . $query
            . $fragment;

        return $result;
    }

    /**
     * Save given image to temporary file and return the path.
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function saveToTemporaryFile()
    {
        // Support data URI (base64) and s3:// schemes in addition to http(s) and local files
        $originalSource = $this->sourceImageUrl;
        $this->sourceImageUrl = $this->encodeUrl($this->sourceImageUrl);

        $headers = $this->optionsBag->appParameters()->parameterByKey('header_extra_options');
        if (is_string($headers)) {
            $headers = [$headers];
        }
        $refresh = $this->optionsBag->get('refresh');
        $forwardRequestHeaders = (array)$this->optionsBag
            ->appParameters()
            ->parameterByKey('forward_request_headers', []);
        if (!empty($forwardRequestHeaders)) {
            $requestHeaders = Request::createFromGlobals()->headers;
            foreach ($forwardRequestHeaders as $name) {
                if ($requestHeaders->has($name)) {
                    $value = $requestHeaders->get($name);
                    $headers[] = "$name: $value";
                    if ('Authorization' === $name) {
                        $this->sourceImagePath .= md5($value);
                    }
                }
            }
        }

        if (file_exists($this->sourceImagePath) && !$refresh) {
            return;
        }

        // Handle data URI
        if (stripos($originalSource, 'data:') === 0) {
            $commaPos = strpos($originalSource, ',');
            if ($commaPos === false) {
                throw new InvalidArgumentException('Invalid data URI');
            }
            $meta = substr($originalSource, 0, $commaPos);
            $dataPart = substr($originalSource, $commaPos + 1);
            $isBase64 = stripos($meta, ';base64') !== false;
            $binary = $isBase64 ? base64_decode($dataPart) : urldecode($dataPart);
            if ($binary === false) {
                throw new InvalidArgumentException('Invalid base64 data');
            }
            file_put_contents($this->sourceImagePath, $binary);
            return;
        }

        // Handle s3://bucket/key
        if (stripos($originalSource, 's3://') === 0) {
            $withoutScheme = substr($originalSource, 5);
            $firstSlash = strpos($withoutScheme, '/');
            if ($firstSlash === false) {
                throw new InvalidArgumentException('Invalid S3 URI, expected s3://bucket/key');
            }
            $bucket = substr($withoutScheme, 0, $firstSlash);
            $key = substr($withoutScheme, $firstSlash + 1);

            // Build HTTP URL using configured endpoint pattern if present
            $params = $this->optionsBag->appParameters()->parameterByKey('aws_s3');
            if (!is_array($params) || empty($params)) {
                throw new AppException('S3 not configured');
            }
            $endpoint = (
                isset($params['endpoint']) && !empty($params['endpoint'])
            )
                ? sprintf($params['endpoint'], $bucket, $params['region'])
                : sprintf(
                    'https://%s.s3.%s.amazonaws.com/',
                    $bucket,
                    $params['region']
                );
            $signedUrl = $endpoint . $key;

            // Fetch via curl (public or signed by custom endpoint). If private, rely on Authorization forwarding.
            $headers = $this->optionsBag->appParameters()->parameterByKey('header_extra_options');
            if (is_string($headers)) {
                $headers = [$headers];
            }
            $forwardRequestHeaders = (array)$this->optionsBag
                ->appParameters()
                ->parameterByKey('forward_request_headers', []);
            if (!empty($forwardRequestHeaders)) {
                $requestHeaders = Request::createFromGlobals()->headers;
                foreach ($forwardRequestHeaders as $name) {
                    if ($requestHeaders->has($name)) {
                        $value = $requestHeaders->get($name);
                        $headers[] = "$name: $value";
                        if ('Authorization' === $name) {
                            $this->sourceImagePath .= md5($value);
                        }
                    }
                }
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $signedUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt(
                $ch,
                CURLOPT_TIMEOUT,
                $this->optionsBag->appParameters()->parameterByKey('source_image_request_timeout')
            );
            $imageData = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new ReadFileException('Curl error: ' . curl_error($ch));
            }
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpCode == 200 && $imageData !== false) {
                file_put_contents($this->sourceImagePath, $imageData);
                return;
            }
            switch ($httpCode) {
                case 400:
                    throw new InvalidArgumentException();
                case 401:
                    throw new UnauthorizedException();
                case 403:
                    throw new AccessDeniedException();
                case 404:
                    throw new FileNotFoundException();
                case 503:
                    throw new ServiceUnavailableException();
                default:
                    throw new AppException();
            }
        }

        if (filter_var($this->sourceImageUrl, FILTER_VALIDATE_URL)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->sourceImageUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Allow redirects
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  // Add custom headers
            curl_setopt(
                $ch,
                CURLOPT_TIMEOUT,
                $this->optionsBag->appParameters()
                     ->parameterByKey('source_image_request_timeout')
            );

            $imageData = curl_exec($ch);

            if (curl_errno($ch)) {
                throw new ReadFileException('Curl error: ' . curl_error($ch));
            } else {
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpCode == 200) {
                    file_put_contents($this->sourceImagePath, $imageData);
                } else {
                    switch ($httpCode) {
                        case 400:
                            throw new InvalidArgumentException();
                        case 401:
                            throw new UnauthorizedException();
                        case 403:
                            throw new AccessDeniedException();
                        case 404:
                            throw new FileNotFoundException();
                        case 503:
                            throw new ServiceUnavailableException();
                        default:
                            throw new AppException();
                    }
                }
            }
            curl_close($ch);
        } else {
            // Handle local file
            if (file_exists($this->sourceImageUrl)) {
                $imageData = file_get_contents($this->sourceImageUrl);
                file_put_contents($this->sourceImagePath, $imageData);
            } else {
                throw new ReadFileException('File ' . $this->sourceImageUrl . ' does not exist.');
            }
        }
    }

    /**
     * Extract and remove an option from the options bag.
     *
     * @param string $key Option key to extract.
     *
     * @return string
     */
    public function extractKey(string $key): string
    {
        $value = '';
        if ($this->optionsBag->has($key)) {
            $value = $this->optionsBag->get($key);
            $this->optionsBag->remove($key);
        }

        return is_null($value) ? '' : $value;
    }

    /**
     * Get the options bag.
     *
     * @return OptionsBag
     */
    public function optionsBag(): OptionsBag
    {
        return $this->optionsBag;
    }

    /**
     * Get original source image spec.
     *
     * @return string
     */
    public function sourceImageUrl(): string
    {
        return $this->sourceImageUrl;
    }

    /**
     * Get temporary local path for the source image.
     *
     * @return string
     */
    public function sourceImagePath(): string
    {
        return $this->sourceImagePath;
    }

    /**
     * Get detected MIME type for the source image.
     *
     * @return string
     */
    public function sourceImageMimeType(): string
    {
        if (isset($this->sourceImageMimeType)) {
            return $this->sourceImageMimeType;
        }

        $this->sourceImageMimeType = $this->sourceImageInfo->mimeType();

        return $this->sourceImageMimeType;
    }

    /**
     * Get parsed metadata for the source image.
     *
     * @return ImageMetaInfo
     */
    public function sourceImageInfo()
    {
        return $this->sourceImageInfo;
    }

    /**
     * Source file mime type.
     *
     * @return string
     */
    public function sourceFileMimeType(): string
    {
        return isset($this->sourceFileMimeType) ? $this->sourceFileMimeType : '';
    }

    /**
     * Check if the input file is a PDF.
     *
     * @return bool
     */
    public function isInputPdf(): bool
    {
        return $this->sourceImageMimeType() == self::PDF_MIME_TYPE;
    }

    /**
     * Check if the input file is a GIF.
     *
     * @return bool
     */
    public function isInputGif(): bool
    {
        return $this->sourceImageMimeType() == self::GIF_MIME_TYPE;
    }
}
