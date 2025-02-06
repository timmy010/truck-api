<?php

namespace Tests\Services;

use App\Models\Cargo;
use App\Services\CargoService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CargoServiceTest extends TestCase
{
    private CargoService $cargoService;
    private Cargo $cargoModel;

    protected function setUp(): void
    {
        $this->cargoModel = $this->createMock(Cargo::class);
        $this->cargoService = new CargoService();

        // Override the Cargo model with a mocked instance
        $this->cargoService->cargoModel = $this->cargoModel;
    }

    public function testCreateCargoSuccess()
    {
        $data = [
            'title' => 'Test Cargo',
            'volume' => 100.00,
            'weight' => 50.00,
            'length' => 10.00,
            'width' => 5.00,
            'depth' => 2.00,
            'cost' => 200.00,
            'order_id' => 1
        ];

        $this->cargoModel
            ->method('create')
            ->willReturn(1); // Simulate returning a valid ID

        $id = $this->cargoService->createCargo($data);
        $this->assertIsInt($id); // Assert an integer ID is returned
    }

    public function testGetAllCargos()
    {
        $this->cargoModel
            ->method('getAll')
            ->willReturn([]);

        $result = $this->cargoService->getAllCargos();
        $this->assertIsArray($result);
    }

    public function testGetCargoByIdSuccess()
    {
        $cargoData = ['id' => 1, 'title' => 'Test Cargo'];

        $this->cargoModel
            ->method('getById')
            ->willReturn($cargoData);

        $result = $this->cargoService->getCargoById(1);
        $this->assertEquals($cargoData, $result);
    }

    public function testGetCargoByIdNotFound()
    {
        $this->cargoModel
            ->method('getById')
            ->willReturn(null);

        $result = $this->cargoService->getCargoById(999); // Use a generic ID
        $this->assertNull($result);
    }

    public function testUpdateCargoSuccess()
    {
        $data = [
            'title' => 'Updated Cargo',
            'volume' => 120.00,
            'weight' => 70.00,
        ];

        $this->cargoModel
            ->method('update')
            ->willReturn(true);

        $result = $this->cargoService->updateCargo(1, $data);
        $this->assertTrue($result);
    }

    public function testUpdateCargoMissingFields()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one of title, volume, or weight must be provided for update.');

        $data = [];
        $this->cargoService->updateCargo(1, $data);
    }

    public function testPatchCargoSuccess()
    {
        $data = [
            'weight' => 75.00,
        ];

        $this->cargoModel
            ->method('patch')
            ->willReturn(true);

        $result = $this->cargoService->patchCargo(1, $data);
        $this->assertTrue($result);
    }

    public function testDeleteCargoSuccess()
    {
        $this->cargoModel
            ->method('delete')
            ->willReturn(true);

        $result = $this->cargoService->deleteCargo(1);
        $this->assertTrue($result);
    }
}