<?php
/**
 * Database Class (Singleton Pattern)
 * PDO Connection with Prepared Statements
 */

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(503);
            echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>ไม่สามารถเชื่อมต่อฐานข้อมูล</title></head><body>';
            echo '<h2>⚠️ ไม่สามารถเชื่อมต่อฐานข้อมูลได้</h2>';
            echo '<p>กรุณาตรวจสอบการตั้งค่า Database Environment Variables บน Railway</p>';
            echo '<pre style="background:#f5f5f5;padding:10px">' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '</body></html>';
            exit(0);
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    private function __clone() {}

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
