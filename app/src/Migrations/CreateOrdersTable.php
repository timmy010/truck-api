<?php

namespace App\Migrations;

use App\Database;

class CreateOrdersTable
{
    public function up()
    {
        $db = (new Database())->getConnection();
        $sql = "
        CREATE TABLE IF NOT EXISTS orders (
            id SERIAL PRIMARY KEY,
            customer_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
            carrier_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
            pickup_location VARCHAR(255),
            delivery_location VARCHAR(255),
            cargo_description TEXT,
            freight_rate DECIMAL(10, 2),
            scheduled_loading_date TIMESTAMP,
            scheduled_unloading_date TIMESTAMP,
            actual_loading_date TIMESTAMP,
            actual_unloading_date TIMESTAMP,
            status VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $db->exec($sql);

        $db->exec("CREATE INDEX idx_customer_id ON orders (customer_id)");
        $db->exec("CREATE INDEX idx_carrier_id ON orders (carrier_id)");
        $db->exec("CREATE INDEX idx_status ON orders (status)");
    }

    public function down()
    {
        $db = (new Database())->getConnection();
        $db->exec("DROP INDEX IF EXISTS idx_customer_id");
        $db->exec("DROP INDEX IF EXISTS idx_carrier_id");
        $db->exec("DROP INDEX IF EXISTS idx_status");
        $db->exec("DROP TABLE IF EXISTS orders");
    }
}
