<?php

namespace App\Controllers;

use App\Services\OrderService;

class OrderController
{
    protected OrderService $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    public function createOrder($request)
    {
        $data = $request->getParsedBody();
        $this->orderService->createOrder($data);
    }

    public function getAllOrders()
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
