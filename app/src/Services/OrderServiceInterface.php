<?php

namespace App\Services;

use App\Models\Order;

interface OrderServiceInterface
{
    public function createOrder($description): Order;
}
