<?php

namespace App\Migrations;

use App\Database;

class CreateOrderStatusTable
{
    public function up()
    {
        $db = (new Database())->getConnection();
        $sql = "
        CREATE TABLE IF NOT EXISTS order_statuses (
            id SERIAL PRIMARY KEY,
            title VARCHAR(100) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $db->exec($sql);

        // Insert predefined statuses
        $statuses = [
            'new',
            'accepted',
            'loading',
            'on_the_way',
            'unloading',
            'finish'
        ];

        foreach ($statuses as $status) {
            $stmt = $db->prepare("INSERT INTO order_statuses (title) VALUES (:title) ON CONFLICT (title) DO NOTHING");
            $stmt->execute([':title' => $status]);
        }
    }

    public function down()
    {
        $db = (new Database())->getConnection();
        $db->exec("DROP TABLE IF EXISTS order_statuses CASCADE");
    }
}