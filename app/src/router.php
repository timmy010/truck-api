<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\UserController;
use App\Controllers\OrderController;
use App\Middleware\ApiKeyMiddleware;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Psr7\Response as SlimResponse;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->add(function (Request $request, RequestHandlerInterface $handler): Response {
    return $handler->handle($request)
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

$app->get('/', function (Request $request, Response $response): Response {
    return $response->withHeader('Content-Type', 'text/html')
        ->withBody(Utils::streamFor(file_get_contents(__DIR__ . '/../public/index.php')));
});

$app->get('/assets/{file}', function ($request, $response, $args) {
    $filePath = __DIR__ . '/../public/assets/' . $args['file'];
    if (file_exists($filePath)) {
        $mimeType = mime_content_type($filePath);
        return $response->withHeader('Content-Type', $mimeType)
            ->withBody(Utils::streamFor(file_get_contents($filePath)));
    }
    return $response->withStatus(404)->write('File not found');
});

$app->group('/api/v1', function (RouteCollectorProxy $group) {
    $group->group('/users', function (RouteCollectorProxy $group) {
        $userController = new UserController();
        $group->post('', [$userController, 'registerUser'])->add(new ApiKeyMiddleware('registerUser'));
        $group->get('', [$userController, 'getUsers'])->add(new ApiKeyMiddleware('getUsers'));
        $group->get('/{id}', [$userController, 'getUserById'])->add(new ApiKeyMiddleware('getUserById'));
        $group->put('/{id}', [$userController, 'updateUser'])->add(new ApiKeyMiddleware('updateUser'));
        $group->delete('/{id}', [$userController, 'deleteUser'])->add(new ApiKeyMiddleware('deleteUser'));
    });

    $group->group('/orders', function (RouteCollectorProxy $group) {
        $orderController = new OrderController();
        $group->post('', [$orderController, 'createOrder'])->add(new ApiKeyMiddleware('createOrder'));
        $group->get('', [$orderController, 'getAllOrders'])->add(new ApiKeyMiddleware('getAllOrders'));
        $group->get('/{id}', [$orderController, 'getOrderById'])->add(new ApiKeyMiddleware('getOrderById'));
        $group->put('/{id}/{status}', [$orderController, 'updateOrderStatus'])->add(new ApiKeyMiddleware('updateOrderStatus'));
        $group->put('/{id}', [$orderController, 'getOrderToWork'])->add(new ApiKeyMiddleware('getOrderToWork'));
        $group->delete('/{id}', [$orderController, 'deleteOrder'])->add(new ApiKeyMiddleware('deleteOrder'));
    });
});

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$errorMiddleware->setErrorHandler(
    HttpNotFoundException::class,
    function (Request $request, Throwable $exception, bool $displayErrorDetails) {
        $response = new SlimResponse();
        $response->getBody()->write('404 NOT FOUND');
        return $response->withStatus(404);
    }
);

$app->run();
