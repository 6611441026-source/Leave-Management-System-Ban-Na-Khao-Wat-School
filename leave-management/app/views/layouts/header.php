<?php
/**
 * Layout Header
 * Sidebar + Topbar + Head
 */
$flash = Session::getFlash();
$currentPage = $_GET['page'] ?? 'dashboard';
$currentAction = $_GET['action'] ?? 'index';
$roleLabels = ['admin' => 'ผู้ดูแลระบบ', 'executive' => 'ผู้บริหาร', 'personnel' => 'บุคลากร'];
$roleBadgeClass = ['admin' => 'bg-danger', 'executive' => 'bg-primary', 'personnel' => 'bg-success'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ระบบบริหารจัดการการลา โรงเรียนบ้านหน้าเขาวัด">
    <title><?= htmlspecialchars($pageTitle ?? 'แดชบอร์ด') ?> | <?= APP_NAME ?></title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>public/assets/images/logo.png">
    <link rel="apple-touch-icon" href="<?= BASE_URL ?>public/assets/images/logo.png">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= BASE_URL ?>public/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- ===== SIDEBAR ===== -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="<?= BASE_URL ?>public/assets/images/logo.png" alt="โลโก้โรงเรียน" class="sidebar-logo-img">
                </div>
                <h5><?= APP_NAME ?></h5>
                <p><?= APP_SUBTITLE ?></p>
            </div>

            <ul class="sidebar-nav">
                <!-- Dashboard -->
                <li class="sidebar-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>?page=dashboard" class="sidebar-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>แดชบอร์ด</span>
                    </a>
                </li>

                <?php if (Auth::isAdmin()): ?>
                <!-- ===== เมนู Admin ===== -->
                <li class="sidebar-heading">จัดการข้อมูล</li>
                <li class="sidebar-item <?= $currentPage === 'leave-types' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>?page=leave-types" class="sidebar-link">
                        <i class="fas fa-list-alt"></i>
                        <span>ประเภทการลา</span>
                    </a>
                </li>
                <li class="sidebar-item <?= $currentPage === 'fiscal-years' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>?page=fiscal-years" class="sidebar-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>ปีงบประมาณ</span>
                    </a>
                </li>
                <li class="sidebar-item <?= $currentPage === 'users' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>?page=users" class="sidebar-link">
                        <i class="fas fa-users-cog"></i>
                        <span>จัดการผู้ใช้งาน</span>
                    </a>
                </li>
                <li class="sidebar-heading">ข้อมูลการลา</li>
                <li class="sidebar-item <?= $currentPage === 'leave-requests' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>?page=leave-requests" class="sidebar-link">
                        <i class="fas fa-file-alt"></i>
                        <span>ข้อมูลการลา</span>
                    </a>
                </li>
                <li class="sidebar-heading">รายงาน</li>
                <li class="sidebar-item <?= $currentPage === 'reports' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>?page=reports" class="sidebar-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>รายงานและสถิติ</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (Auth::isExecutive()): ?>
                <!-- ===== เมนู Executive ===== -->
                <li class="sidebar-heading">การอนุมัติ</li>
                <li class="sidebar-item <?= $currentPage === 'approvals' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>?page=approvals" class="sidebar-link">
                        <i class="fas fa-check-circle"></i>
                        <span>พิจารณาอนุมัติ</span>
                    </a>
                </li>
                <li class="sidebar-heading">รายงาน</li>
                <li class="sidebar-item <?= $currentPage === 'reports' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>?page=reports" class="sidebar-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>รายงานและสถิติ</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (Auth::isPersonnel()): ?>
                <!-- ===== เมนู Personnel ===== -->
                <li class="sidebar-heading">การลา</li>
                <li class="sidebar-item <?= ($currentPage === 'leave-requests' && $currentAction === 'create') ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>?page=leave-requests&action=create" class="sidebar-link">
                        <i class="fas fa-plus-circle"></i>
                        <span>ยื่นใบลา</span>
                    </a>
                </li>
                <li class="sidebar-item <?= ($currentPage === 'leave-requests' && $currentAction === 'index') ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>?page=leave-requests" class="sidebar-link">
                        <i class="fas fa-file-alt"></i>
                        <span>ข้อมูลการลาของฉัน</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Profile & Logout -->
                <li class="sidebar-heading">บัญชีผู้ใช้</li>
                <li class="sidebar-item <?= $currentPage === 'profile' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>?page=profile" class="sidebar-link">
                        <i class="fas fa-user-edit"></i>
                        <span>โปรไฟล์ส่วนตัว</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= BASE_URL ?>?page=logout" class="sidebar-link text-logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>ออกจากระบบ</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- ===== MAIN CONTENT ===== -->
        <div class="main-content">
            <!-- Topbar -->
            <nav class="topbar">
                <div class="topbar-left">
                    <button id="sidebarToggle" class="btn btn-link sidebar-toggle" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="page-title mb-0"><?= htmlspecialchars($pageTitle ?? '') ?></h5>
                </div>

                <div class="topbar-right">
                    <div class="dropdown">
                        <a href="#" class="user-dropdown" data-bs-toggle="dropdown" aria-expanded="false" id="userDropdown">
                            <div class="user-avatar">
                                <?php if (!empty($currentUser['avatar'])): ?>
                                    <img src="<?= BASE_URL ?>public/assets/uploads/<?= htmlspecialchars($currentUser['avatar']) ?>" alt="Avatar" class="avatar-topbar-img">
                                <?php else: ?>
                                    <i class="fas fa-user-circle"></i>
                                <?php endif; ?>
                            </div>
                            <div class="user-info d-none d-md-block">
                                <span class="user-name"><?= htmlspecialchars($currentUser['full_name'] ?? '') ?></span>
                                <span class="badge <?= $roleBadgeClass[$currentUser['role'] ?? ''] ?? 'bg-secondary' ?> user-role-badge">
                                    <?= $roleLabels[$currentUser['role'] ?? ''] ?? '' ?>
                                </span>
                            </div>
                            <i class="fas fa-chevron-down ms-2 d-none d-md-inline"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                            <li class="dropdown-header">
                                <strong><?= htmlspecialchars($currentUser['full_name'] ?? '') ?></strong>
                                <br><small class="text-muted"><?= $roleLabels[$currentUser['role'] ?? ''] ?? '' ?></small>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>?page=profile">
                                    <i class="fas fa-user-circle me-2 text-primary"></i>โปรไฟล์ของฉัน
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= BASE_URL ?>?page=logout">
                                    <i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="content-wrapper">
                <?php if ($flash): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: '<?= $flash['type'] === 'danger' ? 'error' : $flash['type'] ?>',
                            title: '<?= $flash['type'] === 'success' ? 'สำเร็จ!' : 'แจ้งเตือน' ?>',
                            text: '<?= addslashes($flash['message']) ?>',
                            confirmButtonColor: '#2563EB',
                            timer: <?= $flash['type'] === 'success' ? 2000 : 'null' ?>,
                            timerProgressBar: <?= $flash['type'] === 'success' ? 'true' : 'false' ?>
                        });
                    });
                </script>
                <?php endif; ?>
