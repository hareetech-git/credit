<?php
include 'db/config.php';
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

$loan_id = (int)$_GET['id'];
if (!$loan_id) die("Invalid Loan ID");

// Staff list
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

if (!$loan) {
    die("Loan not found.");
}

// Fetch Documents
$docs_res = mysqli_query($conn, "SELECT * FROM loan_application_docs WHERE loan_application_id = $loan_id");
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">Application #L-<?= $loan['id'] ?></div>
                        <div class="card-body">
                            <h5 class="text-center"><?= htmlspecialchars($loan['full_name']) ?></h5>
                            <p class="text-center text-muted"><?= htmlspecialchars($loan['service_name']) ?></p>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Requested:</span> <strong>&#8377;<?= number_format($loan['requested_amount']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Current Tenure:</span> <strong><?= $loan['tenure_years'] ?> Years</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Status:</span> <span class="badge bg-secondary"><?= strtoupper($loan['status']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Interest Rate (p.a.):</span> <strong><?= number_format((float)$loan['interest_rate'], 2) ?>%</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Assigned To:</span>
                                <strong><?= $loan['staff_name'] ? htmlspecialchars($loan['staff_name']) : 'Unassigned' ?></strong>
                            </div>
                            
                            <hr>
                            <form action="db/loan_handler.php" method="POST" class="mb-3">
                                <input type="hidden" name="action" value="assign_staff">
                                <input type="hidden" name="loan_id" value="<?= $loan_id ?>">
                                <label class="form-label fw-bold">Assign Staff</label>
                                <div class="d-flex gap-2">
                                    <select name="staff_id" class="form-select">
                                        <option value="0">Unassigned</option>
                                        <?php foreach ($staff_list as $staff) { ?>
                                            <option value="<?= $staff['id'] ?>" <?= ($loan['assigned_staff_id'] == $staff['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($staff['name']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <button class="btn btn-outline-primary">Assign</button>
                                </div>
                            </form>

                            <form action="db/loan_handler.php" method="POST">
                                <input type="hidden" name="loan_id" value="<?= $loan_id ?>">
                                <input type="hidden" name="action" value="update_loan_status">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Update Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="pending" <?= $loan['status']=='pending'?'selected':'' ?>>Pending</option>
                                        <option value="approved" <?= $loan['status']=='approved'?'selected':'' ?>>Approved</option>
                                        <option value="rejected" <?= $loan['status']=='rejected'?'selected':'' ?>>Rejected</option>
                                        <option value="disbursed" <?= $loan['status']=='disbursed'?'selected':'' ?>>Disbursed</option>
                                    </select>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label class="form-label fw-bold small">Final Tenure (Yrs)</label>
                                        <input type="number" name="tenure_years" class="form-control" value="<?= $loan['tenure_years'] ?>">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold small">Monthly EMI (&#8377;)</label>
                                        <input type="number" name="emi_amount" class="form-control" value="<?= $loan['emi_amount'] ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Interest Rate (p.a.)</label>
                                    <input type="number" step="0.01" name="interest_rate" class="form-control" value="<?= htmlspecialchars($loan['interest_rate']) ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Admin Note</label>
                                    <textarea name="note" class="form-control" placeholder="Rejection reason or approval details..." rows="2"><?= $loan['rejection_note'] ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-dark w-100">Update Application</button>
                            </form>

                            <form action="db/loan_handler.php" method="POST" class="mt-3" onsubmit="return confirm('Delete this loan application? This will also remove its documents.');">
                                <input type="hidden" name="action" value="delete_loan">
                                <input type="hidden" name="loan_id" value="<?= $loan_id ?>">
                                <button type="submit" class="btn btn-outline-danger w-100">Delete Application</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header fw-bold">Submitted Documents</div>
                        <div class="card-body">
                            <form action="db/loan_handler.php" method="POST" enctype="multipart/form-data" class="mb-3">
                                <input type="hidden" name="action" value="upload_doc">
                                <input type="hidden" name="loan_id" value="<?= $loan_id ?>">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold">Document Name</label>
                                        <input type="text" name="doc_name" class="form-control form-control-sm" placeholder="e.g. Bank Statement" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold">Select File</label>
                                        <input type="file" name="doc_file" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-sm btn-primary w-100">Upload</button>
                                    </div>
                                </div>
                                <div class="small text-muted mt-1">Allowed: PDF, JPG, JPEG, PNG, JFIF</div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Document Name</th>
                                            <th>Preview</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($doc = mysqli_fetch_assoc($docs_res)) { ?>
                                            <tr>
                                                <td><?= htmlspecialchars($doc['doc_name']) ?></td>
                                                <td>
                                                    <a href="../<?= $doc['doc_path'] ?>" target="_blank" class="btn btn-sm btn-info text-white">View File</a>
                                                </td>
                                                <td>
                                                    <?php if($doc['status'] == 'verified') echo '<span class="badge bg-success">Verified</span>'; 
                                                          elseif($doc['status'] == 'rejected') echo '<span class="badge bg-danger">Rejected</span>'; 
                                                          else echo '<span class="badge bg-warning">Pending</span>'; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#docModal<?= $doc['id'] ?>">
                                                        Verify / Reject
                                                    </button>

                                                    <div class="modal fade" id="docModal<?= $doc['id'] ?>" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <form action="db/loan_handler.php" method="POST" class="modal-content">
                                                                <input type="hidden" name="action" value="verify_doc">
                                                                <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                                                                <input type="hidden" name="loan_id" value="<?= $loan_id ?>">
                                                                
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Action on <?= $doc['doc_name'] ?></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <label>Status</label>
                                                                    <select name="status" class="form-select mb-3">
                                                                        <option value="verified">Approve (Verify)</option>
                                                                        <option value="rejected">Reject</option>
                                                                    </select>
                                                                    <label>Rejection Reason (If rejecting)</label>
                                                                    <textarea name="reason" class="form-control" rows="3"><?= $doc['rejection_reason'] ?></textarea>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
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
<?php include 'footer.php'; ?>
