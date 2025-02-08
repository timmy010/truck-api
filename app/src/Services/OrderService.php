<?php

namespace App\Services;

use Exception;
use DateTime;
use Generator;
use InvalidArgumentException;
use App\Models\Order;
use App\Loggers\OrderLogger;

class OrderService
{
    private Order $orderModel;
    private CargoService $cargoService;
    protected OrderLogger $logger;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->cargoService = new CargoService();
        $this->logger = new OrderLogger(__DIR__ . '/../logs/order.log');
    }

    /**
     * @throws Exception
     */
    public function createOrder(array $data): int
    {
        $orderData = [
            'customer_id' => $data['customer_id'],
            'pickup_location' => $data['pickup_location'],
            'delivery_location' => $data['delivery_location'],
            'freight_rate' => $data['freight_rate'],
            'scheduled_loading_date' => (new DateTime($data['scheduled_loading_date']))->format('Y-m-d'),
            'scheduled_unloading_date' => (new DateTime($data['scheduled_unloading_date']))->format('Y-m-d'),
            'status' => 1
        ];

        $orderId = $this->orderModel->create($orderData);

        try {
            foreach ($this->getCargos($data['cargos']) as $cargo) {
                $cargo['order_id'] = $orderId;
                $this->cargoService->createCargo($cargo);
            }
        } catch (Exception $e) {
            $this->orderModel->delete($orderId);
            throw new Exception('Cargo creation failed: ' . $e->getMessage());
        }

        return $orderId;
    }

    public function getAllOrders(array $user): array
    {
        return match ($user['role']) {
            'customer' => $this->orderModel->getAllByFilter(['customer_id' => $user['id']]),
            'carrier' => $this->orderModel->getAllByFilter(['carrier_id' => $user['id']]),
            default => $this->orderModel->getAll(),
        };
    }

    public function getOrderById(int $id): ?array
    {
        return $this->orderModel->getById($id);
    }

    /**
     * @throws Exception
     */
    public function updateOrderStatus(int $orderId, int $statusId, int $userId): bool
    {
        $order = $this->getOrderById($orderId);
        if ($order === null) {
            throw new InvalidArgumentException('Order not found');
        }

        if ($order['carrier_id'] !== $userId) {
            throw new Exception('Access denied');
        }

        return $this->updateOrder($orderId, ['status' => $statusId]);
    }

    public function getOrderToWork(int $orderId, int $userId): bool
    {
        $order = $this->getOrderById($orderId);
        if ($order === null) {
            throw new InvalidArgumentException('Order not found');
        }

        return $this->updateOrder($orderId, [
            'status' => 2,
            'carrier_id' => $userId
        ]);
    }

    public function updateOrder(int $id, array $data): bool
    {
        return $this->orderModel->patch($id, $data);
    }

    public function deleteOrder(int $id): bool
    {
        return $this->orderModel->delete($id);
    }

    private function getCargos(array $cargos): Generator
    {
        foreach ($cargos as $cargo) {
            yield $cargo;
        }
    }
}