<?php

namespace App\Models;

use App\Database;
use InvalidArgumentException;

class Order
{
    protected $db;
    protected string $table = 'orders';

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (customer_id, pickup_location, 
        delivery_location, freight_rate, scheduled_loading_date, scheduled_unloading_date, status, created_at, updated_at) 
        VALUES (:customer_id, :pickup_location, :delivery_location, :freight_rate, 
        :scheduled_loading_date, :scheduled_unloading_date, :status, NOW(), NOW())");

        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function getAllByFilter(array $filters = []): array
    {
        $query = "SELECT * FROM {$this->table}";

        $params = [];

        if (!empty($filters)) {
            $filterConditions = [];

            foreach ($filters as $column => $value) {
                $filterConditions[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }

            $query .= " WHERE " . implode(' AND ', $filterConditions);
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function update(int $id, array $data): bool
    {
        if (!isset($data['customer_id'], $data['pickup_location'], $data['delivery_location'], $data['freight_rate'], $data['scheduled_loading_date'], $data['scheduled_unloading_date'], $data['status'])) {
            throw new InvalidArgumentException('Missing required fields for update.');
        }

        $query = "UPDATE {$this->table} SET 
            customer_id = :customer_id, 
            carrier_id = :carrier_id, 
            pickup_location = :pickup_location, 
            delivery_location = :delivery_location, 
            freight_rate = :freight_rate, 
            scheduled_loading_date = :scheduled_loading_date, 
            scheduled_unloading_date = :scheduled_unloading_date, 
            actual_loading_date = :actual_loading_date, 
            actual_unloading_date = :actual_unloading_date, 
            status = :status, 
            updated_at = NOW() 
            WHERE id = :id";

        $data['id'] = $id;

        $stmt = $this->db->prepare($query);
        return $stmt->execute($data);
    }

    public function put(int $id, array $data): bool
    {
        $fields = [];
        foreach ($data as $key => $value) {
            if (in_array($key, [
                'carrier_id',
                'freight_rate',
                'actual_loading_date',
                'actual_unloading_date',
                'status'
            ])) {
                $fields[] = "$key = :$key";
            }
        }

        if (empty($fields)) {
            return false;
        }

        $query = "UPDATE {$this->table} SET " . implode(", ", $fields) . ", updated_at = NOW() WHERE id = :id";
        $data['id'] = $id;

        $stmt = $this->db->prepare($query);
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
