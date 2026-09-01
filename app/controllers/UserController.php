<?php
/**
 * User Controller
 * จัดการข้อมูลผู้ใช้งาน (CRUD) - เฉพาะ Admin
 */

class UserController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index(): void
    {
        $data = [
            'pageTitle' => 'จัดการผู้ใช้งาน',
            'users'     => $this->userModel->findAll('first_name ASC')
        ];
        $this->view('users/index', $data);
    }

    public function store(): void
    {
        $this->validateCsrf();

        $employeeId = $this->sanitize($_POST['employee_id'] ?? '');
        $firstName  = $this->sanitize($_POST['first_name'] ?? '');
        $lastName   = $this->sanitize($_POST['last_name'] ?? '');
        $email      = $this->sanitize($_POST['email'] ?? '');
        $username   = $this->sanitize($_POST['username'] ?? '');
        $password   = $_POST['password'] ?? '';
        $role       = $this->sanitize($_POST['role'] ?? 'personnel');
        $position   = $this->sanitize($_POST['position'] ?? '');
        $department = $this->sanitize($_POST['department'] ?? '');
        $phone      = $this->sanitize($_POST['phone'] ?? '');

        $errors = [];
        if (empty($employeeId)) $errors[] = 'กรุณากรอกรหัสบุคลากร';
        if (empty($firstName))  $errors[] = 'กรุณากรอกชื่อ';
        if (empty($lastName))   $errors[] = 'กรุณากรอกนามสกุล';
        if (empty($email))      $errors[] = 'กรุณากรอกอีเมล';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
            $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
        }
        if (empty($username))   $errors[] = 'กรุณากรอกชื่อผู้ใช้';
        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
        }
        if (!in_array($role, ['admin', 'executive', 'personnel'])) {
            $errors[] = 'บทบาทไม่ถูกต้อง';
        }

        if ($this->userModel->isUsernameExists($username))     $errors[] = 'ชื่อผู้ใช้นี้ถูกใช้แล้ว';
        if ($this->userModel->isEmailExists($email))           $errors[] = 'อีเมลนี้ถูกใช้แล้ว';
        if ($this->userModel->isEmployeeIdExists($employeeId)) $errors[] = 'รหัสบุคลากรนี้ถูกใช้แล้ว';

        if (!empty($errors)) {
            $this->json(['success' => false, 'message' => implode('<br>', $errors)]);
            return;
        }

        $this->userModel->createUser([
            'employee_id' => $employeeId,
            'first_name'  => $firstName,
            'last_name'   => $lastName,
            'email'       => $email,
            'username'    => $username,
            'password'    => $password,
            'role'        => $role,
            'position'    => $position,
            'department'  => $department,
            'phone'       => $phone
        ]);

        $this->json(['success' => true, 'message' => 'เพิ่มผู้ใช้งานสำเร็จ']);
    }

    public function get(int $id = 0): void
    {
        $id = $id ?: (int)($_GET['id'] ?? 0);
        $user = $this->userModel->findById($id);
        if (!$user) {
            $this->json(['success' => false, 'message' => 'ไม่พบข้อมูล'], 404);
            return;
        }
        unset($user['password']);
        $this->json(['success' => true, 'data' => $user]);
    }

    public function update(): void
    {
        $this->validateCsrf();

        $id         = (int)($_POST['id'] ?? 0);
        $employeeId = $this->sanitize($_POST['employee_id'] ?? '');
        $firstName  = $this->sanitize($_POST['first_name'] ?? '');
        $lastName   = $this->sanitize($_POST['last_name'] ?? '');
        $email      = $this->sanitize($_POST['email'] ?? '');
        $username   = $this->sanitize($_POST['username'] ?? '');
        $password   = $_POST['password'] ?? '';
        $role       = $this->sanitize($_POST['role'] ?? 'personnel');
        $position   = $this->sanitize($_POST['position'] ?? '');
        $department = $this->sanitize($_POST['department'] ?? '');
        $phone      = $this->sanitize($_POST['phone'] ?? '');
        $isActive   = (int)($_POST['is_active'] ?? 1);

        $errors = [];
        if ($id <= 0)          $errors[] = 'ข้อมูลไม่ถูกต้อง';
        if (empty($firstName)) $errors[] = 'กรุณากรอกชื่อ';
        if (empty($lastName))  $errors[] = 'กรุณากรอกนามสกุล';

        if ($this->userModel->isUsernameExists($username, $id))     $errors[] = 'ชื่อผู้ใช้นี้ถูกใช้แล้ว';
        if ($this->userModel->isEmailExists($email, $id))           $errors[] = 'อีเมลนี้ถูกใช้แล้ว';
        if ($this->userModel->isEmployeeIdExists($employeeId, $id)) $errors[] = 'รหัสบุคลากรนี้ถูกใช้แล้ว';

        if (!empty($errors)) {
            $this->json(['success' => false, 'message' => implode('<br>', $errors)]);
            return;
        }

        $updateData = [
            'employee_id' => $employeeId,
            'first_name'  => $firstName,
            'last_name'   => $lastName,
            'email'       => $email,
            'username'    => $username,
            'role'        => $role,
            'position'    => $position,
            'department'  => $department,
            'phone'       => $phone,
            'is_active'   => $isActive
        ];

        if (!empty($password)) {
            $updateData['password'] = $password;
        }

        $this->userModel->updateUser($id, $updateData);
        $this->json(['success' => true, 'message' => 'แก้ไขผู้ใช้งานสำเร็จ']);
    }

    public function delete(): void
    {
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
            return;
        }

        if ($id === Auth::id()) {
            $this->json(['success' => false, 'message' => 'ไม่สามารถลบบัญชีของตัวเองได้']);
            return;
        }

        try {
            $this->userModel->delete($id);
            $this->json(['success' => true, 'message' => 'ลบผู้ใช้งานสำเร็จ']);
        } catch (\PDOException $e) {
            $this->json(['success' => false, 'message' => 'ไม่สามารถลบได้ เนื่องจากมีข้อมูลที่เกี่ยวข้อง']);
        }
    }
}
