<?php

namespace App\Models;

use App\Database;
use App\Interfaces\UserInterface;
use InvalidArgumentException;

class User implements UserInterface
{
    protected $db;
    protected $table = 'users';

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // Create a new user
    public function create(array $data)
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, email, password_hash, role, created_at, updated_at) 
            VALUES (:name, :email, :password_hash, :role, NOW(), NOW())");
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    // Retrieve all users
    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    // Retrieve a specific user by ID
    public function getById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ? $result : null;
    }

    // Update a user (PUT: full update)
    public function update(int $id, array $data)
    {
        if (!isset($data['name'], $data['email'], $data['password_hash'], $data['role'])) {
            throw new InvalidArgumentException('Missing required fields for update.');
        }

        $stmt = $this->db->prepare("UPDATE {$this->table} 
            SET name = :name, email = :email, password_hash = :password_hash, role = :role, updated_at = NOW() 
            WHERE id = :id");
        $data['id'] = $id;

        return $stmt->execute($data);
    }

    // Partially update a user (PATCH: partial update)
    public function patch(int $id, array $data)
    {
        // Build the update statement dynamically
        $fields = [];
        foreach ($data as $key => $value) {
            if (in_array($key, ['name', 'email', 'password_hash', 'role'])) {
                $fields[] = "$key = :$key";
            }
        }

        if (empty($fields)) {
            return false;  // No fields to update
        }

        $query = "UPDATE {$this->table} SET " . implode(", ", $fields) . ", updated_at = NOW() WHERE id = :id";
        $data['id'] = $id;

        $stmt = $this->db->prepare($query);
        return $stmt->execute($data);
    }

    // Delete a user
    public function delete(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
