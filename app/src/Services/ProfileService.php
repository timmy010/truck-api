<?php

namespace App\Services;

use App\Models\Profile;

class ProfileService
{
    protected $profileModel;

    public function __construct()
    {
        $this->profileModel = new Profile();
    }

    public function createProfile(array $data)
    {
        $this->profileModel->create($data);
    }

    public function getAllProfiles()
    {
        return $this->profileModel->getAll();
    }

    public function getProfileByUserId(int $userId)
    {
        return $this->profileModel->getByUserId($userId);
    }

    public function updateProfile(int $id, array $data)
    {
        $this->profileModel->update($id, $data);
    }

    public function deleteProfile(int $id)
    {
        $this->profileModel->delete($id);
    }
}
