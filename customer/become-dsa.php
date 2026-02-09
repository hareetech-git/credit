<?php
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

$customer_id = (int)($_SESSION['customer_id'] ?? 0);
$tableReady = false;
$tblRes = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_requests'");
if ($tblRes && mysqli_num_rows($tblRes) > 0) {
    $tableReady = true;
}

$customer = null;
$customerRes = mysqli_query($conn, "SELECT c.full_name, c.email, c.phone, cp.city, cp.state, cp.pin_code, cp.pan_number
                                  FROM customers c
                                  LEFT JOIN customer_profiles cp ON cp.customer_id = c.id
                                  WHERE c.id = $customer_id
                                  LIMIT 1");
if ($customerRes && mysqli_num_rows($customerRes) > 0) {
    $customer = mysqli_fetch_assoc($customerRes);
}

$latestRequest = null;
if ($tableReady) {
    $requestRes = mysqli_query($conn, "SELECT * FROM dsa_requests WHERE customer_id = $customer_id ORDER BY id DESC LIMIT 1");
    if ($requestRes && mysqli_num_rows($requestRes) > 0) {
        $latestRequest = mysqli_fetch_assoc($requestRes);
    }
}

$status = strtolower((string)($latestRequest['status'] ?? ''));
$canSubmit = ($status !== 'pending');
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Become a DSA Agent</h2>
                    <p class="text-muted small mb-0">Submit your details for admin verification. After approval, credentials will be emailed to you.</p>
                </div>
            </div>

            <?php if (!empty($_GET['msg'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['err'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['err']) ?></div>
            <?php endif; ?>
            <?php if (!$tableReady): ?>
                <div class="alert alert-warning">DSA request system is not ready. Please ask admin to run database migration (`dsa_migration.sql`).</div>
            <?php endif; ?>

            <?php if ($latestRequest): ?>
                <div class="card border mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Latest Request: DSA-<?= (int)$latestRequest['id'] ?></h5>
                        <p class="mb-1"><strong>Status:</strong>
                            <?php if ($status === 'approved'): ?>
                                <span class="badge bg-success-subtle text-success">Approved</span>
                            <?php elseif ($status === 'rejected'): ?>
                                <span class="badge bg-danger-subtle text-danger">Rejected</span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-dark">Pending Verification</span>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($latestRequest['admin_note'])): ?>
                            <p class="mb-1"><strong>Admin Note:</strong> <?= nl2br(htmlspecialchars($latestRequest['admin_note'])) ?></p>
                        <?php endif; ?>
                        <?php if ($status === 'approved'): ?>
                            <a href="../dsa/index.php" class="btn btn-dark btn-sm mt-2">Go to DSA Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card border">
                <div class="card-body">
                    <form action="db/dsa_request_handler.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control" required value="<?= htmlspecialchars($customer['full_name'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($customer['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" maxlength="15" required value="<?= htmlspecialchars($customer['phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Firm Name</label>
                                <input type="text" name="firm_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">PAN Number</label>
                                <input type="text" name="pan_number" class="form-control" maxlength="10" value="<?= htmlspecialchars($customer['pan_number'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($customer['city'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($customer['state'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pin Code</label>
                                <input type="text" name="pin_code" class="form-control" maxlength="10" value="<?= htmlspecialchars($customer['pin_code'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Account Number</label>
                                <input type="text" name="account_number" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">IFSC Code</label>
                                <input type="text" name="ifsc_code" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message (Optional)</label>
                                <textarea name="message" class="form-control" rows="3" placeholder="Any additional details for admin verification"></textarea>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-dark" <?= ($canSubmit && $tableReady) ? '' : 'disabled' ?>>Submit Request</button>
                        </div>
                        <?php if (!$canSubmit): ?>
                            <div class="text-muted small mt-2">You already have a pending request. Please wait for admin verification.</div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
