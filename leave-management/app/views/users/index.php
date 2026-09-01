<?php
/**
 * Users View
 * จัดการผู้ใช้งาน (CRUD) - Admin
 */
$roleLabels = ['admin' => 'ผู้ดูแลระบบ', 'executive' => 'ผู้บริหาร', 'personnel' => 'บุคลากร'];
$roleBadgeClass = ['admin' => 'bg-danger', 'executive' => 'bg-primary', 'personnel' => 'bg-success'];
?>

<div class="data-card animate-fade-in">
    <div class="card-header-custom">
        <h5><i class="fas fa-users-cog"></i> ผู้ใช้งานทั้งหมด</h5>
        <button class="btn btn-primary-custom btn-sm" onclick="openAddUserModal()">
            <i class="fas fa-user-plus me-1"></i> เพิ่มผู้ใช้งาน
        </button>
    </div>
    <div class="card-body-custom">
        <div class="table-responsive">
            <table class="table data-table" id="usersTable">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>รหัส</th>
                        <th>ชื่อ - นามสกุล</th>
                        <th>ชื่อผู้ใช้</th>
                        <th>บทบาท</th>
                        <th>ตำแหน่ง</th>
                        <th width="80">สถานะ</th>
                        <th width="120">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><code><?= htmlspecialchars($u['employee_id']) ?></code></td>
                        <td><strong><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></strong></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td>
                            <span class="badge <?= $roleBadgeClass[$u['role']] ?? 'bg-secondary' ?>">
                                <?= $roleLabels[$u['role']] ?? $u['role'] ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($u['position'] ?? '-') ?></td>
                        <td>
                            <?= $u['is_active'] 
                                ? '<span class="badge-approved">ใช้งาน</span>' 
                                : '<span class="badge-rejected">ปิด</span>' ?>
                        </td>
                        <td>
                            <button class="btn btn-sm-action btn-edit" onclick="editUser(<?= $u['id'] ?>)" title="แก้ไข">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php if ($u['id'] !== Auth::id()): ?>
                            <button class="btn btn-sm-action btn-delete" onclick="deleteUser(<?= $u['id'] ?>)" title="ลบ">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add/Edit User -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle"><i class="fas fa-user-plus me-2"></i>เพิ่มผู้ใช้งาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">รหัสบุคลากร <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="userEmpId" name="employee_id" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">บทบาท <span class="text-danger">*</span></label>
                            <select class="form-select" id="userRole" name="role" required>
                                <option value="personnel">บุคลากร</option>
                                <option value="executive">ผู้บริหาร</option>
                                <option value="admin">ผู้ดูแลระบบ</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="userFirstName" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="userLastName" name="last_name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="userEmail" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">เบอร์โทรศัพท์</label>
                            <input type="text" class="form-control" id="userPhone" name="phone">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ตำแหน่ง</label>
                            <input type="text" class="form-control" id="userPosition" name="position">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">สังกัด/กลุ่มงาน</label>
                            <input type="text" class="form-control" id="userDepartment" name="department">
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชื่อผู้ใช้ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="userUsername" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" id="passwordLabel">รหัสผ่าน <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="userPassword" name="password" minlength="6">
                            <small class="text-muted" id="passwordHint">อย่างน้อย 6 ตัวอักษร</small>
                        </div>
                    </div>
                    <div class="mb-3" id="activeField" style="display:none;">
                        <label class="form-label">สถานะ</label>
                        <select class="form-select" id="userIsActive" name="is_active">
                            <option value="1">ใช้งาน</option>
                            <option value="0">ปิดใช้งาน</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary-custom" onclick="saveUser()">
                    <i class="fas fa-save me-1"></i> บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let userModalInstance = null;
let userEditMode = false;

function getUserModal() {
    if (!userModalInstance) {
        userModalInstance = new bootstrap.Modal(document.getElementById('userModal'));
    }
    return userModalInstance;
}

function openAddUserModal() {
    userEditMode = false;
    document.getElementById('userModalTitle').innerHTML = '<i class="fas fa-user-plus me-2"></i>เพิ่มผู้ใช้งาน';
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('userPassword').required = true;
    document.getElementById('passwordLabel').innerHTML = 'รหัสผ่าน <span class="text-danger">*</span>';
    document.getElementById('passwordHint').textContent = 'อย่างน้อย 6 ตัวอักษร';
    document.getElementById('activeField').style.display = 'none';
    getUserModal().show();
}

async function editUser(id) {
    userEditMode = true;
    const res = await fetchApi(`${BASE_URL}?page=users&action=get&id=${id}`);
    if (res.success) {
        const d = res.data;
        document.getElementById('userModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>แก้ไขผู้ใช้งาน';
        document.getElementById('userId').value = d.id;
        document.getElementById('userEmpId').value = d.employee_id;
        document.getElementById('userFirstName').value = d.first_name;
        document.getElementById('userLastName').value = d.last_name;
        document.getElementById('userEmail').value = d.email;
        document.getElementById('userUsername').value = d.username;
        document.getElementById('userRole').value = d.role;
        document.getElementById('userPosition').value = d.position || '';
        document.getElementById('userDepartment').value = d.department || '';
        document.getElementById('userPhone').value = d.phone || '';
        document.getElementById('userIsActive').value = d.is_active;
        document.getElementById('userPassword').value = '';
        document.getElementById('userPassword').required = false;
        document.getElementById('passwordLabel').innerHTML = 'รหัสผ่านใหม่';
        document.getElementById('passwordHint').textContent = 'เว้นว่างถ้าไม่ต้องการเปลี่ยน';
        document.getElementById('activeField').style.display = 'block';
        getUserModal().show();
    }
}

async function saveUser() {
    const action = userEditMode ? 'update' : 'store';
    await submitForm('userForm', `${BASE_URL}?page=users&action=${action}`);
}

function deleteUser(id) {
    confirmDelete(id, `${BASE_URL}?page=users&action=delete`);
}
</script>
