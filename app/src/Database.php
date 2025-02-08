<?php

namespace App;

use PDO;
use PDOException;
use Exception;

class Database
{
    private $pdo;

    public function __construct()
    {
        $dsn = 'pgsql:host=' . $_ENV['POSTGRES_HOST'] . ';dbname=' . $_ENV['POSTGRES_DATABASE'] . ';port=5432';
        $username = $_ENV['POSTGRES_USER'];
        $password = $_ENV['POSTGRES_PASSWORD'];

        try {
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
