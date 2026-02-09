<?php
include 'db/config.php';
include 'header.php';

$tableReady = false;
$tblRes = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_requests'");
if ($tblRes && mysqli_num_rows($tblRes) > 0) {
    $tableReady = true;
}

$status_filter = $_GET['status'] ?? '';
$where = '1=1';
if (in_array($status_filter, ['pending', 'approved', 'rejected'], true)) {
    $safeStatus = mysqli_real_escape_string($conn, $status_filter);
    $where .= " AND r.status = '$safeStatus'";
}

$result = false;
if ($tableReady) {
    $query = "SELECT r.*, a.name AS reviewer_name
              FROM dsa_requests r
              LEFT JOIN admin a ON a.id = r.reviewed_by
              WHERE $where
              ORDER BY r.id DESC";
    $result = mysqli_query($conn, $query);
}

$pendingCount = 0;
if ($tableReady) {
    $pRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM dsa_requests WHERE status='pending'");
    if ($pRes) {
        $pendingCount = (int)(mysqli_fetch_assoc($pRes)['total'] ?? 0);
    }
}
?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">DSA Requests</h2>
                    <p class="text-muted small mb-0">Review customer requests to become DSA agents.</p>
                </div>
                <span class="badge bg-warning-subtle text-dark px-3 py-2">Pending: <?= $pendingCount ?></span>
            </div>

            <?php if (!empty($_GET['msg'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['err'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['err']) ?></div>
            <?php endif; ?>
            <?php if (!$tableReady): ?>
                <div class="alert alert-warning">`dsa_requests` table not found. Please run `dsa_migration.sql` first.</div>
            <?php endif; ?>

            <div class="card border mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="approved" <?= $status_filter === 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="rejected" <?= $status_filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2"><button class="btn btn-dark w-100">Filter</button></div>
                    </form>
                </div>
            </div>

            <div class="card border">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-3">Applicant</th>
                                    <th>Firm</th>
                                    <th>Bank Details</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Reviewed By</th>
                                    <th class="pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-bold"><?= htmlspecialchars($row['full_name']) ?></div>
                                                <div class="small text-muted"><?= htmlspecialchars($row['email']) ?> | <?= htmlspecialchars($row['phone']) ?></div>
                                                <div class="small text-muted">PAN: <?= htmlspecialchars($row['pan_number']) ?></div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold"><?= htmlspecialchars($row['firm_name']) ?></div>
                                                <div class="small text-muted"><?= htmlspecialchars($row['city']) ?>, <?= htmlspecialchars($row['state']) ?> - <?= htmlspecialchars($row['pin_code']) ?></div>
                                            </td>
                                            <td>
                                                <div class="small"><strong><?= htmlspecialchars($row['bank_name']) ?></strong></div>
                                                <div class="small text-muted"><?= htmlspecialchars($row['account_number']) ?></div>
                                                <div class="small text-muted">IFSC: <?= htmlspecialchars($row['ifsc_code']) ?></div>
                                            </td>
                                            <td>
                                                <?php if ($row['status'] === 'approved'): ?>
                                                    <span class="badge bg-success-subtle text-success">Approved</span>
                                                <?php elseif ($row['status'] === 'rejected'): ?>
                                                    <span class="badge bg-danger-subtle text-danger">Rejected</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning-subtle text-dark">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="small text-muted"><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></td>
                                            <td class="small text-muted"><?= htmlspecialchars($row['reviewer_name'] ?? '-') ?></td>
                                            <td class="pe-3" style="min-width: 280px;">
                                                <?php if ($row['status'] === 'pending'): ?>
                                                    <form action="db/dsa_request_action.php" method="POST" class="d-flex gap-2 align-items-start">
                                                        <input type="hidden" name="request_id" value="<?= (int)$row['id'] ?>">
                                                        <textarea name="admin_note" class="form-control form-control-sm" rows="2" placeholder="Admin note (optional)"></textarea>
                                                        <button type="submit" name="action_type" value="approve" class="btn btn-sm btn-success">Approve</button>
                                                        <button type="submit" name="action_type" value="reject" class="btn btn-sm btn-danger">Reject</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="small text-muted">Processed</span>
                                                    <?php if (!empty($row['admin_note'])): ?>
                                                        <div class="small mt-1">Note: <?= nl2br(htmlspecialchars($row['admin_note'])) ?></div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center py-4 text-muted">No DSA requests found.</td></tr>
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
