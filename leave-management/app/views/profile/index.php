<?php
/**
 * Profile Index View
 * หน้าแก้ไขข้อมูลส่วนตัวและโปรไฟล์ผู้ใช้
 */
$roleLabels = ['admin' => 'ผู้ดูแลระบบ', 'executive' => 'ผู้บริหาร', 'personnel' => 'บุคลากร'];
$roleBadgeClass = ['admin' => 'bg-danger', 'executive' => 'bg-primary', 'personnel' => 'bg-success'];
?>

<div class="row g-4">
    <!-- ===== LEFT SIDE: USER PROFILE CARD ===== -->
    <div class="col-xl-4 col-lg-5">
        <div class="data-card animate-fade-in text-center p-4">
            <div class="profile-avatar-wrapper position-relative mx-auto mb-3" style="width: 140px; height: 140px;">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= BASE_URL ?>public/assets/uploads/<?= htmlspecialchars($user['avatar']) ?>" 
                         alt="Profile Avatar" id="avatarPreview" class="rounded-circle img-thumbnail w-100 h-100" style="object-fit: cover;">
                <?php else: ?>
                    <div id="avatarFallback" class="w-100 h-100 rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center border" style="font-size: 64px; color: #2563EB;">
                        <i class="fas fa-user"></i>
                    </div>
                    <img src="" alt="Profile Avatar" id="avatarPreview" class="rounded-circle img-thumbnail w-100 h-100 d-none" style="object-fit: cover;">
                <?php endif; ?>
                <label for="avatarInput" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle shadow" style="width: 38px; height: 38px; padding: 6px 0;" title="เปลี่ยนรูปโปรไฟล์">
                    <i class="fas fa-camera"></i>
                </label>
            </div>

            <h4 class="fw-bold mb-1"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h4>
            <p class="text-muted mb-2"><code><?= htmlspecialchars($user['employee_id']) ?></code></p>
            
            <div class="mb-3">
                <span class="badge <?= $roleBadgeClass[$user['role']] ?? 'bg-secondary' ?> px-3 py-2 fs-6">
                    <?= $roleLabels[$user['role']] ?? $user['role'] ?>
                </span>
            </div>

            <div class="p-3 bg-light rounded-3 text-start mb-3">
                <div class="mb-2"><i class="fas fa-briefcase text-primary me-2"></i><strong>ตำแหน่ง:</strong> <?= htmlspecialchars($user['position'] ?? '-') ?></div>
                <div class="mb-2"><i class="fas fa-building text-primary me-2"></i><strong>สังกัด:</strong> <?= htmlspecialchars($user['department'] ?? '-') ?></div>
                <div class="mb-2"><i class="fas fa-envelope text-primary me-2"></i><strong>อีเมล:</strong> <?= htmlspecialchars($user['email']) ?></div>
                <div><i class="fas fa-phone text-primary me-2"></i><strong>เบอร์โทร:</strong> <?= htmlspecialchars($user['phone'] ?? '-') ?></div>
            </div>

            <!-- Leave Stats Summary -->
            <div class="row g-2 text-center border-top pt-3">
                <div class="col-4">
                    <div class="p-2 bg-warning-subtle rounded-3">
                        <div class="fw-bold text-warning-emphasis fs-5"><?= $stats['pending'] ?? 0 ?></div>
                        <small class="text-muted">รออนุมัติ</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 bg-success-subtle rounded-3">
                        <div class="fw-bold text-success-emphasis fs-5"><?= $stats['approved'] ?? 0 ?></div>
                        <small class="text-muted">อนุมัติแล้ว</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 bg-danger-subtle rounded-3">
                        <div class="fw-bold text-danger-emphasis fs-5"><?= $stats['rejected'] ?? 0 ?></div>
                        <small class="text-muted">ไม่อนุมัติ</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== RIGHT SIDE: EDIT FORMS ===== -->
    <div class="col-xl-8 col-lg-7">
        <!-- Edit Personal Info Card -->
        <div class="data-card animate-fade-in mb-4">
            <div class="card-header-custom">
                <h5><i class="fas fa-user-edit"></i> แก้ไขข้อมูลส่วนตัว</h5>
            </div>
            <div class="card-body-custom">
                <form id="profileForm" enctype="multipart/form-data">
                    <input type="file" id="avatarInput" name="avatar" class="d-none" accept="image/*" onchange="previewImage(this)">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">เบอร์โทรศัพท์</label>
                            <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ตำแหน่ง</label>
                            <input type="text" class="form-control" name="position" value="<?= htmlspecialchars($user['position'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">สังกัด/กลุ่มงาน</label>
                            <input type="text" class="form-control" name="department" value="<?= htmlspecialchars($user['department'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="text-end mt-2">
                        <button type="button" class="btn btn-primary-custom" onclick="saveProfile()">
                            <i class="fas fa-save me-1"></i> บันทึกข้อมูลส่วนตัว
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password Card -->
        <div class="data-card animate-fade-in">
            <div class="card-header-custom">
                <h5><i class="fas fa-key"></i> เปลี่ยนรหัสผ่าน</h5>
            </div>
            <div class="card-body-custom">
                <form id="passwordForm">
                    <div class="mb-3">
                        <label class="form-label">รหัสผ่านปัจจุบัน <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="current_password" required placeholder="กรอกรหัสผ่านปัจจุบัน">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="new_password" minlength="6" required placeholder="อย่างน้อย 6 ตัวอักษร">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="confirm_password" minlength="6" required placeholder="กรอกรหัสผ่านใหม่อีกครั้ง">
                        </div>
                    </div>

                    <div class="text-end mt-2">
                        <button type="button" class="btn btn-warning" onclick="changePassword()" style="border-radius: 10px; font-weight: 600;">
                            <i class="fas fa-lock me-1"></i> เปลี่ยนรหัสผ่าน
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const avatarPreview = document.getElementById('avatarPreview');
            const avatarFallback = document.getElementById('avatarFallback');
            avatarPreview.src = e.target.result;
            avatarPreview.classList.remove('d-none');
            if (avatarFallback) {
                avatarFallback.classList.add('d-none');
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

async function saveProfile() {
    const form = document.getElementById('profileForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    formData.append('csrf_token', CSRF_TOKEN);

    const data = await fetchApi(`${BASE_URL}?page=profile&action=update`, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF_TOKEN }
    });

    if (data.success) {
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ!',
            text: data.message,
            confirmButtonColor: '#2563EB',
            timer: 1500,
            timerProgressBar: true
        }).then(() => {
            location.reload();
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

async function changePassword() {
    const form = document.getElementById('passwordForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    formData.append('csrf_token', CSRF_TOKEN);
    const body = formToUrlEncoded(formData);

    const data = await fetchApi(`${BASE_URL}?page=profile&action=updatePassword`, {
        method: 'POST',
        body: body
    });

    if (data.success) {
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ!',
            text: data.message,
            confirmButtonColor: '#2563EB',
            timer: 1500,
            timerProgressBar: true
        }).then(() => {
            form.reset();
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
</script>
