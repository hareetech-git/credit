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
?>
<div class="content-page"><div class="content"><div class="container-fluid pt-4">
    <div class="mb-4"><h2 class="fw-bold text-dark mb-1">Welcome, <?= htmlspecialchars($dsa_name) ?></h2><p class="text-muted small mb-0">Track your submitted loan leads and their status.</p></div>
    <div class="row">
        <div class="col-md-3 mb-3"><div class="card border"><div class="card-body"><div class="small text-muted">Total Leads</div><div class="fs-2 fw-bold"><?= (int)$stats['total'] ?></div></div></div></div>
        <div class="col-md-3 mb-3"><div class="card border"><div class="card-body"><div class="small text-muted">Pending</div><div class="fs-2 fw-bold"><?= (int)$stats['pending'] ?></div></div></div></div>
        <div class="col-md-3 mb-3"><div class="card border"><div class="card-body"><div class="small text-muted">Approved</div><div class="fs-2 fw-bold"><?= (int)$stats['approved'] ?></div></div></div></div>
        <div class="col-md-3 mb-3"><div class="card border"><div class="card-body"><div class="small text-muted">Rejected</div><div class="fs-2 fw-bold"><?= (int)$stats['rejected'] ?></div></div></div></div>
    </div>
</div></div></div>
<?php include 'footer.php'; ?>
