<?php
/**
 * Auth Controller
 * จัดการเข้าสู่ระบบ/ออกจากระบบ
 */

class AuthController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * แสดงฟอร์มเข้าสู่ระบบ
     */
    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirect(BASE_URL . '?page=dashboard');
        }
        $this->loginView('auth/login');
    }

    /**
     * ดำเนินการเข้าสู่ระบบ
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '?page=login');
            return;
        }

        $username = $this->sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // ตรวจสอบ CSRF Token
        if (!Session::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('danger', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
            $this->redirect(BASE_URL . '?page=login');
            return;
        }

        // ตรวจสอบ Input
        if (empty($username) || empty($password)) {
            Session::setFlash('danger', 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน');
            $this->redirect(BASE_URL . '?page=login');
            return;
        }

        // ยืนยันตัวตน
        $user = $this->userModel->authenticate($username, $password);
        if ($user) {
            Auth::login($user);
            Session::setFlash('success', 'เข้าสู่ระบบสำเร็จ ยินดีต้อนรับ ' . $user['first_name']);
            $this->redirect(BASE_URL . '?page=dashboard');
        } else {
            Session::setFlash('danger', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
            $this->redirect(BASE_URL . '?page=login');
        }
    }

    /**
     * ออกจากระบบ
     */
    public function logout(): void
    {
        Auth::logout();
        // เริ่ม Session ใหม่เพื่อเก็บ flash message
        Session::start();
        Session::setFlash('success', 'ออกจากระบบสำเร็จ');
        $this->redirect(BASE_URL . '?page=login');
    }
}
