<?php

use App\Migrations\CreateAdminRolePermissionsTable;
use App\Migrations\CreateOrdersTable;
use App\Migrations\CreateProfilesTable;
use App\Migrations\CreateUsersTable;

require __DIR__ . '/../vendor/autoload.php';

$migrations = [
    new CreateUsersTable(),
    new CreateOrdersTable(),
    new CreateProfilesTable(),
    new CreateAdminRolePermissionsTable(),
];

$successCount = 0;
$errorCount = 0;

foreach ($migrations as $migration) {
    try {
        $migration->up();
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
