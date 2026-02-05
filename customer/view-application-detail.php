<?php
include 'db/config.php';
include 'header.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login.php"); exit;
}

$loan_id = (int)$_GET['id'];
$customer_id = $_SESSION['customer_id'];

// Fetch Main Application Detail (Security check included in WHERE)
$query = "SELECT la.*, s.service_name, s.sub_category_id FROM loan_applications la 
          JOIN services s ON la.service_id = s.id 
          WHERE la.id = $loan_id AND la.customer_id = $customer_id";
$res = mysqli_query($conn, $query);
$loan = mysqli_fetch_assoc($res);

if (!$loan) { header("Location: my-applications.php"); exit; }

// Fetch Individual Document Status
$docQuery = "SELECT * FROM loan_application_docs WHERE loan_application_id = $loan_id ORDER BY created_at DESC";
$docs = mysqli_query($conn, $docQuery);

include 'topbar.php';
include 'sidebar.php';
?>

<style>
    :root { --slate-900: #0f172a; --slate-200: #e2e8f0; }
    .content-page { background-color: #f8fafc; min-height: 100vh; padding-bottom: 50px; }
    .card-modern { border: 1px solid var(--slate-200); border-radius: 20px; background: #fff; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
    .doc-row { border-bottom: 1px solid var(--slate-200); padding: 15px 0; }
    .doc-row:last-child { border-bottom: none; }
    
    /* Dynamic Status Badge Colors */
    .status-badge-pending { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; border-radius: 10px; }
    .status-badge-approved { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; border-radius: 10px; }
    .status-badge-rejected { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 10px; }
</style>

<div class="content-page">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="card-modern p-4 mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <span class="badge bg-light text-muted border px-3 py-2 mb-2">APPLICATION DETAILS</span>
                            <h3 class="fw-bold text-dark"><?= htmlspecialchars($loan['service_name']) ?> <span class="text-muted fw-normal">#LA-<?= $loan_id ?></span></h3>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <?php 
                                $statusClass = 'pending';
                                if($loan['status'] == 'approved') $statusClass = 'approved';
                                if($loan['status'] == 'rejected') $statusClass = 'rejected';
                            ?>
                            <div class="status-badge-<?= $statusClass ?> d-inline-block px-4 py-2 fw-bold">
                                STATUS: <?= strtoupper($loan['status']) ?>
                            </div>
                            <div class="mt-2">
                                <a href="enquiry_add.php?loan_type=<?= (int)$loan['sub_category_id'] ?>" class="btn btn-sm btn-dark">Raise Enquiry</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-4">
                        <?php if($loan['status'] == 'rejected'): ?>
                            <div class="alert alert-danger rounded-4 border-0 p-4 shadow-sm mb-4">
                                <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-1"></i> Rejection Reason</h6>
                                <p class="small mb-0"><?= !empty($loan['rejection_note']) ? htmlspecialchars($loan['rejection_note']) : 'Contact support for more information.' ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="card-modern p-4">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Loan Summary</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Amount:</span>
                                <span class="fw-bold small">&#8377;<?= number_format($loan['requested_amount'], 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Tenure:</span>
                                <span class="fw-bold small"><?= $loan['tenure_years'] ?? 'N/A' ?> Years</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">EMI:</span>
                                <span class="fw-bold small">&#8377;<?= number_format($loan['emi_amount'], 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Interest Rate (p.a.):</span>
                                <span class="fw-bold small"><?= number_format((float)$loan['interest_rate'], 2) ?>%</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Applied on:</span>
                                <span class="fw-bold small"><?= date('d M Y, h:i A', strtotime($loan['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card-modern p-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <h5 class="fw-bold text-dark mb-0">Document Verification History</h5>
                                <?php if ($loan['status'] !== 'disbursed'): ?>
                                    <form action="db/get_documents.php?action=upload" method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
                                        <input type="hidden" name="loan_id" value="<?= $loan_id ?>">
                                        <input type="text" name="doc_name" class="form-control form-control-sm" placeholder="Document name" required>
                                        <input type="file" name="doc_file" class="form-control form-control-sm" required>
                                        <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <div class="small text-muted mb-3">Allowed: PDF, JPG, JPEG, PNG, JFIF</div>
                            
                            <?php if(mysqli_num_rows($docs) > 0): ?>
                                <?php while($d = mysqli_fetch_assoc($docs)): ?>
                                    <div class="doc-row">
                                        <div class="row align-items-center">
                                            <div class="col-md-7">
                                                <div class="fw-bold text-dark small"><?= htmlspecialchars($d['doc_name']) ?></div>
                                                <a href="../<?= $d['doc_path'] ?>" target="_blank" class="text-decoration-none small text-primary"><i class="fas fa-external-link-alt"></i> View Uploaded File</a>
                                                <div class="text-muted" style="font-size: 0.7rem;">Uploaded: <?= date('d M Y, h:i A', strtotime($d['created_at'])) ?></div>
                                            </div>
                                            <div class="col-md-5 text-md-end mt-2 mt-md-0">
                                                <?php if($d['status'] == 'verified'): ?>
                                                    <span class="text-success small fw-bold"><i class="fas fa-check-circle"></i> VERIFIED</span>
                                                <?php elseif($d['status'] == 'rejected'): ?>
                                                    <span class="text-danger small fw-bold"><i class="fas fa-times-circle"></i> REJECTED</span>
                                                    <div class="text-muted" style="font-size: 0.65rem;"><?= htmlspecialchars($d['rejection_reason'] ?? 'Invalid document') ?></div>
                                                    <form action="db/get_documents.php?action=delete" method="POST" class="mt-2 d-inline-block">
                                                        <input type="hidden" name="doc_id" value="<?= (int)$d['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete & Reupload</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-warning small fw-bold"><i class="fas fa-clock"></i> PENDING REVIEW</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-center text-muted py-4">No documents found for this application.</p>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
