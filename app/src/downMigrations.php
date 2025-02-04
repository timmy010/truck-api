<?php

use App\Migrations\CreateCargosTable;
use App\Migrations\CreateOrdersTable;
use App\Migrations\CreateOrderStatusTable;
use App\Migrations\CreateProfilesTable;
use App\Migrations\CreateUsersTable;

require __DIR__ . '/../vendor/autoload.php';

$migrations = [
    new CreateOrderStatusTable(),
    new CreateUsersTable(),
    new CreateOrdersTable(),
    new CreateCargosTable(),
    new CreateProfilesTable(),
];

$successCount = 0;
$errorCount = 0;

foreach ($migrations as $migration) {
    try {
        $migration->down();
        echo "Migration for " . get_class($migration) . " executed successfully.\n";
        $successCount++;
    } catch (Exception $e) {
        echo "Error in migration for " . get_class($migration) . ": " . $e->getMessage() . "\n";
        $errorCount++;
    }
}

echo "\nMigration Summary:\n";
echo "Total Migrations: " . count($migrations) . "\n";
echo "Successful: " . $successCount . "\n";
echo "Failed: " . $errorCount . "\n";

if ($errorCount > 0) {
    exit(1);
}
