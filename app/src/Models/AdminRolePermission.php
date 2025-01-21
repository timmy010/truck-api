<?php

namespace App\Models;

use App\Database;
use InvalidArgumentException;

class AdminRolePermission
{
    protected $db;
    protected $table = 'admin_role_permissions';

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // Assign a permission to an admin (POST)
    public function assignPermission(array $data)
    {
        if (!isset($data['admin_id'], $data['permission'])) {
            throw new InvalidArgumentException('Missing required fields for assigning permissions.');
        }

        $stmt = $this->db->prepare("INSERT INTO {$this->table} (admin_id, permission, created_at, updated_at) 
                                      VALUES (:admin_id, :permission, NOW(), NOW())");
        return $stmt->execute($data);
    }

    // Retrieve all admin permissions (GET)
    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    // Retrieve permissions by admin ID (GET)
    public function getByAdminId(int $adminId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE admin_id = :admin_id");
        $stmt->execute(['admin_id' => $adminId]);
        return $stmt->fetchAll();
    }

    // Delete a specific admin permission (DELETE)
    public function delete(int $id)
    {
        // Optionally check if the permission exists before deleting
        $existingPermission = $this->getById($id);
        if (!$existingPermission) {
            throw new InvalidArgumentException('Permission not found.');
        }

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Retrieve a specific permission by ID (GET)
    public function getById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Delete all permissions by admin ID (DELETE)
    public function deleteAllByAdminId(int $adminId)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE admin_id = :admin_id");
        return $stmt->execute(['admin_id' => $adminId]);
    }
}
