<?php

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/vendor/autoload.php';

use Core\Handler\ErrorHandler;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
/**
 * Create directories if they don't exist
 */
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}
if (!is_dir(TMP_DIR)) {
    mkdir(TMP_DIR, 0777, true);
}

/**
 * Create Silex application
 */
$app = new Application();

/**
 * Load application parameters
 */
$app['params'] = new \Core\Entity\AppParameters(__DIR__ . '/config/parameters.yml');

/**
 * Logging via Monolog settings
 */
$logLevel = $app['params']->parameterByKey('log_level');
$logger = new Core\Entity\Logger('flyimg', $logLevel);
$app['logger'] = $logger->getLogger();

$exceptionHandlerFunction = function (\Exception $e) use ($app): Response {
    $request = $app['request_stack']->getCurrentRequest();
    $app['logger']->error(
        '',
        [
            'error' => $e->getMessage(),
            'file' => $e->getFile() . ':' . $e->getLine(),
            'uri' => is_null($request) ? '' : $request->getUri(),
            'user_agent' => is_null($request) ? '' : $request->headers->get('User-Agent'),
            'http_client_ip' => is_null($request) ? '' : $request->headers->get('X-Forwarded-For'),
        ]
    );
    $htmlErrorHandler = new ErrorHandler($app['params']->parameterByKey('debug'));
    return new Response($htmlErrorHandler->render($e)->getAsString(), 500);
};

if (!isset($_ENV['env']) || $_ENV['env'] !== 'test') {
    $app->error($exceptionHandlerFunction);
}

/**
 * Routes
 */
$routesResolver = new \Core\Resolver\RoutesResolver();
$app['routes'] = $app->extend(
    'routes',
    function (RouteCollection $routes) use ($routesResolver) {
        return $routesResolver->parseRoutesFromYamlFile($routes, __DIR__ . '/config/routes.yml');
    }
);

/**
 * Register Storage provider
 */
switch ($app['params']->parameterByKey('storage_system')) {
    case 's3':
        $app->register(new \Core\StorageProvider\S3StorageProvider());
        break;
    case 'local':
    default:
        $app->register(new \Core\StorageProvider\LocalStorageProvider());
        break;
}

/**
 * Controller Resolver
 *
 * @param \Silex\Application $app
 *
 * @return \Core\Resolver\ControllerResolver
 */
$app['resolver'] = function (\Silex\Application $app) {
    return new \Core\Resolver\ControllerResolver($app, $app['logger']);
};

/**
 * Register Image Handler
 *
 * @param \Silex\Application $app
 *
 * @return \Core\Handler\ImageHandler
 */
$app['image.handler'] = function (\Silex\Application $app): \Core\Handler\ImageHandler {
    return new \Core\Handler\ImageHandler(
        $app['flysystems']['storage_handler'],
        $app['params']
    );
};

/**
 * To generate a hashed url when security key is enabled
 * Example usage: php app.php encrypt w_200,h_200,c_1/Rovinj-Croatia.jpg
 */
if (!empty($argv[1]) && !empty($argv[2]) && $argv[1] == 'encrypt') {
    printf("Hashed request: %s\n", $app['image.handler']->securityHandler()->encrypt($argv[2]));
    return;
}

/**
 * Debug configuration
 */
$app['debug'] = $app['params']->parameterByKey('debug');

return $app;
