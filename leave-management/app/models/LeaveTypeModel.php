<?php
/**
 * Leave Type Model
 * จัดการข้อมูลประเภทการลา
 */

class LeaveTypeModel extends Model
{
    protected string $table = 'leave_types';

    /**
     * ดึงประเภทการลาที่ Active
     */
    public function getAllActive(): array
    {
        return $this->findWhere(['is_active' => 1], 'name ASC');
    }

    /**
     * ค้นหาประเภทการลา
     */
    public function search(string $keyword): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM leave_types WHERE name LIKE :keyword OR description LIKE :keyword2 ORDER BY name ASC"
        );
        $stmt->execute([
            'keyword'  => '%' . $keyword . '%',
            'keyword2' => '%' . $keyword . '%'
        ]);
        return $stmt->fetchAll();
    }
}
