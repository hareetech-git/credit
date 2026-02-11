<?php
include 'db/config.php';
include 'header.php';
// Ensure FontAwesome is loaded
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">';
include 'topbar.php';
include 'sidebar.php';

$id = $_GET['id'] ?? 0;

$id = $_GET['id'] ?? 0;

if ($id) {
    // If there is an ID, it's an EDIT action
    if (!hasAccess($conn, 'cust_update')) {
        echo "<script>alert('Access Denied: You cannot edit customers'); window.location='dashboard.php';</script>";
        exit;
    }
} else {
    // If there is no ID, it's a CREATE action
    if (!hasAccess($conn, 'cust_create')) {
        echo "<script>alert('Access Denied: You cannot add customers'); window.location='dashboard.php';</script>";
        exit;
    }
}
$customer = [];
$profile = [];

if ($id) {
    $id = (int)$id;
    $c_res = mysqli_query($conn, "SELECT * FROM customers WHERE id=$id");
    $customer = mysqli_fetch_assoc($c_res);
    $p_res = mysqli_query($conn, "SELECT * FROM customer_profiles WHERE customer_id=$id");
    $profile = mysqli_fetch_assoc($p_res);
    if (!empty($profile['pan_number'])) {
        $profile['pan_number'] = uc_decrypt_sensitive($profile['pan_number']);
    }
}
?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
        --blue-500: #3b82f6;
    }
    .content-page { background-color: #fcfcfd; }
    
    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        margin-bottom: 24px;
    }

    .card-modern .card-header {
        background: #f8fafc;
        border-bottom: 1px solid var(--slate-200);
        padding: 16px 24px;
        font-weight: 700;
        color: var(--slate-900);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .form-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--slate-600);
        text-transform: uppercase;
        letter-spacing: 0.025em;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border: 1px solid var(--slate-200);
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.9rem;
        color: var(--slate-900);
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--slate-900);
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.05);
        outline: none;
    }

    .btn-save {
        background: var(--slate-900);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 12px 40px;
        font-weight: 600;
        transition: opacity 0.2s;
    }
    .btn-save:hover { opacity: 0.9; background: var(--slate-600); }

    .input-group-text {
        background: #f8fafc;
        border: 1px solid var(--slate-200);
        color: var(--slate-600);
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid ">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1"><?= $id ? 'Edit Customer' : 'Create Customer' ?></h2>
                    <p class="text-muted small mb-0">Fill in the primary and KYC details for the borrower profile.</p>
                </div>
                <a href="customers.php" class="btn btn-outline-secondary px-4 fw-bold" style="border-radius: 10px;">
                    <i class="fas fa-arrow-left me-2"></i> Back to List
                </a>
            </div>

            <form action="db/insert/customer_insert_update.php" method="POST">
                <input type="hidden" name="action" value="<?= $id ? 'update' : 'create' ?>">
                <input type="hidden" name="customer_id" value="<?= $id ?>">

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card card-modern h-100">
                            <div class="card-header">
                                <i class="fas fa-user-circle me-2"></i> Account Information
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control" placeholder="John Doe" value="<?= $customer['full_name'] ?? '' ?>" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="email" class="form-control" placeholder="Enter Your email" value="<?= $customer['email'] ?? '' ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" name="phone" class="form-control" placeholder="Enter Your Mobile Number" value="<?= $customer['phone'] ?? '' ?>" required>
                                    </div>
                                </div>

                                <?php if (!$id) { ?>
                                    <div class="mb-3">
                                        <label class="form-label">Temporary Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="mb-0">
                                    <label class="form-label">Account Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active" <?= ($customer['status'] ?? '') == 'active' ? 'selected' : '' ?>>Active (Enabled)</option>
                                        <option value="blocked" <?= ($customer['status'] ?? '') == 'blocked' ? 'selected' : '' ?>>Blocked (Disabled)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card card-modern h-100">
                            <div class="card-header">
                                <i class="fas fa-id-card me-2"></i> KYC & Financial Data
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">PAN Number</label>
                                        <input type="text" name="pan_number" class="form-control" placeholder="ABCDE1234F" value="<?= $profile['pan_number'] ?? '' ?>" style="text-transform: uppercase;">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date of Birth</label>
                                        <input type="date" name="birth_date" class="form-control" value="<?= $profile['birth_date'] ?? '' ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" name="city" class="form-control" value="<?= $profile['city'] ?? '' ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">State</label>
                                        <input type="text" name="state" class="form-control" value="<?= $profile['state'] ?? '' ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Pin Code</label>
                                        <input type="text" name="pin_code" class="form-control" value="<?= $profile['pin_code'] ?? '' ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Employment Type</label>
                                    <select name="employee_type" class="form-select">
                                        <option value="">-- Choose Type --</option>
                                        <option value="salaried" <?= ($profile['employee_type'] ?? '') == 'salaried' ? 'selected' : '' ?>>Salaried Professional</option>
                                        <option value="self_employed" <?= ($profile['employee_type'] ?? '') == 'self_employed' ? 'selected' : '' ?>>Self Employed / Business</option>
                                    </select>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label">Monthly Net Income (₹)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" name="monthly_income" class="form-control" placeholder="Enter amount" value="<?= $profile['monthly_income'] ?? '' ?>">
                                    </div>
                                    <small class="text-muted mt-1 d-block" style="font-size: 0.7rem;">Provide the average monthly earnings after taxes.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end align-items-center gap-3 mt-4 mb-5">
                    <button type="reset" class="btn btn-link text-muted text-decoration-none fw-bold small">Reset Form</button>
                    <button type="submit" class="btn btn-save shadow-sm">
                        <i class="fas fa-check-circle me-2"></i> <?= $id ? 'Update Customer Record' : 'Save New Customer' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
