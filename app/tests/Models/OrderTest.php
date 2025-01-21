<?php

namespace Tests\Models;

use App\Models\User;
use PHPUnit\Framework\TestCase;
use App\Models\Order;

class OrderTest extends TestCase
{
    protected Order $order;
    protected User $user;
    protected int $userId;

    protected function setUp(): void
    {
        $this->order = new Order();
        $this->user = new User();

        // Create a user for testing
        $this->userId = $this->user->create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password_hash' => 'hashed_password',
            'role' => 'customer',
        ]);
    }

    public function testCreateOrder()
    {
        $data = [
            'customer_id' => $this->userId, // Use the dynamically created user ID
            'carrier_id' => null,
            'pickup_location' => 'Location A',
            'delivery_location' => 'Location B',
            'cargo_description' => 'Some Cargo',
            'freight_rate' => 100.50,
            'scheduled_loading_date' => null,
            'scheduled_unloading_date' => null,
            'actual_loading_date' => null,
            'actual_unloading_date' => null,
            'status' => 'pending',
        ];

        // Create the order
        $this->order->create($data);

        // Retrieve the created order
        $createdOrder = $this->order->getById($this->order->getLastInsertId());
        $this->assertEquals('Location A', $createdOrder['pickup_location']);
    }

    public function testGetAllOrders()
    {
        $orders = $this->order->getAll(); // Ensure the method name matches the one in your model
        $this->assertIsArray($orders);
    }

    public function testDeleteOrder()
    {
        // Create an order to delete
        $data = [
            'customer_id' => $this->userId,
            'carrier_id' => null,
            'pickup_location' => 'Location A',
            'delivery_location' => 'Location B',
            'cargo_description' => 'Some Cargo',
            'freight_rate' => 100.50,
            'scheduled_loading_date' => null,
            'scheduled_unloading_date' => null,
            'actual_loading_date' => null,
            'actual_unloading_date' => null,
            'status' => 'pending',
        ];

        $this->order->create($data);
        $lastOrderId = $this->order->getLastInsertId(); // Get the last inserted order ID

        // Delete the order
        $this->order->delete($lastOrderId);
        $deletedOrder = $this->order->getById($lastOrderId);
        $this->assertNull($deletedOrder);
    }

    public function testGetOrdersByUserId()
    {
        // Create an order for the user
        $data = [
            'customer_id' => $this->userId,
            'carrier_id' => null,
            'pickup_location' => 'Location A',
            'delivery_location' => 'Location B',
            'cargo_description' => 'Some Cargo',
            'freight_rate' => 100.50,
            'scheduled_loading_date' => null,
            'scheduled_unloading_date' => null,
            'actual_loading_date' => null,
            'actual_unloading_date' => null,
            'status' => 'pending',
        ];

        $this->order->create($data);

        // Retrieve orders by user ID
        $orders = $this->order->getOrdersByUserId($this->userId);
        $this->assertIsArray($orders);
        $this->assertNotEmpty($orders); // Ensure there is at least one order
    }

    protected function tearDown(): void
    {
        // Optionally clean up the created user and associated data if necessary
        $this->user->delete($this->userId);
    }
}
