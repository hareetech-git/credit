<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'header.php';
dsaRequireAccess($conn, 'dsa_profile_manage');
include 'topbar.php';
include 'sidebar.php';

$dsa_id = (int)($_SESSION['dsa_id'] ?? 0);
$query = "SELECT d.*, dp.firm_name, dp.pan_number, dp.city, dp.state, dp.pin_code, dp.bank_name, dp.account_number, dp.ifsc_code
          FROM dsa d
          LEFT JOIN dsa_profiles dp ON dp.dsa_id = d.id
          WHERE d.id = $dsa_id
          LIMIT 1";
$res = mysqli_query($conn, $query);
$data = $res ? mysqli_fetch_assoc($res) : null;

if ($data) {
    $data['pan_number'] = uc_decrypt_sensitive((string)($data['pan_number'] ?? ''));
    $data['account_number'] = uc_decrypt_sensitive((string)($data['account_number'] ?? ''));
}
?>
<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
    }
    .content-page { background-color: #f8fafc; min-height: 100vh; }
    .page-hero {
        background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
        border: 1px solid var(--slate-200);
        border-radius: 16px;
        padding: 18px 22px;
        margin-bottom: 20px;
    }
    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        overflow: hidden;
    }
    .card-modern .card-header {
        border-bottom: 1px solid var(--slate-200);
        background: #f8fafc;
    }
    .form-label {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.06em;
        color: var(--slate-600);
    }
    .form-control {
        border: 1px solid #dbe2ea;
        border-radius: 10px;
        min-height: 42px;
    }
    .form-control:focus {
        border-color: var(--slate-900);
        box-shadow: 0 0 0 0.18rem rgba(15, 23, 42, 0.08);
    }
    .readonly-field {
        background-color: #f1f5f9 !important;
        color: #64748b !important;
    }
    .badge-lock {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 0.72rem;
        font-weight: 700;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            <div class="page-hero d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-dark mb-1">My Profile</h2>
                    <p class="text-muted small mb-0">Manage your DSA profile and bank details.</p>
                </div>
                <span class="badge-lock"><i class="fas fa-lock me-1"></i> Email is locked</span>
            </div>

            <?php if (!empty($_GET['msg'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars((string)$_GET['msg']) ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['err'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars((string)$_GET['err']) ?></div>
            <?php endif; ?>

            <?php if ($data): ?>
            <form action="db/update_profile.php" method="POST" class="row g-4">
                <div class="col-lg-5">
                    <div class="card card-modern h-100">
                        <div class="card-header py-3 px-4">
                            <h6 class="mb-0 fw-bold">Account Details</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($data['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email (Not Editable)</label>
                                <input type="text" class="form-control readonly-field" value="<?= htmlspecialchars($data['email']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($data['phone']) ?>" maxlength="10" pattern="[6-9]{1}[0-9]{9}" title="Enter valid 10-digit mobile number starting with 6-9" required>
                            </div>
                            <div>
                                <label class="form-label">Firm Name</label>
                                <input type="text" name="firm_name" class="form-control" value="<?= htmlspecialchars($data['firm_name'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card card-modern mb-4">
                        <div class="card-header py-3 px-4">
                            <h6 class="mb-0 fw-bold">KYC Details</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">PAN Number</label>
                                    <input type="text" class="form-control readonly-field" maxlength="10" style="text-transform: uppercase;" value="<?= htmlspecialchars($data['pan_number'] ?? '') ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pin Code</label>
                                    <input type="text" class="form-control readonly-field" maxlength="10" value="<?= htmlspecialchars($data['pin_code'] ?? '') ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control readonly-field" value="<?= htmlspecialchars($data['city'] ?? '') ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">State</label>
                                    <input type="text" class="form-control readonly-field" value="<?= htmlspecialchars($data['state'] ?? '') ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-modern">
                        <div class="card-header py-3 px-4">
                            <h6 class="mb-0 fw-bold">Bank Details</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Bank Name</label>
                                    <input type="text" class="form-control readonly-field" value="<?= htmlspecialchars($data['bank_name'] ?? '') ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">IFSC Code</label>
                                    <input type="text" class="form-control readonly-field" maxlength="20" style="text-transform: uppercase;" value="<?= htmlspecialchars($data['ifsc_code'] ?? '') ?>" readonly>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Account Number</label>
                                    <input type="text" class="form-control readonly-field" value="<?= htmlspecialchars($data['account_number'] ?? '') ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-dark px-4">Update Profile</button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
