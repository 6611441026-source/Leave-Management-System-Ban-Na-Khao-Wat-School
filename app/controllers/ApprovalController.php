<?php
/**
 * Approval Controller
 * พิจารณาอนุมัติการลา - เฉพาะผู้บริหาร
 */

class ApprovalController extends Controller
{
    private LeaveRequestModel $leaveRequestModel;

    public function __construct()
    {
        $this->leaveRequestModel = new LeaveRequestModel();
    }

    /**
     * แสดงรายการใบลาสำหรับอนุมัติ
     */
    public function index(): void
    {
        $status = $_GET['status'] ?? '';
        $filters = [];
        if (!empty($status)) {
            $filters['status'] = $status;
        }

        $data = [
            'pageTitle'     => 'พิจารณาอนุมัติการลา',
            'requests'      => $this->leaveRequestModel->getAllWithDetails($filters),
            'currentStatus' => $status
        ];

        $this->view('approvals/index', $data);
    }

    /**
     * อนุมัติใบลา (AJAX)
     */
    public function approve(): void
    {
        $this->validateCsrf();

        $id     = (int)($_POST['id'] ?? 0);
        $remark = $this->sanitize($_POST['admin_remark'] ?? '');

        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
            return;
        }

        $request = $this->leaveRequestModel->findById($id);
        if (!$request || $request['status'] !== 'pending') {
            $this->json(['success' => false, 'message' => 'ไม่สามารถอนุมัติได้ ใบลานี้ถูกดำเนินการแล้ว']);
            return;
        }

        $this->leaveRequestModel->approve($id, Auth::id(), $remark ?: null);
        $this->json(['success' => true, 'message' => 'อนุมัติการลาสำเร็จ']);
    }

    /**
     * ไม่อนุมัติใบลา (AJAX)
     */
    public function reject(): void
    {
        $this->validateCsrf();

        $id     = (int)($_POST['id'] ?? 0);
        $remark = $this->sanitize($_POST['admin_remark'] ?? '');

        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
            return;
        }

        $request = $this->leaveRequestModel->findById($id);
        if (!$request || $request['status'] !== 'pending') {
            $this->json(['success' => false, 'message' => 'ไม่สามารถดำเนินการได้ ใบลานี้ถูกดำเนินการแล้ว']);
            return;
        }

        $this->leaveRequestModel->reject($id, Auth::id(), $remark ?: null);
        $this->json(['success' => true, 'message' => 'ไม่อนุมัติการลาเรียบร้อย']);
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

        $this->json(['success' => true, 'data' => $request]);
    }
}
