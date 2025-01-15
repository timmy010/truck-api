<?php

namespace App\Services;

use App\Models\User;
use Predis\Client;

class UserService
{
    private $redisClient;

    public function __construct()
    {
        $this->redisClient = new Client();
    }

    public function createUser($name): User
    {
        $user = new User(uniqid(), $name);
        $this->redisClient->set('user_' . $user->getId(), serialize($user));
        return $user;
    }

    public function getUser($id): ?User
    {
        $userData = $this->redisClient->get('user_' . $id);
        return $userData ? unserialize($userData) : null;
    }
}
