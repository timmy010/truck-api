<?php

namespace App\Models;

use App\Database;
use InvalidArgumentException;
use PDO;

class Cargo
{
    protected PDO $db;
    protected string $table = 'cargos';

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function create(array $data): false|int
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (title, volume, weight, length, width, depth, cost, order_id, created_at, updated_at) 
            VALUES (:title, :volume, :weight, :length, :width, :depth, :cost, :order_id, NOW(), NOW())");
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function getAll(): false|array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function getById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function update(int $id, array $data): bool
    {
        if (!isset($data['title'], $data['volume'], $data['weight'])) {
            throw new InvalidArgumentException('Missing required fields for update.');
        }

        $stmt = $this->db->prepare("UPDATE {$this->table} 
            SET title = :title, volume = :volume, weight = :weight, length = :length, width = :width, depth = :depth, cost = :cost, updated_at = NOW()
            WHERE id = :id");
        $data['id'] = $id;

        return $stmt->execute($data);
    }

    public function patch(int $id, array $data): bool
    {
        // Build the update statement dynamically
        $fields = [];
        foreach ($data as $key => $value) {
            if (in_array($key, ['title', 'volume', 'weight', 'length', 'width', 'depth', 'cost'])) {
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

    public function delete(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}