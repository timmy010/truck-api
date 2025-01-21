<?php

namespace App\Interfaces;

interface ProfileInterface
{
    public function create(array $data);
    public function getAll();
    public function getByUserId(int $userId);
    public function update(int $id, array $data);
    public function delete(int $id);
}
