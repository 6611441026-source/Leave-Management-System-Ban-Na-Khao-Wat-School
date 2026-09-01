<?php

define('BASE_PATH', getcwd());
require 'app/config/config.php';
require 'core/Database.php';

try {
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->query('SELECT DATABASE() AS db_name');
    $row = $stmt->fetch();
    echo $row['db_name'];
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage());
    exit(1);
}
