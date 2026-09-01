<?php
/**
 * Leave Request Model
 * จัดการข้อมูลใบลา พร้อมรายงานและสถิติ
 */

class LeaveRequestModel extends Model
{
    protected string $table = 'leave_requests';

    /**
     * ดึงข้อมูลใบลาพร้อมรายละเอียด (JOIN ตารางที่เกี่ยวข้อง)
     */
    public function getWithDetails(int $id): ?array
    {
        $sql = "SELECT lr.*, 
                    u.first_name, u.last_name, u.employee_id, u.position, u.department,
                    lt.name AS leave_type_name,
                    fy.name AS fiscal_year_name,
                    CONCAT(a.first_name, ' ', a.last_name) AS approver_name
                FROM leave_requests lr
                JOIN users u ON lr.user_id = u.id
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                JOIN fiscal_years fy ON lr.fiscal_year_id = fy.id
                LEFT JOIN users a ON lr.approved_by = a.id
                WHERE lr.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * ดึงข้อมูลใบลาทั้งหมดพร้อมรายละเอียด (รองรับ Filter)
     */
    public function getAllWithDetails(array $filters = []): array
    {
        $sql = "SELECT lr.*, 
                    u.first_name, u.last_name, u.employee_id, u.position, u.department,
                    lt.name AS leave_type_name,
                    fy.name AS fiscal_year_name,
                    CONCAT(a.first_name, ' ', a.last_name) AS approver_name
                FROM leave_requests lr
                JOIN users u ON lr.user_id = u.id
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                JOIN fiscal_years fy ON lr.fiscal_year_id = fy.id
                LEFT JOIN users a ON lr.approved_by = a.id";

        $where = [];
        $params = [];

        if (!empty($filters['user_id'])) {
            $where[] = 'lr.user_id = :user_id';
            $params['user_id'] = $filters['user_id'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'lr.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['leave_type_id'])) {
            $where[] = 'lr.leave_type_id = :leave_type_id';
            $params['leave_type_id'] = $filters['leave_type_id'];
        }
        if (!empty($filters['fiscal_year_id'])) {
            $where[] = 'lr.fiscal_year_id = :fiscal_year_id';
            $params['fiscal_year_id'] = $filters['fiscal_year_id'];
        }
        if (!empty($filters['start_date'])) {
            $where[] = 'lr.start_date >= :start_date';
            $params['start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = 'lr.end_date <= :end_date';
            $params['end_date'] = $filters['end_date'];
        }
        if (!empty($filters['month'])) {
            $where[] = 'MONTH(lr.start_date) = :month';
            $params['month'] = $filters['month'];
        }
        if (!empty($filters['year'])) {
            $where[] = 'YEAR(lr.start_date) = :year';
            $params['year'] = $filters['year'];
        }

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY lr.created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * ดึงใบลาของผู้ใช้
     */
    public function getByUser(int $userId): array
    {
        return $this->getAllWithDetails(['user_id' => $userId]);
    }

    /**
     * ดึงใบลารออนุมัติ
     */
    public function getPending(): array
    {
        return $this->getAllWithDetails(['status' => 'pending']);
    }

    /**
     * นับจำนวนใบลาตามสถานะ
     */
    public function countByStatus(string $status, ?int $userId = null): int
    {
        $sql = "SELECT COUNT(*) as cnt FROM leave_requests WHERE status = :status";
        $params = ['status' => $status];
        if ($userId !== null) {
            $sql .= " AND user_id = :user_id";
            $params['user_id'] = $userId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetch()['cnt'];
    }

    /**
     * อนุมัติใบลา
     */
    public function approve(int $id, int $approverId, ?string $remark = null): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE leave_requests 
             SET status = 'approved', approved_by = :approver, approved_at = NOW(), admin_remark = :remark, updated_at = NOW() 
             WHERE id = :id"
        );
        return $stmt->execute([
            'id'       => $id,
            'approver' => $approverId,
            'remark'   => $remark
        ]);
    }

    /**
     * ไม่อนุมัติใบลา
     */
    public function reject(int $id, int $approverId, ?string $remark = null): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE leave_requests 
             SET status = 'rejected', approved_by = :approver, approved_at = NOW(), admin_remark = :remark, updated_at = NOW() 
             WHERE id = :id"
        );
        return $stmt->execute([
            'id'       => $id,
            'approver' => $approverId,
            'remark'   => $remark
        ]);
    }

    /**
     * สถิติตามประเภทการลา
     */
    public function getStatsByLeaveType(?int $fiscalYearId = null): array
    {
        $sql = "SELECT lt.name, COUNT(lr.id) as total_requests, COALESCE(SUM(lr.total_days), 0) as total_days
                FROM leave_requests lr
                JOIN leave_types lt ON lr.leave_type_id = lt.id";
        $params = [];
        if ($fiscalYearId) {
            $sql .= " WHERE lr.fiscal_year_id = :fiscal_year_id";
            $params['fiscal_year_id'] = $fiscalYearId;
        }
        $sql .= " GROUP BY lt.id, lt.name ORDER BY total_requests DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * สถิติตามเดือน
     */
    public function getStatsByMonth(?int $fiscalYearId = null): array
    {
        $sql = "SELECT MONTH(lr.start_date) as month, COUNT(lr.id) as total_requests, 
                    COALESCE(SUM(lr.total_days), 0) as total_days
                FROM leave_requests lr";
        $params = [];
        if ($fiscalYearId) {
            $sql .= " WHERE lr.fiscal_year_id = :fiscal_year_id";
            $params['fiscal_year_id'] = $fiscalYearId;
        }
        $sql .= " GROUP BY MONTH(lr.start_date) ORDER BY month";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * สถิติตามสถานะ
     */
    public function getStatsByStatus(?int $fiscalYearId = null): array
    {
        $sql = "SELECT lr.status, COUNT(lr.id) as total_requests
                FROM leave_requests lr";
        $params = [];
        if ($fiscalYearId) {
            $sql .= " WHERE lr.fiscal_year_id = :fiscal_year_id";
            $params['fiscal_year_id'] = $fiscalYearId;
        }
        $sql .= " GROUP BY lr.status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * สถิติรายบุคคล
     */
    public function getStatsByUser(?int $fiscalYearId = null): array
    {
        $sql = "SELECT u.id, CONCAT(u.first_name, ' ', u.last_name) as full_name, u.position,
                    COUNT(lr.id) as total_requests, COALESCE(SUM(lr.total_days), 0) as total_days
                FROM leave_requests lr
                JOIN users u ON lr.user_id = u.id";
        $params = [];
        if ($fiscalYearId) {
            $sql .= " WHERE lr.fiscal_year_id = :fiscal_year_id";
            $params['fiscal_year_id'] = $fiscalYearId;
        }
        $sql .= " GROUP BY u.id, full_name, u.position ORDER BY total_days DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * ดึงจำนวนวันลาที่ใช้ไปในปีงบประมาณ
     */
    public function getUserLeaveDaysInFiscalYear(int $userId, int $leaveTypeId, int $fiscalYearId): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(total_days), 0) as total 
             FROM leave_requests 
             WHERE user_id = :user_id 
               AND leave_type_id = :leave_type_id 
               AND fiscal_year_id = :fiscal_year_id 
               AND status != 'rejected'"
        );
        $stmt->execute([
            'user_id'        => $userId,
            'leave_type_id'  => $leaveTypeId,
            'fiscal_year_id' => $fiscalYearId
        ]);
        return (float)$stmt->fetch()['total'];
    }
}
