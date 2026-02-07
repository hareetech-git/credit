<?php
include 'db/config.php';
include 'header.php';

// Ensure FontAwesome is loaded
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">';
include 'topbar.php';
include 'sidebar.php';

$loan_id = (int)$_GET['id'];
if (!$loan_id) die("Invalid Loan ID");

// Staff list for reassignment
$staff_list = [];
$staff_res = mysqli_query($conn, "SELECT id, name FROM staff WHERE status='active' ORDER BY name");
while ($s = mysqli_fetch_assoc($staff_res)) {
    $staff_list[] = $s;
}

// Fetch Loan Details
$loan_sql = "SELECT l.*, c.full_name, c.email, c.phone, s.service_name, st.name AS staff_name
             FROM loan_applications l
             JOIN customers c ON l.customer_id = c.id
             JOIN services s ON l.service_id = s.id
             LEFT JOIN staff st ON l.assigned_staff_id = st.id
             WHERE l.id = $loan_id";
$loan = mysqli_fetch_assoc(mysqli_query($conn, $loan_sql));

if (!$loan) die("Loan not found.");
$loan_interest_type = strtolower((string)($loan['interest_type'] ?? 'year'));
if ($loan_interest_type !== 'month') {
    $loan_interest_type = 'year';
}

// Fetch Documents
$docs_res = mysqli_query($conn, "SELECT * FROM loan_application_docs WHERE loan_application_id = $loan_id");
?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-700: #334155;
        --slate-200: #e2e8f0;
        --blue-500: #3b82f6;
    }
    .content-page { background-color: #fcfcfd; }
    
    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .card-header-slate {
        background: var(--slate-900);
        color: white;
        padding: 12px 20px;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .form-label { font-weight: 700; text-transform: uppercase; font-size: 0.7rem; color: var(--slate-600); }
    .form-control, .form-select { border-radius: 8px; border: 1px solid var(--slate-200); }
    
    .data-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f8fafc; }
    .data-label { color: var(--slate-600); font-size: 0.85rem; }
    .data-value { font-weight: 700; color: var(--slate-900); font-size: 0.85rem; }

    .badge-status { padding: 5px 10px; border-radius: 6px; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; }

    .table-modern thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--slate-600);
        padding: 12px 20px;
        border: none;
    }
    .table-modern tbody td { padding: 12px 20px; vertical-align: middle; border-bottom: 1px solid var(--slate-200); }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-0">Loan Processing Desk</h2>
                    <p class="text-muted small">Application Reference: <span class="fw-bold text-primary">#L-<?= $loan['id'] ?></span></p>
                </div>
                <a href="loan_applications.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Back to Queue
                </a>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card-modern">
                        <div class="card-header-slate">Application Overview</div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($loan['full_name']) ?></h5>
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($loan['service_name']) ?></span>
                            </div>

                            <div class="data-item"><span class="data-label">Requested Amount</span><span class="data-value">₹<?= number_format($loan['requested_amount']) ?></span></div>
                            <div class="data-item"><span class="data-label">Tenure</span><span class="data-value"><?= (int)$loan['tenure_years'] ?> Months</span></div>
                            <div class="data-item"><span class="data-label">Interest Rate</span><span class="data-value"><?= number_format((float)$loan['interest_rate'], 2) ?>% (<?= strtoupper($loan_interest_type) ?>)</span></div>
                            <div class="data-item">
                                <span class="data-label">Current Status</span>
                                <span class="badge-status bg-dark text-white"><?= strtoupper($loan['status']) ?></span>
                            </div>
                            <div class="data-item">
                                <span class="data-label">Assigned Agent</span>
                                <span class="data-value text-primary"><?= $loan['staff_name'] ? htmlspecialchars($loan['staff_name']) : 'Not Assigned' ?></span>
                            </div>

                            <hr class="my-4">

                            <form action="db/loan_handler.php" method="POST" class="bg-light p-3 rounded-3 border">
                                <input type="hidden" name="loan_id" value="<?= $loan_id ?>">
                                <input type="hidden" name="action" value="update_loan_status">
                                
                                <div class="mb-3">
                                    <label class="form-label">Update Application Decision</label>
                                    <select name="status" class="form-select form-select-sm" required>
                                        <option value="pending" <?= $loan['status']=='pending'?'selected':'' ?>>Pending / Under Review</option>
                                        <option value="approved" <?= $loan['status']=='approved'?'selected':'' ?>>Approve Loan</option>
                                        <option value="rejected" <?= $loan['status']=='rejected'?'selected':'' ?>>Reject Loan</option>
                                        <option value="disbursed" <?= $loan['status']=='disbursed'?'selected':'' ?>>Mark as Disbursed</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Approved Amount (Rs)</label>
                                    <input type="number" min="0" step="0.01" name="requested_amount" id="admin_requested_amount" class="form-control form-control-sm" value="<?= htmlspecialchars($loan['requested_amount']) ?>" required>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label">Tenure (Months)</label>
                                        <input type="number" min="1" name="tenure_months" id="admin_tenure_months" class="form-control form-control-sm" value="<?= (int)$loan['tenure_years'] ?>" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">EMI (Rs)</label>
                                        <input type="number" min="0" step="0.01" name="emi_amount" id="admin_emi_amount" class="form-control form-control-sm" value="<?= htmlspecialchars($loan['emi_amount']) ?>" readonly>
                                    </div>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label">Interest Rate</label>
                                        <input type="number" min="0" step="0.01" name="interest_rate" id="admin_interest_rate" class="form-control form-control-sm" value="<?= htmlspecialchars($loan['interest_rate']) ?>" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Interest Type</label>
                                        <select name="interest_type" id="admin_interest_type" class="form-select form-select-sm" required>
                                            <option value="year" <?= $loan_interest_type === 'year' ? 'selected' : '' ?>>Yearly</option>
                                            <option value="month" <?= $loan_interest_type === 'month' ? 'selected' : '' ?>>Monthly</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Note for Customer</label>
                                    <textarea name="note" class="form-control form-control-sm" rows="2" placeholder="Detail reason for decision..."><?= $loan['rejection_note'] ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-dark btn-sm w-100 fw-bold">Update Application</button>
                            </form>

                            <hr>
                            <form action="db/loan_handler.php" method="POST">
                                <input type="hidden" name="action" value="assign_staff">
                                <input type="hidden" name="loan_id" value="<?= $loan_id ?>">
                                <label class="form-label">Reassign Application</label>
                                <div class="input-group">
                                    <select name="staff_id" class="form-select form-select-sm">
                                        <option value="0">Unassigned</option>
                                        <?php foreach ($staff_list as $staff) { ?>
                                            <option value="<?= $staff['id'] ?>" <?= ($loan['assigned_staff_id'] == $staff['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($staff['name']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <button class="btn btn-outline-dark btn-sm">Assign</button>
                                </div>
                            </form>

                            <form action="db/loan_handler.php" method="POST" class="mt-3 text-center" onsubmit="return confirm('Permanently delete this application?');">
                                <input type="hidden" name="action" value="delete_loan">
                                <input type="hidden" name="loan_id" value="<?= $loan_id ?>">
                                <button type="submit" class="btn btn-link text-danger btn-sm text-decoration-none fw-bold">Delete Application</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card-modern">
                        <div class="card-header-slate d-flex justify-content-between align-items-center">
                            <span>Document Verification Matrix</span>
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <div class="card-body">
                            <form action="db/loan_handler.php" method="POST" enctype="multipart/form-data" class="mb-4 p-3 border rounded-3 bg-light">
                                <input type="hidden" name="action" value="upload_doc">
                                <input type="hidden" name="loan_id" value="<?= $loan_id ?>">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label">Manual Doc Upload</label>
                                        <input type="text" name="doc_name" class="form-control form-control-sm" placeholder="Document Name" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="file" name="doc_file" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold" style="background: var(--blue-500); border:none;">Upload</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-modern mb-0">
                                    <thead>
                                        <tr>
                                            <th>Document Identity</th>
                                            <th>Current Status</th>
                                            <th class="text-end">Verification</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($doc = mysqli_fetch_assoc($docs_res)) { ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-dark"><?= htmlspecialchars($doc['doc_name']) ?></div>
                                                    <a href="../<?= $doc['doc_path'] ?>" target="_blank" class="text-primary small fw-bold">
                                                        <i class="fas fa-external-link-alt me-1"></i> Open File
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php 
                                                        if($doc['status'] == 'verified') echo '<span class="badge bg-success-subtle text-success border border-success px-2 py-1" style="font-size:10px;">VERIFIED</span>'; 
                                                        elseif($doc['status'] == 'rejected') echo '<span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1" style="font-size:10px;">REJECTED</span>'; 
                                                        else echo '<span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1" style="font-size:10px;">PENDING</span>'; 
                                                    ?>
                                                </td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#docModal<?= $doc['id'] ?>">
                                                        Process Doc
                                                    </button>

                                                    <div class="modal fade text-start" id="docModal<?= $doc['id'] ?>" tabindex="-1">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <form action="db/loan_handler.php" method="POST" class="modal-content border-0 shadow-lg">
                                                                <input type="hidden" name="action" value="verify_doc">
                                                                <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                                                                <input type="hidden" name="loan_id" value="<?= $loan_id ?>">
                                                                <div class="modal-header bg-light">
                                                                    <h6 class="modal-title fw-bold">Verify: <?= $doc['doc_name'] ?></h6>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body py-4">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Review Result</label>
                                                                        <select name="status" class="form-select">
                                                                            <option value="verified" <?= $doc['status']=='verified'?'selected':'' ?>>Approve Document</option>
                                                                            <option value="rejected" <?= $doc['status']=='rejected'?'selected':'' ?>>Reject / Request Re-upload</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-0">
                                                                        <label class="form-label">Remarks for Applicant</label>
                                                                        <textarea name="reason" class="form-control" rows="3" placeholder="Explain rejection or discrepancies found..."><?= $doc['rejection_reason'] ?></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer border-0">
                                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-dark px-4">Submit Decision</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    var amountEl = document.getElementById('admin_requested_amount');
    var tenureEl = document.getElementById('admin_tenure_months');
    var rateEl = document.getElementById('admin_interest_rate');
    var typeEl = document.getElementById('admin_interest_type');
    var emiEl = document.getElementById('admin_emi_amount');

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
