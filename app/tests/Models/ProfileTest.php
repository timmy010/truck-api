<?php

namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use App\Models\Profile;

class ProfileTest extends TestCase
{
    protected Profile $profile;
    protected User $user;
    protected int $userId;

    protected function setUp(): void
    {
        $this->profile = new Profile();
        $this->user = new User();

        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password_hash' => 'hashed_password',
            'role' => 'customer'
        ];
        $this->userId = $this->user->create($userData);
    }

    public function testCreateProfile()
    {
        $data = [
            'user_id' => $this->userId,
            'phone' => '1234567890',
            'actual_address' => 'Actual Address',
            'legal_address' => 'Legal Address',
            'company_name' => 'Company Inc.',
            'inn' => '123456789012',
            'ogrn' => '1234567891234'
        ];
        $this->profile->create($data);

        $createdProfile = $this->profile->getByUserId($this->userId);
        $this->assertEquals('1234567890', $createdProfile['phone']);
    }

    public function testGetAllProfiles()
    {
        $data = [
            'user_id' => $this->userId,
            'phone' => '1234567890',
            'actual_address' => 'Actual Address',
            'legal_address' => 'Legal Address',
            'company_name' => 'Company Inc.',
            'inn' => '123456789012',
            'ogrn' => '1234567891234'
        ];
        $this->profile->create($data);

        $profiles = $this->profile->getAll();
        $this->assertIsArray($profiles);
        $this->assertNotEmpty($profiles);
    }

    public function testDeleteProfile()
    {
        $data = [
            'user_id' => $this->userId,
            'phone' => '1234567890',
            'actual_address' => 'Actual Address',
            'legal_address' => 'Legal Address',
            'company_name' => 'Company Inc.',
            'inn' => '123456789012',
            'ogrn' => '1234567891234'
        ];
        $this->profile->create($data);

        $createdProfile = $this->profile->getByUserId($this->userId);
        $this->assertNotNull($createdProfile);

        $profileId = $createdProfile['id'];
        $this->profile->delete($profileId);

        $deletedProfile = $this->profile->getByUserId($this->userId);
        $this->assertNull($deletedProfile);
    }

    protected function tearDown(): void
    {
        $this->user->delete($this->userId);
    }
}
