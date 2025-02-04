<?php

namespace App\Migrations;

use App\Database;

class CreateProfilesTable
{
    public function up()
    {
        $db = (new Database())->getConnection();
        $sql = "
        CREATE TABLE IF NOT EXISTS profiles (
            id SERIAL PRIMARY KEY,
            user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
            phone VARCHAR(15),
            actual_address VARCHAR(255),
            legal_address VARCHAR(255),
            company_name VARCHAR(255),
            inn VARCHAR(15),
            ogrn VARCHAR(15),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $db->exec($sql);

        $db->exec("CREATE INDEX IF NOT EXISTS idx_user_id ON profiles (user_id)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_phone ON profiles (phone)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_company_name ON profiles (company_name)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_inn ON profiles (inn)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_ogrn ON profiles (ogrn)");
    }

    public function down()
    {
        $db = (new Database())->getConnection();
        $db->exec("DROP INDEX IF EXISTS idx_user_id");
        $db->exec("DROP INDEX IF EXISTS idx_phone");
        $db->exec("DROP INDEX IF EXISTS idx_company_name");
        $db->exec("DROP INDEX IF EXISTS idx_inn");
        $db->exec("DROP INDEX IF EXISTS idx_ogrn");
        $db->exec("DROP TABLE IF EXISTS profiles CASCADE");
    }
}
