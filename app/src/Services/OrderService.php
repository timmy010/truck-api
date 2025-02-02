<?php

namespace App\Services;

use App\Models\Order;
use InvalidArgumentException;

class OrderService
{
    private Order $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
    }

    public function createOrder(array $data): int
    {
        $this->validateOrderData($data);
        return $this->orderModel->create($data);
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

    private function validateOrderData(array $data): void
    {
        if (empty($data['customer_id'])) {
            throw new InvalidArgumentException('Customer ID is required.');
        }
        if (empty($data['pickup_location'])) {
            throw new InvalidArgumentException('Pickup location is required.');
        }
        if (empty($data['delivery_location'])) {
            throw new InvalidArgumentException('Delivery location is required.');
        }
        if (empty($data['freight_rate'])) {
            throw new InvalidArgumentException('Freight rate is required.');
        }
        if (empty($data['scheduled_loading_date'])) {
            throw new InvalidArgumentException('Scheduled loading date is required.');
        }
        if (empty($data['scheduled_unloading_date'])) {
            throw new InvalidArgumentException('Scheduled unloading date is required.');
        }
    }
}