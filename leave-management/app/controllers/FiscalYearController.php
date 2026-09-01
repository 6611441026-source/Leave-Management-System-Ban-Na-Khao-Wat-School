<?php
/**
 * Fiscal Year Controller
 * จัดการข้อมูลปีงบประมาณ (CRUD) - เฉพาะ Admin
 */

class FiscalYearController extends Controller
{
    private FiscalYearModel $fiscalYearModel;

    public function __construct()
    {
        $this->fiscalYearModel = new FiscalYearModel();
    }

    public function index(): void
    {
        $data = [
            'pageTitle'   => 'จัดการปีงบประมาณ',
            'fiscalYears' => $this->fiscalYearModel->findAll('start_date DESC')
        ];
        $this->view('fiscal-years/index', $data);
    }

    public function store(): void
    {
        $this->validateCsrf();

        $name      = $this->sanitize($_POST['name'] ?? '');
        $startDate = $_POST['start_date'] ?? '';
        $endDate   = $_POST['end_date'] ?? '';

        if (empty($name) || empty($startDate) || empty($endDate)) {
            $this->json(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
            return;
        }

        if (strtotime($startDate) >= strtotime($endDate)) {
            $this->json(['success' => false, 'message' => 'วันสิ้นสุดต้องมากกว่าวันเริ่มต้น']);
            return;
        }

        $this->fiscalYearModel->create([
            'name'       => $name,
            'start_date' => $startDate,
            'end_date'   => $endDate
        ]);

        $this->json(['success' => true, 'message' => 'เพิ่มปีงบประมาณสำเร็จ']);
    }

    public function get(int $id = 0): void
    {
        $id = $id ?: (int)($_GET['id'] ?? 0);
        $fiscalYear = $this->fiscalYearModel->findById($id);
        if (!$fiscalYear) {
            $this->json(['success' => false, 'message' => 'ไม่พบข้อมูล'], 404);
            return;
        }
        $this->json(['success' => true, 'data' => $fiscalYear]);
    }

    public function update(): void
    {
        $this->validateCsrf();

        $id        = (int)($_POST['id'] ?? 0);
        $name      = $this->sanitize($_POST['name'] ?? '');
        $startDate = $_POST['start_date'] ?? '';
        $endDate   = $_POST['end_date'] ?? '';

        if ($id <= 0 || empty($name) || empty($startDate) || empty($endDate)) {
            $this->json(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
            return;
        }

        if (strtotime($startDate) >= strtotime($endDate)) {
            $this->json(['success' => false, 'message' => 'วันสิ้นสุดต้องมากกว่าวันเริ่มต้น']);
            return;
        }

        $this->fiscalYearModel->update($id, [
            'name'       => $name,
            'start_date' => $startDate,
            'end_date'   => $endDate
        ]);

        $this->json(['success' => true, 'message' => 'แก้ไขปีงบประมาณสำเร็จ']);
    }

    public function delete(): void
    {
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
            return;
        }

        try {
            $this->fiscalYearModel->delete($id);
            $this->json(['success' => true, 'message' => 'ลบปีงบประมาณสำเร็จ']);
        } catch (\PDOException $e) {
            $this->json(['success' => false, 'message' => 'ไม่สามารถลบได้ เนื่องจากมีข้อมูลการลาที่เกี่ยวข้อง']);
        }
    }

    public function setCurrent(): void
    {
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
            return;
        }

        $this->fiscalYearModel->setCurrent($id);
        $this->json(['success' => true, 'message' => 'กำหนดปีงบประมาณปัจจุบันสำเร็จ']);
    }
}
