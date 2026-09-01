<?php
/**
 * Authentication Middleware
 * ตรวจสอบการเข้าสู่ระบบและสิทธิ์การเข้าถึง
 */

class AuthMiddleware
{
    /**
     * ต้องเข้าสู่ระบบแล้ว
     */
    public static function requireAuth(): void
    {
        if (!Auth::check()) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                http_response_code(401);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            header('Location: ' . BASE_URL . '?page=login');
            exit;
        }
    }

    /**
     * ต้องมีบทบาทตามที่กำหนด
     */
    public static function requireRole(string|array $roles): void
    {
        self::requireAuth();
        if (!Auth::hasRole($roles)) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์เข้าถึง'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            Session::setFlash('danger', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
            header('Location: ' . BASE_URL . '?page=dashboard');
            exit;
        }
    }

    /**
     * ต้องเป็น Guest (ยังไม่ได้เข้าสู่ระบบ)
     */
    public static function guest(): void
    {
        if (Auth::check()) {
            header('Location: ' . BASE_URL . '?page=dashboard');
            exit;
        }
    }
}
