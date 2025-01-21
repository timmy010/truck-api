<?php

namespace App\Models;

use App\Database;
use App\Interfaces\ProfileInterface;
use InvalidArgumentException;

class Profile implements ProfileInterface
{
    protected $db;
    protected $table = 'profiles';

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // Create a new profile
    public function create(array $data)
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} 
            (user_id, phone, actual_address, legal_address, company_name, inn, ogrn, created_at, updated_at) 
            VALUES (:user_id, :phone, :actual_address, :legal_address, :company_name, :inn, :ogrn, NOW(), NOW())");
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    // Retrieve all profiles
    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    // Retrieve a profile by user ID
    public function getByUserId(int $userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        return $result ? $result : null;
    }

    // Update a profile (PUT: full update)
    public function update(int $id, array $data)
    {
        if (!isset($data['user_id'], $data['phone'], $data['actual_address'],
            $data['legal_address'], $data['company_name'],
            $data['inn'], $data['ogrn'])) {
            throw new InvalidArgumentException('Missing required fields for update.');
        }

        $stmt = $this->db->prepare("UPDATE {$this->table} 
            SET user_id = :user_id, phone = :phone, actual_address = :actual_address, 
                legal_address = :legal_address, company_name = :company_name, 
                inn = :inn, ogrn = :ogrn, updated_at = NOW() 
            WHERE id = :id");

        $data['id'] = $id;
        return $stmt->execute($data);
    }

    // Partially update a profile (PATCH: partial update)
    public function patch(int $id, array $data)
    {
        // Build the update statement dynamically
        $fields = [];
        foreach ($data as $key => $value) {
            if (in_array($key, ['user_id', 'phone', 'actual_address',
                'legal_address', 'company_name', 'inn', 'ogrn'])) {
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

    // Delete a profile
    public function delete(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
