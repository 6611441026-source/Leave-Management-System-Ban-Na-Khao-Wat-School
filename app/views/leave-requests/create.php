<?php
/**
 * Leave Request - Create Form
 * แบบฟอร์มยื่นใบลา - สำหรับบุคลากร
 */
?>

<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10">
        <div class="data-card animate-fade-in">
            <div class="card-header-custom">
                <h5><i class="fas fa-plus-circle"></i> ยื่นใบลา</h5>
                <a href="<?= BASE_URL ?>?page=leave-requests" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> กลับ
                </a>
            </div>
            <div class="card-body-custom">
                <form id="leaveRequestForm" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ประเภทการลา <span class="text-danger">*</span></label>
                            <select class="form-select" id="leave_type_id" name="leave_type_id" required>
                                <option value="">-- เลือกประเภทการลา --</option>
                                <?php foreach ($leaveTypes as $lt): ?>
                                <option value="<?= $lt['id'] ?>" data-max="<?= $lt['max_days'] ?>">
                                    <?= htmlspecialchars($lt['name']) ?>
                                    <?= $lt['max_days'] > 0 ? "(สูงสุด {$lt['max_days']} วัน/ปี)" : '' ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ปีงบประมาณ <span class="text-danger">*</span></label>
                            <select class="form-select" id="fiscal_year_id" name="fiscal_year_id" required>
                                <option value="">-- เลือกปีงบประมาณ --</option>
                                <?php foreach ($fiscalYears as $fy): ?>
                                <option value="<?= $fy['id'] ?>" <?= ($currentFiscalYear && $fy['id'] == $currentFiscalYear['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($fy['name']) ?>
                                    <?= $fy['is_current'] ? '(ปัจจุบัน)' : '' ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">วันที่เริ่มลา <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">วันที่สิ้นสุด <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">จำนวนวัน</label>
                            <input type="number" class="form-control" id="total_days" name="total_days" readonly 
                                   style="background: #f0f7ff; font-weight: 700; font-size: 1.1rem; color: #2563EB;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">เหตุผลการลา <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reason" name="reason" rows="4" required 
                                  placeholder="กรุณาระบุเหตุผลการลา"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">แนบไฟล์ <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="attachment" name="attachment" required
                               accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                        <small class="text-muted">กรุณาแนบไฟล์เพื่อยื่นใบลา รองรับ: JPG, PNG, GIF, PDF, DOC, DOCX (สูงสุด 5MB)</small>
                    </div>

                    <div class="text-end">
                        <a href="<?= BASE_URL ?>?page=leave-requests" class="btn btn-secondary me-2">
                            <i class="fas fa-times me-1"></i> ยกเลิก
                        </a>
                        <button type="button" class="btn btn-primary-custom" onclick="submitLeaveRequest()">
                            <i class="fas fa-paper-plane me-1"></i> ยื่นใบลา
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // คำนวณจำนวนวันลาอัตโนมัติ
    calculateDays('start_date', 'end_date', 'total_days');
});

async function submitLeaveRequest() {
    // Validate
    const form = document.getElementById('leaveRequestForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const result = await Swal.fire({
        title: 'ยืนยันการยื่นใบลา',
        text: 'คุณต้องการยื่นใบลาหรือไม่?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563EB',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> ยื่นใบลา',
        cancelButtonText: 'ยกเลิก'
    });

    if (result.isConfirmed) {
        const formData = new FormData(form);
        formData.append('csrf_token', CSRF_TOKEN);

        const data = await fetchApi(`${BASE_URL}?page=leave-requests&action=store`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF_TOKEN }
        });

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: data.message,
                confirmButtonColor: '#2563EB'
            }).then(() => {
                window.location.href = `${BASE_URL}?page=leave-requests`;
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด',
                html: data.message,
                confirmButtonColor: '#2563EB'
            });
        }
    }
}
</script>
