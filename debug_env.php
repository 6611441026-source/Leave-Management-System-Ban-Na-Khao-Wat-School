<?php
// Debug: Show actual resolved environment variables from Railway
// DELETE this file after debugging!
header('Content-Type: text/plain');

$keys = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_CHARSET', 'PORT', 'MYSQL_URL', 'DATABASE_URL'];

foreach ($keys as $key) {
    $fromGetenv = getenv($key);
    $fromEnv    = $_ENV[$key] ?? '(not in $_ENV)';
    echo "$key:\n";
    echo "  getenv() = " . ($fromGetenv === false ? '(false/not set)' : $fromGetenv) . "\n";
    echo "  \$_ENV    = $fromEnv\n\n";
}

// Try connecting
$host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'not-set');
$port = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '3306');
$name = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'not-set');
$user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'not-set');
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($_ENV['DB_PASS'] ?? '');

echo "=== Trying connection ===\n";
echo "Host: $host\nPort: $port\nDB: $name\nUser: $user\nPass: " . (empty($pass) ? '(empty)' : '***') . "\n\n";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "✅ Connection SUCCESS!\n";
} catch (Exception $e) {
    echo "❌ Connection FAILED: " . $e->getMessage() . "\n";
}
