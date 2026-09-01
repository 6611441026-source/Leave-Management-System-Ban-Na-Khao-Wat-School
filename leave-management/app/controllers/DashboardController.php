<?php
/**
 * Dashboard Controller
 * แสดงแดชบอร์ดตามบทบาทผู้ใช้
 */

class DashboardController extends Controller
{
    private LeaveRequestModel $leaveRequestModel;
    private UserModel $userModel;

    public function __construct()
    {
        $this->leaveRequestModel = new LeaveRequestModel();
        $this->userModel = new UserModel();
    }

    public function index(): void
    {
        $role = Auth::role();
        $data = ['pageTitle' => 'แดชบอร์ด'];

        if ($role === 'admin') {
            // Admin Dashboard
            $data['totalPersonnel'] = $this->userModel->countByRole('personnel');
            $data['totalRequests'] = $this->leaveRequestModel->count();
            $data['pendingCount'] = $this->leaveRequestModel->countByStatus('pending');
            $data['approvedCount'] = $this->leaveRequestModel->countByStatus('approved');
            $data['recentRequests'] = $this->leaveRequestModel->getAllWithDetails();

        } elseif ($role === 'executive') {
            // Executive Dashboard
            $data['pendingCount'] = $this->leaveRequestModel->countByStatus('pending');
            $data['approvedCount'] = $this->leaveRequestModel->countByStatus('approved');
            $data['rejectedCount'] = $this->leaveRequestModel->countByStatus('rejected');
            $data['pendingRequests'] = $this->leaveRequestModel->getPending();

        } else {
            // Personnel Dashboard
            $userId = Auth::id();
            $data['myRequests'] = $this->leaveRequestModel->countByStatus('pending', $userId)
                                + $this->leaveRequestModel->countByStatus('approved', $userId)
                                + $this->leaveRequestModel->countByStatus('rejected', $userId);
            $data['pendingCount'] = $this->leaveRequestModel->countByStatus('pending', $userId);
            $data['approvedCount'] = $this->leaveRequestModel->countByStatus('approved', $userId);
            $data['rejectedCount'] = $this->leaveRequestModel->countByStatus('rejected', $userId);
            $data['recentRequests'] = $this->leaveRequestModel->getByUser($userId);
        }

        $this->view('dashboard/index', $data);
    }
}
