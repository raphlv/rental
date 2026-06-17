<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Create the database first via standard PDO
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbName = 'rental_db';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "<h3>1. Database '$dbName' verified/created successfully.</h3>";
} catch (PDOException $e) {
    die("Database creation failed: " . $e->getMessage());
}

// 2. Modify the .env file to use the new database and correct URLs
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    
    // Replace DB_DATABASE
    $envContent = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE=' . $dbName, $envContent);
    // Replace APP_URL
    $envContent = preg_replace('/APP_URL=.*/', 'APP_URL=http://localhost/rental/public', $envContent);
    
    file_put_contents($envPath, $envContent);
    echo "<h3>2. .env file updated successfully.</h3>";
} else {
    echo "<h3>2. Warning: .env file not found.</h3>";
}

// 3. Bootstrap Laravel and run migrations + seed
try {
    echo "<h3>3. Bootstrapping Laravel and running migrations...</h3>";
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    // Clear config cache to make sure .env changes are read
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    
    // Run migrations and seeders
    $exitCode = \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
        '--seed' => true,
        '--force' => true
    ]);
    
    $output = \Illuminate\Support\Facades\Artisan::output();
    echo "<pre>Migration Output:\n" . htmlspecialchars($output) . "</pre>";
    
    if ($exitCode === 0) {
        echo "<h3 style='color: green;'>Success! Database is ready and seeded.</h3>";
        echo "<p><a href='/rental/public/' style='font-size: 1.2rem; font-weight: bold;'>Go to Rental Dashboard</a></p>";
    } else {
        echo "<h3 style='color: red;'>Migration failed with exit code: $exitCode</h3>";
    }
} catch (\Exception $e) {
    echo "<h3 style='color: red;'>Error running migrations:</h3>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
