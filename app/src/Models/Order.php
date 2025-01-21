<?php

namespace App\Models;

use App\Database;
use App\Interfaces\OrderInterface;
use InvalidArgumentException;

class Order implements OrderInterface
{
    protected $db;
    protected $table = 'orders';

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // Create a new order
    public function create(array $data)
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} 
            (customer_id, carrier_id, pickup_location, delivery_location, cargo_description, 
             freight_rate, scheduled_loading_date, scheduled_unloading_date, 
             actual_loading_date, actual_unloading_date, status, created_at, updated_at) 
             VALUES (:customer_id, :carrier_id, :pickup_location, :delivery_location, 
             :cargo_description, :freight_rate, :scheduled_loading_date, 
             :scheduled_unloading_date, :actual_loading_date, 
             :actual_unloading_date, :status, NOW(), NOW())");
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    // Retrieve all orders
    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    // Retrieve a specific order by ID
    public function getById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ? $result : null;
    }

    // Update an existing order (PUT)
    public function update(int $id, array $data)
    {
        if (!isset($data['customer_id'], $data['carrier_id'], $data['pickup_location'],
            $data['delivery_location'], $data['cargo_description'],
            $data['freight_rate'], $data['scheduled_loading_date'],
            $data['scheduled_unloading_date'], $data['actual_loading_date'],
            $data['actual_unloading_date'], $data['status'])) {
            throw new InvalidArgumentException('Missing required fields for update.');
        }

        $stmt = $this->db->prepare("UPDATE {$this->table} 
            SET customer_id = :customer_id, 
                carrier_id = :carrier_id, 
                pickup_location = :pickup_location, 
                delivery_location = :delivery_location, 
                cargo_description = :cargo_description, 
                freight_rate = :freight_rate, 
                scheduled_loading_date = :scheduled_loading_date, 
                scheduled_unloading_date = :scheduled_unloading_date, 
                actual_loading_date = :actual_loading_date, 
                actual_unloading_date = :actual_unloading_date, 
                status = :status, 
                updated_at = NOW() 
            WHERE id = :id");

        $data['id'] = $id;
        return $stmt->execute($data);
    }

    // Partially update an existing order (PATCH)
    public function patch(int $id, array $data)
    {
        // Build the update statement dynamically
        $fields = [];
        foreach ($data as $key => $value) {
            if (in_array($key, ['customer_id', 'carrier_id', 'pickup_location',
                'delivery_location', 'cargo_description',
                'freight_rate', 'scheduled_loading_date',
                'scheduled_unloading_date', 'actual_loading_date',
                'actual_unloading_date', 'status'])) {
                $fields[] = "$key = :$key";
            }
        }

        if (empty($fields)) {
            return false;  // No fields to update
        }

        $query = "UPDATE {$this->table} SET " . implode(", ", $fields) .
            ", updated_at = NOW() WHERE id = :id";
        $data['id'] = $id;

        $stmt = $this->db->prepare($query);
        return $stmt->execute($data);
    }

    // Delete an order
    public function delete(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Get the last inserted ID
    public function getLastInsertId()
    {
        return $this->db->lastInsertId();
    }

    // Retrieve orders by user ID
    public function getOrdersByUserId(int $userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE customer_id = :userId");
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll();
    }
}
