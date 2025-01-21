<?php

require 'vendor/autoload.php';

use App\Controllers\UserController;
use App\Controllers\OrderController;
use App\Controllers\ProfileController;
use App\Controllers\AdminRolePermissionController;

$app = new \Slim\App();

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

$app->run();
