<?php
include 'db/config.php';
include 'header.php';
if (!hasAccess($conn, 'cust_read')) {
    echo "<script>alert('Access Denied: You do not have permission to view customers.'); window.location='dashboard.php';</script>";
    exit();
}
// Ensure FontAwesome is loaded
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">';
include 'topbar.php';
include 'sidebar.php';

$id = (int)$_GET['id'];
if ($id === 0) {
    echo "<script>window.location='customers.php';</script>";
    exit;
}

// 1. Fetch Customer & Profile Data
$query = "SELECT c.*, cp.* FROM customers c 
          LEFT JOIN customer_profiles cp ON c.id = cp.customer_id 
          WHERE c.id = $id";
$result = mysqli_query($conn, $query);
$cust = mysqli_fetch_assoc($result);

if (!$cust) die("Customer not found.");

// 2. Fetch Loan History
$loan_query = "SELECT l.*, s.service_name 
               FROM loan_applications l 
               LEFT JOIN services s ON l.service_id = s.id 
               WHERE l.customer_id = $id 
               ORDER BY l.created_at DESC";
$loan_res = mysqli_query($conn, $loan_query);
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
    }

    .info-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: var(--slate-600);
        font-weight: 700;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
        display: block;
    }
    
    .info-value {
        font-size: 0.95rem;
        color: var(--slate-900);
        font-weight: 500;
    }

    .avatar-large {
        width: 100px; height: 100px;
        background: var(--slate-900);
        color: white;
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 2.5rem;
        margin: 0 auto;
    }

    /* Badges */
    .badge-soft {
        padding: 5px 12px; border-radius: 8px; font-weight: 600; font-size: 0.8rem;
        border: 1px solid transparent;
    }
    .badge-active { background: #f0fdf4; color: #16a34a; border-color: #dcfce7; }
    .badge-blocked { background: #fef2f2; color: #dc2626; border-color: #fee2e2; }

    /* Loan Status Badges */
    .badge-pending   { background: #fffbeb; color: #92400e; }
    .badge-approved  { background: #eff6ff; color: #1e40af; }
    .badge-rejected  { background: #fef2f2; color: #991b1b; }
    .badge-disbursed { background: #f0fdf4; color: #166534; }

    .table-modern thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--slate-600);
        padding: 14px 24px;
        border: none;
    }
    .table-modern tbody td {
        padding: 14px 24px;
        font-size: 0.9rem;
        border-bottom: 1px solid var(--slate-200);
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Customer Overview</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a href="customers.php" class="text-decoration-none text-muted">Customer Directory</a></li>
                            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Profile Details</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="customers.php" class="btn btn-outline-secondary px-4 py-2 fw-bold" style="border-radius: 10px;">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </a>
                    <a href="customer_add.php?id=<?= $id ?>" class="btn btn-dark px-4 py-2 fw-bold" style="border-radius: 10px;">
                        <i class="fas fa-edit me-2"></i> Edit Account
                    </a>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card card-modern h-100">
                        <div class="card-body text-center p-4">
                            <div class="avatar-large mb-3 shadow-sm">
                                <?= strtoupper(substr($cust['full_name'], 0, 1)) ?>
                            </div>
                            <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($cust['full_name']) ?></h4>
                            <p class="text-muted small mb-4"><?= htmlspecialchars($cust['email']) ?></p>
                            
                            <span class="badge-soft <?= ($cust['status'] == 'active') ? 'badge-active' : 'badge-blocked' ?> px-4 py-2">
                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i> <?= ucfirst($cust['status']) ?>
                            </span>

                            <hr class="my-4" style="opacity: 0.05;">

                            <div class="text-start">
                                <div class="mb-4">
                                    <span class="info-label">Direct Contact</span>
                                    <div class="info-value"><i class="fas fa-phone-alt me-2 text-muted"></i> <?= htmlspecialchars($cust['phone']) ?></div>
                                </div>
                                <div class="mb-4">
                                    <span class="info-label">Account Created</span>
                                    <div class="info-value"><i class="fas fa-calendar-day me-2 text-muted"></i> <?= date('d M, Y', strtotime($cust['created_at'])) ?></div>
                                </div>
                           
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card card-modern h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-fingerprint me-2 text-primary"></i> KYC & Financial Data</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <span class="info-label">Permanent Account (PAN)</span>
                                    <div class="info-value p-2 border rounded bg-light"><?= $cust['pan_number'] ?: 'Not Provided' ?></div>
                                </div>
                                <div class="col-md-6">
                                    <span class="info-label">Aadhaar Identity</span>
                                    <div class="info-value p-2 border rounded bg-light"><?= $cust['aadhaar_number'] ?: 'Not Provided' ?></div>
                                </div>
                                <div class="col-md-4">
                                    <span class="info-label">Employment Type</span>
                                    <div class="info-value text-capitalize"><?= str_replace('_', ' ', $cust['employee_type']) ?: '--' ?></div>
                                </div>
                                <div class="col-md-4">
                                    <span class="info-label">Monthly Net Pay</span>
                                    <div class="info-value text-success fw-bold"><?= $cust['monthly_income'] ? '₹' . number_format($cust['monthly_income']) : '--' ?></div>
                                </div>
                                <div class="col-md-4">
                                    <span class="info-label">Date of Birth</span>
                                    <div class="info-value"><?= $cust['birth_date'] ? date('d-m-Y', strtotime($cust['birth_date'])) : '--' ?></div>
                                </div>
                                <div class="col-12">
                                    <span class="info-label">Residential Address</span>
                                    <div class="info-value">
                                        <?php if($cust['city'] || $cust['state']): ?>
                                            <?= htmlspecialchars($cust['city']) ?>, <?= htmlspecialchars($cust['state']) ?> - <?= htmlspecialchars($cust['pin_code']) ?>
                                        <?php else: ?>
                                            <span class="text-muted italic">No address details stored.</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card card-modern">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-history me-2 text-primary"></i> Loan Application History</h6>
                            <span class="badge bg-light text-dark border fw-bold"><?= mysqli_num_rows($loan_res) ?> Entries</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-modern mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Application ID</th>
                                            <th>Product Service</th>
                                            <th>Loan Amount</th>
                                            <th>Tenure</th>
                                            <th>Applied Date</th>
                                            <th>Status</th>
                                            <th class="text-end">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($loan_res) > 0) : ?>
                                            <?php while ($loan = mysqli_fetch_assoc($loan_res)) : ?>
                                                <tr>
                                                    <td class="text-muted fw-bold">#APP-<?= $loan['id'] ?></td>
                                                    <td class="fw-bold text-dark"><?= htmlspecialchars($loan['service_name']) ?></td>
                                                    <td class="text-primary fw-bold">₹<?= number_format($loan['requested_amount']) ?></td>
                                                    <td><?= $loan['tenure_years'] ?> Years</td>
                                                    <td class="text-muted small"><?= date('d M, Y', strtotime($loan['created_at'])) ?></td>
                                                    <td>
                                                        <?php 
                                                            $status = $loan['status'];
                                                            $badgeClass = "badge-$status";
                                                        ?>
                                                        <span class="badge-soft <?= $badgeClass ?> px-3 py-2 text-capitalize">
                                                            <?= $status ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="loan_view.php?id=<?= $loan['id'] ?>" class="btn btn-sm btn-link text-decoration-none fw-bold">
                                                            Review <i class="fas fa-chevron-right ms-1"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-5 text-muted">
                                                    <i class="fas fa-folder-open fa-2x mb-3 opacity-25"></i><br>
                                                    No loan applications found for this borrower.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
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