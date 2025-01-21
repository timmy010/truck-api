<?php

namespace App\Controllers;

use App\Services\UserService;

class UserController
{
    protected UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function registerUser($request)
    {
        $data = $request->getParsedBody();
        $this->userService->registerUser($data);
    }

    public function getUserById($id)
    {
        return $this->userService->getUserById($id);
    }

    public function updateUser($id, $request)
    {
        $data = $request->getParsedBody();
        $this->userService->updateUser($id, $data);
    }

    public function deleteUser($id)
    {
        $this->userService->deleteUser($id);
    }
}
