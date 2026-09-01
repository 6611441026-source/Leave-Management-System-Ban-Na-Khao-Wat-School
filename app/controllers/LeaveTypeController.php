<?php
/**
 * Leave Type Controller
 * จัดการข้อมูลประเภทการลา (CRUD) - เฉพาะ Admin
 */

class LeaveTypeController extends Controller
{
    private LeaveTypeModel $leaveTypeModel;

    public function __construct()
    {
        $this->leaveTypeModel = new LeaveTypeModel();
    }

    /**
     * แสดงรายการประเภทการลา
     */
    public function index(): void
    {
        $data = [
            'pageTitle'  => 'จัดการประเภทการลา',
            'leaveTypes' => $this->leaveTypeModel->findAll('name ASC')
        ];
        $this->view('leave-types/index', $data);
    }

    /**
     * เพิ่มประเภทการลา (AJAX)
     */
    public function store(): void
    {
        $this->validateCsrf();

        $name        = $this->sanitize($_POST['name'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $maxDays     = (int)($_POST['max_days'] ?? 0);

        if (empty($name)) {
            $this->json(['success' => false, 'message' => 'กรุณากรอกชื่อประเภทการลา']);
            return;
        }

        $this->leaveTypeModel->create([
            'name'        => $name,
            'description' => $description,
            'max_days'    => $maxDays
        ]);

        $this->json(['success' => true, 'message' => 'เพิ่มประเภทการลาสำเร็จ']);
    }

    /**
     * ดึงข้อมูลประเภทการลา (AJAX)
     */
    public function get(int $id = 0): void
    {
        $id = $id ?: (int)($_GET['id'] ?? 0);
        $leaveType = $this->leaveTypeModel->findById($id);
        if (!$leaveType) {
            $this->json(['success' => false, 'message' => 'ไม่พบข้อมูล'], 404);
            return;
        }
        $this->json(['success' => true, 'data' => $leaveType]);
    }

    /**
     * แก้ไขประเภทการลา (AJAX)
     */
    public function update(): void
    {
        $this->validateCsrf();

        $id          = (int)($_POST['id'] ?? 0);
        $name        = $this->sanitize($_POST['name'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $maxDays     = (int)($_POST['max_days'] ?? 0);

        if ($id <= 0 || empty($name)) {
            $this->json(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
            return;
        }

        $this->leaveTypeModel->update($id, [
            'name'        => $name,
            'description' => $description,
            'max_days'    => $maxDays
        ]);

        $this->json(['success' => true, 'message' => 'แก้ไขประเภทการลาสำเร็จ']);
    }

    /**
     * ลบประเภทการลา (AJAX)
     */
    public function delete(): void
    {
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
            return;
        }

        try {
            $this->leaveTypeModel->delete($id);
            $this->json(['success' => true, 'message' => 'ลบประเภทการลาสำเร็จ']);
        } catch (\PDOException $e) {
            $this->json(['success' => false, 'message' => 'ไม่สามารถลบได้ เนื่องจากมีข้อมูลการลาที่เกี่ยวข้อง']);
        }
    }

    /**
     * ค้นหาประเภทการลา (AJAX)
     */
    public function search(): void
    {
        $keyword = $this->sanitize($_GET['keyword'] ?? '');
        $results = $this->leaveTypeModel->search($keyword);
        $this->json(['success' => true, 'data' => $results]);
    }
}
