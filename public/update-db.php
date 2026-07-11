<?php

// Prevent execution if not authorized
if (isset($_GET['key']) && $_GET['key'] !== 'metrica_update_2026') {
    die('Unauthorized access key.');
}

if (!isset($_GET['key'])) {
    echo "<h1>Database Update tool</h1>";
    echo "<p>Please visit this URL with the correct security key to run updates, for example:</p>";
    echo "<code>" . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "?key=metrica_update_2026</code>";
    exit;
}

define('LARAVEL_START', microtime(true));

// Locate vendor autoload
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
} else {
    die('Autoload file not found. Make sure vendor folder exists.');
}

// Boot Laravel Application
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

try {
    // Run migrations programmatically
    Artisan::call('migrate', ['--force' => true]);
    $output = Artisan::output();

    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 24px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc;'>";
    echo "<h1 style='color: #15803d; margin-top: 0;'>Database Updated Successfully!</h1>";
    echo "<p>Migrations output:</p>";
    echo "<pre style='background: #0f172a; color: #f8fafc; padding: 16px; border-radius: 6px; overflow-x: auto; font-family: monospace; font-size: 13px;'>" . htmlentities($output) . "</pre>";
    echo "<p style='color: #b91c1c; font-weight: bold;'>⚠️ IMPORTANT SECURITY NOTICE: Please delete the <code>update-db.php</code> file from your server immediately after use.</p>";
    echo "</div>";
} catch (\Throwable $e) {
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 24px; border: 1px solid #fecaca; border-radius: 8px; background: #fff5f5;'>";
    echo "<h1 style='color: #b91c1c; margin-top: 0;'>Database Migration Failed!</h1>";
    echo "<p>Error details:</p>";
    echo "<pre style='background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 6px; overflow-x: auto; font-family: monospace; font-size: 13px;'>" . htmlentities($e->getMessage()) . "</pre>";
    echo "</div>";
}
