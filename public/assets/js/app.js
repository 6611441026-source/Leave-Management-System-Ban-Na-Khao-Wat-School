/**
 * ระบบบริหารจัดการการลา - โรงเรียนบ้านหน้าเขาวัด
 * Custom JavaScript (ES6)
 */

'use strict';

// ===== Sidebar Toggle =====
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');

    if (sidebarToggle && sidebar) {
        // สร้าง Overlay
        let overlay = document.querySelector('.sidebar-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);
        }

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    // Init DataTables ถ้ามี
    initDataTables();
});

// ===== DataTables Initialization =====
function initDataTables() {
    document.querySelectorAll('.data-table').forEach(table => {
        if (!$.fn.DataTable.isDataTable(table)) {
            $(table).DataTable({
                responsive: true,
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    infoEmpty: "ไม่มีข้อมูล",
                    infoFiltered: "(กรองจาก _MAX_ รายการ)",
                    zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                    paginate: {
                        first: "หน้าแรก",
                        last: "หน้าสุดท้าย",
                        next: '<i class="fas fa-chevron-right"></i>',
                        previous: '<i class="fas fa-chevron-left"></i>'
                    }
                },
                pageLength: 10,
                order: [[0, 'desc']],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
            });
        }
    });
}

// ===== AJAX Helper with CSRF =====
async function fetchApi(url, options = {}) {
    const defaultHeaders = {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': CSRF_TOKEN
    };

    // ถ้าไม่ใช่ FormData ให้ใช้ JSON headers
    if (!(options.body instanceof FormData)) {
        defaultHeaders['Content-Type'] = 'application/x-www-form-urlencoded';
    }

    const config = {
        ...options,
        headers: {
            ...defaultHeaders,
            ...options.headers
        }
    };

    try {
        const response = await fetch(url, config);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('API Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้',
            confirmButtonColor: '#2563EB'
        });
        return { success: false, message: 'Network error' };
    }
}

// ===== Form Data to URL Encoded =====
function formToUrlEncoded(formData) {
    const params = new URLSearchParams();
    for (const [key, value] of formData.entries()) {
        if (!(value instanceof File)) {
            params.append(key, value);
        }
    }
    return params.toString();
}

// ===== SweetAlert Confirm Delete =====
function confirmDelete(id, url, tableSel) {
    Swal.fire({
        title: 'ยืนยันการลบ',
        text: 'คุณต้องการลบข้อมูลนี้หรือไม่? การดำเนินการนี้ไม่สามารถย้อนกลับได้',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash me-1"></i> ลบ',
        cancelButtonText: 'ยกเลิก',
        reverseButtons: true
    }).then(async (result) => {
        if (result.isConfirmed) {
            const body = `csrf_token=${encodeURIComponent(CSRF_TOKEN)}&id=${id}`;
            const data = await fetchApi(url, {
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
    });
}

// ===== Submit Form via AJAX =====
async function submitForm(formId, url, successCallback) {
    const form = document.getElementById(formId);
    if (!form) return;

    const formData = new FormData(form);

    // ตรวจสอบว่ามีไฟล์หรือไม่
    let hasFile = false;
    for (const [key, value] of formData.entries()) {
        if (value instanceof File && value.size > 0) {
            hasFile = true;
            break;
        }
    }

    let data;
    if (hasFile) {
        // ส่งเป็น FormData (multipart)
        formData.append('csrf_token', CSRF_TOKEN);
        data = await fetchApi(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF_TOKEN }
        });
    } else {
        // ส่งเป็น URL encoded
        formData.append('csrf_token', CSRF_TOKEN);
        const body = formToUrlEncoded(formData);
        data = await fetchApi(url, {
            method: 'POST',
            body: body
        });
    }

    if (data.success) {
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ!',
            text: data.message,
            confirmButtonColor: '#2563EB',
            timer: 1500,
            timerProgressBar: true
        }).then(() => {
            if (successCallback) {
                successCallback(data);
            } else {
                location.reload();
            }
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'ผิดพลาด',
            html: data.message,
            confirmButtonColor: '#2563EB'
        });
    }

    return data;
}

// ===== Calculate Leave Days =====
function calculateDays(startDateId, endDateId, totalDaysId) {
    const startInput = document.getElementById(startDateId);
    const endInput = document.getElementById(endDateId);
    const totalInput = document.getElementById(totalDaysId);

    if (!startInput || !endInput || !totalInput) return;

    const parseDateOnly = (value) => {
        if (!value) return null;
        const [year, month, day] = value.split('-').map(Number);
        return new Date(Date.UTC(year, month - 1, day));
    };

    const calculate = () => {
        const start = parseDateOnly(startInput.value);
        const end = parseDateOnly(endInput.value);

        if (start && end && end >= start) {
            const diffTime = end.getTime() - start.getTime();
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
            totalInput.value = diffDays;
        } else {
            totalInput.value = '';
        }
    };

    startInput.addEventListener('input', calculate);
    startInput.addEventListener('change', calculate);
    endInput.addEventListener('input', calculate);
    endInput.addEventListener('change', calculate);

    calculate();
}

// ===== Format Date (Thai) =====
function formatDateThai(dateStr) {
    if (!dateStr) return '-';
    const months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
                    'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    const date = new Date(dateStr);
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear() + 543;
    return `${day} ${month} ${year}`;
}

// ===== Format DateTime (Thai) =====
function formatDateTimeThai(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    const formatted = formatDateThai(dateStr);
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${formatted} ${hours}:${minutes} น.`;
}

// ===== Get Status Badge HTML =====
function getStatusBadge(status) {
    const map = {
        'pending':  '<span class="badge-pending"><i class="fas fa-clock me-1"></i>รออนุมัติ</span>',
        'approved': '<span class="badge-approved"><i class="fas fa-check-circle me-1"></i>อนุมัติแล้ว</span>',
        'rejected': '<span class="badge-rejected"><i class="fas fa-times-circle me-1"></i>ไม่อนุมัติ</span>'
    };
    return map[status] || status;
}
