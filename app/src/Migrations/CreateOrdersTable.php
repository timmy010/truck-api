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
            customer_id INTEGER REFERENCES users(id) ON DELETE CASCADE NOT NULL,
            carrier_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
            pickup_location VARCHAR(255) NOT NULL,
            delivery_location VARCHAR(255) NOT NULL,
            freight_rate DECIMAL(10, 2) NOT NULL,
            scheduled_loading_date TIMESTAMP NOT NULL,
            scheduled_unloading_date TIMESTAMP NOT NULL,
            actual_loading_date TIMESTAMP,
            actual_unloading_date TIMESTAMP,
            status INTEGER REFERENCES order_statuses(id) ON DELETE CASCADE NOT NULL,
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
