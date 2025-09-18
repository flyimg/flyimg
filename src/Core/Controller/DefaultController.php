<?php

namespace Core\Controller;

use Core\Entity\Response;

/**
 * Class DefaultController
 * @package Core\Controller
 */
class DefaultController extends CoreController
{
    /**
     * Render the index page (or empty body when demo disabled).
     *
     * @return string
     */
    public function indexAction()
    {
        // Check if demo page is enabled
        if (!$this->app['params']->parameterByKey('demo_page_enabled', true)) {
            $this->response->setContent('');
            $this->response->setStatusCode(200);
            return $this->response;
        }

        return $this->render('Default/index');
    }

    /**
     * Process an image from a URL-like source and return the generated image.
     *
     * @param string $options  Image transformation options.
     * @param string $imageSrc Source image spec (URL, data URI, local path, s3 URI).
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function uploadAction(string $options, ?string $imageSrc = null): Response
    {
        $image = $this->imageHandler()->processImage($options, $imageSrc);

        $this->response->generateImageResponse($image);

        return $this->response;
    }

    /**
     * Accept binary or base64 payload in request body and process with options.
     *
     * @param string $options Image transformation options.
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function uploadStreamAction(string $options): Response
    {
        $request = $this->app['request_stack']->getCurrentRequest();

        $contentType = $request ? $request->headers->get('Content-Type', '') : '';
        $rawBody = $request ? $request->getContent() : '';

        // Determine source URL placeholder
        if (stripos($contentType, 'application/json') !== false) {
            $data = json_decode($rawBody, true);
            $source = isset($data['dataUri']) ? (string)$data['dataUri'] : '';
            if (!$source && isset($data['base64'])) {
                $source = 'data:application/octet-stream;base64,'
                    . $data['base64'];
            }
        } elseif (
            stripos($contentType, 'application/octet-stream') !== false
            || stripos($contentType, 'image/') !== false
        ) {
            $source = 'data:application/octet-stream;base64,'
                . base64_encode($rawBody);
        } else {
            // Fallback: try to treat body as a data URI or base64 string directly
            $trimmed = trim($rawBody);
            $source = (stripos($trimmed, 'data:') === 0)
                ? $trimmed
                : 'data:application/octet-stream;base64,' . $trimmed;
        }

        $image = $this->imageHandler()->processImage($options, $source);

        $this->response->generateImageResponse($image);

        return $this->response;
    }

    /**
     * Generate output file path for a transformed image.
     *
     * @param string $options  Image transformation options.
     * @param string $imageSrc Source image spec (URL, data URI, local path, s3 URI).
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function pathAction(string $options, ?string $imageSrc = null): Response
    {
        $image = $this->imageHandler()->processImage($options, $imageSrc);

        $this->response->generatePathResponse($image);

        return $this->response;
    }
}
