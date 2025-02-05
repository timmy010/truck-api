<?php

namespace App\Services;

use App\Models\Cargo;
use InvalidArgumentException;

class CargoService
{
    private Cargo $cargoModel;

    public function __construct()
    {
        $this->cargoModel = new Cargo();
    }

    public function createCargo(array $data): int
    {
        return $this->cargoModel->create($data);
    }

    public function getAllCargos(): array
    {
        return $this->cargoModel->getAll();
    }

    public function getCargoById(int $id): ?array
    {
        return $this->cargoModel->getById($id);
    }

    public function updateCargo(int $id, array $data): bool
    {
        if (!isset($data['title']) && !isset($data['volume']) && !isset($data['weight'])) {
            throw new InvalidArgumentException('At least one of title, volume, or weight must be provided for update.');
        }
        return $this->cargoModel->update($id, $data);
    }

    public function patchCargo(int $id, array $data): bool
    {
        return $this->cargoModel->patch($id, $data);
    }

    public function deleteCargo(int $id): bool
    {
        return $this->cargoModel->delete($id);
    }
}