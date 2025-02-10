<?php

namespace App\Controllers;

use App\Services\OrderService;
use InvalidArgumentException;
use Exception;
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
        $this->orderService = new OrderService();
        $this->logger = new OrderLogger(__DIR__ . '/../logs/order.log');
    }

    public function createOrder(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        try {
            $orderId = $this->orderService->createOrder($data);

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
        try {
            $currentUser = $request->getAttribute('user');

            $orders = $this->orderService->getAllOrders($currentUser);

            if (count($orders) > 0) {
                $response->getBody()->write(json_encode($orders));
                return $response->withHeader('Content-Type', 'application/json');
            }

            return $response->withStatus(404)->withBody(Utils::streamFor('Not Found'));
        } catch (Throwable $e) {
            $this->logger->logError('Get all orders failed', ['error' => $e->getMessage()]);
            return $response->withStatus(500)->withBody(Utils::streamFor('Internal Server Error'));
        }
    }

    public function getOrderById(Request $request, Response $response, array $args): Response
    {
        try {
            $orderId = $args['id'];
            $currentUser = $request->getAttribute('user');

            $order = $this->orderService->getOrderById($orderId);

            if ($order === null) {
                return $response->withStatus(404)->withBody(Utils::streamFor('Not Found'));
            }

            if (
                $currentUser['id'] === (int)$order['customer_id']
                || $currentUser['id'] === (int)$order['carrier_id']
                || $currentUser['id'] === 1
            ) {
                $response->getBody()->write(json_encode($order));
                return $response->withHeader('Content-Type', 'application/json');
            }
            return $response->withStatus(403)->withBody(Utils::streamFor('Access denied.'));
        } catch (Throwable $e) {
            $this->logger->logError('Get all orders failed', ['error' => $e->getMessage()]);
            return $response->withStatus(500)->withBody(Utils::streamFor('Internal Server Error'));
        }
    }

    public function updateOrderStatus(Request $request, Response $response, array $args): Response
    {
        $orderId = $args['id'];
        $statusId = $args['status'];
        $currentUser = $request->getAttribute('user');
        try {
            $this->orderService->updateOrderStatus($orderId, $statusId, $currentUser['id']);

            return $response->withStatus(200)->withBody(Utils::streamFor("Status for order {$orderId} updated"));
        } catch (InvalidArgumentException $e) {
            $this->logger->logError('Update status failed. Order not found', [
                'orderId' => $orderId,
                'error' => $e->getMessage()
            ]);
            return $response->withStatus(404)->withBody(Utils::streamFor("Order not found"));
        } catch (Throwable $e) {
            $this->logger->logError('Update status failed. Other', [
                'orderId' => $orderId,
                'error' => $e->getMessage()
            ]);
            return $response->withStatus(500)->withBody(Utils::streamFor("Internal Server Error: {$e->getMessage()}"));
        }
    }

    public function getOrderToWork(Request $request, Response $response, array $args): Response
    {
        $orderId = $args['id'];
        $currentUser = $request->getAttribute('user');
        try {
            $this->orderService->getOrderToWork($orderId ,$currentUser['id']);

            return $response->withStatus(200)->withBody(Utils::streamFor("Order {$orderId} get in work"));
        } catch (InvalidArgumentException $e) {
            $this->logger->logError('Get order to work failed', [
                'orderId' => $orderId,
                'error' => $e->getMessage()
            ]);
            return $response->withStatus(404)->withBody(Utils::streamFor($e->getMessage()));
        } catch (Throwable $e) {
            $this->logger->logError('Get order to work failed. Other', [
                'orderId' => $orderId,
                'error' => $e->getMessage()
            ]);
            return $response->withStatus(500)->withBody(Utils::streamFor('Internal Server Error'));
        }
    }

    public function deleteOrder(Request $request, Response $response, array $args): Response
    {
        try {
            $order = $this->orderService->getOrderById($args['id']);
            if ($order) {
                $this->orderService->deleteOrder($args['id']);
                return $response->withStatus(200)->withBody(Utils::streamFor('Order deleted'));
            }

            return $response->withStatus(404)->withBody(Utils::streamFor('Order Not Found'));
        } catch (Exception $e) {
            $this->logger->logError('Update order failed', ['error' => $e->getMessage()]);
            return $response->withStatus(500)->withBody(Utils::streamFor('Internal Server Error'));
        }
    }
}
