<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Psr7\Utils;

abstract class AbstractController
{
    public function prepareJsonResponse(Response $response, array $payload): Response
    {
        $payload = json_encode($payload);
        return $response->withBody(Utils::streamFor($payload));
    }
}
