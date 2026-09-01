<?php
/**
 * Fiscal Year Model
 * จัดการข้อมูลปีงบประมาณ
 */

class FiscalYearModel extends Model
{
    protected string $table = 'fiscal_years';

    /**
     * ดึงปีงบประมาณปัจจุบัน
     */
    public function getCurrent(): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM fiscal_years WHERE is_current = 1 AND is_active = 1 LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * ดึงปีงบประมาณที่ Active ทั้งหมด
     */
    public function getAllActive(): array
    {
        return $this->findWhere(['is_active' => 1], 'start_date DESC');
    }

    /**
     * กำหนดปีงบประมาณปัจจุบัน
     */
    public function setCurrent(int $id): bool
    {
        // ยกเลิกปีงบประมาณปัจจุบันทั้งหมดก่อน
        $this->db->exec("UPDATE fiscal_years SET is_current = 0");
        // กำหนดปีงบประมาณที่เลือกเป็นปัจจุบัน
        $stmt = $this->db->prepare("UPDATE fiscal_years SET is_current = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
