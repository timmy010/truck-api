<?php

namespace App\Services;

use Exception;
use DateTime;
use Generator;
use App\Models\Order;
use InvalidArgumentException;
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
    public function createOrder(array $data, int $customerId): int
    {
        $orderData = [
            'customer_id' => $customerId,
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

    private function getCargos(array $cargos): Generator
    {
        foreach ($cargos as $cargo) {
            yield $cargo;
        }
    }

    public function getAllOrders(): array
    {
        return $this->orderModel->getAll();
    }

    public function getOrderById(int $id): ?array
    {
        return $this->orderModel->getById($id);
    }

    public function updateOrder(int $id, array $data): bool
    {
        if (!isset($data['customer_id'])) {
            throw new InvalidArgumentException('Customer ID is required for update.');
        }
        return $this->orderModel->update($id, $data);
    }

    public function deleteOrder(int $id): bool
    {
        return $this->orderModel->delete($id);
    }
}