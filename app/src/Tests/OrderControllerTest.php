<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\OrderController;

class OrderControllerTest extends TestCase
{
    private $orderController;

    protected function setUp(): void
    {
        $this->orderController = new OrderController();
    }

    public function testIndex()
    {
        $orderList = $this->orderController->index();
        $this->assertNotEmpty($orderList, "Order list should not be empty.");
    }

    public function testCreate()
    {
        $orderCreationResult = $this->orderController->create();
        $this->assertStringContainsString("Order created with ID:", $orderCreationResult, "Order creation result should contain order ID.");
    }
}
