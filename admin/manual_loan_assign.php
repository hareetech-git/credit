<?php
include 'db/config.php';
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

$search = mysqli_real_escape_string($conn, trim((string)($_GET['search'] ?? '')));
$status = mysqli_real_escape_string($conn, trim((string)($_GET['status'] ?? '')));

$staff_list = [];
$staff_res = mysqli_query($conn, "
    SELECT DISTINCT s.id, s.name, s.email
    FROM staff s
    WHERE s.status='active'
      AND (
        EXISTS (
            SELECT 1
            FROM role_permissions rp
            INNER JOIN permissions p ON p.id = rp.permission_id
            WHERE rp.role_id = s.role_id AND p.perm_key = 'loan_process'
        )
        OR EXISTS (
            SELECT 1
            FROM staff_permissions sp
            INNER JOIN permissions p2 ON p2.id = sp.permission_id
            WHERE sp.staff_id = s.id AND p2.perm_key = 'loan_process'
        )
      )
      AND (
        EXISTS (
            SELECT 1
            FROM role_permissions rp
            INNER JOIN permissions p ON p.id = rp.permission_id
            WHERE rp.role_id = s.role_id AND p.perm_key = 'loan_manual_assign'
        )
        OR EXISTS (
            SELECT 1
            FROM staff_permissions sp
            INNER JOIN permissions p2 ON p2.id = sp.permission_id
            WHERE sp.staff_id = s.id AND p2.perm_key = 'loan_manual_assign'
        )
      )
    ORDER BY s.name
");
if ($staff_res) {
    while ($row = mysqli_fetch_assoc($staff_res)) {
        $staff_list[] = $row;
    }
}

$query = "SELECT l.id, l.status, l.requested_amount, l.created_at,
                 c.full_name, c.phone,
                 sv.service_name,
                 st.name AS assigned_staff_name, st.id AS assigned_staff_id
          FROM loan_applications l
          INNER JOIN customers c ON c.id = l.customer_id
          INNER JOIN services sv ON sv.id = l.service_id
          LEFT JOIN staff st ON st.id = l.assigned_staff_id
          WHERE 1=1";

if ($search !== '') {
    $query .= " AND (c.full_name LIKE '%$search%' OR c.phone LIKE '%$search%' OR l.id LIKE '%$search%')";
}
if ($status !== '') {
    $query .= " AND l.status = '$status'";
}
$query .= " ORDER BY l.id DESC";

$loans = mysqli_query($conn, $query);
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Manual Loan Assign</h2>
                    <p class="text-muted small mb-0">Assign applications manually. Assigned staff receives email immediately.</p>
                </div>
            </div>

            <?php if (!empty($_GET['msg'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['err'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['err']) ?></div>
            <?php endif; ?>

            <div class="card border mb-3">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Loan ID, customer, phone" value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                <option value="disbursed" <?= $status === 'disbursed' ? 'selected' : '' ?>>Disbursed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-dark w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="manual_loan_assign.php" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-3">Loan</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Current Staff</th>
                                    <th class="pe-3">Assign</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if ($loans && mysqli_num_rows($loans) > 0): ?>
                                <?php while ($loan = mysqli_fetch_assoc($loans)): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold">#L-<?= (int)$loan['id'] ?></div>
                                            <div class="small text-muted"><?= date('d M Y', strtotime($loan['created_at'])) ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold"><?= htmlspecialchars($loan['full_name']) ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($loan['phone']) ?></div>
                                        </td>
                                        <td><?= htmlspecialchars($loan['service_name']) ?></td>
                                        <td>INR <?= format_inr((float)$loan['requested_amount'], 2) ?></td>
                                        <td><span class="badge bg-light text-dark text-capitalize"><?= htmlspecialchars($loan['status']) ?></span></td>
                                        <td><?= htmlspecialchars($loan['assigned_staff_name'] ?: 'Unassigned') ?></td>
                                        <td class="pe-3" style="min-width:260px;">
                                            <form action="db/loan_handler.php" method="POST" class="d-flex gap-2">
                                                <input type="hidden" name="action" value="assign_staff">
                                                <input type="hidden" name="loan_id" value="<?= (int)$loan['id'] ?>">
                                                <input type="hidden" name="redirect_to" value="manual">
                                                <select name="staff_id" class="form-select form-select-sm" required>
                                                    <option value="0">Unassign</option>
                                                    <?php foreach ($staff_list as $s): ?>
                                                        <option value="<?= (int)$s['id'] ?>" <?= (int)$loan['assigned_staff_id'] === (int)$s['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['email']) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-dark">Save</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No loan applications found.</td>
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
