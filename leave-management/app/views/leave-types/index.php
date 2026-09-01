<?php
/**
 * Leave Types View
 * จัดการประเภทการลา (CRUD) - Admin
 */
?>

<div class="data-card animate-fade-in">
    <div class="card-header-custom">
        <h5><i class="fas fa-list-alt"></i> ประเภทการลาทั้งหมด</h5>
        <button class="btn btn-primary-custom btn-sm" onclick="openAddModal()">
            <i class="fas fa-plus me-1"></i> เพิ่มประเภทการลา
        </button>
    </div>
    <div class="card-body-custom">
        <div class="table-responsive">
            <table class="table data-table" id="leaveTypesTable">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>ชื่อประเภทการลา</th>
                        <th>รายละเอียด</th>
                        <th width="120">วันลาสูงสุด/ปี</th>
                        <th width="100">สถานะ</th>
                        <th width="120">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaveTypes as $lt): ?>
                    <tr>
                        <td><?= $lt['id'] ?></td>
                        <td><strong><?= htmlspecialchars($lt['name']) ?></strong></td>
                        <td><?= htmlspecialchars($lt['description'] ?? '-') ?></td>
                        <td class="text-center">
                            <?= $lt['max_days'] > 0 ? '<strong>' . $lt['max_days'] . '</strong> วัน' : '<span class="text-muted">ไม่จำกัด</span>' ?>
                        </td>
                        <td>
                            <?= $lt['is_active'] 
                                ? '<span class="badge-approved">ใช้งาน</span>' 
                                : '<span class="badge-rejected">ปิดใช้งาน</span>' ?>
                        </td>
                        <td>
                            <button class="btn btn-sm-action btn-edit" onclick="editLeaveType(<?= $lt['id'] ?>)" title="แก้ไข">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm-action btn-delete" onclick="deleteLeaveType(<?= $lt['id'] ?>)" title="ลบ">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add/Edit -->
<div class="modal fade" id="leaveTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-plus-circle me-2"></i>เพิ่มประเภทการลา</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="leaveTypeForm">
                    <input type="hidden" id="leaveTypeId" name="id">
                    <div class="mb-3">
                        <label class="form-label">ชื่อประเภทการลา <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="leaveTypeName" name="name" required placeholder="เช่น ลากิจ, ลาป่วย">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">รายละเอียด</label>
                        <textarea class="form-control" id="leaveTypeDesc" name="description" rows="3" placeholder="คำอธิบายเพิ่มเติม"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">จำนวนวันลาสูงสุดต่อปี</label>
                        <input type="number" class="form-control" id="leaveTypeMaxDays" name="max_days" min="0" value="0" placeholder="0 = ไม่จำกัด">
                        <small class="text-muted">กรอก 0 หากไม่จำกัดจำนวนวัน</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary-custom" id="btnSave" onclick="saveLeaveType()">
                    <i class="fas fa-save me-1"></i> บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let leaveTypeModalInstance = null;
let editMode = false;

function getLeaveTypeModal() {
    if (!leaveTypeModalInstance) {
        leaveTypeModalInstance = new bootstrap.Modal(document.getElementById('leaveTypeModal'));
    }
    return leaveTypeModalInstance;
}

function openAddModal() {
    editMode = false;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>เพิ่มประเภทการลา';
    document.getElementById('leaveTypeForm').reset();
    document.getElementById('leaveTypeId').value = '';
    getLeaveTypeModal().show();
}

async function editLeaveType(id) {
    editMode = true;
    const res = await fetchApi(`${BASE_URL}?page=leave-types&action=get&id=${id}`);
    if (res.success) {
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>แก้ไขประเภทการลา';
        document.getElementById('leaveTypeId').value = res.data.id;
        document.getElementById('leaveTypeName').value = res.data.name;
        document.getElementById('leaveTypeDesc').value = res.data.description || '';
        document.getElementById('leaveTypeMaxDays').value = res.data.max_days;
        getLeaveTypeModal().show();
    }
}

async function saveLeaveType() {
    const action = editMode ? 'update' : 'store';
    const url = `${BASE_URL}?page=leave-types&action=${action}`;
    await submitForm('leaveTypeForm', url);
}

function deleteLeaveType(id) {
    confirmDelete(id, `${BASE_URL}?page=leave-types&action=delete`);
}
</script>
