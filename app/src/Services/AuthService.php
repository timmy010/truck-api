<?php

namespace App\Services;

class AuthService
{
    public function generateToken($userId)
    {
        // Генерация токена (например, JWT)
        return base64_encode($userId . ':' . time());
    }

    public function validateToken($token)
    {
        // Валидация токена
        return true; // Пример, всегда возвращает true
    }
}
