<?php

namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use App\Services\UserService;

class UserServiceTest extends TestCase
{
    protected UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = new UserService();
    }

    public function testRegisterUser()
    {
        $data = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'role' => 'customer'
        ];

        $userId = $this->userService->registerUser($data); // Register the user and get the ID

        $user = $this->userService->getUserById($userId);
        $this->assertNotNull($user);
        $this->assertEquals('New User', $user['name']);
        $this->assertEquals('newuser@example.com', $user['email']);

        $this->userService->deleteUser($userId);
    }

    public function testUpdateUser()
    {
        $data = [
            'name' => 'Initial User',
            'email' => 'initialuser@example.com',
            'password' => 'password123',
            'role' => 'customer'
        ];

        $userId = $this->userService->registerUser($data);

        $updateData = [
            'name' => 'Updated User',
            'email' => 'updated@example.com'
        ];
        $this->userService->updateUser($userId, $updateData);

        $updatedUser = $this->userService->getUserById($userId);
        $this->assertEquals('Updated User', $updatedUser['name']);
        $this->assertEquals('updated@example.com', $updatedUser['email']);

        $this->userService->deleteUser($userId);
    }

    public function testDeleteUser()
    {
        $data = [
            'name' => 'User to Delete',
            'email' => 'deletableuser@example.com',
            'password' => 'password123',
            'role' => 'customer'
        ];

        $userId = $this->userService->registerUser($data);

        $this->userService->deleteUser($userId);

        $deletedUser = $this->userService->getUserById($userId);
        $this->assertNull($deletedUser);
    }
}
