<?php
/**
 * Authentication Helper Class
 * จัดการการยืนยันตัวตนและสิทธิ์การเข้าถึง (RBAC)
 */

class Auth
{
    /**
     * เข้าสู่ระบบ - บันทึกข้อมูลผู้ใช้ลง Session
     */
    public static function login(array $user): void
    {
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('username', $user['username']);
        Session::set('role', $user['role']);
        Session::set('full_name', $user['first_name'] . ' ' . $user['last_name']);
        Session::set('avatar', $user['avatar'] ?? null);
        Session::set('logged_in', true);
        Session::set('login_time', time());
    }

    public static function updateSession(array $user): void
    {
        Session::set('full_name', $user['first_name'] . ' ' . $user['last_name']);
        if (isset($user['avatar'])) {
            Session::set('avatar', $user['avatar']);
        }
    }

    /**
     * ออกจากระบบ
     */
    public static function logout(): void
    {
        Session::destroy();
    }

    /**
     * ตรวจสอบว่าเข้าสู่ระบบแล้วหรือไม่
     */
    public static function check(): bool
    {
        return Session::get('logged_in', false) === true;
    }

    /**
     * ดึงข้อมูลผู้ใช้ปัจจุบัน
     */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return [
            'id'        => Session::get('user_id'),
            'username'  => Session::get('username'),
            'role'      => Session::get('role'),
            'full_name' => Session::get('full_name'),
            'avatar'    => Session::get('avatar'),
        ];
    }

    /**
     * ดึง ID ผู้ใช้ปัจจุบัน
     */
    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    /**
     * ดึงบทบาทผู้ใช้ปัจจุบัน
     */
    public static function role(): ?string
    {
        return Session::get('role');
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    public static function isExecutive(): bool
    {
        return self::role() === 'executive';
    }

    public static function isPersonnel(): bool
    {
        return self::role() === 'personnel';
    }

    /**
     * ตรวจสอบว่าผู้ใช้มีบทบาทตามที่กำหนดหรือไม่
     */
    public static function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        return in_array(self::role(), $roles);
    }
}
