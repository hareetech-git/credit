<?php
include 'header.php';
dsaRequireAccess($conn, 'dsa_dashboard_view');
include 'topbar.php';
include 'sidebar.php';

$dsa_id = (int)($_SESSION['dsa_id'] ?? 0);
$dsa_name = $_SESSION['dsa_name'] ?? 'DSA Agent';

$stats = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'disbursed' => 0];
if ($dsa_id > 0) {
    $res = mysqli_query($conn, "SELECT COUNT(*) AS total, SUM(status='pending') AS pending, SUM(status='approved') AS approved, SUM(status='rejected') AS rejected, SUM(status='disbursed') AS disbursed FROM loan_applications WHERE dsa_id = $dsa_id");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        if ($row) {
            $stats = $row;
        }
    }
}

$today_count = 0;
$todayRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM loan_applications WHERE dsa_id = $dsa_id AND DATE(created_at) = CURDATE()");
if ($todayRes) {
    $today_count = (int)(mysqli_fetch_assoc($todayRes)['total'] ?? 0);
}

$recentLeads = mysqli_query($conn, "SELECT la.id, la.requested_amount, la.status, la.created_at, c.full_name, s.service_name
                                    FROM loan_applications la
                                    INNER JOIN customers c ON c.id = la.customer_id
                                    INNER JOIN services s ON s.id = la.service_id
                                    WHERE la.dsa_id = $dsa_id
                                    ORDER BY la.id DESC
                                    LIMIT 5");
?>
<div class="content-page"><div class="content"><div class="container-fluid pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Welcome, <?= htmlspecialchars($dsa_name) ?></h2>
            <p class="text-muted small mb-0">All lead counts below are based only on loans submitted by your DSA account.</p>
        </div>
        <?php if (dsaHasAccess($conn, 'dsa_lead_create')): ?>
            <a href="add-lead.php" class="btn btn-dark">Add New Lead</a>
        <?php endif; ?>
    </div>
    <div class="row">
        <div class="col-md-3 mb-3"><div class="card border"><div class="card-body"><div class="small text-muted">Total Leads</div><div class="fs-2 fw-bold"><?= (int)$stats['total'] ?></div></div></div></div>
        <div class="col-md-3 mb-3"><div class="card border"><div class="card-body"><div class="small text-muted">Pending</div><div class="fs-2 fw-bold"><?= (int)$stats['pending'] ?></div></div></div></div>
        <div class="col-md-3 mb-3"><div class="card border"><div class="card-body"><div class="small text-muted">Approved</div><div class="fs-2 fw-bold"><?= (int)$stats['approved'] ?></div></div></div></div>
        <div class="col-md-3 mb-3"><div class="card border"><div class="card-body"><div class="small text-muted">Rejected</div><div class="fs-2 fw-bold"><?= (int)$stats['rejected'] ?></div></div></div></div>
    </div>
    <div class="row">
        <div class="col-md-3 mb-3"><div class="card border"><div class="card-body"><div class="small text-muted">Disbursed</div><div class="fs-2 fw-bold"><?= (int)$stats['disbursed'] ?></div></div></div></div>
        <div class="col-md-3 mb-3"><div class="card border"><div class="card-body"><div class="small text-muted">Today's Leads</div><div class="fs-2 fw-bold"><?= $today_count ?></div></div></div></div>
    </div>

    <div class="card border mt-2">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Recent Leads</h5>
            <a href="my-applications.php" class="small">View All</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-3">Lead</th>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recentLeads && mysqli_num_rows($recentLeads) > 0): while ($lead = mysqli_fetch_assoc($recentLeads)): ?>
                            <tr>
                                <td class="ps-3"><?= htmlspecialchars((string)$lead['full_name']) ?></td>
                                <td><?= htmlspecialchars((string)$lead['service_name']) ?></td>
                                <td>Rs <?= format_inr((float)$lead['requested_amount'], 2) ?></td>
                                <td><span class="badge bg-secondary-subtle text-dark"><?= htmlspecialchars(ucfirst((string)$lead['status'])) ?></span></td>
                                <td><?= date('d M, Y', strtotime((string)$lead['created_at'])) ?></td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">No leads found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div></div></div>
<?php include 'footer.php'; ?>
