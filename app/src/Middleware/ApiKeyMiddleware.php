<?php

namespace App\Middleware;

use App\Services\AuthService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Psr7\Response;
use InvalidArgumentException;

class ApiKeyMiddleware implements MiddlewareInterface
{
    private AuthService $authService;
    private array $allowedRoles;
    private string $action;

    public function __construct(AuthService $authService, string $action)
    {
        $this->authService = $authService;
        $this->action = $action;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $apiKey = $request->getHeaderLine('Authorization');

        if (empty($apiKey)) {
            return $this->respondWithError('API key is required.', 403);
        }

        try {
            $user = $this->authService->authorize(trim($apiKey));
            $request = $request->withAttribute('user', $user);

            if (!$this->authService->isAuthorized($user, $this->action)) {
                return $this->respondWithError('Access denied. You do not have the required role.', 403);
            }
        } catch (InvalidArgumentException $e) {
            return $this->respondWithError($e->getMessage(), 403);
        }

        return $handler->handle($request);
    }

    private function respondWithError(string $message, int $status): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    }
}