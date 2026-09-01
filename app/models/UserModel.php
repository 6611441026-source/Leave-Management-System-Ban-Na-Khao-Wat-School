<?php
/**
 * User Model
 * จัดการข้อมูลผู้ใช้งาน
 */

class UserModel extends Model
{
    protected string $table = 'users';

    /**
     * ค้นหาด้วย Username
     */
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username AND is_active = 1 LIMIT 1");
        $stmt->execute(['username' => $username]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * ยืนยันตัวตน
     */
    public function authenticate(string $username, string $password): ?array
    {
        $user = $this->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    /**
     * ดึงบุคลากรทั้งหมดที่ Active
     */
    public function getAllPersonnel(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = 'personnel' AND is_active = 1 ORDER BY first_name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * ดึงผู้ใช้ทั้งหมดที่ Active
     */
    public function getAllActive(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE is_active = 1 ORDER BY first_name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * นับจำนวนผู้ใช้ตาม Role
     */
    public function countByRole(string $role): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM users WHERE role = :role AND is_active = 1");
        $stmt->execute(['role' => $role]);
        return (int)$stmt->fetch()['cnt'];
    }

    /**
     * สร้างผู้ใช้ใหม่พร้อม Hash Password
     */
    public function createUser(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return $this->create($data);
    }

    /**
     * แก้ไขผู้ใช้ (Hash Password ถ้ามี)
     */
    public function updateUser(int $id, array $data): bool
    {
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']);
        }
        return $this->update($id, $data);
    }

    /**
     * ตรวจสอบ Username ซ้ำ
     */
    public function isUsernameExists(string $username, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as cnt FROM users WHERE username = :username";
        $params = ['username' => $username];
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetch()['cnt'] > 0;
    }

    /**
     * ตรวจสอบ Email ซ้ำ
     */
    public function isEmailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as cnt FROM users WHERE email = :email";
        $params = ['email' => $email];
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetch()['cnt'] > 0;
    }

    /**
     * ตรวจสอบรหัสบุคลากรซ้ำ
     */
    public function isEmployeeIdExists(string $employeeId, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as cnt FROM users WHERE employee_id = :employee_id";
        $params = ['employee_id' => $employeeId];
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetch()['cnt'] > 0;
    }
}
