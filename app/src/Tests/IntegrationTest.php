<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\OrderController;
use App\Controllers\UserController;

class IntegrationTest extends TestCase
{
    private $orderController;
    private $userController;

    protected function setUp(): void
    {
        $this->orderController = new OrderController();
        $this->userController = new UserController();
    }

    public function testOrderAndUserCreation()
    {
        $orderResult = $this->orderController->create();
        $this->assertStringContainsString("Order created with ID:", $orderResult);

        $userResult = $this->userController->create();
        $this->assertStringContainsString("User created with ID:", $userResult);
    }
}
