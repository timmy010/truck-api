<?php

namespace App\Services;

use App\Models\Order;

class OrderService implements OrderServiceInterface
{
    public function createOrder($description): Order
    {
        return new Order(uniqid(), $description);
    }
}
