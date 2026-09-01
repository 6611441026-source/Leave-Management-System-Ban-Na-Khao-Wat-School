<?php
/**
 * Configuration File
 * ระบบบริหารจัดการการลา - โรงเรียนบ้านหน้าเขาวัด
 */

// ป้องกันการเข้าถึงไฟล์โดยตรง
if (!defined('BASE_PATH')) {
    exit('No direct script access allowed');
}

function loadDotEnv(string $path): void
{
    if (!is_file($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $parts = explode('=', $line, 2);
        $key   = trim($parts[0]);
        $value = isset($parts[1]) ? trim($parts[1]) : '';

        if ($key === '') {
            continue;
        }

        // Only set if NOT already defined in the environment (Railway vars take priority)
        $existing = getenv($key);
        if (($existing === false || $existing === '') && !isset($_ENV[$key])) {
            putenv($key . '=' . $value);
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }
}

loadDotEnv(BASE_PATH . '/.env');

// =========================================
// Parse MYSQL_URL / DATABASE_URL if provided (Railway)
// =========================================
$mysqlUrl = getenv('MYSQL_URL') ?: getenv('DATABASE_URL') ?: ($_ENV['MYSQL_URL'] ?? $_ENV['DATABASE_URL'] ?? '');
if (!empty($mysqlUrl)) {
    $parsed = parse_url($mysqlUrl);
    if ($parsed !== false) {
        if (!empty($parsed['host']))     { putenv('DB_HOST=' . $parsed['host']); $_ENV['DB_HOST'] = $parsed['host']; }
        if (!empty($parsed['port']))     { putenv('DB_PORT=' . $parsed['port']); $_ENV['DB_PORT'] = $parsed['port']; }
        if (!empty($parsed['user']))     { putenv('DB_USER=' . $parsed['user']); $_ENV['DB_USER'] = $parsed['user']; }
        if (!empty($parsed['pass']))     { putenv('DB_PASS=' . $parsed['pass']); $_ENV['DB_PASS'] = $parsed['pass']; }
        if (!empty($parsed['path']))     { $db = ltrim($parsed['path'], '/'); putenv('DB_NAME=' . $db); $_ENV['DB_NAME'] = $db; }
    }
}

// =========================================
// Database Configuration
// =========================================
// ใช้ $_ENV แทน getenv() เพื่อให้ค่าจาก .env ชนะค่าใน process environment เดิม
$dbHost    = $_ENV['DB_HOST']    ?? getenv('DB_HOST');
$dbPort    = $_ENV['DB_PORT']    ?? getenv('DB_PORT');
$dbName    = $_ENV['DB_NAME']    ?? getenv('DB_NAME');
$dbUser    = $_ENV['DB_USER']    ?? getenv('DB_USER');
$dbPass    = $_ENV['DB_PASS']    ?? '';
$dbCharset = $_ENV['DB_CHARSET'] ?? getenv('DB_CHARSET');

if ($dbHost === false || $dbHost === '') {
    $dbHost = '127.0.0.1';
}
if ($dbPort === false || $dbPort === '') {
    $dbPort = '3306';
}
if ($dbName === false || $dbName === '') {
    $dbName = 'leave_management';
}
if ($dbUser === false || $dbUser === '') {
    $dbUser = 'root';
}
if ($dbPass === false) {
    $dbPass = '';
}
if ($dbCharset === false || $dbCharset === '') {
    $dbCharset = 'utf8mb4';
}

define('DB_HOST', $dbHost);
define('DB_PORT', $dbPort);
define('DB_NAME', $dbName);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass);
define('DB_CHARSET', $dbCharset);

// =========================================
// Application Configuration
// =========================================
define('APP_NAME', 'ระบบบริหารจัดการการลา');
define('APP_SUBTITLE', 'โรงเรียนบ้านหน้าเขาวัด');
define('APP_VERSION', '1.0.0');

// Auto-detect base URL (handling SSL proxies like Railway)
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
    || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on');

$protocol  = $isHttps ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseUrl   = $protocol . '://' . $host . ($scriptDir === '/' ? '/' : $scriptDir . '/');
define('BASE_URL', $baseUrl);

// =========================================
// Session Configuration
// =========================================
define('SESSION_LIFETIME', 1800); // 30 นาที
define('SESSION_NAME', 'leave_mgmt_session');

// =========================================
// Upload Configuration
// =========================================
define('UPLOAD_DIR', BASE_PATH . '/public/assets/uploads/');
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
]);
define('UPLOAD_ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);

// =========================================
// Timezone
// =========================================
date_default_timezone_set('Asia/Bangkok');

// =========================================
// Error Reporting (ปิดใน Production)
// =========================================
error_reporting(E_ALL);
ini_set('display_errors', 1);
