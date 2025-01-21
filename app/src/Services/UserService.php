<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;

class UserService
{
    protected $userModel;
    protected $orderModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->orderModel = new Order();
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

    public function updateUser(int $id, array $data)
    {
        $existingUser = $this->userModel->getById($id);

        if ($existingUser) {
            $updatedData = array_merge($existingUser, $data);

            $filteredData = [
                'name' => $updatedData['name'],
                'email' => $updatedData['email'],
                'password_hash' => $updatedData['password_hash'],
                'role' => $updatedData['role'],
            ];

            // Update the user with filtered data
            $this->userModel->update($id, $filteredData);
        }
    }

    public function deleteUser(int $id)
    {
        $this->userModel->delete($id);
    }

    public function getUserOrders(int $userId)
    {
        return $this->orderModel->getOrdersByUserId($userId);
    }
}
