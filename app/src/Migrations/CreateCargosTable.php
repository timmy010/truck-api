<?php

namespace App\Migrations;

use App\Database;

class CreateCargosTable
{
    public function up()
    {
        $db = (new Database())->getConnection();
        $sql = "
        CREATE TABLE IF NOT EXISTS cargos (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            volume DECIMAL(10, 2) NOT NULL,
            weight DECIMAL(10, 2) NOT NULL,
            length DECIMAL(10, 2),
            width DECIMAL(10, 2),
            depth DECIMAL(10, 2),
            cost DECIMAL(10, 2),
            order_id INTEGER REFERENCES orders(id) ON DELETE CASCADE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $db->exec($sql);

        $db->exec("CREATE INDEX IF NOT EXISTS idx_title ON cargos (title)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_order_id ON cargos (order_id)");
    }

    public function down()
    {
        $db = (new Database())->getConnection();
        $db->exec("DROP INDEX IF EXISTS idx_title");
        $db->exec("DROP INDEX IF EXISTS idx_order_id");
        $db->exec("DROP TABLE IF EXISTS cargos CASCADE");
    }
}