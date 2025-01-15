<?php

namespace App\Controllers;

use App\Services\OrderServiceInterface;
use App\Services\RedisService;
use App\Services\RabbitMQService;

class OrderController
{
    private $orderService;
    private $redisService;
    private $rabbitMQService;

    public function __construct(OrderServiceInterface $orderService, RedisService $redisService, RabbitMQService $rabbitMQService)
    {
        $this->orderService = new OrderService();
        $this->redisService = new RedisService();
        $this->rabbitMQService = new RabbitMQService();
    }

    public function index()
    {
        // Пример использования Redis
        $this->redisService->setValue('order_list', 'List of orders');
        $orders = $this->redisService->getValue('order_list');

        return $orders;
    }

    public function create()
    {
        $order = $this->orderService->createOrder("New Order");

        // Пример использования RabbitMQ
        $this->rabbitMQService->publishMessage('order_queue', 'Order created: ' . $order->getId());

        return "Order created with ID: " . $order->getId();
    }
}
