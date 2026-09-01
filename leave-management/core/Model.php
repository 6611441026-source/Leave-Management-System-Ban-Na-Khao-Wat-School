<?php
/**
 * Base Model Class
 * CRUD Operations ด้วย PDO Prepared Statements
 */

class Model
{
    protected PDO $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * ดึงข้อมูลทั้งหมด
     */
    public function findAll(string $orderBy = 'id DESC'): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    }

    /**
     * ค้นหาด้วย ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * ค้นหาตามเงื่อนไข
     */
    public function findWhere(array $conditions, string $orderBy = 'id DESC'): array
    {
        $where = [];
        $params = [];
        foreach ($conditions as $key => $value) {
            $where[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }
        $whereClause = implode(' AND ', $where);
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$whereClause} ORDER BY {$orderBy}");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * เพิ่มข้อมูล
     */
    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    /**
     * แก้ไขข้อมูล
     */
    public function update(int $id, array $data): bool
    {
        $set = [];
        foreach (array_keys($data) as $key) {
            $set[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $set);
        $data['id'] = $id;
        $stmt = $this->db->prepare("UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id");
        return $stmt->execute($data);
    }

    /**
     * ลบข้อมูล
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * นับจำนวนข้อมูล
     */
    public function count(array $conditions = []): int
    {
        if (empty($conditions)) {
            $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM {$this->table}");
        } else {
            $where = [];
            $params = [];
            foreach ($conditions as $key => $value) {
                $where[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
            $whereClause = implode(' AND ', $where);
            $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM {$this->table} WHERE {$whereClause}");
            $stmt->execute($params);
        }
        return (int) $stmt->fetch()['cnt'];
    }
}
