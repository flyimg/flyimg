<?php

namespace Tests\Core\Controller;

use Core\Controller\CoreController;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Tests\Core\BaseTest;

class CoreControllerTest extends BaseTest
{
    /**
     *
     */
    protected function tearDown(): void
    {
        unset($this->app);
    }

    /**
     *
     */
    public function testTemplateRender()
    {
        $coreController = new CoreController($this->app);
        $response = $coreController->render('Default/index');
        $this->assertInstanceOf('Core\Entity\Response', $response);
    }

    /**
     *
     */
    public function testNonExistingTemplateRender()
    {
        $this->expectException(FileNotFoundException::class);
        $coreController = new CoreController($this->app);
        $coreController->render('notExistFile');
    }

    /**
     * Creates the application.
     *
     * @return HttpKernelInterface
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../../../app.php';
        $app['debug'] = true;
        unset($app['exception_handler']);

        return $app;
    }
}
