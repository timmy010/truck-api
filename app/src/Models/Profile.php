<?php

namespace App\Models;

use App\Database;
use App\Interfaces\ProfileInterface;
use InvalidArgumentException;
use PDO;

class Profile
{
    protected PDO $db;
    protected string $table = 'profiles';

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function create(array $data): false|string
    {
        $columns = ["user_id", "phone", "actual_address", "legal_address", "company_name", "inn"];
        $values = [
            ':user_id' => $data['user_id'],
            ':phone' => $data['phone'],
            ':actual_address' => $data['actual_address'],
            ':legal_address' => $data['legal_address'],
            ':company_name' => $data['company_name'],
            ':inn' => $data['inn']
        ];

        if (isset($data['ogrn'])) {
            $columns[] = "ogrn";
            $values[':ogrn'] = $data['ogrn'];
        }

        $sql = sprintf(
            "INSERT INTO {$this->table} (%s, created_at, updated_at) VALUES (%s, NOW(), NOW())",
            implode(", ", $columns),
            implode(", ", array_keys($values))
        );

        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);

        return $this->db->lastInsertId();
    }

    public function getAll(): false|array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function getByUserId(int $userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        return $result ?: null;
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

    public function patch(int $id, array $data): bool
    {
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

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
