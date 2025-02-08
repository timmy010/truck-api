<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Loggers\UserLogger;
use GuzzleHttp\Psr7\Utils;
use InvalidArgumentException;

class UserService
{
    protected User $userModel;
    protected Order $orderModel;
    protected UserLogger $logger;

    public function __construct()
    {
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->logger = new UserLogger(__DIR__ . '/../logs/user.log');
    }

    public function registerUser(array $data): false|string
    {
        if (!isset($data['name'], $data['email'], $data['password'], $data['role'])) {
            throw new InvalidArgumentException('Invalid input');
        }

        if ($this->userModel->getByEmail($data['email'])) {
            throw new InvalidArgumentException('Email already in use.');
        }

        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']);

        $data['api_key'] = bin2hex(random_bytes(16));

        return $this->userModel->create($data);
    }

    public function getAllUsers(): false|array
    {
        return $this->userModel->getAll();
    }

    public function getUserById(int $id)
    {
        return $this->userModel->getById($id);
    }

    public function updateUser(int $id, array $data): ?array
    {
        $existingUser = $this->userModel->getById($id);
        $this->logger->logInfo('updateUser', ['$existingUser' => $existingUser]);

        if ($existingUser) {
            $updatedData = array_merge($existingUser, $data);

            $filteredData = [
                'name' => $updatedData['name'],
                'email' => $updatedData['email'],
                'password_hash' => $updatedData['password_hash'],
                'role' => $updatedData['role'],
            ];

            $this->logger->logInfo('updateUser', ['$filteredData' => $filteredData]);

            $resultUpdate = $this->userModel->update($id, $filteredData);
            $this->logger->logInfo('updateUser', ['result' => $resultUpdate]);

            if ($resultUpdate)  {
                return $this->userModel->getById($id);
            }
        }
        return null;
    }

    public function deleteUser(int $id): void
    {
        $this->userModel->delete($id);
    }

    public function setApiKey(int $userId, string $apiKey): bool
    {
        return $this->userModel->updateApiKey($userId, $apiKey);
    }

    public function getByApiKey(string $apiKey): ?array
    {
        return $this->userModel->getByApiKey($apiKey);
    }
}
