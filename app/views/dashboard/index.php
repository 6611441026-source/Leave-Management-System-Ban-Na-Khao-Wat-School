<?php
/**
 * Dashboard View
 * แสดงแดชบอร์ดตามบทบาทผู้ใช้ (Admin / Executive / Personnel)
 */
$role = Auth::role();
?>

<!-- ===== ADMIN DASHBOARD ===== -->
<?php if ($role === 'admin'): ?>
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number"><?= $totalPersonnel ?? 0 ?></div>
                    <div class="stat-label">จำนวนบุคลากร</div>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number"><?= $totalRequests ?? 0 ?></div>
                    <div class="stat-label">จำนวนใบลาทั้งหมด</div>
                </div>
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number"><?= $pendingCount ?? 0 ?></div>
                    <div class="stat-label">รออนุมัติ</div>
                </div>
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number"><?= $approvedCount ?? 0 ?></div>
                    <div class="stat-label">อนุมัติแล้ว</div>
                </div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- ===== EXECUTIVE DASHBOARD ===== -->
<?php elseif ($role === 'executive'): ?>
<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="stat-card warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number"><?= $pendingCount ?? 0 ?></div>
                    <div class="stat-label">รออนุมัติ</div>
                </div>
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="stat-card success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number"><?= $approvedCount ?? 0 ?></div>
                    <div class="stat-label">อนุมัติแล้ว</div>
                </div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="stat-card danger">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number"><?= $rejectedCount ?? 0 ?></div>
                    <div class="stat-label">ไม่อนุมัติ</div>
                </div>
                <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- ===== PERSONNEL DASHBOARD ===== -->
<?php else: ?>
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number"><?= $myRequests ?? 0 ?></div>
                    <div class="stat-label">ใบลาของฉัน</div>
                </div>
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number"><?= $pendingCount ?? 0 ?></div>
                    <div class="stat-label">รออนุมัติ</div>
                </div>
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number"><?= $approvedCount ?? 0 ?></div>
                    <div class="stat-label">อนุมัติแล้ว</div>
                </div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card danger">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number"><?= $rejectedCount ?? 0 ?></div>
                    <div class="stat-label">ไม่อนุมัติ</div>
                </div>
                <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ===== RECENT REQUESTS TABLE ===== -->
<?php
$tableData = $recentRequests ?? $pendingRequests ?? [];
$tableTitle = ($role === 'executive') ? 'ใบลารออนุมัติ' : 'ใบลาล่าสุด';
?>
<div class="data-card animate-fade-in">
    <div class="card-header-custom">
        <h5><i class="fas fa-list-alt"></i> <?= $tableTitle ?></h5>
        <?php if ($role === 'personnel'): ?>
        <a href="<?= BASE_URL ?>?page=leave-requests&action=create" class="btn btn-primary-custom btn-sm">
            <i class="fas fa-plus me-1"></i> ยื่นใบลา
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body-custom">
        <?php if (empty($tableData)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>ยังไม่มีข้อมูลใบลา</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <?php if ($role !== 'personnel'): ?><th>ผู้ยื่นลา</th><?php endif; ?>
                        <th>ประเภทการลา</th>
                        <th>วันที่เริ่ม</th>
                        <th>วันที่สิ้นสุด</th>
                        <th>จำนวนวัน</th>
                        <th>สถานะ</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tableData as $req): ?>
                    <tr>
                        <td><?= $req['id'] ?></td>
                        <?php if ($role !== 'personnel'): ?>
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
                            <button class="btn btn-sm-action btn-view" onclick="viewLeaveDetail(<?= $req['id'] ?>)" title="ดูรายละเอียด">
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
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-alt me-2"></i>รายละเอียดใบลา</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Dynamic content -->
            </div>
        </div>
    </div>
</div>

<script>
async function viewLeaveDetail(id) {
    const page = '<?= ($role === 'executive') ? "approvals" : "leave-requests" ?>';
    const res = await fetchApi(`${BASE_URL}?page=${page}&action=detail&id=${id}`);
    if (res.success) {
        const d = res.data;
        document.getElementById('detailContent').innerHTML = `
            <div class="detail-row"><div class="detail-label">ผู้ยื่นลา</div><div class="detail-value">${d.first_name} ${d.last_name}</div></div>
            <div class="detail-row"><div class="detail-label">รหัสบุคลากร</div><div class="detail-value">${d.employee_id}</div></div>
            <div class="detail-row"><div class="detail-label">ตำแหน่ง</div><div class="detail-value">${d.position || '-'}</div></div>
            <div class="detail-row"><div class="detail-label">ประเภทการลา</div><div class="detail-value">${d.leave_type_name}</div></div>
            <div class="detail-row"><div class="detail-label">วันที่เริ่มลา</div><div class="detail-value">${formatDateThai(d.start_date)}</div></div>
            <div class="detail-row"><div class="detail-label">วันที่สิ้นสุด</div><div class="detail-value">${formatDateThai(d.end_date)}</div></div>
            <div class="detail-row"><div class="detail-label">จำนวนวัน</div><div class="detail-value"><strong>${d.total_days}</strong> วัน</div></div>
            <div class="detail-row"><div class="detail-label">เหตุผล</div><div class="detail-value">${d.reason}</div></div>
            <div class="detail-row"><div class="detail-label">สถานะ</div><div class="detail-value">${getStatusBadge(d.status)}</div></div>
            ${d.approver_name ? `<div class="detail-row"><div class="detail-label">ผู้อนุมัติ</div><div class="detail-value">${d.approver_name}</div></div>` : ''}
            ${d.approved_at ? `<div class="detail-row"><div class="detail-label">วันที่อนุมัติ</div><div class="detail-value">${formatDateTimeThai(d.approved_at)}</div></div>` : ''}
            ${d.admin_remark ? `<div class="detail-row"><div class="detail-label">หมายเหตุ</div><div class="detail-value">${d.admin_remark}</div></div>` : ''}
            ${d.attachment ? `<div class="detail-row"><div class="detail-label">ไฟล์แนบ</div><div class="detail-value"><a href="${BASE_URL}public/assets/uploads/${d.attachment}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-1"></i>ดาวน์โหลด</a></div></div>` : ''}
        `;
        new bootstrap.Modal(document.getElementById('detailModal')).show();
    }
}
</script>
