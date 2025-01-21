<?php

namespace App\Controllers;

use App\Services\AdminRolePermissionService;

class AdminRolePermissionController
{
    protected $adminRolePermissionService;

    public function __construct()
    {
        $this->adminRolePermissionService = new AdminRolePermissionService();
    }

    public function assignPermission($request)
    {
        $data = $request->getParsedBody();
        $this->adminRolePermissionService->assignPermission($data['admin_id'], $data['permission']);
    }

    public function getAllPermissions()
    {
        return $this->adminRolePermissionService->getAllPermissions();
    }

    public function getPermissionsByAdminId($adminId)
    {
        return $this->adminRolePermissionService->getPermissionsByAdminId($adminId);
    }

    public function deletePermission($id)
    {
        $this->adminRolePermissionService->deletePermission($id);
    }
}
