<?php

namespace App\Models;


use App\Database;
use InvalidArgumentException;
use PDO;

class User
{
    protected PDO $db;
    protected string $table = 'users';

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function create(array $data): false|string
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, email, password_hash, api_key, role, created_at, updated_at) 
            VALUES (:name, :email, :password_hash, :api_key, :role, NOW(), NOW())");
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function getAll(): false|array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getByEmail(string $email)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getByApiKey(string $apiKey)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE api_key = :api_key");
        $stmt->execute(['api_key' => $apiKey]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function updateApiKey(int $userId, string $apiKey): bool
    {
        return $this->patch($userId, ['api_key' => $apiKey]);
    }

    public function update(int $id, array $data): bool
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

    public function patch(int $id, array $data): bool
    {
        $fields = [];
        foreach ($data as $key => $value) {
            if (in_array($key, ['name', 'email', 'password_hash', 'api_key', 'role'])) {
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
