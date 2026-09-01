<?php
/**
 * Front Controller
 * ระบบบริหารจัดการการลา - โรงเรียนบ้านหน้าเขาวัด
 * จุดเริ่มต้นของทุก Request
 */

// กำหนด Base Path
define('BASE_PATH', __DIR__);

// โหลด Configuration
require_once BASE_PATH . '/app/config/config.php';

// โหลด Core Classes
require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/core/Session.php';
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/Router.php';

// โหลด Middleware
require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';

// โหลด Models
require_once BASE_PATH . '/app/models/UserModel.php';
require_once BASE_PATH . '/app/models/LeaveTypeModel.php';
require_once BASE_PATH . '/app/models/FiscalYearModel.php';
require_once BASE_PATH . '/app/models/LeaveRequestModel.php';

// โหลด Controllers
require_once BASE_PATH . '/app/controllers/AuthController.php';
require_once BASE_PATH . '/app/controllers/DashboardController.php';
require_once BASE_PATH . '/app/controllers/LeaveTypeController.php';
require_once BASE_PATH . '/app/controllers/FiscalYearController.php';
require_once BASE_PATH . '/app/controllers/UserController.php';
require_once BASE_PATH . '/app/controllers/LeaveRequestController.php';
require_once BASE_PATH . '/app/controllers/ApprovalController.php';
require_once BASE_PATH . '/app/controllers/ReportController.php';
require_once BASE_PATH . '/app/controllers/ProfileController.php';

// เริ่ม Session
Session::start();

// โหลด Routes และ Dispatch
$router = require_once BASE_PATH . '/routes.php';
$router->dispatch();
