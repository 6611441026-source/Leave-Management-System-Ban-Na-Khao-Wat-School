-- =========================================
-- ระบบบริหารจัดการการลา
-- โรงเรียนบ้านหน้าเขาวัด
-- Database Schema
-- =========================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- CREATE DATABASE IF NOT EXISTS leave_management
--     CHARACTER SET utf8mb4
--     COLLATE utf8mb4_unicode_ci;
-- 
-- USE leave_management;

-- =========================================
-- ลบตารางเดิม (ถ้ามี) ตามลำดับ FK
-- =========================================
DROP TABLE IF EXISTS leave_requests;
DROP TABLE IF EXISTS fiscal_years;
DROP TABLE IF EXISTS leave_types;
DROP TABLE IF EXISTS users;

-- =========================================
-- ตาราง users (ผู้ใช้งาน)
-- =========================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(20) NOT NULL UNIQUE COMMENT 'รหัสบุคลากร',
    first_name VARCHAR(100) NOT NULL COMMENT 'ชื่อ',
    last_name VARCHAR(100) NOT NULL COMMENT 'นามสกุล',
    email VARCHAR(150) NOT NULL UNIQUE COMMENT 'อีเมล',
    username VARCHAR(50) NOT NULL UNIQUE COMMENT 'ชื่อผู้ใช้',
    password VARCHAR(255) NOT NULL COMMENT 'รหัสผ่าน (bcrypt)',
    role ENUM('admin', 'executive', 'personnel') NOT NULL DEFAULT 'personnel' COMMENT 'บทบาท',
    position VARCHAR(100) DEFAULT NULL COMMENT 'ตำแหน่ง',
    department VARCHAR(100) DEFAULT NULL COMMENT 'สังกัด/กลุ่มงาน',
    phone VARCHAR(20) DEFAULT NULL COMMENT 'เบอร์โทรศัพท์',
    avatar VARCHAR(255) DEFAULT NULL COMMENT 'รูปโปรไฟล์',
    is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'สถานะการใช้งาน',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางผู้ใช้งาน';

-- =========================================
-- ตาราง leave_types (ประเภทการลา)
-- =========================================
CREATE TABLE leave_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'ชื่อประเภทการลา',
    description TEXT DEFAULT NULL COMMENT 'รายละเอียด',
    max_days INT NOT NULL DEFAULT 0 COMMENT 'จำนวนวันลาสูงสุดต่อปี',
    is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'สถานะ',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางประเภทการลา';

-- =========================================
-- ตาราง fiscal_years (ปีงบประมาณ)
-- =========================================
CREATE TABLE fiscal_years (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL COMMENT 'ชื่อปีงบประมาณ',
    start_date DATE NOT NULL COMMENT 'วันเริ่มต้น',
    end_date DATE NOT NULL COMMENT 'วันสิ้นสุด',
    is_current TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'ปีงบประมาณปัจจุบัน',
    is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'สถานะ',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางปีงบประมาณ';

-- =========================================
-- ตาราง leave_requests (ใบลา)
-- =========================================
CREATE TABLE leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'รหัสผู้ยื่นลา',
    leave_type_id INT NOT NULL COMMENT 'ประเภทการลา',
    fiscal_year_id INT NOT NULL COMMENT 'ปีงบประมาณ',
    start_date DATE NOT NULL COMMENT 'วันที่เริ่มลา',
    end_date DATE NOT NULL COMMENT 'วันที่สิ้นสุดลา',
    total_days DECIMAL(5,1) NOT NULL COMMENT 'จำนวนวันลา',
    reason TEXT NOT NULL COMMENT 'เหตุผลการลา',
    attachment VARCHAR(255) DEFAULT NULL COMMENT 'ไฟล์แนบ',
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' COMMENT 'สถานะ',
    approved_by INT DEFAULT NULL COMMENT 'ผู้อนุมัติ',
    approved_at DATETIME DEFAULT NULL COMMENT 'วันที่อนุมัติ',
    admin_remark TEXT DEFAULT NULL COMMENT 'หมายเหตุจากผู้บริหาร',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_leave_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_leave_type FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE RESTRICT,
    CONSTRAINT fk_leave_fiscal FOREIGN KEY (fiscal_year_id) REFERENCES fiscal_years(id) ON DELETE RESTRICT,
    CONSTRAINT fk_leave_approver FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_dates (start_date, end_date),
    INDEX idx_fiscal (fiscal_year_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางใบลา';

-- =========================================
-- ข้อมูลเริ่มต้น - ประเภทการลา
-- =========================================
INSERT INTO leave_types (name, description, max_days) VALUES
('ลากิจ', 'การลากิจส่วนตัว เพื่อดำเนินกิจธุระที่จำเป็น', 10),
('ลาป่วย', 'การลาป่วย เนื่องจากเจ็บป่วยไม่สามารถปฏิบัติงานได้', 30),
('ลาออก', 'การลาออกจากราชการ', 0);

-- =========================================
-- ข้อมูลเริ่มต้น - ปีงบประมาณ
-- =========================================
INSERT INTO fiscal_years (name, start_date, end_date, is_current) VALUES
('ปีงบประมาณ 2569', '2025-10-01', '2026-09-30', 1);

-- =========================================
-- หมายเหตุ: ข้อมูลผู้ใช้ตัวอย่างจะถูกสร้างผ่าน install.php
-- เพื่อใช้ password_hash() ของ PHP ในการเข้ารหัสรหัสผ่าน
-- =========================================
