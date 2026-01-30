<?php
include 'db/config.php';
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

$loan_id = (int)$_GET['id'];
if (!$loan_id) die("Invalid Loan ID");

// Fetch Loan Details
$loan_sql = "SELECT l.*, c.full_name, c.email, c.phone, s.service_name 
             FROM loan_applications l
             JOIN customers c ON l.customer_id = c.id
             JOIN services s ON l.service_id = s.id
             WHERE l.id = $loan_id";
$loan = mysqli_fetch_assoc(mysqli_query($conn, $loan_sql));

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
                                <span>Amount:</span> <strong>₹<?= number_format($loan['requested_amount']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tenure:</span> <strong><?= $loan['tenure_years'] ?> Years</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Status:</span> <span class="badge bg-secondary"><?= strtoupper($loan['status']) ?></span>
                            </div>
                            
                            <hr>
                            <form action="db/loan_handler.php" method="POST">
                                <input type="hidden" name="loan_id" value="<?= $loan_id ?>">
                                <input type="hidden" name="action" value="update_loan_status">
                                
                                <label class="form-label fw-bold">Update Status</label>
                                <select name="status" class="form-select mb-2" required>
                                    <option value="pending" <?= $loan['status']=='pending'?'selected':'' ?>>Pending</option>
                                    <option value="approved" <?= $loan['status']=='approved'?'selected':'' ?>>Approved</option>
                                    <option value="rejected" <?= $loan['status']=='rejected'?'selected':'' ?>>Rejected</option>
                                    <option value="disbursed" <?= $loan['status']=='disbursed'?'selected':'' ?>>Disbursed</option>
                                </select>
                                
                                <textarea name="note" class="form-control mb-3" placeholder="Rejection/Approval Note..." rows="2"><?= $loan['rejection_note'] ?></textarea>
                                
                                <button class="btn btn-dark w-100">Update Application</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header fw-bold">Submitted Documents</div>
                        <div class="card-body">
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
                                                    <?php if($doc['status'] == 'verified') echo '<span class="badge bg-success">Verified</span>'; ?>
                                                    <?php if($doc['status'] == 'rejected') echo '<span class="badge bg-danger">Rejected</span>'; ?>
                                                    <?php if($doc['status'] == 'pending') echo '<span class="badge bg-warning">Pending</span>'; ?>
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
                                                                    <textarea name="reason" class="form-control"></textarea>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button class="btn btn-primary">Save Changes</button>
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