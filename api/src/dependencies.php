<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

require_once __DIR__ . '/../app/lib/modern_api_response.php';

$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        $path = $request->getUri()->getPath();
        if (strpos($path, '/api/') === 0 || $path === '/api') {
            $payload = [
                'success' => false,
                'error' => [
                    'code' => 500,
                    'message' => \App\Lib\ModernApiResponse::publicErrorMessage($exception->getMessage()),
                ],
            ];
            return \App\Lib\ModernApiResponse::json($response, $payload, 500);
        }

        return $c['defaultErrorHandler']($request, $response, $exception);
    };
};

$container['phpErrorHandler'] = $container['errorHandler'];
