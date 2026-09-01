<?php
/**
 * install_users.php
 * สร้างบัญชีผู้ใช้ทดสอบสำหรับระบบ
 * รันครั้งเดียวหลังจาก import schema.sql
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/app/config/config.php';

$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
    DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
);

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);
} catch (PDOException $e) {
    echo "[ERROR] ไม่สามารถเชื่อมต่อฐานข้อมูล: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

// รายการผู้ใช้ทดสอบ
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
            password = VALUES(password),
            position = VALUES(position),
            department = VALUES(department),
            phone = VALUES(phone)";

$stmt = $pdo->prepare($sql);

foreach ($users as $user) {
    $stmt->execute($user);
    echo "  สร้างบัญชี [{$user['role']}] {$user['username']} เรียบร้อย" . PHP_EOL;
}

echo PHP_EOL;
echo "====================================" . PHP_EOL;
echo " สร้างบัญชีผู้ใช้ทดสอบสำเร็จทั้งหมด" . PHP_EOL;
echo "====================================" . PHP_EOL;
echo " admin      / admin1234" . PHP_EOL;
echo " executive  / exec1234" . PHP_EOL;
echo " staff      / staff1234" . PHP_EOL;
echo "====================================" . PHP_EOL;
