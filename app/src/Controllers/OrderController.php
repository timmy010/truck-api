<?php

namespace App\Controllers;

use App\Services\OrderService;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Utils;
use App\Loggers\OrderLogger;
use Throwable;

class OrderController extends AbstractController
{
    protected OrderService $orderService;
    protected OrderLogger $logger;

    public function __construct()
    {
        parent::__construct();
        $this->orderService = new OrderService();
        $this->logger = new OrderLogger(__DIR__ . '/../logs/order.log');
    }

    public function createOrder(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $currentUser = $request->getAttribute('user');

        try {
            $orderId = $this->orderService->createOrder($data, $currentUser['id']);

            return $this->prepareJsonResponse($response, ['order_id' => $orderId]);
        } catch (InvalidArgumentException $e) {
            return $response->withStatus(404)->withBody(Utils::streamFor('Invalid input'));
        } catch (Throwable $e) {
            $this->logger->logError('Order created failed', ['error' => $e->getMessage()]);
            return $response->withStatus(500)->withBody(Utils::streamFor('Internal Server Error'));
        }
    }

    public function getAllOrders(Request $request, Response $response): Response
    {
        return $this->orderService->getAllOrders();
    }

    public function getUserOrders($userId)
    {
        return $this->orderService->getOrdersByUserId($userId);
    }

    public function updateOrder($id, $request)
    {
        $data = $request->getParsedBody();
        $this->orderService->update($id, $data);
    }

    public function deleteOrder($id)
    {
        $this->orderService->delete($id);
    }
}
