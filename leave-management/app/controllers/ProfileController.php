<?php
/**
 * Profile Controller
 * จัดการข้อมูลส่วนตัวและโปรไฟล์ของผู้ใช้งานทุกคน
 */

class ProfileController extends Controller
{
    private UserModel $userModel;
    private LeaveRequestModel $leaveRequestModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->leaveRequestModel = new LeaveRequestModel();
    }

    /**
     * แสดงหน้าโปรไฟล์ส่วนตัว
     */
    public function index(): void
    {
        $userId = Auth::id();
        $user = $this->userModel->findById($userId);

        if (!$user) {
            Session::setFlash('danger', 'ไม่พบข้อมูลผู้ใช้');
            $this->redirect(BASE_URL . '?page=dashboard');
            return;
        }

        unset($user['password']);

        // สถิติการลาของผู้ใช้
        $stats = [
            'total'    => $this->leaveRequestModel->countByStatus('pending', $userId)
                        + $this->leaveRequestModel->countByStatus('approved', $userId)
                        + $this->leaveRequestModel->countByStatus('rejected', $userId),
            'pending'  => $this->leaveRequestModel->countByStatus('pending', $userId),
            'approved' => $this->leaveRequestModel->countByStatus('approved', $userId),
            'rejected' => $this->leaveRequestModel->countByStatus('rejected', $userId),
        ];

        $data = [
            'pageTitle' => 'โปรไฟล์ส่วนตัว',
            'user'      => $user,
            'stats'     => $stats
        ];

        $this->view('profile/index', $data);
    }

    /**
     * อัปเดตข้อมูลส่วนตัว (AJAX)
     */
    public function update(): void
    {
        $this->validateCsrf();

        $userId = Auth::id();
        $user = $this->userModel->findById($userId);

        if (!$user) {
            $this->json(['success' => false, 'message' => 'ไม่พบข้อมูลผู้ใช้งาน']);
            return;
        }

        $firstName  = $this->sanitize($_POST['first_name'] ?? '');
        $lastName   = $this->sanitize($_POST['last_name'] ?? '');
        $email      = $this->sanitize($_POST['email'] ?? '');
        $phone      = $this->sanitize($_POST['phone'] ?? '');
        $position   = $this->sanitize($_POST['position'] ?? '');
        $department = $this->sanitize($_POST['department'] ?? '');

        // Validation
        $errors = [];
        if (empty($firstName)) $errors[] = 'กรุณากรอกชื่อ';
        if (empty($lastName))  $errors[] = 'กรุณากรอกนามสกุล';
        if (empty($email))     $errors[] = 'กรุณากรอกอีเมล';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
            $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
        }

        if ($this->userModel->isEmailExists($email, $userId)) {
            $errors[] = 'อีเมลนี้ถูกใช้งานโดยผู้ใช้อื่นแล้ว';
        }

        if (!empty($errors)) {
            $this->json(['success' => false, 'message' => implode('<br>', $errors)]);
            return;
        }

        $updateData = [
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
            'phone'      => $phone,
            'position'   => $position,
            'department' => $department
        ];

        // จัดการอัปโหลดรูปโปรไฟล์
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            try {
                $avatarFilename = $this->uploadFile($_FILES['avatar']);
                $updateData['avatar'] = $avatarFilename;
            } catch (\RuntimeException $e) {
                $this->json(['success' => false, 'message' => $e->getMessage()]);
                return;
            }
        }

        $this->userModel->update($userId, $updateData);
        Auth::updateSession($updateData);

        $this->json(['success' => true, 'message' => 'อัปเดตข้อมูลส่วนตัวสำเร็จ']);
    }

    /**
     * เปลี่ยนรหัสผ่าน (AJAX)
     */
    public function updatePassword(): void
    {
        $this->validateCsrf();

        $userId = Auth::id();
        $user = $this->userModel->findById($userId);

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPassword, $user['password'])) {
            $this->json(['success' => false, 'message' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง']);
            return;
        }

        if (strlen($newPassword) < 6) {
            $this->json(['success' => false, 'message' => 'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร']);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $this->json(['success' => false, 'message' => 'รหัสผ่านใหม่และการยืนยันรหัสผ่านไม่ตรงกัน']);
            return;
        }

        $this->userModel->updateUser($userId, ['password' => $newPassword]);
        $this->json(['success' => true, 'message' => 'เปลี่ยนรหัสผ่านสำเร็จ']);
    }
}
