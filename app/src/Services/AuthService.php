<?php

namespace App\Services;

use InvalidArgumentException;

class AuthService
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function register(array $data): array
    {
        $userId = $this->userService->registerUser($data);
        $apiKey = bin2hex(random_bytes(16));

        $this->userService->setApiKey($userId, $apiKey);

        return ['api_key' => $apiKey];
    }

    public function authorize(string $apiKey): array
    {
        $user = $this->userService->getByApiKey($apiKey);

        if (!$user) {
            throw new InvalidArgumentException('Invalid API key.');
        }

        return $user;
    }

    public function isAuthorized(array $user, string $action): bool
    {
        $role = $user['role'];

        if ($role === 'admin') {
            return true;
        }

        return match ($action) {
            'getUserById', 'updateUser', 'getProfileByUserId', 'updateProfile' => in_array(
                $role,
                ['customer', 'carrier']
            ),
            'createOrder' => $role === 'customer',
            'updateOrderStatus', 'getAllOrders' => $role === 'carrier',
            default => false,
        };
    }
}
