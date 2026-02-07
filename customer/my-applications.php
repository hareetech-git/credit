<?php
include 'db/config.php';
include 'header.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Fetch all applications for the logged-in customer
$query = "SELECT la.*, s.service_name, s.sub_category_id
          FROM loan_applications la 
          JOIN services s ON la.service_id = s.id 
          WHERE la.customer_id = $customer_id 
          ORDER BY la.created_at DESC";
$result = mysqli_query($conn, $query);

include 'topbar.php';
include 'sidebar.php';
?>

<style>
    :root { --slate-900: #0f172a; --slate-600: #475569; --slate-200: #e2e8f0; }
    .content-page { background-color: #f8fafc; min-height: 100vh; }
    .card-modern { border: 1px solid var(--slate-200); border-radius: 20px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); transition: 0.3s; }
    .card-modern:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
    .status-badge { padding: 6px 14px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
    .bg-pending { background: #fef3c7; color: #92400e; }
    .bg-approved { background: #d1fae5; color: #065f46; }
    .bg-rejected { background: #fee2e2; color: #991b1b; }
</style>

<div class="content-page">
    <div class="container-fluid pt-4">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold text-dark mb-1">My Applications</h2>
                        <p class="text-muted small">Track the status of your active and past loan requests.</p>
                    </div>
                    <a href="../apply-loan.php" class="btn btn-dark rounded-pill px-4 fw-bold">New Request</a>
                </div>

                <div class="row">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <div class="col-md-6 col-xl-4 mb-4">
                                <div class="card-modern p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <span class="text-muted small fw-bold">#LA-<?= $row['id'] ?></span>
                                        <span class="status-badge bg-<?= $row['status'] ?>"><?= $row['status'] ?></span>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($row['service_name']) ?></h5>
                                    <h4 class="fw-bold text-primary mb-3">₹<?= format_inr($row['requested_amount'], 2) ?></h4>
                                    
                                    <div class="border-top pt-3 mt-3">
                                        <div class="d-flex justify-content-between small text-muted mb-2">
                                            <span>Applied Date:</span>
                                            <span class="text-dark fw-semibold"><?= date('d M Y', strtotime($row['created_at'])) ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between small text-muted mb-3">
                                            <span>Tenure:</span>
                                            <span class="text-dark fw-semibold"><?= $row['tenure_years'] ?> Years</span>
                                        </div>
                                        <a href="view-application-detail.php?id=<?= $row['id'] ?>" class="btn btn-outline-dark btn-sm w-100 fw-bold rounded-pill mb-2">View Full Status</a>
                                        <a href="enquiry_add.php?loan_type=<?= (int)$row['sub_category_id'] ?>" class="btn btn-dark btn-sm w-100 fw-bold rounded-pill">Raise Enquiry</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="ri-file-list-3-line display-1 text-light"></i>
                            <p class="text-muted mt-3">You haven't submitted any applications yet.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
