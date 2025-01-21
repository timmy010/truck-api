<?php

namespace App\Migrations;

use App\Database;

class CreateAdminRolePermissionsTable
{
    public function up()
    {
        $db = (new Database())->getConnection();
        $sql = "
        CREATE TABLE IF NOT EXISTS admin_role_permissions (
            id SERIAL PRIMARY KEY,
            admin_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
            permission VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $db->exec($sql);
    }

    public function down()
    {
        $db = (new Database())->getConnection();
        $db->exec("DROP TABLE IF EXISTS admin_role_permissions");
    }
}
