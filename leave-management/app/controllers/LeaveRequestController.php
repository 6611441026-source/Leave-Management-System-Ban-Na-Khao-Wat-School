<?php
/**
 * Leave Request Controller
 * จัดการใบลา - สำหรับบุคลากร
 */

class LeaveRequestController extends Controller
{
    private LeaveRequestModel $leaveRequestModel;
    private LeaveTypeModel $leaveTypeModel;
    private FiscalYearModel $fiscalYearModel;

    public function __construct()
    {
        $this->leaveRequestModel = new LeaveRequestModel();
        $this->leaveTypeModel    = new LeaveTypeModel();
        $this->fiscalYearModel   = new FiscalYearModel();
    }

    /**
     * แสดงรายการใบลา
     */
    public function index(): void
    {
        $filters = [];

        // บุคลากรดูได้เฉพาะของตัวเอง
        if (Auth::isPersonnel()) {
            $filters['user_id'] = Auth::id();
        }

        if (!empty($_GET['status']))         $filters['status'] = $this->sanitize($_GET['status']);
        if (!empty($_GET['leave_type_id']))  $filters['leave_type_id'] = (int)$_GET['leave_type_id'];
        if (!empty($_GET['fiscal_year_id'])) $filters['fiscal_year_id'] = (int)$_GET['fiscal_year_id'];
        if (!empty($_GET['start_date']))     $filters['start_date'] = $_GET['start_date'];
        if (!empty($_GET['end_date']))       $filters['end_date'] = $_GET['end_date'];

        $data = [
            'pageTitle'   => Auth::isPersonnel() ? 'ข้อมูลการลาของฉัน' : 'ข้อมูลการลา',
            'requests'    => $this->leaveRequestModel->getAllWithDetails($filters),
            'leaveTypes'  => $this->leaveTypeModel->getAllActive(),
            'fiscalYears' => $this->fiscalYearModel->getAllActive(),
            'filters'     => $filters
        ];

        $this->view('leave-requests/index', $data);
    }

    /**
     * แสดงฟอร์มยื่นใบลา
     */
    public function create(): void
    {
        $data = [
            'pageTitle'         => 'ยื่นใบลา',
            'leaveTypes'        => $this->leaveTypeModel->getAllActive(),
            'fiscalYears'       => $this->fiscalYearModel->getAllActive(),
            'currentFiscalYear' => $this->fiscalYearModel->getCurrent()
        ];

        $this->view('leave-requests/create', $data);
    }

    /**
     * บันทึกใบลา (AJAX)
     */
    public function store(): void
    {
        $this->validateCsrf();

        $userId       = Auth::id();
        $leaveTypeId  = (int)($_POST['leave_type_id'] ?? 0);
        $fiscalYearId = (int)($_POST['fiscal_year_id'] ?? 0);
        $startDate    = $_POST['start_date'] ?? '';
        $endDate      = $_POST['end_date'] ?? '';
        $totalDays    = (float)($_POST['total_days'] ?? 0);
        $reason       = $this->sanitize($_POST['reason'] ?? '');

        // Validation
        $errors = [];
        if ($leaveTypeId <= 0)  $errors[] = 'กรุณาเลือกประเภทการลา';
        if ($fiscalYearId <= 0) $errors[] = 'กรุณาเลือกปีงบประมาณ';
        if (empty($startDate))  $errors[] = 'กรุณาระบุวันที่เริ่มลา';
        if (empty($endDate))    $errors[] = 'กรุณาระบุวันที่สิ้นสุด';
        if ($totalDays <= 0)    $errors[] = 'จำนวนวันลาต้องมากกว่า 0';
        if (empty($reason))    $errors[] = 'กรุณาระบุเหตุผลการลา';

        if (!empty($startDate) && !empty($endDate) && strtotime($startDate) > strtotime($endDate)) {
            $errors[] = 'วันสิ้นสุดต้องไม่น้อยกว่าวันเริ่มลา';
        }

        // ตรวจสอบจำนวนวันลาคงเหลือ
        if ($leaveTypeId > 0 && $fiscalYearId > 0) {
            $leaveType = $this->leaveTypeModel->findById($leaveTypeId);
            if ($leaveType && $leaveType['max_days'] > 0) {
                $usedDays = $this->leaveRequestModel->getUserLeaveDaysInFiscalYear($userId, $leaveTypeId, $fiscalYearId);
                if (($usedDays + $totalDays) > $leaveType['max_days']) {
                    $remaining = $leaveType['max_days'] - $usedDays;
                    $errors[] = "วันลาคงเหลือไม่เพียงพอ (คงเหลือ {$remaining} วัน จากทั้งหมด {$leaveType['max_days']} วัน)";
                }
            }
        }

        if (!empty($errors)) {
            $this->json(['success' => false, 'message' => implode('<br>', $errors)]);
            return;
        }

        // จัดการไฟล์แนบ (บังคับต้องแนบ)
        if (!isset($_FILES['attachment']) || $_FILES['attachment']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'กรุณาแนบไฟล์ก่อนยื่นใบลา']);
            return;
        }

        $attachment = null;
        try {
            $attachment = $this->uploadFile($_FILES['attachment']);
        } catch (\RuntimeException $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()]);
            return;
        }

        $this->leaveRequestModel->create([
            'user_id'        => $userId,
            'leave_type_id'  => $leaveTypeId,
            'fiscal_year_id' => $fiscalYearId,
            'start_date'     => $startDate,
            'end_date'       => $endDate,
            'total_days'     => $totalDays,
            'reason'         => $reason,
            'attachment'     => $attachment,
            'status'         => 'pending'
        ]);

        $this->json(['success' => true, 'message' => 'ยื่นใบลาสำเร็จ']);
    }

    /**
     * ดูรายละเอียดใบลา (AJAX)
     */
    public function detail(int $id = 0): void
    {
        $id = $id ?: (int)($_GET['id'] ?? 0);
        $request = $this->leaveRequestModel->getWithDetails($id);

        if (!$request) {
            $this->json(['success' => false, 'message' => 'ไม่พบข้อมูล'], 404);
            return;
        }

        // บุคลากรดูได้เฉพาะของตัวเอง
        if (Auth::isPersonnel() && (int)$request['user_id'] !== Auth::id()) {
            $this->json(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ดูข้อมูลนี้'], 403);
            return;
        }

        $this->json(['success' => true, 'data' => $request]);
    }
}
