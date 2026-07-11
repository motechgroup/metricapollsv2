<?php
/**
 * Metrica Polls - Database Connection Diagnoser
 */
header('Content-Type: text/plain; charset=utf-8');

try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    // Boot the console kernel to bootstrap database configurations
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    // Query active connection details
    $defaultConnection = DB::getDefaultConnection();
    $driverName = DB::connection()->getDriverName();
    $databaseName = DB::connection()->getDatabaseName();
    $dbHost = config("database.connections.{$defaultConnection}.host", 'N/A');

    echo "--- LARAVEL ACTIVE DATABASE DIAGNOSTIC ---\n\n";
    echo "Default Connection:  " . $defaultConnection . "\n";
    echo "Active Driver (PDO): " . $driverName . "\n";
    echo "Database Host:       " . $dbHost . "\n";
    echo "Database Name:       " . $databaseName . "\n";

} catch (\Throwable $e) {
    echo "Database diagnostic failed: " . $e->getMessage() . "\n";
}
