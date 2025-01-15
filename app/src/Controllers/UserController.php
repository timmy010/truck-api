<?php

namespace App\Controllers;

use App\Services\UserService;

class UserController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function create()
    {
        $user = $this->userService->createUser("New User");
        return "User created with ID: " . $user->getId();
    }
}
