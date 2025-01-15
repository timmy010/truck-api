<?php

use PHPUnit\Framework\TestCase;
use App\Services\OrderService;

class OrderServiceTest extends TestCase
{
     public function testCreateOrder()
     {
         $service = new OrderService();
         $order = $service->createOrder("Test Order");

         $this->assertNotNull($order);
         $this->assertEquals("Test Order", $order->getDescription());
     }
}
