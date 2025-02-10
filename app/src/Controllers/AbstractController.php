<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Utils;

abstract class AbstractController
{
    public function prepareJsonResponse(Response $response, array $payload): Response
    {
        $payload = json_encode($payload);
        return $response->withBody(Utils::streamFor($payload));
    }

    public function isUserParamIncorrect(Request $request, Response $response, $args): bool
    {
        $currentUser = $request->getAttribute('user');
        return $currentUser['id'] !== 1 && $currentUser['id'] !== (int)$args['id'];
    }
}
