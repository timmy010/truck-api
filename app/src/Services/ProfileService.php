<?php

namespace App\Services;

use App\Models\Profile;

class ProfileService
{
    protected Profile $profileModel;

    public function __construct()
    {
        $this->profileModel = new Profile();
    }

    public function createProfile(array $data): false|string
    {
        return $this->profileModel->create($data);
    }

    public function getProfiles(): false|array
    {
        return $this->profileModel->getAll();
    }

    public function getProfileByUserId(int $userId)
    {
        return $this->profileModel->getByUserId($userId);
    }

    public function getProfileById(int $id)
    {
        return $this->profileModel->getById($id);
    }

    public function updateProfile(int $id, array $data): void
    {
        $this->profileModel->update($id, $data);
    }

    public function deleteProfile(int $id): bool
    {
        return $this->profileModel->delete($id);
    }
}
