<?php

namespace Tests\Models;

use App\Models\User;
use PHPUnit\Framework\TestCase;
use App\Models\AdminRolePermission;

class AdminRolePermissionTest extends TestCase
{
    protected AdminRolePermission $adminRolePermission;
    protected int $userId;

    protected function setUp(): void
    {
        $this->adminRolePermission = new AdminRolePermission();

        // Dynamically create a user with the 'admin' role for tests
        $this->userId = $this->createTestAdmin();
    }

    protected function createTestAdmin(): int
    {
        // Logic to create a test user with 'admin' role and return the user ID
        $userModel = new User();
        return $userModel->create([
            'name' => 'Test Admin',
            'email' => 'testadmin@example.com',
            'password_hash' => 'hashed_password',
            'role' => 'admin',
        ]);
    }

    public function testAssignPermission()
    {
        $permission = 'view_orders';
        $this->adminRolePermission->assignPermission([
            'admin_id' => $this->userId, // Use userId as this is just an admin user
            'permission' => $permission
        ]);

        $permissions = $this->adminRolePermission->getByAdminId($this->userId);
        $this->assertCount(1, $permissions);
        $this->assertEquals($permission, $permissions[0]['permission']);
    }

    public function testGetAllPermissions()
    {
        $permissions = $this->adminRolePermission->getAll();
        $this->assertIsArray($permissions);
    }

    public function testDeletePermission()
    {
        $permission = 'delete_orders';
        $this->adminRolePermission->assignPermission([
            'admin_id' => $this->userId, // Use userId as this is just an admin user
            'permission' => $permission
        ]);

        $permissionsBeforeDelete = $this->adminRolePermission->getByAdminId($this->userId);
        $permissionIdToDelete = $permissionsBeforeDelete[0]['id']; // Assuming we take the first permission

        $this->adminRolePermission->delete($permissionIdToDelete);

        // Verify that the permission was deleted
        $deletedPermissions = $this->adminRolePermission->getByAdminId($this->userId);
        $this->assertCount(0, $deletedPermissions);
    }

    protected function tearDown(): void
    {
        // Clean up created user and their permissions
        $this->adminRolePermission->deleteAllByAdminId($this->userId);
        $this->deleteTestAdmin($this->userId);
    }

    protected function deleteTestAdmin(int $userId)
    {
        $userModel = new User();
        $userModel->delete($userId); // Assuming a method exists to delete the user
    }
}
