<?php
/**
 * Approvals View
 * พิจารณาอนุมัติการลา - Executive
 */
?>

<!-- Status Filter Tabs -->
<div class="mb-4">
    <div class="btn-group" role="group">
        <a href="<?= BASE_URL ?>?page=approvals" class="btn <?= empty($currentStatus) ? 'btn-primary' : 'btn-outline-primary' ?>">
            <i class="fas fa-list me-1"></i> ทั้งหมด
        </a>
        <a href="<?= BASE_URL ?>?page=approvals&status=pending" class="btn <?= $currentStatus === 'pending' ? 'btn-warning' : 'btn-outline-warning' ?>">
            <i class="fas fa-clock me-1"></i> รออนุมัติ
        </a>
        <a href="<?= BASE_URL ?>?page=approvals&status=approved" class="btn <?= $currentStatus === 'approved' ? 'btn-success' : 'btn-outline-success' ?>">
            <i class="fas fa-check-circle me-1"></i> อนุมัติแล้ว
        </a>
        <a href="<?= BASE_URL ?>?page=approvals&status=rejected" class="btn <?= $currentStatus === 'rejected' ? 'btn-danger' : 'btn-outline-danger' ?>">
            <i class="fas fa-times-circle me-1"></i> ไม่อนุมัติ
        </a>
    </div>
</div>

<div class="data-card animate-fade-in">
    <div class="card-header-custom">
        <h5><i class="fas fa-check-circle"></i> รายการใบลา</h5>
    </div>
    <div class="card-body-custom">
        <?php if (empty($requests)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>ไม่มีใบลาในรายการ</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table data-table" id="approvalsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ผู้ยื่นลา</th>
                        <th>ประเภท</th>
                        <th>วันที่เริ่ม</th>
                        <th>วันที่สิ้นสุด</th>
                        <th>จำนวนวัน</th>
                        <th>เหตุผล</th>
                        <th>สถานะ</th>
                        <th width="180">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                    <tr>
                        <td><?= $req['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($req['first_name'] . ' ' . $req['last_name']) ?></strong>
                            <br><small class="text-muted"><?= htmlspecialchars($req['position'] ?? '') ?></small>
                        </td>
                        <td><?= htmlspecialchars($req['leave_type_name']) ?></td>
                        <td><?= date('d/m/', strtotime($req['start_date'])) . (date('Y', strtotime($req['start_date'])) + 543) ?></td>
                        <td><?= date('d/m/', strtotime($req['end_date'])) . (date('Y', strtotime($req['end_date'])) + 543) ?></td>
                        <td class="text-center"><strong><?= $req['total_days'] ?></strong></td>
                        <td><small><?= htmlspecialchars(mb_strimwidth($req['reason'], 0, 40, '...')) ?></small></td>
                        <td>
                            <?php
                            $statusMap = [
                                'pending'  => '<span class="badge-pending"><i class="fas fa-clock me-1"></i>รออนุมัติ</span>',
                                'approved' => '<span class="badge-approved"><i class="fas fa-check-circle me-1"></i>อนุมัติแล้ว</span>',
                                'rejected' => '<span class="badge-rejected"><i class="fas fa-times-circle me-1"></i>ไม่อนุมัติ</span>'
                            ];
                            echo $statusMap[$req['status']] ?? $req['status'];
                            ?>
                        </td>
                        <td>
                            <button class="btn btn-sm-action btn-view" onclick="viewApprovalDetail(<?= $req['id'] ?>)" title="ดูรายละเอียด">
                                <i class="fas fa-eye"></i>
                            </button>
                            <?php if ($req['status'] === 'pending'): ?>
                            <button class="btn btn-sm btn-success" onclick="approveRequest(<?= $req['id'] ?>)" title="อนุมัติ">
                                <i class="fas fa-check me-1"></i>อนุมัติ
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="rejectRequest(<?= $req['id'] ?>)" title="ไม่อนุมัติ">
                                <i class="fas fa-times me-1"></i>ไม่อนุมัติ
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="approvalDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-alt me-2"></i>รายละเอียดใบลา</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="approvalDetailContent"></div>
            <div class="modal-footer" id="approvalDetailFooter"></div>
        </div>
    </div>
</div>

<script>
async function viewApprovalDetail(id) {
    const res = await fetchApi(`${BASE_URL}?page=approvals&action=detail&id=${id}`);
    if (res.success) {
        const d = res.data;
        document.getElementById('approvalDetailContent').innerHTML = `
            <div class="detail-row"><div class="detail-label">ผู้ยื่นลา</div><div class="detail-value"><strong>${d.first_name} ${d.last_name}</strong></div></div>
            <div class="detail-row"><div class="detail-label">รหัสบุคลากร</div><div class="detail-value">${d.employee_id}</div></div>
            <div class="detail-row"><div class="detail-label">ตำแหน่ง</div><div class="detail-value">${d.position || '-'}</div></div>
            <div class="detail-row"><div class="detail-label">สังกัด</div><div class="detail-value">${d.department || '-'}</div></div>
            <hr>
            <div class="detail-row"><div class="detail-label">ประเภทการลา</div><div class="detail-value">${d.leave_type_name}</div></div>
            <div class="detail-row"><div class="detail-label">วันที่เริ่มลา</div><div class="detail-value">${formatDateThai(d.start_date)}</div></div>
            <div class="detail-row"><div class="detail-label">วันที่สิ้นสุด</div><div class="detail-value">${formatDateThai(d.end_date)}</div></div>
            <div class="detail-row"><div class="detail-label">จำนวนวัน</div><div class="detail-value"><strong>${d.total_days}</strong> วัน</div></div>
            <div class="detail-row"><div class="detail-label">เหตุผล</div><div class="detail-value">${d.reason}</div></div>
            <div class="detail-row"><div class="detail-label">สถานะ</div><div class="detail-value">${getStatusBadge(d.status)}</div></div>
            ${d.approver_name ? `<div class="detail-row"><div class="detail-label">ผู้อนุมัติ</div><div class="detail-value">${d.approver_name}</div></div>` : ''}
            ${d.admin_remark ? `<div class="detail-row"><div class="detail-label">หมายเหตุ</div><div class="detail-value"><em>${d.admin_remark}</em></div></div>` : ''}
            ${d.attachment ? `<div class="detail-row"><div class="detail-label">ไฟล์แนบ</div><div class="detail-value"><a href="${BASE_URL}public/assets/uploads/${d.attachment}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-1"></i>ดาวน์โหลด</a></div></div>` : ''}
        `;

        let footerHtml = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>';
        if (d.status === 'pending') {
            footerHtml = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-danger" onclick="bootstrap.Modal.getInstance(document.getElementById('approvalDetailModal')).hide(); rejectRequest(${d.id})">
                    <i class="fas fa-times me-1"></i>ไม่อนุมัติ
                </button>
                <button type="button" class="btn btn-success" onclick="bootstrap.Modal.getInstance(document.getElementById('approvalDetailModal')).hide(); approveRequest(${d.id})">
                    <i class="fas fa-check me-1"></i>อนุมัติ
                </button>
            `;
        }
        document.getElementById('approvalDetailFooter').innerHTML = footerHtml;
        new bootstrap.Modal(document.getElementById('approvalDetailModal')).show();
    }
}

async function approveRequest(id) {
    const { value: remark } = await Swal.fire({
        title: 'อนุมัติการลา',
        input: 'textarea',
        inputLabel: 'หมายเหตุ (ไม่จำเป็น)',
        inputPlaceholder: 'กรอกหมายเหตุ...',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22C55E',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-check me-1"></i> อนุมัติ',
        cancelButtonText: 'ยกเลิก',
        inputAttributes: { 'aria-label': 'หมายเหตุ' }
    });

    if (remark !== undefined) {
        const body = `csrf_token=${encodeURIComponent(CSRF_TOKEN)}&id=${id}&admin_remark=${encodeURIComponent(remark || '')}`;
        const res = await fetchApi(`${BASE_URL}?page=approvals&action=approve`, {
            method: 'POST',
            body: body
        });
        if (res.success) {
            Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: res.message, confirmButtonColor: '#2563EB', timer: 1500, timerProgressBar: true })
                .then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, confirmButtonColor: '#2563EB' });
        }
    }
}

async function rejectRequest(id) {
    const { value: remark } = await Swal.fire({
        title: 'ไม่อนุมัติการลา',
        input: 'textarea',
        inputLabel: 'หมายเหตุ (กรุณาระบุเหตุผล)',
        inputPlaceholder: 'ระบุเหตุผลที่ไม่อนุมัติ...',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-times me-1"></i> ไม่อนุมัติ',
        cancelButtonText: 'ยกเลิก',
        inputAttributes: { 'aria-label': 'หมายเหตุ' }
    });

    if (remark !== undefined) {
        const body = `csrf_token=${encodeURIComponent(CSRF_TOKEN)}&id=${id}&admin_remark=${encodeURIComponent(remark || '')}`;
        const res = await fetchApi(`${BASE_URL}?page=approvals&action=reject`, {
            method: 'POST',
            body: body
        });
        if (res.success) {
            Swal.fire({ icon: 'success', title: 'ดำเนินการแล้ว', text: res.message, confirmButtonColor: '#2563EB', timer: 1500, timerProgressBar: true })
                .then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, confirmButtonColor: '#2563EB' });
        }
    }
}
</script>
