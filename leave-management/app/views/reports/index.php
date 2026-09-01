<?php
/**
 * Reports View
 * รายงานและสถิติการลา - Admin/Executive
 * แสดง Chart.js + DataTables + Print/Export
 */

// คำนวณสรุปภาพรวม
$totalLeaveRequests = 0;
$totalLeaveDays = 0;
foreach ($statsByLeaveType as $row) {
    $totalLeaveRequests += (int)$row['total_requests'];
    $totalLeaveDays += (float)$row['total_days'];
}

// เตรียมข้อมูลสำหรับ Chart.js - ประเภทการลา
$chartLeaveTypeLabels = json_encode(array_column($statsByLeaveType, 'name'), JSON_UNESCAPED_UNICODE);
$chartLeaveTypeData = json_encode(array_map('intval', array_column($statsByLeaveType, 'total_requests')));
$chartLeaveTypeDays = json_encode(array_map('floatval', array_column($statsByLeaveType, 'total_days')));

// เตรียมข้อมูลสำหรับ Chart.js - รายเดือน
$thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
$monthLabels = [];
$monthData = [];
$monthDays = [];
foreach ($statsByMonth as $row) {
    $monthLabels[] = $thaiMonths[$row['month']] ?? $row['month'];
    $monthData[] = (int)$row['total_requests'];
    $monthDays[] = (float)$row['total_days'];
}
$chartMonthLabels = json_encode($monthLabels, JSON_UNESCAPED_UNICODE);
$chartMonthData = json_encode($monthData);
$chartMonthDays = json_encode($monthDays);

// เตรียมข้อมูลสำหรับ Chart.js - สถานะ
$statusLabels = [];
$statusData = [];
$statusColors = ['pending' => '#F59E0B', 'approved' => '#22C55E', 'rejected' => '#EF4444'];
$statusNames = ['pending' => 'รออนุมัติ', 'approved' => 'อนุมัติแล้ว', 'rejected' => 'ไม่อนุมัติ'];
$statusColorsArr = [];
foreach ($statsByStatus as $row) {
    $statusLabels[] = $statusNames[$row['status']] ?? $row['status'];
    $statusData[] = (int)$row['total_requests'];
    $statusColorsArr[] = $statusColors[$row['status']] ?? '#6b7280';
}
$chartStatusLabels = json_encode($statusLabels, JSON_UNESCAPED_UNICODE);
$chartStatusData = json_encode($statusData);
$chartStatusColors = json_encode($statusColorsArr);
?>

<!-- Print Only Official Header -->
<div class="print-only-header">
    <img src="<?= BASE_URL ?>public/assets/images/logo.png" alt="โลโก้โรงเรียน" class="print-logo-img">
    <h3>รายงานสรุปสถิติการลาของบุคลากร</h3>
    <h5>โรงเรียนบ้านหน้าเขาวัด (สพฐ.)</h5>
    <p>
        <?= $selectedFiscalYear ? 'ข้อมูลประจำปีงบประมาณ' : 'ข้อมูลสรุปปีงบประมาณทั้งหมด' ?>
        | วันที่ออกรายงาน: <?= date('d/m/') . (date('Y') + 543) ?>
    </p>
</div>

<!-- Filter & Actions Bar -->
<div class="filter-section animate-fade-in mb-4">
    <form method="GET" action="<?= BASE_URL ?>" id="reportFilterForm">
        <input type="hidden" name="page" value="reports">
        <div class="row g-3 align-items-end">
            <div class="col-md-5 col-lg-4">
                <label class="form-label fw-bold"><i class="fas fa-calendar-alt me-1 text-primary"></i> เลือกปีงบประมาณ</label>
                <select class="form-select" name="fiscal_year_id" onchange="this.form.submit()">
                    <option value="">-- แสดงปีงบประมาณทั้งหมด --</option>
                    <?php foreach ($fiscalYears as $fy): ?>
                    <option value="<?= $fy['id'] ?>" <?= ($selectedFiscalYear == $fy['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($fy['name']) ?> <?= $fy['is_current'] ? '(ปีปัจจุบัน)' : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-7 col-lg-8 text-md-end">
                <button type="button" class="btn btn-outline-secondary me-2" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> พิมพ์รายงาน
                </button>
                <button type="submit" class="btn btn-primary-custom">
                    <i class="fas fa-filter me-1"></i> กรองข้อมูล
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Summary Cards Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number"><?= $totalLeaveRequests ?></div>
                    <div class="stat-label">การลาทั้งหมด (ครั้ง)</div>
                </div>
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number"><?= number_format($totalLeaveDays, 1) ?></div>
                    <div class="stat-label">จำนวนวันลารวม (วัน)</div>
                </div>
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <?php
                    $pendingCount = 0;
                    foreach ($statsByStatus as $s) {
                        if ($s['status'] === 'pending') $pendingCount = $s['total_requests'];
                    }
                    ?>
                    <div class="stat-number"><?= $pendingCount ?></div>
                    <div class="stat-label">ใบลารออนุมัติ</div>
                </div>
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card danger">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number"><?= count($statsByUser) ?></div>
                    <div class="stat-label">บุคลากรที่ยื่นลา</div>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="row g-4 mb-4">
    <div class="col-xl-6">
        <div class="chart-card animate-fade-in">
            <h6><i class="fas fa-chart-bar me-2 text-primary"></i>สถิติตามประเภทการลา (จำนวนครั้ง)</h6>
            <div class="chart-container-wrapper">
                <canvas id="chartLeaveType"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="chart-card animate-fade-in">
            <h6><i class="fas fa-chart-pie me-2 text-primary"></i>สัดส่วนสถานะการอนุมัติ</h6>
            <div class="chart-container-wrapper">
                <canvas id="chartStatus"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="chart-card animate-fade-in">
            <h6><i class="fas fa-chart-line me-2 text-primary"></i>แนวโน้มการลารายเดือน (ครั้ง vs วัน)</h6>
            <div class="chart-container-wrapper" style="height: 320px;">
                <canvas id="chartMonthly"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Table: Individual Stats -->
<div class="data-card animate-fade-in">
    <div class="card-header-custom">
        <h5><i class="fas fa-list-ol"></i> สถิติการลาจำแนกรายบุคคล</h5>
    </div>
    <div class="card-body-custom">
        <?php if (empty($statsByUser)): ?>
        <div class="empty-state">
            <i class="fas fa-info-circle"></i>
            <p>ไม่พบข้อมูลสถิติตามเงื่อนไขที่เลือก</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table data-table" id="reportUserStatsTable">
                <thead>
                    <tr>
                        <th width="60">ลำดับ</th>
                        <th>ชื่อ - นามสกุล บุคลากร</th>
                        <th>ตำแหน่ง</th>
                        <th width="140" class="text-center">จำนวนการลา (ครั้ง)</th>
                        <th width="140" class="text-center">รวมจำนวนวัน (วัน)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statsByUser as $idx => $stat): ?>
                    <tr>
                        <td class="text-center"><?= $idx + 1 ?></td>
                        <td><strong><?= htmlspecialchars($stat['full_name']) ?></strong></td>
                        <td><?= htmlspecialchars($stat['position'] ?? '-') ?></td>
                        <td class="text-center"><span class="badge bg-primary px-3 py-2"><?= $stat['total_requests'] ?> ครั้ง</span></td>
                        <td class="text-center"><span class="badge bg-success-subtle text-success fw-bold px-3 py-2 fs-6"><?= number_format($stat['total_days'], 1) ?> วัน</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Print Only Official Signatures -->
<div class="print-only-footer">
    <div class="signature-container">
        <div class="signature-box">
            <div class="signature-line"></div>
            <p class="mb-1">( <?= htmlspecialchars($currentUser['full_name'] ?? '................................................') ?> )</p>
            <p class="text-muted small">ตำแหน่ง <?= htmlspecialchars($currentUser['position'] ?? 'ผู้จัดทำรายงาน') ?></p>
            <p class="text-muted small">ผู้จัดทำรายงาน</p>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <p class="mb-1">( ........................................................... )</p>
            <p class="text-muted small">ผู้อำนวยการโรงเรียนบ้านหน้าเขาวัด</p>
            <p class="text-muted small">ผู้อนุมัติรายงาน</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const chartColors = {
        primary: '#2563EB',
        primaryLight: 'rgba(37, 99, 235, 0.15)',
        success: '#22C55E',
        successLight: 'rgba(34, 197, 94, 0.15)',
        warning: '#F59E0B',
        danger: '#EF4444',
        purple: '#8B5CF6',
        teal: '#14B8A6'
    };

    const colorPalette = [
        '#2563EB', '#22C55E', '#F59E0B', '#EF4444', '#8B5CF6', '#14B8A6', '#EC4899'
    ];

    // 1. Bar Chart - ประเภทการลา
    const ctxType = document.getElementById('chartLeaveType');
    if (ctxType) {
        new Chart(ctxType, {
            type: 'bar',
            data: {
                labels: <?= $chartLeaveTypeLabels ?>,
                datasets: [{
                    label: 'จำนวนครั้ง',
                    data: <?= $chartLeaveTypeData ?>,
                    backgroundColor: colorPalette,
                    borderRadius: 8,
                    barThickness: 36
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            afterLabel: function(context) {
                                const days = <?= $chartLeaveTypeDays ?>;
                                return 'รวมทั้งสิ้น: ' + days[context.dataIndex] + ' วัน';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { family: 'Sarabun' } },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: { ticks: { font: { family: 'Sarabun', weight: '600' } }, grid: { display: false } }
                }
            }
        });
    }

    // 2. Doughnut Chart - สถานะการอนุมัติ
    const ctxStatus = document.getElementById('chartStatus');
    if (ctxStatus) {
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: <?= $chartStatusLabels ?>,
                datasets: [{
                    data: <?= $chartStatusData ?>,
                    backgroundColor: <?= $chartStatusColors ?>,
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { family: 'Sarabun', size: 13 }, padding: 18, usePointStyle: true }
                    }
                }
            }
        });
    }

    // 3. Line Chart - สถิติตามเดือน
    const ctxMonth = document.getElementById('chartMonthly');
    if (ctxMonth) {
        new Chart(ctxMonth, {
            type: 'line',
            data: {
                labels: <?= $chartMonthLabels ?>,
                datasets: [
                    {
                        label: 'จำนวนครั้งที่ลา',
                        data: <?= $chartMonthData ?>,
                        borderColor: chartColors.primary,
                        backgroundColor: chartColors.primaryLight,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'จำนวนวันรวม',
                        data: <?= $chartMonthDays ?>,
                        borderColor: chartColors.success,
                        backgroundColor: chartColors.successLight,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { font: { family: 'Sarabun', size: 13 }, usePointStyle: true }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { family: 'Sarabun' } },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: { ticks: { font: { family: 'Sarabun' } }, grid: { display: false } }
                }
            }
        });
    }
});
</script>
