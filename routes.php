<?php
/**
 * Routes Configuration
 * กำหนดเส้นทาง URL และสิทธิ์การเข้าถึงตาม Role
 */

$router = new Router();

// Dashboard & Profile - ทุก Role
$router->register('dashboard', 'DashboardController', ['admin', 'executive', 'personnel']);
$router->register('profile',   'ProfileController',   ['admin', 'executive', 'personnel']);

// Admin - จัดการข้อมูลพื้นฐาน
$router->register('leave-types',  'LeaveTypeController',  ['admin']);
$router->register('fiscal-years', 'FiscalYearController', ['admin']);
$router->register('users',        'UserController',       ['admin']);

// บุคลากร + Admin - จัดการใบลา
$router->register('leave-requests', 'LeaveRequestController', ['admin', 'personnel']);

// ผู้บริหาร - อนุมัติการลา
$router->register('approvals', 'ApprovalController', ['executive']);

// Admin + ผู้บริหาร - รายงานและสถิติ
$router->register('reports', 'ReportController', ['admin', 'executive']);

return $router;
