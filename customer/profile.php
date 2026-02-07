<?php
include 'db/config.php';
include 'header.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Fetch combined data
$query = "SELECT c.*, cp.* FROM customers c 
          LEFT JOIN customer_profiles cp ON c.id = cp.customer_id 
          WHERE c.id = $customer_id";
$res = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($res);

// 1. Initials Logic
$name_parts = explode(' ', trim($data['full_name']));
$initials = strtoupper(substr($name_parts[0], 0, 1) . (count($name_parts) > 1 ? substr(end($name_parts), 0, 1) : ''));

// 2. Percentage Calculation (15 fields total)
$profile_fields = [
    'full_name', 'email', 'phone', 'pan_number',
    'birth_date', 'state', 'city', 'pin_code', 'employee_type', 'monthly_income',
    'reference1_name', 'reference1_phone', 'reference2_name', 'reference2_phone'
];
$filled_count = 0;
foreach ($profile_fields as $field) { if (!empty($data[$field])) $filled_count++; }
$percentage = round(($filled_count / count($profile_fields)) * 100);

$kyc_status = (!empty($data['pan_number']));
?>

<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<style>
    :root { --slate-900: #0f172a; --slate-600: #475569; --slate-200: #e2e8f0; }
    .content-page { background-color: #f8fafc; min-height: 100vh; }
    .avatar-circle { width: 75px; height: 75px; background: var(--slate-900); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; font-weight: 800; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .nav-pills .nav-link { color: var(--slate-600); font-weight: 600; border-radius: 10px; padding: 12px 20px; }
    .nav-pills .nav-link.active { background: var(--slate-900); color: white; }
    .card-modern { border: 1px solid var(--slate-200); border-radius: 20px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
    .form-control:read-only { background-color: #f1f5f9; color: #64748b; cursor: not-allowed; }
    .form-label { font-weight: 700; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; color: var(--slate-600); }
    .invalid-feedback { font-size: 0.75rem; font-weight: 600; color: #ef4444; }
</style>

<div class="content-page">
    <div class="container-fluid pt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="card-modern p-4 mb-4">
                    <div class="d-flex align-items-center flex-wrap gap-4">
                        <div class="avatar-circle"><?= $initials ?></div>
                        <div class="flex-grow-1">
                            <h3 class="fw-bold mb-1"><?= htmlspecialchars($data['full_name']) ?></h3>
                            <div class="d-flex align-items-center gap-3" style="max-width: 400px;">
                                <div class="progress w-100" style="height: 8px; border-radius: 10px; background: #e2e8f0;">
                                    <div class="progress-bar bg-dark" style="width: <?= $percentage ?>%"></div>
                                </div>
                                <span class="fw-bold small"><?= $percentage ?>%</span>
                            </div>
                        </div>
                        <div class="ms-auto">
                            <?php if($kyc_status): ?>
                                <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill"><i class="fas fa-check-circle me-1"></i> KYC VERIFIED</span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-dark border border-warning px-3 py-2 rounded-pill"><i class="fas fa-clock me-1"></i> KYC PENDING</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-pills mb-4 gap-2" id="profileTabs">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#tab-personal"><i class="fas fa-id-card me-2"></i>Personal</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#tab-financial"><i class="fas fa-wallet me-2"></i>Financial</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#tab-address"><i class="fas fa-map-marked-alt me-2"></i>Address & Refs</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#tab-security"><i class="fas fa-shield-alt me-2"></i>Security</a></li>
                </ul>

                <form id="profileForm" action="db/update_profile.php" method="POST">
                    <div class="tab-content">
                        
                        <div class="tab-pane fade show active" id="tab-personal">
                            <div class="card-modern p-4 p-md-5">
                                <div class="row g-4">
                                    <div class="col-md-6"><label class="form-label">Email</label><input type="text" class="form-control" value="<?= $data['email'] ?>" readonly></div>
                                    <div class="col-md-6"><label class="form-label">Phone</label><input type="text" class="form-control" value="<?= $data['phone'] ?>" readonly></div>
                                    <div class="col-md-6"><label class="form-label">PAN Number</label><input type="text" name="pan_number" id="pan_number" class="form-control" value="<?= $data['pan_number'] ?>" <?= !empty($data['pan_number']) ? 'readonly' : '' ?> maxlength="10" style="text-transform: uppercase;"></div>
                                    <div class="col-md-6"><label class="form-label">Birth Date</label><input type="date" name="birth_date" class="form-control" value="<?= $data['birth_date'] ?>"></div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-financial">
                            <div class="card-modern p-4 p-md-5">
                                <div class="row g-4">
                                    <div class="col-md-6"><label class="form-label">Employment</label><select name="employee_type" class="form-select"><option value="salaried" <?= $data['employee_type']=='salaried'?'selected':'' ?>>Salaried</option><option value="business" <?= $data['employee_type']=='business'?'selected':'' ?>>Business</option></select></div>
                                    <div class="col-md-6"><label class="form-label">Company</label><input type="text" name="company_name" class="form-control" value="<?= $data['company_name'] ?>"></div>
                                    <div class="col-md-6"><label class="form-label">Income</label><input type="number" name="monthly_income" class="form-control" value="<?= $data['monthly_income'] ?>"></div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-address">
                            <div class="card-modern p-4 p-md-5">
                                <div class="row g-4 mb-4">
                                    <div class="col-md-4"><label class="form-label">City</label><input type="text" name="city" class="form-control" value="<?= $data['city'] ?>"></div>
                                    <div class="col-md-4"><label class="form-label">State</label><input type="text" name="state" class="form-control" value="<?= $data['state'] ?>"></div>
                                    <div class="col-md-4"><label class="form-label">Pin Code</label><input type="text" name="pin_code" id="pin_code" class="form-control" value="<?= $data['pin_code'] ?>" maxlength="6"></div>
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-6"><label class="form-label">Ref 1 Name</label><input type="text" name="reference1_name" class="form-control mb-2" value="<?= $data['reference1_name'] ?>"><label class="form-label">Ref 1 Phone</label><input type="text" name="reference1_phone" class="form-control" value="<?= $data['reference1_phone'] ?>" maxlength="10"></div>
                                    <div class="col-md-6"><label class="form-label">Ref 2 Name</label><input type="text" name="reference2_name" class="form-control mb-2" value="<?= $data['reference2_name'] ?>"><label class="form-label">Ref 2 Phone</label><input type="text" name="reference2_phone" class="form-control" value="<?= $data['reference2_phone'] ?>" maxlength="10"></div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-security">
                            <div class="card-modern p-4 p-md-5">
                                <div class="row g-4">
                                    <div class="col-md-4"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-control" placeholder="Required to change"></div>
                                    <div class="col-md-4"><label class="form-label">New Password</label><input type="password" name="new_password" id="new_password" class="form-control" placeholder="Min 8 chars"></div>
                                    <div class="col-md-4"><label class="form-label">Confirm Password</label><input type="password" id="confirm_password" class="form-control"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-dark px-5 py-2 fw-bold" style="border-radius: 12px;"><i class="fas fa-save me-2"></i> Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="statusToast" class="toast align-items-center border-0 text-white" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex"><div class="toast-body"><i id="toastIcon" class="fas me-2"></i><span id="toastMessage"></span></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>
    </div>
</div>

<?php include 'footer.php'; ?>
<script src="assets/js/validate_profile.js"></script>
<script>
    // Toast Trigger
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const msg = urlParams.get('msg');
        const err = urlParams.get('err');
        if (msg || err) {
            const toastEl = document.getElementById('statusToast');
            toastEl.classList.add(msg ? 'bg-success' : 'bg-danger');
            document.getElementById('toastIcon').classList.add(msg ? 'fa-check-circle' : 'fa-times-circle');
            document.getElementById('toastMessage').innerText = msg || err;
            new bootstrap.Toast(toastEl).show();
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>
