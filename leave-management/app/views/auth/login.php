<?php
/**
 * Login Page
 * หน้าเข้าสู่ระบบ (Split-Screen Modern Design)
 */
$flash = Session::getFlash();
$csrfToken = Session::generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ระบบบริหารจัดการการลา โรงเรียนบ้านหน้าเขาวัด - เข้าสู่ระบบ">
    <title>เข้าสู่ระบบ | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Sarabun', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eef2f7;
            padding: 20px;
        }

        /* ===== Main Container Card ===== */
        .login-wrapper {
            width: 100%;
            max-width: 1000px;
            min-height: 600px;
            background: #ffffff;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 25px 70px rgba(15, 76, 129, 0.18), 0 10px 30px rgba(0, 0, 0, 0.05);
            display: flex;
            animation: fadeInScale 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.96) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* ===== Left Hero Section ===== */
        .login-hero {
            flex: 1.1;
            background: linear-gradient(135deg, #0F4C81 0%, #1e5bb8 50%, #2563EB 100%);
            padding: 50px 45px;
            color: #ffffff;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        /* Organic Background Circles */
        .hero-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            pointer-events: none;
        }
        .hero-shape-1 {
            width: 380px;
            height: 380px;
            top: -100px;
            right: -100px;
            background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0.02) 70%);
        }
        .hero-shape-2 {
            width: 260px;
            height: 260px;
            bottom: -50px;
            left: -50px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        }
        .hero-shape-3 {
            width: 180px;
            height: 180px;
            bottom: 120px;
            right: 40px;
            background: rgba(255, 255, 255, 0.06);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .logo-box {
            width: 110px;
            height: 110px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 35px;
            transition: transform 0.3s;
        }
        .logo-box:hover { transform: scale(1.05) rotate(-2deg); }
        .logo-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .welcome-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hero-title {
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1.25;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            opacity: 0.9;
            font-weight: 400;
            margin-bottom: 25px;
        }

        .hero-desc {
            font-size: 0.92rem;
            line-height: 1.7;
            opacity: 0.8;
            max-width: 380px;
        }

        .hero-footer {
            position: relative;
            z-index: 2;
            font-size: 0.82rem;
            opacity: 0.7;
        }

        /* ===== Right Form Section ===== */
        .login-form-panel {
            flex: 1;
            padding: 55px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .form-header {
            margin-bottom: 32px;
        }

        .form-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #0F4C81;
            margin-bottom: 6px;
        }

        .form-header p {
            color: #6b7280;
            font-size: 0.92rem;
        }

        /* Form Group Customization */
        .custom-input-group {
            position: relative;
            margin-bottom: 22px;
        }

        .custom-input-group label {
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            color: #374151;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-left {
            position: absolute;
            left: 16px;
            color: #9ca3af;
            font-size: 17px;
            transition: color 0.3s;
            pointer-events: none;
        }

        .custom-input {
            width: 100%;
            height: 52px;
            padding: 12px 16px 12px 48px;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            font-size: 0.95rem;
            background: #f9fafb;
            color: #1f2937;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .custom-input:focus {
            outline: none;
            border-color: #2563EB;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .custom-input-group:focus-within .input-icon-left {
            color: #2563EB;
        }

        .btn-toggle-pass {
            position: absolute;
            right: 16px;
            background: none;
            border: none;
            color: #9ca3af;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-toggle-pass:hover {
            color: #2563EB;
            background: #eff6ff;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            font-size: 0.88rem;
        }

        .form-check-input:checked {
            background-color: #2563EB;
            border-color: #2563EB;
        }

        .form-check-label {
            color: #4b5563;
            cursor: pointer;
        }

        /* Sign In Button */
        .btn-submit-login {
            width: 100%;
            height: 52px;
            background: linear-gradient(135deg, #0F4C81 0%, #2563EB 100%);
            color: #ffffff;
            border: none;
            border-radius: 14px;
            font-size: 1.05rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.45);
            color: #ffffff;
        }

        .btn-submit-login:active {
            transform: translateY(0);
        }

        /* Responsive Breakpoints */
        @media (max-width: 880px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 480px;
            }
            .login-hero {
                padding: 40px 30px;
            }
            .hero-shape-1 { width: 250px; height: 250px; }
            .hero-title { font-size: 1.8rem; }
            .logo-box { width: 90px; height: 90px; margin-bottom: 20px; }
            .login-form-panel { padding: 40px 30px; }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <!-- ===== LEFT HERO PANEL ===== -->
        <div class="login-hero">
            <div class="hero-shape hero-shape-1"></div>
            <div class="hero-shape hero-shape-2"></div>
            <div class="hero-shape hero-shape-3"></div>

            <div class="hero-content">
                <div class="logo-box">
                    <img src="<?= BASE_URL ?>public/assets/images/logo.png" alt="โลโก้โรงเรียนบ้านหน้าเขาวัด">
                </div>

                <span class="welcome-badge"><i class="fas fa-graduation-cap me-1"></i> ยินดีต้อนรับ</span>
                <h1 class="hero-title"><?= APP_NAME ?></h1>
                <h3 class="hero-subtitle"><?= APP_SUBTITLE ?></h3>
                <p class="hero-desc">
                    ระบบบริหารจัดการและติดตามข้อมูลการลาของบุคลากรทางการศึกษา 
                    ใช้งานง่าย รวดเร็ว และรองรับทุกอุปกรณ์
                </p>
            </div>

            <div class="hero-footer">
                <p class="mb-0">&copy; <?= date('Y') + 543 ?> โรงเรียนบ้านหน้าเขาวัด (สพฐ.)</p>
            </div>
        </div>

        <!-- ===== RIGHT FORM PANEL ===== -->
        <div class="login-form-panel">
            <div class="form-header">
                <h2>เข้าสู่ระบบ</h2>
                <p>กรุณากรอกชื่อผู้ใช้และรหัสผ่านเพื่อเข้าใช้งานระบบ</p>
            </div>

            <form action="<?= BASE_URL ?>?page=login" method="POST" id="loginForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <!-- Username Input -->
                <div class="custom-input-group">
                    <label for="username">ชื่อผู้ใช้ (Username)</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon-left"></i>
                        <input type="text" class="custom-input" id="username" name="username" 
                               placeholder="กรอกชื่อผู้ใช้" required autocomplete="username" autofocus>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="custom-input-group">
                    <label for="password">รหัสผ่าน (Password)</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon-left"></i>
                        <input type="password" class="custom-input" id="password" name="password" 
                               placeholder="กรอกรหัสผ่าน" required autocomplete="current-password">
                        <button type="button" class="btn-toggle-pass" onclick="togglePassword()">SHOW</button>
                    </div>
                </div>

                <!-- Options -->
                <div class="form-options">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe" checked>
                        <label class="form-check-label" for="rememberMe">จำชื่อผู้ใช้ไว้ในระบบ</label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-submit-login" id="btnLogin">
                    <span>เข้าสู่ระบบ</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const btn = document.querySelector('.btn-toggle-pass');
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = 'HIDE';
            } else {
                input.type = 'password';
                btn.textContent = 'SHOW';
            }
        }

        <?php if ($flash): ?>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '<?= $flash['type'] === 'danger' ? 'error' : $flash['type'] ?>',
                title: '<?= $flash['type'] === 'success' ? 'สำเร็จ' : ($flash['type'] === 'warning' ? 'แจ้งเตือน' : 'ผิดพลาด') ?>',
                text: '<?= addslashes($flash['message']) ?>',
                confirmButtonColor: '#2563EB',
                timer: <?= $flash['type'] === 'success' ? 2000 : 'null' ?>,
                timerProgressBar: <?= $flash['type'] === 'success' ? 'true' : 'false' ?>
            });
        });
        <?php endif; ?>

        // Prevent Double Submit
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('btnLogin');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังเข้าสู่ระบบ...';
        });
    </script>
</body>
</html>
