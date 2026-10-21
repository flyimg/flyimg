<?php

namespace Tests\Core\Handler;

use Core\Exception\AccessDeniedException;
use Core\Exception\FileNotFoundException;
use Core\Exception\InvalidArgumentException;
use Core\Exception\RateLimitExceededException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Core\BaseTest;

/**
 * Tests the application-level error handler registered in app.php:
 * the HTTP status code of the response must follow the exception.
 */
class AppErrorHandlerTest extends BaseTest
{
    protected function tearDown(): void
    {
        unset($this->app);
    }

    private function handle(\Exception $e): Response
    {
        return $this->app['error.handler']($e);
    }

    public function testUnknownRouteReturns404()
    {
        $response = $this->handle(new NotFoundHttpException('No route found for "GET /.env"'));
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testMethodNotAllowedReturns405WithAllowHeader()
    {
        $response = $this->handle(new MethodNotAllowedHttpException(['GET'], 'No route found: Method Not Allowed'));
        $this->assertSame(405, $response->getStatusCode());
        $this->assertSame('GET', $response->headers->get('Allow'));
    }

    public function testFileNotFoundReturns404()
    {
        $response = $this->handle(new FileNotFoundException());
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testInvalidArgumentReturns400()
    {
        $response = $this->handle(new InvalidArgumentException());
        $this->assertSame(400, $response->getStatusCode());
    }

    public function testAccessDeniedReturns403()
    {
        $response = $this->handle(new AccessDeniedException());
        $this->assertSame(403, $response->getStatusCode());
    }

    public function testRateLimitReturns429WithHeaders()
    {
        $response = $this->handle(new RateLimitExceededException('Rate limit exceeded', 30, 100));
        $this->assertSame(429, $response->getStatusCode());
        $this->assertSame('100', $response->headers->get('X-RateLimit-Limit'));
        $this->assertSame('0', $response->headers->get('X-RateLimit-Remaining'));
        $this->assertSame('30', $response->headers->get('Retry-After'));
    }

    public function testGenericExceptionStillReturns500()
    {
        $response = $this->handle(new \RuntimeException('boom'));
        $this->assertSame(500, $response->getStatusCode());
    }

    public function testResponseIsHtml()
    {
        $response = $this->handle(new NotFoundHttpException('not found'));
        $this->assertStringStartsWith('text/html', (string) $response->headers->get('Content-Type'));
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * End-to-end: once the handler is registered on the kernel, a request
     * to an unknown path must get a 404, not a 500.
     */
    public function testUnknownPathThroughKernelReturns404()
    {
        $this->app->error($this->app['error.handler']);
        $client = static::createClient();
        $client->request('GET', '/this-route-does-not-exist');
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }

    public function testWrongMethodThroughKernelReturns405()
    {
        $this->app->error($this->app['error.handler']);
        $client = static::createClient();
        $client->request('DELETE', '/upload/w_100');
        $this->assertSame(405, $client->getResponse()->getStatusCode());
    }

    /**
     * Creates the application.
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../../../app.php';
        $app['debug'] = true;
        unset($app['exception_handler']);

        return $app;
    }
}
