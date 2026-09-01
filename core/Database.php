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
            $this->ensureSchemaInitialized();
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

    private function ensureSchemaInitialized(): void
    {
        try {
            $stmt = $this->pdo->query("SHOW TABLES LIKE 'users'");
            if ($stmt && $stmt->rowCount() === 0) {
                $schemaFile = defined('BASE_PATH') ? BASE_PATH . '/database/schema.sql' : __DIR__ . '/../database/schema.sql';
                if (file_exists($schemaFile)) {
                    $sql = file_get_contents($schemaFile);
                    $this->pdo->exec($sql);
                }

                $this->seedDefaultUsers();
            }
        } catch (\Throwable $e) {
            error_log("Schema auto-init error: " . $e->getMessage());
        }
    }

    private function seedDefaultUsers(): void
    {
        $users = [
            [
                'employee_id' => 'ADM001',
                'first_name'  => 'สมศักดิ์',
                'last_name'   => 'รักดี',
                'email'       => 'admin@school.ac.th',
                'username'    => 'admin',
                'password'    => password_hash('admin1234', PASSWORD_DEFAULT),
                'role'        => 'admin',
                'position'    => 'ผู้ดูแลระบบ',
                'department'  => 'สำนักงาน',
                'phone'       => '081-000-0001',
            ],
            [
                'employee_id' => 'EXE001',
                'first_name'  => 'อนันต์',
                'last_name'   => 'ผู้บริหาร',
                'email'       => 'executive@school.ac.th',
                'username'    => 'executive',
                'password'    => password_hash('exec1234', PASSWORD_DEFAULT),
                'role'        => 'executive',
                'position'    => 'ผู้อำนวยการโรงเรียน',
                'department'  => 'บริหาร',
                'phone'       => '081-000-0002',
            ],
            [
                'employee_id' => 'STF001',
                'first_name'  => 'ศิริพร',
                'last_name'   => 'สมบูรณ์',
                'email'       => 'staff@school.ac.th',
                'username'    => 'staff',
                'password'    => password_hash('staff1234', PASSWORD_DEFAULT),
                'role'        => 'personnel',
                'position'    => 'ครูผู้ช่วย',
                'department'  => 'วิชาการ',
                'phone'       => '081-000-0003',
            ],
        ];

        $sql = "INSERT INTO users 
                    (employee_id, first_name, last_name, email, username, password, role, position, department, phone)
                VALUES 
                    (:employee_id, :first_name, :last_name, :email, :username, :password, :role, :position, :department, :phone)
                ON DUPLICATE KEY UPDATE
                    password = VALUES(password)";

        $stmt = $this->pdo->prepare($sql);
        foreach ($users as $user) {
            $stmt->execute($user);
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
