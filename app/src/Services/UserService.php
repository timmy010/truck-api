<?php

namespace App\Services;

use AllowDynamicProperties;
use App\Models\User;
use App\Models\Order;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use App\Loggers\UserLogger;

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

    public function registerUser(array $data)
    {
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']);
        return $this->userModel->create($data);
    }

    public function getAllUsers()
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

    public function getUserOrders(int $userId)
    {
        return $this->orderModel->getOrdersByUserId($userId);
    }
}
