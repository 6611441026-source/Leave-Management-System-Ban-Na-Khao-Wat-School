<?php
/**
 * Router Class
 * จัดการเส้นทาง URL และ Dispatch ไปยัง Controller
 */

class Router
{
    private array $routes = [];
    private array $roleAccess = [];

    /**
     * ลงทะเบียนเส้นทาง
     */
    public function register(string $page, string $controller, array $allowedRoles = []): void
    {
        $this->routes[$page] = $controller;
        $this->roleAccess[$page] = $allowedRoles;
    }

    /**
     * Dispatch Request ไปยัง Controller ที่เหมาะสม
     */
    public function dispatch(): void
    {
        $page = $_GET['page'] ?? 'login';
        $action = $_GET['action'] ?? 'index';
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;

        // หน้า Login ไม่ต้องเข้าสู่ระบบ
        if ($page === 'login') {
            $controller = new AuthController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->login();
            } else {
                $controller->loginForm();
            }
            return;
        }

        // Logout
        if ($page === 'logout') {
            $controller = new AuthController();
            $controller->logout();
            return;
        }

        // ตรวจสอบว่าเข้าสู่ระบบแล้ว
        if (!Auth::check()) {
            header('Location: ' . BASE_URL . '?page=login');
            exit;
        }

        // ตรวจสอบว่า Route มีอยู่
        if (!isset($this->routes[$page])) {
            http_response_code(404);
            echo '<div style="text-align:center;margin-top:100px;font-family:Sarabun,sans-serif;">
                    <h1>404</h1><p>ไม่พบหน้าที่ร้องขอ</p>
                    <a href="' . BASE_URL . '?page=dashboard">กลับหน้าหลัก</a>
                  </div>';
            exit;
        }

        // ตรวจสอบสิทธิ์การเข้าถึงตาม Role
        $allowedRoles = $this->roleAccess[$page];
        if (!empty($allowedRoles) && !Auth::hasRole($allowedRoles)) {
            http_response_code(403);
            Session::setFlash('danger', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
            header('Location: ' . BASE_URL . '?page=dashboard');
            exit;
        }

        // สร้าง Controller Instance
        $controllerClass = $this->routes[$page];
        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo '<h1>500 - Internal Server Error</h1>';
            exit;
        }

        $controller = new $controllerClass();

        // ทำความสะอาดชื่อ Action
        $action = preg_replace('/[^a-zA-Z0-9_]/', '', $action);

        if (!method_exists($controller, $action)) {
            http_response_code(404);
            echo '<div style="text-align:center;margin-top:100px;font-family:Sarabun,sans-serif;">
                    <h1>404</h1><p>ไม่พบฟังก์ชันที่ร้องขอ</p>
                    <a href="' . BASE_URL . '?page=dashboard">กลับหน้าหลัก</a>
                  </div>';
            exit;
        }

        // เรียก Action
        if ($id !== null) {
            $controller->$action($id);
        } else {
            $controller->$action();
        }
    }
}
