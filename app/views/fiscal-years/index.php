<?php
/**
 * Fiscal Years View
 * จัดการปีงบประมาณ (CRUD) - Admin
 */
?>

<div class="data-card animate-fade-in">
    <div class="card-header-custom">
        <h5><i class="fas fa-calendar-alt"></i> ปีงบประมาณทั้งหมด</h5>
        <button class="btn btn-primary-custom btn-sm" onclick="openAddFYModal()">
            <i class="fas fa-plus me-1"></i> เพิ่มปีงบประมาณ
        </button>
    </div>
    <div class="card-body-custom">
        <div class="table-responsive">
            <table class="table data-table" id="fiscalYearsTable">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>ชื่อปีงบประมาณ</th>
                        <th>วันเริ่มต้น</th>
                        <th>วันสิ้นสุด</th>
                        <th width="130">ปัจจุบัน</th>
                        <th width="160">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fiscalYears as $fy): ?>
                    <tr>
                        <td><?= $fy['id'] ?></td>
                        <td><strong><?= htmlspecialchars($fy['name']) ?></strong></td>
                        <td><?= date('d/m/', strtotime($fy['start_date'])) . (date('Y', strtotime($fy['start_date'])) + 543) ?></td>
                        <td><?= date('d/m/', strtotime($fy['end_date'])) . (date('Y', strtotime($fy['end_date'])) + 543) ?></td>
                        <td class="text-center">
                            <?php if ($fy['is_current']): ?>
                                <span class="badge-current"><i class="fas fa-star me-1"></i>ปัจจุบัน</span>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-primary" onclick="setCurrentFY(<?= $fy['id'] ?>)" title="กำหนดเป็นปีปัจจุบัน">
                                    <i class="fas fa-check"></i> ตั้งเป็นปัจจุบัน
                                </button>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm-action btn-edit" onclick="editFiscalYear(<?= $fy['id'] ?>)" title="แก้ไข">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php if (!$fy['is_current']): ?>
                            <button class="btn btn-sm-action btn-delete" onclick="deleteFiscalYear(<?= $fy['id'] ?>)" title="ลบ">
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

<!-- Modal Add/Edit -->
<div class="modal fade" id="fyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fyModalTitle"><i class="fas fa-plus-circle me-2"></i>เพิ่มปีงบประมาณ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="fyForm">
                    <input type="hidden" id="fyId" name="id">
                    <div class="mb-3">
                        <label class="form-label">ชื่อปีงบประมาณ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="fyName" name="name" required placeholder="เช่น ปีงบประมาณ 2569">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันเริ่มต้น <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fyStartDate" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันสิ้นสุด <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fyEndDate" name="end_date" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary-custom" onclick="saveFiscalYear()">
                    <i class="fas fa-save me-1"></i> บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let fyModalInstance = null;
let fyEditMode = false;

function getFyModal() {
    if (!fyModalInstance) {
        fyModalInstance = new bootstrap.Modal(document.getElementById('fyModal'));
    }
    return fyModalInstance;
}

function openAddFYModal() {
    fyEditMode = false;
    document.getElementById('fyModalTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>เพิ่มปีงบประมาณ';
    document.getElementById('fyForm').reset();
    document.getElementById('fyId').value = '';
    getFyModal().show();
}

async function editFiscalYear(id) {
    fyEditMode = true;
    const res = await fetchApi(`${BASE_URL}?page=fiscal-years&action=get&id=${id}`);
    if (res.success) {
        document.getElementById('fyModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>แก้ไขปีงบประมาณ';
        document.getElementById('fyId').value = res.data.id;
        document.getElementById('fyName').value = res.data.name;
        document.getElementById('fyStartDate').value = res.data.start_date;
        document.getElementById('fyEndDate').value = res.data.end_date;
        getFyModal().show();
    }
}

async function saveFiscalYear() {
    const action = fyEditMode ? 'update' : 'store';
    await submitForm('fyForm', `${BASE_URL}?page=fiscal-years&action=${action}`);
}

function deleteFiscalYear(id) {
    confirmDelete(id, `${BASE_URL}?page=fiscal-years&action=delete`);
}

async function setCurrentFY(id) {
    const result = await Swal.fire({
        title: 'ยืนยัน',
        text: 'ต้องการกำหนดเป็นปีงบประมาณปัจจุบันหรือไม่?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563EB',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    });

    if (result.isConfirmed) {
        const body = `csrf_token=${encodeURIComponent(CSRF_TOKEN)}&id=${id}`;
        const res = await fetchApi(`${BASE_URL}?page=fiscal-years&action=setCurrent`, {
            method: 'POST',
            body: body
        });
        if (res.success) {
            Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: res.message, confirmButtonColor: '#2563EB', timer: 1500, timerProgressBar: true })
                .then(() => location.reload());
        }
    }
}
</script>
