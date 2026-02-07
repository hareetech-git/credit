<?php
include 'db/config.php';
include 'header.php';
// Professional icons and typography
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">';
include 'topbar.php';
include 'sidebar.php';

if (!hasAccess($conn, 'loan_view')) {
    header('Location: dashboard.php?err=Access denied');
    exit();
}

$loan_id = (int)$_GET['id'];
if (!$loan_id) die("Invalid Loan ID");

$staff_id = (int)$_SESSION['staff_id'];
$can_process = hasAccess($conn, 'loan_process');
$can_delete = hasAccess($conn, 'loan_delete');

$loan_sql = "SELECT l.*, c.full_name, c.email, c.phone, s.service_name, st.name AS staff_name
             FROM loan_applications l
             JOIN customers c ON l.customer_id = c.id
             JOIN services s ON l.service_id = s.id
             LEFT JOIN staff st ON l.assigned_staff_id = st.id
             WHERE l.id = $loan_id AND l.assigned_staff_id = $staff_id";
$loan = mysqli_fetch_assoc(mysqli_query($conn, $loan_sql));

if (!$loan) {
    header('Location: loan_applications.php?err=Loan not assigned to you');
    exit();
}
$loan_interest_type = strtolower((string)($loan['interest_type'] ?? 'year'));
if ($loan_interest_type !== 'month') {
    $loan_interest_type = 'year';
}

$docs_res = mysqli_query($conn, "SELECT * FROM loan_application_docs WHERE loan_application_id = $loan_id");
?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-800: #1e293b;
        --slate-500: #64748b;
        --slate-200: #e2e8f0;
        --blue-600: #2563eb;
        --blue-50: #eff6ff;
    }
    .content-page { background-color: #f8fafc; padding-bottom: 50px; }
    
    /* Summary Hero Card */
    .loan-hero {
        background: linear-gradient(135deg, var(--slate-900) 0%, #1e293b 100%);
        color: white;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 16px;
        background: #ffffff;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .card-title-sm {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--slate-500);
        margin-bottom: 15px;
        display: block;
    }

    .form-control, .form-select {
        border: 1px solid var(--slate-200);
        padding: 12px;
        font-size: 0.9rem;
        border-radius: 10px;
        transition: 0.2s;
    }
    .form-control:focus { border-color: var(--blue-600); box-shadow: 0 0 0 4px var(--blue-50); }

    /* Verification Rows */
    .doc-item {
        padding: 16px;
        border-bottom: 1px solid var(--slate-200);
        transition: 0.2s;
    }
    .doc-item:last-child { border-bottom: none; }
    .doc-item:hover { background: #fdfdfd; }

    .btn-slate { background: var(--slate-900); color: white; border-radius: 10px; font-weight: 600; padding: 10px 20px; }
    .btn-slate:hover { background: #000; color: white; }

    .status-pill {
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.05em;
    }
</style>

<div class="content-page">
    <div class="container-fluid pt-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-0">Underwriting Workflow</h3>
                <span class="text-muted">Agent: <?= htmlspecialchars($loan['staff_name']) ?></span>
            </div>
            <a href="loan_applications.php" class="btn btn-white border rounded-pill px-4 btn-sm shadow-sm fw-bold">
                <i class="fas fa-arrow-left me-2"></i>Exit to Queue
            </a>
        </div>

        <div class="loan-hero">
            <div class="row align-items-center">
                <div class="col-md-6 border-end border-secondary">
                    <span class="opacity-75 small">Applicant Profile</span>
                    <h2 class="fw-bold mb-1"><?= htmlspecialchars($loan['full_name']) ?></h2>
                    <p class="mb-0 opacity-75"><i class="fas fa-envelope me-2"></i><?= $loan['email'] ?> | <i class="fas fa-phone me-2"></i><?= $loan['phone'] ?></p>
                </div>
                <div class="col-md-6 ps-md-5">
                    <div class="row text-center">
                        <div class="col-4">
                            <span class="opacity-75 x-small d-block text-uppercase">Amount</span>
                            <span class="h4 fw-bold mb-0">₹<?= number_format($loan['requested_amount']) ?></span>
                        </div>
                        <div class="col-4">
                            <span class="opacity-75 x-small d-block text-uppercase">Tenure</span>
                            <span class="h4 fw-bold mb-0"><?= (int)$loan['tenure_years'] ?>M</span>
                        </div>
                        <div class="col-4">
                            <span class="opacity-75 x-small d-block text-uppercase">Interest</span>
                            <span class="h4 fw-bold mb-0"><?= number_format((float)$loan['interest_rate'], 2) ?>% (<?= strtoupper($loan_interest_type) ?>)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card-modern p-4">
                    <span class="card-title-sm">Officer Decision Terminal</span>
                    
                    <?php if ($can_process): ?>
                        <form action="db/loan_handler.php" method="POST">
                            <input type="hidden" name="loan_id" value="<?= $loan_id ?>">
                            <input type="hidden" name="action" value="update_loan_status">
                            
                            <div class="mb-4">
                                <label class="form-label">Application Status</label>
                                <select name="status" class="form-select bg-light fw-bold" required>
                                    <option value="pending" <?= $loan['status']=='pending'?'selected':'' ?>>Under Review</option>
                                    <option value="approved" <?= $loan['status']=='approved'?'selected':'' ?>>Approve Loan</option>
                                    <option value="rejected" <?= $loan['status']=='rejected'?'selected':'' ?>>Reject Application</option>
                                    <option value="disbursed" <?= $loan['status']=='disbursed'?'selected':'' ?>>Funds Disbursed</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Approved Amount (Rs)</label>
                                <input type="number" min="0" step="0.01" name="requested_amount" id="staff_requested_amount" class="form-control" value="<?= htmlspecialchars($loan['requested_amount']) ?>" required>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label class="form-label">Tenure (Months)</label>
                                    <input type="number" min="1" name="tenure_months" id="staff_tenure_months" class="form-control" value="<?= (int)$loan['tenure_years'] ?>" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">EMI (Rs)</label>
                                    <input type="number" min="0" step="0.01" name="emi_amount" id="staff_emi_amount" class="form-control" value="<?= htmlspecialchars($loan['emi_amount']) ?>" readonly>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label class="form-label">Interest Rate</label>
                                    <input type="number" min="0" step="0.01" name="interest_rate" id="staff_interest_rate" class="form-control" value="<?= htmlspecialchars($loan['interest_rate']) ?>" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Interest Type</label>
                                    <select name="interest_type" id="staff_interest_type" class="form-select" required>
                                        <option value="year" <?= $loan_interest_type === 'year' ? 'selected' : '' ?>>Yearly</option>
                                        <option value="month" <?= $loan_interest_type === 'month' ? 'selected' : '' ?>>Monthly</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Internal & Customer Note</label>
                                <textarea name="note" class="form-control" rows="4" placeholder="Detail the basis for this decision..."><?= $loan['rejection_note'] ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-slate w-100 shadow-lg py-3">Commit Decision</button>
                        </form>
                    <?php endif; ?>

                    <?php if ($can_delete): ?>
                        <form action="db/loan_handler.php" method="POST" class="mt-4" onsubmit="return confirm('Archive this request?');">
                            <input type="hidden" name="action" value="delete_loan">
                            <input type="hidden" name="loan_id" value="<?= $loan_id ?>">
                            <button type="submit" class="btn btn-link text-danger w-100 fw-bold text-decoration-none small">Archive Application</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card-modern">
                    <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center">
                        <span class="card-title-sm mb-0">Verification Evidence</span>
                        <span class="badge bg-dark rounded-pill px-3"><?= mysqli_num_rows($docs_res) ?> Files</span>
                    </div>
                    
                    <div class="card-body p-0">
                        <?php while($doc = mysqli_fetch_assoc($docs_res)): ?>
                            <div class="doc-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($doc['doc_name']) ?></h6>
                                    <div class="d-flex align-items-center gap-3">
                                        <a href="../<?= $doc['doc_path'] ?>" target="_blank" class="text-blue-600 small fw-bold"><i class="fas fa-eye me-1"></i> Preview Asset</a>
                                        <?php if($doc['status'] == 'verified'): ?>
                                            <span class="status-pill bg-success text-white">VERIFIED</span>
                                        <?php elseif($doc['status'] == 'rejected'): ?>
                                            <span class="status-pill bg-danger text-white">REJECTED</span>
                                        <?php else: ?>
                                            <span class="status-pill bg-warning text-dark">PENDING</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-white border rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#docModal<?= $doc['id'] ?>">Verify</button>

                                <div class="modal fade" id="docModal<?= $doc['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="db/loan_handler.php" method="POST" class="modal-content border-0 shadow-lg" style="border-radius:20px;">
                                            <input type="hidden" name="action" value="verify_doc">
                                            <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                                            <input type="hidden" name="loan_id" value="<?= $loan_id ?>">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="fw-800">Verify Asset</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body py-4">
                                                <label class="form-label">Review Status</label>
                                                <select name="status" class="form-select mb-4">
                                                    <option value="verified" <?= $doc['status']=='verified'?'selected':'' ?>>Verify & Approve</option>
                                                    <option value="rejected" <?= $doc['status']=='rejected'?'selected':'' ?>>Flag/Reject Asset</option>
                                                </select>
                                                <label class="form-label">Reasoning (Optional)</label>
                                                <textarea name="reason" class="form-control" rows="3"><?= $doc['rejection_reason'] ?></textarea>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="submit" class="btn btn-slate w-100 py-3">Submit Review</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
(function () {
    var amountEl = document.getElementById('staff_requested_amount');
    var tenureEl = document.getElementById('staff_tenure_months');
    var rateEl = document.getElementById('staff_interest_rate');
    var typeEl = document.getElementById('staff_interest_type');
    var emiEl = document.getElementById('staff_emi_amount');

    if (!amountEl || !tenureEl || !rateEl || !typeEl || !emiEl) return;

    function calcEmi() {
        var p = parseFloat(amountEl.value) || 0;
        var n = parseInt(tenureEl.value, 10) || 0;
        var r = parseFloat(rateEl.value) || 0;
        var type = typeEl.value === 'month' ? 'month' : 'year';

        if (p <= 0 || n <= 0) {
            emiEl.value = '';
            return;
        }

        var monthlyRate = type === 'year' ? (r / 1200) : (r / 100);
        var emi = 0;

        if (monthlyRate <= 0) {
            emi = p / n;
        } else {
            var factor = Math.pow(1 + monthlyRate, n);
            emi = (p * monthlyRate * factor) / (factor - 1);
        }

        emiEl.value = isFinite(emi) ? emi.toFixed(2) : '';
    }

    amountEl.addEventListener('input', calcEmi);
    tenureEl.addEventListener('input', calcEmi);
    rateEl.addEventListener('input', calcEmi);
    typeEl.addEventListener('change', calcEmi);
    calcEmi();
})();
</script>

<?php include 'footer.php'; ?>
