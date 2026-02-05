<?php

include 'db/config.php';
include 'header.php';
 include 'topbar.php';

include 'sidebar.php';

$customer_id = $_SESSION['customer_id'] ?? 0;
$adminName = $_SESSION['customer_name'] ?? 'Customer';

$stats = [
    'total' => 0,
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0,
    'disbursed' => 0
];
$docStats = [
    'pending_docs' => 0,
    'rejected_docs' => 0
];

if ($customer_id) {
    $loanRes = mysqli_query($conn, "
        SELECT 
            COUNT(*) AS total,
            SUM(status='pending') AS pending,
            SUM(status='approved') AS approved,
            SUM(status='rejected') AS rejected,
            SUM(status='disbursed') AS disbursed
        FROM loan_applications
        WHERE customer_id = $customer_id
    ");
    if ($loanRes) {
        $stats = mysqli_fetch_assoc($loanRes);
    }

    $docRes = mysqli_query($conn, "
        SELECT 
            SUM(d.status='pending') AS pending_docs,
            SUM(d.status='rejected') AS rejected_docs
        FROM loan_application_docs d
        JOIN loan_applications la ON la.id = d.loan_application_id
        WHERE la.customer_id = $customer_id
    ");
    if ($docRes) {
        $docStats = mysqli_fetch_assoc($docRes);
    }
}
?>




<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
        --blue-600: #2563eb;
    }

    .content-page { background-color: #fcfcfd; }
    
    /* Elegant Greeting */
    .greeting-header {
        padding: 40px 0;
        border-bottom: 1px solid var(--slate-200);
        margin-bottom: 40px;
    }
    .greeting-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--slate-900);
        letter-spacing: -0.02em;
    }
    .greeting-header p {
        color: var(--slate-600);
        font-size: 1rem;
    }

    /* Premium Card Design */
    .stat-card-link {
        text-decoration: none !important;
        display: block;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid var(--slate-200);
        border-radius: 16px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    /* Subtle hover: Deep border and soft shadow */
    .stat-card-link:hover .stat-card {
        border-color: var(--slate-900);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
    }

    .stat-card .label {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.1em;
        color: var(--slate-600);
        margin-bottom: 8px;
        display: block;
    }

    .stat-card .value {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--slate-900);
        display: block;
    }

    .stat-card .footer-link {
        margin-top: 16px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--blue-600);
        display: flex;
        align-items: center;
    }

    .stat-card .footer-link i {
        margin-left: 4px;
        transition: transform 0.2s;
    }

    .stat-card-link:hover .footer-link i {
        transform: translateX(4px);
    }

    /* Quick Action Buttons */
    .action-btn {
        background: var(--slate-900);
        color: white;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        border: none;
        transition: opacity 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    
    .action-btn-outline {
        background: transparent;
        border: 1px solid var(--slate-200);
        color: var(--slate-900);
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
    }

    .action-btn-outline:hover {
        background: var(--slate-900);
        color: white;
        border-color: var(--slate-900);
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="greeting-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h1>Welcome, <?= htmlspecialchars($adminName) ?></h1>
                        <p class="mb-0">Track your applications and document verification status.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                
                <div class="col-md-3 mb-4">
                    <a href="my-applications.php" class="stat-card-link">
                        <div class="stat-card">
                            <span class="label">Total Applications</span>
                            <span class="value"><?= (int)$stats['total'] ?></span>
                            <div class="footer-link">
                                View applications <i class="ri-arrow-right-line"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3 mb-4">
                    <a href="my-applications.php" class="stat-card-link">
                        <div class="stat-card">
                            <span class="label">Pending</span>
                            <span class="value"><?= (int)$stats['pending'] ?></span>
                            <div class="footer-link">
                                Pending reviews <i class="ri-arrow-right-line"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3 mb-4">
                    <a href="my-applications.php" class="stat-card-link">
                        <div class="stat-card">
                            <span class="label">Approved</span>
                            <span class="value"><?= (int)$stats['approved'] ?></span>
                            <div class="footer-link">
                                Approved loans <i class="ri-arrow-right-line"></i>
                            </div>
                        </div>
                    </a>
                </div>
                
                <div class="col-md-3 mb-4">
                    <a href="documents.php" class="stat-card-link">
                        <div class="stat-card">
                            <span class="label">Rejected Docs</span>
                            <span class="value"><?= (int)$docStats['rejected_docs'] ?></span>
                            <div class="footer-link">
                                Fix documents <i class="ri-arrow-right-line"></i>
                            </div>
                        </div>
                    </a>
                </div>

            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="p-4 bg-white rounded-4 border">
                        <h5 class="fw-bold mb-4">Quick Actions</h5>
                        <div class="d-flex gap-3">
                            <a href="../apply-loan.php" class="action-btn">Apply for Loan</a>
                            <a href="my-applications.php" class="action-btn-outline">Track Applications</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
