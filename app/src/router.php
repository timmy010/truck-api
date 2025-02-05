<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\UserController;
use App\Controllers\OrderController;
use App\Controllers\ProfileController;
use App\Middleware\ApiKeyMiddleware;
use App\Services\AuthService;
use App\Services\UserService;
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

$app->group('/api/v1', function (RouteCollectorProxy $group) {
    $group->group('/users', function (RouteCollectorProxy $group) {
        $userController = new UserController();
        $group->post('', [$userController, 'registerUser'])->add(new ApiKeyMiddleware('registerUser'));
        $group->get('', [$userController, 'getUsers'])->add(new ApiKeyMiddleware('getUsers'));
        $group->get('/{id}', [$userController, 'getUserById'])->add(new ApiKeyMiddleware('getUserById'));
        $group->put('/{id}', [$userController, 'updateUser'])->add(new ApiKeyMiddleware('updateUser'));
        $group->delete('/{id}', [$userController, 'deleteUser'])->add(new ApiKeyMiddleware('deleteUser'));
    });

    $group->group('/profiles', function (RouteCollectorProxy $group) {
        $profileController = new ProfileController();
        $group->post('', [$profileController, 'createProfile'])->add(new ApiKeyMiddleware('createProfile'));
        $group->get('', [$profileController, 'getProfiles'])->add(new ApiKeyMiddleware('getProfiles'));
        $group->get('/{userId}', [$profileController, 'getProfileByUserId'])->add(new ApiKeyMiddleware('getProfileByUserId'));
        $group->put('/{id}', [$profileController, 'updateProfile'])->add(new ApiKeyMiddleware('updateProfile'));
        $group->delete('/{id}', [$profileController, 'deleteProfile'])->add(new ApiKeyMiddleware('deleteProfile'));
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
