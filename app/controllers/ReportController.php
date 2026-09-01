<?php
/**
 * Report Controller
 * รายงานและสถิติการลา - สำหรับ Admin/Executive
 */

class ReportController extends Controller
{
    private LeaveRequestModel $leaveRequestModel;
    private LeaveTypeModel $leaveTypeModel;
    private FiscalYearModel $fiscalYearModel;
    private UserModel $userModel;

    public function __construct()
    {
        $this->leaveRequestModel = new LeaveRequestModel();
        $this->leaveTypeModel    = new LeaveTypeModel();
        $this->fiscalYearModel   = new FiscalYearModel();
        $this->userModel         = new UserModel();
    }

    /**
     * แสดงหน้ารายงาน
     */
    public function index(): void
    {
        $fiscalYearId = !empty($_GET['fiscal_year_id']) ? (int)$_GET['fiscal_year_id'] : null;

        // ใช้ปีงบประมาณปัจจุบันถ้าไม่ได้เลือก
        if (!$fiscalYearId) {
            $currentFY = $this->fiscalYearModel->getCurrent();
            $fiscalYearId = $currentFY ? $currentFY['id'] : null;
        }

        $data = [
            'pageTitle'          => 'รายงานและสถิติการลา',
            'fiscalYears'        => $this->fiscalYearModel->getAllActive(),
            'leaveTypes'         => $this->leaveTypeModel->getAllActive(),
            'personnel'          => $this->userModel->getAllPersonnel(),
            'selectedFiscalYear' => $fiscalYearId,
            'statsByLeaveType'   => $this->leaveRequestModel->getStatsByLeaveType($fiscalYearId),
            'statsByMonth'       => $this->leaveRequestModel->getStatsByMonth($fiscalYearId),
            'statsByStatus'      => $this->leaveRequestModel->getStatsByStatus($fiscalYearId),
            'statsByUser'        => $this->leaveRequestModel->getStatsByUser($fiscalYearId),
        ];

        $this->view('reports/index', $data);
    }

    /**
     * กรองข้อมูลรายงาน (AJAX)
     */
    public function filter(): void
    {
        $filters = [];

        if (!empty($_GET['user_id']))        $filters['user_id'] = (int)$_GET['user_id'];
        if (!empty($_GET['leave_type_id']))  $filters['leave_type_id'] = (int)$_GET['leave_type_id'];
        if (!empty($_GET['fiscal_year_id'])) $filters['fiscal_year_id'] = (int)$_GET['fiscal_year_id'];
        if (!empty($_GET['month']))          $filters['month'] = (int)$_GET['month'];
        if (!empty($_GET['year']))           $filters['year'] = (int)$_GET['year'];
        if (!empty($_GET['status']))         $filters['status'] = $this->sanitize($_GET['status']);

        $requests = $this->leaveRequestModel->getAllWithDetails($filters);
        $this->json(['success' => true, 'data' => $requests]);
    }

    /**
     * ดึงสถิติ (AJAX)
     */
    public function stats(): void
    {
        $fiscalYearId = !empty($_GET['fiscal_year_id']) ? (int)$_GET['fiscal_year_id'] : null;

        $data = [
            'byLeaveType' => $this->leaveRequestModel->getStatsByLeaveType($fiscalYearId),
            'byMonth'     => $this->leaveRequestModel->getStatsByMonth($fiscalYearId),
            'byStatus'    => $this->leaveRequestModel->getStatsByStatus($fiscalYearId),
            'byUser'      => $this->leaveRequestModel->getStatsByUser($fiscalYearId),
        ];

        $this->json(['success' => true, 'data' => $data]);
    }
}
