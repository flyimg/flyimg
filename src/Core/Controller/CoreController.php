<?php

namespace Core\Controller;

use Core\Entity\Response;
use Core\Handler\ImageHandler;
use Silex\Application;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class CoreController
{
    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->response = new Response(
            $this->app['image.handler'],
            $this->app['flysystems'],
            $this->app['params']
        );
    }

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @return ImageHandler
     */
    public function imageHandler(): ImageHandler
    {
        return $this->app['image.handler'];
    }

    /**
     * @param string $templateName
     *
     * @return Response
     */
    public function render(string $templateName): Response
    {
        $composerJsonContent = file_get_contents(ROOT_DIR . '/composer.json');
        $version = json_decode($composerJsonContent, true)['version'];
        $title = $this->app['params']->parameterByKey('home_page_title');

        $templateFullPath = ROOT_DIR . '/src/Core/Views/' . $templateName . '.html';

        if (!file_exists($templateFullPath)) {
            throw new FileNotFoundException('Template file note exist: ' . $templateFullPath);
        }

        ob_start();
        include($templateFullPath);
        $body = ob_get_contents();
        
        // Get current domain for dynamic URLs
        $request = $this->app['request_stack']->getCurrentRequest();
        $currentDomain = $request ? $request->getSchemeAndHttpHost() : '';
        
        $body = strtr($body, [
            '{$version}' => $version,
            '{$title}' => $title,
            '{$current_domain}' => $currentDomain
        ]);
        ob_end_clean();

        $this->response->setContent($body);

        return $this->response;
    }
}
