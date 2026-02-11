<?php
include 'db/config.php';
include 'header.php';
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">';
include 'topbar.php';
include 'sidebar.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "<script>window.location='dsa_list.php';</script>";
    exit;
}

$query = "SELECT d.*, dp.firm_name, dp.pan_number, dp.city, dp.state, dp.pin_code, dp.bank_name, dp.account_number, dp.ifsc_code,
                 dept.name AS department_name, a.name AS creator_name
          FROM dsa d
          LEFT JOIN dsa_profiles dp ON dp.dsa_id = d.id
          LEFT JOIN departments dept ON dept.id = d.department_id
          LEFT JOIN admin a ON a.id = d.created_by
          WHERE d.id = $id
          LIMIT 1";
$res = mysqli_query($conn, $query);
$dsa = $res ? mysqli_fetch_assoc($res) : null;

if (!$dsa) {
    echo "<script>window.location='dsa_list.php';</script>";
    exit;
}

$dsa['pan_number'] = uc_decrypt_sensitive((string)($dsa['pan_number'] ?? ''));
$dsa['account_number'] = uc_decrypt_sensitive((string)($dsa['account_number'] ?? ''));

$leadStats = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'disbursed' => 0];
$statsRes = mysqli_query($conn, "SELECT COUNT(*) AS total,
                                        SUM(status='pending') AS pending,
                                        SUM(status='approved') AS approved,
                                        SUM(status='rejected') AS rejected,
                                        SUM(status='disbursed') AS disbursed
                                 FROM loan_applications
                                 WHERE dsa_id = $id");
if ($statsRes) {
    $leadStats = mysqli_fetch_assoc($statsRes) ?: $leadStats;
}

$leadRes = mysqli_query($conn, "SELECT l.id, l.requested_amount, l.tenure_years, l.status, l.created_at, l.assigned_staff_id,
                                       c.full_name AS customer_name, c.phone AS customer_phone,
                                       s.service_name, st.name AS assigned_staff_name
                                FROM loan_applications l
                                JOIN customers c ON c.id = l.customer_id
                                JOIN services s ON s.id = l.service_id
                                LEFT JOIN staff st ON st.id = l.assigned_staff_id
                                WHERE l.dsa_id = $id
                                ORDER BY l.id DESC");
?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
    }
    .content-page { background-color: #fcfcfd; }
    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
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
        font-size: 0.92rem;
        color: var(--slate-900);
        font-weight: 500;
    }
    .avatar-large {
        width: 92px; height: 92px;
        background: var(--slate-900);
        color: white;
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 2.1rem;
        margin: 0 auto;
    }
    .stat-box {
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        background: #fff;
        padding: 14px;
    }
    .stat-label { font-size: 0.72rem; text-transform: uppercase; color: var(--slate-600); font-weight: 700; letter-spacing: 0.04em; }
    .stat-val { font-size: 1.5rem; font-weight: 800; color: var(--slate-900); }
    .table-modern thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--slate-600);
        padding: 14px 18px;
        border: none;
    }
    .table-modern tbody td {
        padding: 14px 18px;
        font-size: 0.88rem;
        border-bottom: 1px solid var(--slate-200);
        vertical-align: middle;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">DSA Profile Details</h2>
                    <p class="text-muted small mb-0">Lead owner profile and all leads created by this DSA agent.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="dsa_list.php" class="btn btn-outline-secondary px-4 py-2 fw-bold" style="border-radius: 10px;">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </a>
                    <a href="dsa_add.php?id=<?= (int)$dsa['id'] ?>" class="btn btn-dark px-4 py-2 fw-bold" style="border-radius: 10px;">
                        <i class="fas fa-edit me-2"></i> Edit DSA
                    </a>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <div class="card card-modern h-100">
                        <div class="card-body text-center p-4">
                            <div class="avatar-large mb-3"><?= strtoupper(substr((string)$dsa['name'], 0, 1)) ?></div>
                            <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars((string)$dsa['name']) ?></h4>
                            <p class="text-muted small mb-2"><?= htmlspecialchars((string)$dsa['email']) ?></p>
                            <div class="text-muted small mb-3"><?= htmlspecialchars((string)$dsa['phone']) ?></div>
                            <?php if (($dsa['status'] ?? '') === 'active'): ?>
                                <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 rounded-pill">Inactive</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card card-modern h-100">
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <span class="info-label">Department</span>
                                    <div class="info-value"><?= htmlspecialchars((string)($dsa['department_name'] ?? 'Unassigned')) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <span class="info-label">Created By</span>
                                    <div class="info-value"><?= htmlspecialchars((string)($dsa['creator_name'] ?? 'System')) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <span class="info-label">Firm Name</span>
                                    <div class="info-value"><?= htmlspecialchars((string)($dsa['firm_name'] ?? '-')) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <span class="info-label">PAN Number</span>
                                    <div class="info-value"><?= htmlspecialchars((string)($dsa['pan_number'] ?? '-')) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <span class="info-label">City / State</span>
                                    <div class="info-value"><?= htmlspecialchars(trim((string)($dsa['city'] ?? '') . ', ' . (string)($dsa['state'] ?? ''), ', ')) ?: '-' ?></div>
                                </div>
                                <div class="col-md-6">
                                    <span class="info-label">Pin Code</span>
                                    <div class="info-value"><?= htmlspecialchars((string)($dsa['pin_code'] ?? '-')) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <span class="info-label">Bank Name</span>
                                    <div class="info-value"><?= htmlspecialchars((string)($dsa['bank_name'] ?? '-')) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <span class="info-label">Account Number</span>
                                    <div class="info-value"><?= htmlspecialchars((string)($dsa['account_number'] ?? '-')) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <span class="info-label">IFSC Code</span>
                                    <div class="info-value"><?= htmlspecialchars((string)($dsa['ifsc_code'] ?? '-')) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <span class="info-label">Joined Date</span>
                                    <div class="info-value"><?= !empty($dsa['created_at']) ? date('d M, Y', strtotime((string)$dsa['created_at'])) : '-' ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-2"><div class="stat-box"><div class="stat-label">Total Leads</div><div class="stat-val"><?= (int)($leadStats['total'] ?? 0) ?></div></div></div>
                <div class="col-md-2"><div class="stat-box"><div class="stat-label">Pending</div><div class="stat-val"><?= (int)($leadStats['pending'] ?? 0) ?></div></div></div>
                <div class="col-md-2"><div class="stat-box"><div class="stat-label">Approved</div><div class="stat-val"><?= (int)($leadStats['approved'] ?? 0) ?></div></div></div>
                <div class="col-md-2"><div class="stat-box"><div class="stat-label">Rejected</div><div class="stat-val"><?= (int)($leadStats['rejected'] ?? 0) ?></div></div></div>
                <div class="col-md-2"><div class="stat-box"><div class="stat-label">Disbursed</div><div class="stat-val"><?= (int)($leadStats['disbursed'] ?? 0) ?></div></div></div>
            </div>

            <div class="card card-modern">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-list-check me-2 text-primary"></i> Leads Created By This DSA</h6>
                    <span class="badge bg-light text-dark border fw-bold"><?= mysqli_num_rows($leadRes) ?> Entries</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Lead ID</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Assigned Staff</th>
                                    <th>Date</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($leadRes && mysqli_num_rows($leadRes) > 0): while ($lead = mysqli_fetch_assoc($leadRes)): ?>
                                    <tr>
                                        <td class="text-muted fw-bold">#L-<?= (int)$lead['id'] ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars((string)$lead['customer_name']) ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars((string)$lead['customer_phone']) ?></div>
                                        </td>
                                        <td><?= htmlspecialchars((string)$lead['service_name']) ?></td>
                                        <td class="fw-bold text-primary">INR <?= format_inr((float)$lead['requested_amount'], 2) ?></td>
                                        <td><span class="badge bg-light text-dark text-capitalize"><?= htmlspecialchars((string)$lead['status']) ?></span></td>
                                        <td><?= htmlspecialchars((string)($lead['assigned_staff_name'] ?? 'Unassigned')) ?></td>
                                        <td class="small text-muted"><?= date('d M, Y', strtotime((string)$lead['created_at'])) ?></td>
                                        <td class="text-end">
                                            <a href="loan_view.php?id=<?= (int)$lead['id'] ?>" class="btn btn-sm btn-link text-decoration-none fw-bold">
                                                Review <i class="fas fa-chevron-right ms-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">No leads found for this DSA agent.</td>
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

<?php include 'footer.php'; ?>
