<?php

namespace App\Interfaces;

interface AdminRolePermissionInterface
{
    public function assignPermission($adminId, $permission);
    public function getAll();
    public function getByAdminId(int $adminId);
    public function delete(int $id);
}
