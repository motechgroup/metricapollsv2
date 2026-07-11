<?php
/**
 * Metrica Polls - Shared Hosting Web Installer
 * Boots Laravel kernel internally to run migrations, seeds, and configurations.
 */

define('LARAVEL_START', microtime(true));

// 1. Check if vendor/autoload.php exists
$vendorExists = file_exists(__DIR__ . '/vendor/autoload.php');
if (!$vendorExists) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Metrica Polls Installer - Dependencies Missing</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6; color: #1f2937; margin: 0; padding: 40px 20px; }
            .card { max-width: 600px; margin: 40px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; }
            h1 { color: #b91c1c; font-size: 22px; margin-top: 0; }
            p { font-size: 15px; line-height: 1.6; }
            code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-family: monospace; font-size: 14px; }
            .btn { display: inline-block; background: #13254A; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: 600; margin-top: 15px; }
        </style>
    </head>
    <body>
        <div class="card">
            <h1>Dependencies Missing (vendor/autoload.php)</h1>
            <p>The application dependencies are not installed. Since shared hosting environments often do not allow running <code>composer install</code> directly via SSH, please follow these simple steps:</p>
            <ol style="padding-left: 20px; font-size: 14px; line-height: 1.8; margin-bottom: 20px;">
                <li>Run <code>composer install --no-dev --optimize-autoloader</code> locally on your development computer.</li>
                <li>Upload the generated <code>vendor/</code> directory to your shared hosting root folder.</li>
                <li>Refresh this page to complete the database configuration.</li>
            </ol>
            <a href="javascript:location.reload();" class="btn">Refresh Installer</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// 2. Check Server Requirements & Permissions
$requirements = [
    'PHP >= 8.2' => PHP_VERSION_ID >= 80200,
    'BCMath Extension' => extension_loaded('bcmath'),
    'Ctype Extension' => extension_loaded('ctype'),
    'Fileinfo Extension' => extension_loaded('fileinfo'),
    'JSON Extension' => extension_loaded('json'),
    'Mbstring Extension' => extension_loaded('mbstring'),
    'OpenSSL Extension' => extension_loaded('openssl'),
    'PDO Extension' => extension_loaded('pdo'),
    'Tokenizer Extension' => extension_loaded('tokenizer'),
    'XML Extension' => extension_loaded('xml'),
];

$writePermissions = [
    'storage/' => is_writable(__DIR__ . '/storage'),
    'bootstrap/cache/' => is_writable(__DIR__ . '/bootstrap/cache'),
    'database/' => is_writable(__DIR__ . '/database'),
    'root directory' => is_writable(__DIR__),
];

$errors = [];
foreach ($requirements as $name => $passed) {
    if (!$passed) $errors[] = "Requirement failed: {$name} is required.";
}
foreach ($writePermissions as $name => $passed) {
    if (!$passed) $errors[] = "Permission failed: {$name} directory must be writable.";
}

// 3. Handle Env Submission & Installation Trigger
$message = '';
$step = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
    $dbConnection = $_POST['db_connection'] ?? 'sqlite';
    $dbHost = $_POST['db_host'] ?? '127.0.0.1';
    $dbPort = $_POST['db_port'] ?? '3306';
    $dbName = $_POST['db_database'] ?? 'metricapolls';
    $dbUser = $_POST['db_username'] ?? 'root';
    $dbPass = $_POST['db_password'] ?? '';
    $appUrl = $_POST['app_url'] ?? 'http://localhost';

    // Build .env content
    $envTemplate = "APP_NAME=\"Metrica Polls\"\n" .
                   "APP_ENV=production\n" .
                   "APP_KEY=\n" .
                   "APP_DEBUG=false\n" .
                   "APP_URL=" . rtrim($appUrl, '/') . "\n\n" .
                   "LOG_CHANNEL=stack\n" .
                   "LOG_LEVEL=debug\n\n";

    if ($dbConnection === 'sqlite') {
        $dbPath = __DIR__ . '/database/database.sqlite';
        if (!file_exists($dbPath)) {
            touch($dbPath);
        }
        $envTemplate .= "DB_CONNECTION=sqlite\n" .
                        "DB_DATABASE=\"{$dbPath}\"\n";
    } else {
        $envTemplate .= "DB_CONNECTION={$dbConnection}\n" .
                        "DB_HOST={$dbHost}\n" .
                        "DB_PORT={$dbPort}\n" .
                        "DB_DATABASE={$dbName}\n" .
                        "DB_USERNAME={$dbUser}\n" .
                        "DB_PASSWORD=\"{$dbPass}\"\n";
    }

    $envTemplate .= "\nSESSION_DRIVER=database\n" .
                    "SESSION_LIFETIME=120\n\n" .
                    "CACHE_STORE=database\n\n" .
                    "MAIL_MAILER=smtp\n" .
                    "MAIL_HOST=smtp.mailtrap.io\n" .
                    "MAIL_PORT=2525\n" .
                    "MAIL_USERNAME=null\n" .
                    "MAIL_PASSWORD=null\n" .
                    "MAIL_ENCRYPTION=null\n" .
                    "MAIL_FROM_ADDRESS=\"noreply@metricapolls.com\"\n" .
                    "MAIL_FROM_NAME=\"Metrica Polls\"\n";

    file_put_contents(__DIR__ . '/.env', $envTemplate);

    try {
        // Boot Laravel Kernel Internally
        require_once __DIR__ . '/vendor/autoload.php';
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

        // Run key generation
        $kernel->call('key:generate', ['--force' => true]);

        // Run database migrations
        $kernel->call('migrate:fresh', ['--force' => true]);

        // Run database seeders
        $kernel->call('db:seed', ['--force' => true]);

        // Create storage symlink
        try {
            $kernel->call('storage:link');
        } catch (\Exception $se) {
            // Ignore if symlink already exists
        }

        $step = 3; // Success!
    } catch (\Throwable $e) {
        $message = "Database connection or migration failed: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine();
        $step = 2; // Return to form
    }
} elseif (file_exists(__DIR__ . '/.env') && empty($_POST)) {
    $step = 2;
}

?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metrica Polls Web Installer</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6; color: #1f2937; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 60px auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; }
        .logo-header { text-align: center; margin-bottom: 24px; }
        .logo-header h1 { font-size: 24px; color: #13254A; margin: 6px 0; }
        .logo-header p { font-size: 14px; color: #6b7280; margin: 0; }
        .alert-error { background-color: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; padding: 12px; rounded: 6px; font-size: 14px; margin-bottom: 20px; border-radius: 6px; }
        .requirement-list { list-style: none; padding: 0; margin-bottom: 20px; }
        .requirement-list li { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
        .badge-success { color: #047857; font-weight: bold; }
        .badge-fail { color: #b91c1c; font-weight: bold; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-group input, .form-group select { width: 100%; padding: 8px 12px; box-sizing: border-box; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; outline: none; }
        .form-group input:focus, .form-group select:focus { border-color: #13254A; }
        .btn-submit { display: block; width: 100%; background-color: #13254A; color: white; border: none; padding: 12px; font-size: 14px; font-weight: 600; border-radius: 6px; cursor: pointer; text-align: center; text-decoration: none; }
        .btn-submit:hover { background-color: #0A58CA; }
        .success-box { text-align: center; }
        .success-icon { font-size: 48px; color: #047857; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-header">
            <h1>Metrica Polls</h1>
            <p>Shared Hosting Application Setup</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert-error">
                <strong>System Issues Detected:</strong>
                <p>Please resolve the requirements below to install the system.</p>
            </div>
            <ul class="requirement-list">
                <?php foreach ($requirements as $name => $passed): ?>
                    <li>
                        <span><?php echo $name; ?></span>
                        <span class="<?php echo $passed ? 'badge-success' : 'badge-fail'; ?>"><?php echo $passed ? 'PASSED' : 'FAILED'; ?></span>
                    </li>
                <?php endforeach; ?>
                <?php foreach ($writePermissions as $name => $passed): ?>
                    <li>
                        <span>Writable: <?php echo $name; ?></span>
                        <span class="<?php echo $passed ? 'badge-success' : 'badge-fail'; ?>"><?php echo $passed ? 'YES' : 'NO (Chmod required)'; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($step === 1): ?>
            <div style="text-align: center; padding: 20px 0;">
                <p style="font-size: 15px; color: #4b5563; margin-bottom: 24px;">All system configurations and write permissions look perfect. Ready to initialize settings.</p>
                <form method="POST">
                    <input type="hidden" name="dummy" value="1">
                    <button type="submit" class="btn-submit" onclick="this.innerHTML='Initializing installation...';">Start Configuration Setup</button>
                </form>
            </div>
        <?php elseif ($step === 2): ?>
            <?php if ($message): ?>
                <div class="alert-error"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Database Connection Driver</label>
                    <select name="db_connection" onchange="toggleDbFields(this.value)">
                        <option value="sqlite">SQLite (Recommended for fast setup)</option>
                        <option value="mysql">MySQL / MariaDB</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Application Deployment URL</label>
                    <input type="url" name="app_url" value="<?php echo 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'); ?>" required>
                </div>
                <div id="mysql_fields" style="display:none;">
                    <div class="form-group">
                        <label>Database Host</label>
                        <input type="text" name="db_host" value="127.0.0.1">
                    </div>
                    <div class="form-group">
                        <label>Database Port</label>
                        <input type="text" name="db_port" value="3306">
                    </div>
                    <div class="form-group">
                        <label>Database Name</label>
                        <input type="text" name="db_database" value="metricapolls">
                    </div>
                    <div class="form-group">
                        <label>Database Username</label>
                        <input type="text" name="db_username" value="root">
                    </div>
                    <div class="form-group">
                        <label>Database Password</label>
                        <input type="password" name="db_password" value="">
                    </div>
                </div>
                <button type="submit" class="btn-submit" onclick="this.innerHTML='Setting up database schema and seed profiles...';">Run Installer</button>
            </form>
            <script>
                function toggleDbFields(val) {
                    document.getElementById('mysql_fields').style.display = val === 'mysql' ? 'block' : 'none';
                }
            </script>
        <?php elseif ($step === 3): ?>
            <div class="success-box">
                <div class="success-icon">✓</div>
                <h2>Metrica Polls Installed Successfully!</h2>
                <p style="color: #4b5563; font-size: 14px; margin-bottom: 24px;">Database migration has run, RBAC roles are created, and default system credentials have been seeded.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 6px; margin-bottom: 24px; text-align: left; font-size: 13px;">
                    <strong>Next Steps:</strong>
                    <ol style="margin-top: 8px; padding-left: 20px;">
                        <li>Delete this file (<code>install.php</code>) from your server root immediately for security.</li>
                        <li>Log in to your admin dashboard via Google Login.</li>
                    </ol>
                </div>
                
                <a href="./" class="btn-submit">Go to Metrica Polls Home</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
