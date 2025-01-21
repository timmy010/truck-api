<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    protected Order $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
    }

    public function createOrder(array $data)
    {
        $this->orderModel->create($data);
    }

    public function getAllOrders()
    {
        return $this->orderModel->all();
    }

    public function getOrderById(int $id)
    {
        return $this->orderModel->getById($id);
    }

    public function updateOrder(int $id, array $data)
    {
        $this->orderModel->update($id, $data);
    }

    public function deleteOrder(int $id)
    {
        $this->orderModel->delete($id);
    }

    public function getOrdersByUserId(int $userId)
    {
        return $this->orderModel->getOrdersByUserId($userId);
    }
}
