<?php

namespace App\Controllers;

use App\Services\ProfileService;

class ProfileController
{
    protected $profileService;

    public function __construct()
    {
        $this->profileService = new ProfileService();
    }

    public function createProfile($request)
    {
        $data = $request->getParsedBody();
        $this->profileService->createProfile($data);
    }

    public function getAllProfiles()
    {
        return $this->profileService->getAllProfiles();
    }

    public function getProfileByUserId($userId)
    {
        return $this->profileService->getProfileByUserId($userId);
    }

    public function updateProfile($id, $request)
    {
        $data = $request->getParsedBody();
        $this->profileService->updateProfile($id, $data);
    }

    public function deleteProfile($id)
    {
        $this->profileService->deleteProfile($id);
    }
}
