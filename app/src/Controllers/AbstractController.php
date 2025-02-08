<?php

namespace App\Controllers;

use App\Services\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Utils;

abstract class AbstractController
{
    protected UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function prepareJsonResponse(Response $response, array $payload): Response
    {
        $payload = json_encode($payload);
        return $response->withBody(Utils::streamFor($payload));
    }
}
