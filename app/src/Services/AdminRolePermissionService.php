<?php

namespace App\Services;

use App\Models\AdminRolePermission;

class AdminRolePermissionService
{
    protected $adminRolePermissionModel;

    public function __construct()
    {
        $this->adminRolePermissionModel = new AdminRolePermission();
    }

    public function assignPermission($adminId, $permission)
    {
        $this->adminRolePermissionModel->assignPermission($adminId, $permission);
    }

    public function getAllPermissions()
    {
        return $this->adminRolePermissionModel->getAll();
    }

    public function getPermissionsByAdminId(int $adminId)
    {
        return $this->adminRolePermissionModel->getByAdminId($adminId);
    }

    public function deletePermission(int $id)
    {
        $this->adminRolePermissionModel->delete($id);
    }
}
