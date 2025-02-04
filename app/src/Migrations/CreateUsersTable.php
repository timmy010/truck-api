<?php

namespace App\Migrations;

use App\Database;
use Dotenv\Dotenv;

class CreateUsersTable
{
    public function up()
    {
        $db = (new Database())->getConnection();
        $sql = "
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            api_key VARCHAR(255),
            role VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $db->exec($sql);

        $db->exec("CREATE INDEX IF NOT EXISTS idx_name ON users (name)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_email ON users (email)");

        $stmt = $db->prepare("INSERT INTO users (name, email, password_hash, api_key, role, created_at, updated_at) 
            VALUES (:name, :email, :password_hash, :api_key, :role, NOW(), NOW())");
        $stmt->execute($this->prepareAdminUserdata());
    }

    public function down()
    {
        $db = (new Database())->getConnection();
        $db->exec("DROP INDEX IF EXISTS idx_name");
        $db->exec("DROP INDEX IF EXISTS idx_email");
        $db->exec("DROP TABLE IF EXISTS users CASCADE");
    }

    private function prepareAdminUserdata(): array
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();

        return [
            'name' => 'admin',
            'email' => $_ENV['ADMIN_USER_EMAIL'],
            'password_hash' => password_hash($_ENV['ADMIN_USER_PASSWORD'], PASSWORD_DEFAULT),
            'api_key' => bin2hex(random_bytes(16)),
            'role' => 'admin'
        ];
    }
}
