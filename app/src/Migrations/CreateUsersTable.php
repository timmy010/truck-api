<?php

namespace App\Migrations;

use App\Database;

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
            role VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $db->exec($sql);

        $db->exec("CREATE INDEX idx_name ON users (name)");
        $db->exec("CREATE INDEX idx_email ON users (email)");
    }

    public function down()
    {
        $db = (new Database())->getConnection();
        $db->exec("DROP TABLE IF EXISTS users");
        $db->exec("DROP INDEX IF EXISTS idx_name");
        $db->exec("DROP INDEX IF EXISTS idx_email");
    }
}
