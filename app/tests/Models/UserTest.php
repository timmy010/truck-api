<?php

namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testCreateUser()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password_hash' => 'hashed_password',
            'role' => 'customer'
        ];

        $userId = $this->user->create($data);

        $createdUser = $this->user->getById($userId);

        $this->assertEquals('Test User', $createdUser['name']);
        $this->assertEquals('test@example.com', $createdUser['email']);

        $this->user->delete($userId);
    }

    public function testGetAllUsers()
    {
        $users = $this->user->getAll();
        $this->assertIsArray($users);
    }

    public function testDeleteUser()
    {
        $data = [
            'name' => 'User to Delete',
            'email' => 'delete@example.com',
            'password_hash' => 'hashed_password',
            'role' => 'customer'
        ];

        $userId = $this->user->create($data);

        $this->user->delete($userId);

        $deletedUser = $this->user->getById($userId);
        $this->assertNull($deletedUser);
    }
}
