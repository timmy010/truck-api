<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\UserController;
use App\Controllers\OrderController;
use App\Controllers\ProfileController;
use App\Controllers\AdminRolePermissionController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->addErrorMiddleware(true, false, false);
$app->addBodyParsingMiddleware();

$app->add(function (Request $request, RequestHandlerInterface $handler): Response {
    return $handler->handle($request)
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// User Routes
$userController = new UserController();
$app->post('/users', [$userController, 'registerUser']);
$app->get('/users/{id}', [$userController, 'getUserById']);
$app->put('/users/{id}', [$userController, 'updateUser']);
$app->delete('/users/{id}', [$userController, 'deleteUser']);

// Order Routes
$orderController = new OrderController();
$app->post('/orders', [$orderController, 'createOrder']);
$app->get('/orders', [$orderController, 'getAllOrders']);
$app->get('/users/{id}/orders', [$orderController, 'getUserOrders']);

// Profile Routes
$profileController = new ProfileController();
$app->post('/profiles', [$profileController, 'createProfile']);
$app->get('/profiles/{userId}', [$profileController, 'getProfileByUserId']);
$app->put('/profiles/{id}', [$profileController, 'updateProfile']);
$app->delete('/profiles/{id}', [$profileController, 'deleteProfile']);

// Admin Role Permission Routes
$adminRolePermissionController = new AdminRolePermissionController();
$app->post('/admin/permissions', [$adminRolePermissionController, 'assignPermission']);
$app->get('/admin/permissions', [$adminRolePermissionController, 'getAllPermissions']);
$app->get('/admin/permissions/{adminId}', [$adminRolePermissionController, 'getPermissionsByAdminId']);
$app->delete('/admin/permissions/{id}', [$adminRolePermissionController, 'deletePermission']);

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->run();
