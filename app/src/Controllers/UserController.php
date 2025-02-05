<?php

namespace App\Controllers;

use App\Services\UserService;
use Exception;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Utils;
use App\Loggers\UserLogger;

class UserController extends AbstractController
{
    protected UserService $userService;

    protected UserLogger $logger;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();

        $this->logger = new UserLogger(__DIR__ . '/../logs/user.log');
    }

    public function registerUser(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            if ($data === null) {
                throw new InvalidArgumentException('Invalid input');
            }

            $userId = $this->userService->registerUser($data);

            return $this->prepareJsonResponse($response, ['user_id' => $userId]);
        } catch (InvalidArgumentException $e) {
            $this->logger->logError('User registration failed', ['error' => $e->getMessage()]);
            return $response->withStatus(404)->withBody(Utils::streamFor("Invalid input: {$e->getMessage()}"));
        } catch (Exception $e) {
            $this->logger->logError('User registration failed', ['error' => $e->getMessage()]);
            return $response->withStatus(500)->withBody(Utils::streamFor('Internal Server Error'));
        }
    }

    public function getUsers(Request $request, Response $response, $args): Response
    {
        try {
            $users = $this->userService->getAllUsers();

            if ($users !== false && count($users) > 0) {
                $response->getBody()->write(json_encode($users));
                return $response->withHeader('Content-Type', 'application/json');
            }

            return $response->withStatus(404)->withBody(Utils::streamFor('Not Found'));
        } catch (Exception $e) {
            $this->logger->logError('Get all users failed', ['error' => $e->getMessage()]);
            return $response->withStatus(500)->withBody(Utils::streamFor('Internal Server Error'));
        }
    }

    public function getUserById(Request $request, Response $response, $args): Response
    {
        try {
            $currentUser = $request->getAttribute('user');

            if ($currentUser['id'] !== (int) $args['id']) {
                return $response->withStatus(403)->withBody(Utils::streamFor('Access denied.'));
            }

            $user = $this->userService->getUserById($args['id']);

            if ($user) {
                $response->getBody()->write(json_encode($user));
                return $response->withHeader('Content-Type', 'application/json');
            }

            return $response->withStatus(404)->withBody(Utils::streamFor('Not Found'));
        } catch (Exception $e) {
            $this->logger->logError('Get user failed', ['error' => $e->getMessage()]);
            return $response->withStatus(500)->withBody(Utils::streamFor('Internal Server Error'));
        }
    }

    public function updateUser(Request $request, Response $response, $args): Response
    {
        try {
            $currentUser = $request->getAttribute('user');

            if ($currentUser['id'] !== (int) $args['id']) {
                return $response->withStatus(403)->withBody(Utils::streamFor('Access denied.'));
            }

            $data = $request->getParsedBody();
            $user = $this->userService->updateUser($args['id'], $data);

            if ($user) {
                $response->getBody()->write(json_encode($user));
                return $response->withHeader('Content-Type', 'application/json');
            }

            return $response->withStatus(204)->withBody(Utils::streamFor('Not Updated'));
        } catch (Exception $e) {
            $this->logger->logError('Update user failed', ['error' => $e->getMessage()]);
            return $response->withStatus(500)->withBody(Utils::streamFor('Internal Server Error'));
        }
    }

    public function deleteUser(Request $request, Response $response, $args): Response
    {
        try {
            $user = $this->userService->getUserById($args['id']);
            if ($user) {
                $this->userService->deleteUser($args['id']);
                return $response->withStatus(200)->withBody(Utils::streamFor('User deleted'));
            }

            return $response->withStatus(404)->withBody(Utils::streamFor('User Not Found'));
        } catch (Exception $e) {
            $this->logger->logError('Update user failed', ['error' => $e->getMessage()]);
            return $response->withStatus(500)->withBody(Utils::streamFor('Internal Server Error'));
        }
    }
}
