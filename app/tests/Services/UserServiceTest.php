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
        $randomName = 'User_' . bin2hex(random_bytes(5)); // Random name
        $randomEmail = $randomName . '@example.com'; // Random email based on the name

        $data = [
            'name' => $randomName,
            'email' => $randomEmail,
            'password' => 'password123',
            'role' => 'customer'
        ];

        $userId = $this->userService->registerUser($data); // Register the user and get the ID

        $user = $this->userService->getUserById($userId);
        $this->assertNotNull($user);
        $this->assertEquals($randomName, $user['name']);
        $this->assertEquals($randomEmail, $user['email']);

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
