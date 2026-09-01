<?php
/**
 * Base Controller Class
 * จัดการ View rendering, JSON response, CSRF, File upload
 */

class Controller
{
    /**
     * แสดง View พร้อม Layout (header + footer)
     */
    protected function view(string $viewPath, array $data = []): void
    {
        extract($data);
        $csrfField = Session::csrfField();
        $csrfToken = Session::generateCsrfToken();
        $currentUser = Auth::user();

        include BASE_PATH . '/app/views/layouts/header.php';
        include BASE_PATH . '/app/views/' . $viewPath . '.php';
        include BASE_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * แสดง View แบบไม่มี Layout (สำหรับหน้า Login)
     */
    protected function loginView(string $viewPath, array $data = []): void
    {
        extract($data);
        include BASE_PATH . '/app/views/' . $viewPath . '.php';
    }

    /**
     * ส่งข้อมูล JSON
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirect ไปยัง URL
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * ตรวจสอบว่าเป็น AJAX Request
     */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * ตรวจสอบ CSRF Token
     */
    protected function validateCsrf(): bool
    {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!Session::validateCsrfToken($token)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'CSRF token ไม่ถูกต้อง กรุณารีเฟรชหน้าเว็บ'], 403);
            } else {
                Session::setFlash('danger', 'เกิดข้อผิดพลาดด้านความปลอดภัย กรุณาลองใหม่');
                $this->redirect(BASE_URL);
            }
            return false;
        }
        return true;
    }

    /**
     * ทำความสะอาดข้อมูล Input (ป้องกัน XSS)
     */
    protected function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * อัปโหลดไฟล์พร้อมตรวจสอบความปลอดภัย
     */
    protected function uploadFile(array $file): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // ตรวจสอบขนาดไฟล์
        if ($file['size'] > UPLOAD_MAX_SIZE) {
            throw new \RuntimeException('ขนาดไฟล์เกินที่กำหนด (สูงสุด 5MB)');
        }

        // ตรวจสอบ MIME Type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES)) {
            throw new \RuntimeException('ประเภทไฟล์ไม่ได้รับอนุญาต');
        }

        // ตรวจสอบนามสกุลไฟล์
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, UPLOAD_ALLOWED_EXTENSIONS)) {
            throw new \RuntimeException('นามสกุลไฟล์ไม่ได้รับอนุญาต');
        }

        // สร้างชื่อไฟล์แบบสุ่ม
        $newFilename = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = UPLOAD_DIR . $newFilename;

        // สร้างโฟลเดอร์ถ้ายังไม่มี
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \RuntimeException('ไม่สามารถอัปโหลดไฟล์ได้');
        }

        return $newFilename;
    }
}
