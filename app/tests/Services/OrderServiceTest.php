<?php

namespace Tests\Services;

use App\Models\Order;
use App\Services\OrderService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class OrderServiceTest extends TestCase
{
    private OrderService $orderService;
    private Order $orderModel;

    protected function setUp(): void
    {
        // Create a mock for the Order model
        $this->orderModel = $this->createMock(Order::class);
        // Instantiate the OrderService with the mocked model
        $this->orderService = new OrderService();
        $this->orderService->orderModel = $this->orderModel;
    }

    public function testCreateOrderSuccess()
    {
        $data = [
            'customer_id' => 1,
            'carrier_id' => null,
            'pickup_location' => 'Location A',
            'delivery_location' => 'Location B',
            'freight_rate' => 150.00,
            'scheduled_loading_date' => '2023-10-01 08:00:00',
            'scheduled_unloading_date' => '2023-10-01 10:00:00',
        ];

        // Mock the create method to return a successful insert
        $this->orderModel
            ->method('create')
            ->willReturn(1);

        $id = $this->orderService->createOrder($data);
        $this->assertIsInt($id);
    }

    public function testCreateOrderMissingFields()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Customer ID is required.');

        $data = [
            'carrier_id' => null,
            'pickup_location' => 'Location A',
            'delivery_location' => 'Location B',
            'freight_rate' => 150.00,
            'scheduled_loading_date' => '2023-10-01 08:00:00',
            'scheduled_unloading_date' => '2023-10-01 10:00:00',
        ];

        $this->orderService->createOrder($data);
    }

    public function testGetAllOrders()
    {
        $this->orderModel
            ->method('getAll')
            ->willReturn([]);

        $result = $this->orderService->getAllOrders();
        $this->assertIsArray($result);
    }

    public function testGetOrderByIdSuccess()
    {
        $orderData = ['id' => 1, 'customer_id' => 1];

        $this->orderModel
            ->method('getById')
            ->willReturn($orderData);

        $result = $this->orderService->getOrderById(1);
        $this->assertEquals($orderData, $result);
    }

    public function testGetOrderByIdNotFound()
    {
        $this->orderModel
            ->method('getById')
            ->willReturn(null);

        $result = $this->orderService->getOrderById(999); // Example ID that would not exist
        $this->assertNull($result);
    }

    public function testUpdateOrderSuccess()
    {
        $data = [
            'customer_id' => 1,
            'pickup_location' => 'New Location',
            'delivery_location' => 'Location B',
            'freight_rate' => 150.00,
            'scheduled_loading_date' => '2023-10-01 08:00:00',
            'scheduled_unloading_date' => '2023-10-01 10:00:00',
            'status' => 1,
        ];

        // Mock the update method to return true on success
        $this->orderModel
            ->method('update')
            ->willReturn(true);

        $result = $this->orderService->updateOrder(1, $data);
        $this->assertTrue($result);
    }

    public function testUpdateOrderMissingFields()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Customer ID is required.');

        $data = []; // No fields provided
        $this->orderService->updateOrder(1, $data);
    }

    public function testDeleteOrderSuccess()
    {
        // Mock the delete method to return true on successful deletion
        $this->orderModel
            ->method('delete')
            ->willReturn(true);

        $result = $this->orderService->deleteOrder(1);
        $this->assertTrue($result);
    }
}