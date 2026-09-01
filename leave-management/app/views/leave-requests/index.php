<?php
/**
 * Leave Requests List View
 * รายการใบลา - Personnel (ของฉัน) / Admin (ทั้งหมด)
 */
?>

<!-- Filter Section -->
<div class="filter-section animate-fade-in">
    <form method="GET" action="<?= BASE_URL ?>">
        <input type="hidden" name="page" value="leave-requests">
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label">ประเภทการลา</label>
                <select class="form-select" name="leave_type_id">
                    <option value="">ทั้งหมด</option>
                    <?php foreach ($leaveTypes as $lt): ?>
                    <option value="<?= $lt['id'] ?>" <?= (($filters['leave_type_id'] ?? '') == $lt['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($lt['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">ปีงบประมาณ</label>
                <select class="form-select" name="fiscal_year_id">
                    <option value="">ทั้งหมด</option>
                    <?php foreach ($fiscalYears as $fy): ?>
                    <option value="<?= $fy['id'] ?>" <?= (($filters['fiscal_year_id'] ?? '') == $fy['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($fy['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">สถานะ</label>
                <select class="form-select" name="status">
                    <option value="">ทั้งหมด</option>
                    <option value="pending" <?= (($filters['status'] ?? '') === 'pending') ? 'selected' : '' ?>>รออนุมัติ</option>
                    <option value="approved" <?= (($filters['status'] ?? '') === 'approved') ? 'selected' : '' ?>>อนุมัติแล้ว</option>
                    <option value="rejected" <?= (($filters['status'] ?? '') === 'rejected') ? 'selected' : '' ?>>ไม่อนุมัติ</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">วันที่เริ่ม</label>
                <input type="date" class="form-control" name="start_date" value="<?= $filters['start_date'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">วันที่สิ้นสุด</label>
                <input type="date" class="form-control" name="end_date" value="<?= $filters['end_date'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary-custom w-100">
                    <i class="fas fa-search me-1"></i> ค้นหา
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="data-card animate-fade-in">
    <div class="card-header-custom">
        <h5><i class="fas fa-file-alt"></i> <?= htmlspecialchars($pageTitle) ?></h5>
        <?php if (Auth::isPersonnel()): ?>
        <a href="<?= BASE_URL ?>?page=leave-requests&action=create" class="btn btn-primary-custom btn-sm">
            <i class="fas fa-plus me-1"></i> ยื่นใบลา
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body-custom">
        <?php if (empty($requests)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>ไม่พบข้อมูลการลา</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table data-table" id="leaveRequestsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <?php if (Auth::isAdmin()): ?><th>ผู้ยื่นลา</th><?php endif; ?>
                        <th>ประเภท</th>
                        <th>วันที่เริ่ม</th>
                        <th>วันที่สิ้นสุด</th>
                        <th>จำนวนวัน</th>
                        <th>สถานะ</th>
                        <th>หมายเหตุ</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                    <tr>
                        <td><?= $req['id'] ?></td>
                        <?php if (Auth::isAdmin()): ?>
                        <td>
                            <strong><?= htmlspecialchars($req['first_name'] . ' ' . $req['last_name']) ?></strong>
                            <br><small class="text-muted"><?= htmlspecialchars($req['position'] ?? '') ?></small>
                        </td>
                        <?php endif; ?>
                        <td><?= htmlspecialchars($req['leave_type_name']) ?></td>
                        <td><?= date('d/m/', strtotime($req['start_date'])) . (date('Y', strtotime($req['start_date'])) + 543) ?></td>
                        <td><?= date('d/m/', strtotime($req['end_date'])) . (date('Y', strtotime($req['end_date'])) + 543) ?></td>
                        <td class="text-center"><strong><?= $req['total_days'] ?></strong></td>
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
                            <?= $req['admin_remark'] ? '<small>' . htmlspecialchars(mb_strimwidth($req['admin_remark'], 0, 30, '...')) . '</small>' : '<span class="text-muted">-</span>' ?>
                        </td>
                        <td>
                            <button class="btn btn-sm-action btn-view" onclick="viewRequestDetail(<?= $req['id'] ?>)" title="ดูรายละเอียด">
                                <i class="fas fa-eye"></i>
                            </button>
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
<div class="modal fade" id="requestDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-alt me-2"></i>รายละเอียดใบลา</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="requestDetailContent"></div>
        </div>
    </div>
</div>

<script>
async function viewRequestDetail(id) {
    const res = await fetchApi(`${BASE_URL}?page=leave-requests&action=detail&id=${id}`);
    if (res.success) {
        const d = res.data;
        document.getElementById('requestDetailContent').innerHTML = `
            <div class="detail-row"><div class="detail-label">ผู้ยื่นลา</div><div class="detail-value"><strong>${d.first_name} ${d.last_name}</strong></div></div>
            <div class="detail-row"><div class="detail-label">รหัสบุคลากร</div><div class="detail-value">${d.employee_id}</div></div>
            <div class="detail-row"><div class="detail-label">ตำแหน่ง</div><div class="detail-value">${d.position || '-'}</div></div>
            <div class="detail-row"><div class="detail-label">สังกัด</div><div class="detail-value">${d.department || '-'}</div></div>
            <hr>
            <div class="detail-row"><div class="detail-label">ประเภทการลา</div><div class="detail-value">${d.leave_type_name}</div></div>
            <div class="detail-row"><div class="detail-label">ปีงบประมาณ</div><div class="detail-value">${d.fiscal_year_name}</div></div>
            <div class="detail-row"><div class="detail-label">วันที่เริ่มลา</div><div class="detail-value">${formatDateThai(d.start_date)}</div></div>
            <div class="detail-row"><div class="detail-label">วันที่สิ้นสุด</div><div class="detail-value">${formatDateThai(d.end_date)}</div></div>
            <div class="detail-row"><div class="detail-label">จำนวนวัน</div><div class="detail-value"><strong>${d.total_days}</strong> วัน</div></div>
            <div class="detail-row"><div class="detail-label">เหตุผล</div><div class="detail-value">${d.reason}</div></div>
            <hr>
            <div class="detail-row"><div class="detail-label">สถานะ</div><div class="detail-value">${getStatusBadge(d.status)}</div></div>
            ${d.approver_name ? `<div class="detail-row"><div class="detail-label">ผู้อนุมัติ</div><div class="detail-value">${d.approver_name}</div></div>` : ''}
            ${d.approved_at ? `<div class="detail-row"><div class="detail-label">วันที่พิจารณา</div><div class="detail-value">${formatDateTimeThai(d.approved_at)}</div></div>` : ''}
            ${d.admin_remark ? `<div class="detail-row"><div class="detail-label">หมายเหตุ</div><div class="detail-value"><em>${d.admin_remark}</em></div></div>` : ''}
            ${d.attachment ? `<div class="detail-row"><div class="detail-label">ไฟล์แนบ</div><div class="detail-value"><a href="${BASE_URL}public/assets/uploads/${d.attachment}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-1"></i>ดาวน์โหลด</a></div></div>` : ''}
            <div class="detail-row"><div class="detail-label">วันที่ยื่น</div><div class="detail-value">${formatDateTimeThai(d.created_at)}</div></div>
        `;
        new bootstrap.Modal(document.getElementById('requestDetailModal')).show();
    }
}
</script>
