<?php
include 'db/config.php';
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

// Staff list
$staff_list = [];
$staff_res = mysqli_query($conn, "SELECT id, name FROM staff WHERE status='active' ORDER BY name");
while ($s = mysqli_fetch_assoc($staff_res)) {
    $staff_list[] = $s;
}

// Fetch Loans with Customer Name & Service Name
$query = "SELECT l.*, c.full_name, c.phone, s.service_name, st.name AS staff_name
          FROM loan_applications l
          JOIN customers c ON l.customer_id = c.id
          JOIN services s ON l.service_id = s.id
          LEFT JOIN staff st ON l.assigned_staff_id = st.id
          ORDER BY l.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            <h4 class="mb-4">Loan Applications</h4>

            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#App ID</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Interest Rate (p.a.)</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td>#L-<?= $row['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['full_name']) ?></strong><br>
                                        <small><?= htmlspecialchars($row['phone']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($row['service_name']) ?></td>
                                    <td>&#8377;<?= number_format($row['requested_amount']) ?></td>
                                    <td><?= number_format((float)$row['interest_rate'], 2) ?>%</td>
                                    <td>
                                        <?php
                                        $badges = [
                                            'pending' => 'bg-warning',
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            'disbursed' => 'bg-info'
                                        ];
                                        ?>
                                        <span class="badge <?= $badges[$row['status']] ?>"><?= ucfirst($row['status']) ?></span>
                                    </td>
                                    <td>
                                        <form action="db/loan_handler.php" method="POST" class="d-flex gap-2 align-items-center">
                                            <input type="hidden" name="action" value="assign_staff">
                                            <input type="hidden" name="loan_id" value="<?= $row['id'] ?>">
                                            <select name="staff_id" class="form-select form-select-sm">
                                                <option value="0">Unassigned</option>
                                                <?php foreach ($staff_list as $staff) { ?>
                                                    <option value="<?= $staff['id'] ?>" <?= ($row['assigned_staff_id'] == $staff['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($staff['name']) ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <button class="btn btn-sm btn-outline-primary">Assign</button>
                                        </form>
                                        <?php if (!empty($row['assigned_at'])) { ?>
                                            <small class="text-muted">Since <?= date('d M, Y', strtotime($row['assigned_at'])) ?></small>
                                        <?php } ?>
                                    </td>
                                    <td><?= date('d M, Y', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <a href="loan_view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Process</a>
                                        <form action="db/loan_handler.php" method="POST" class="d-inline-block ms-1" onsubmit="return confirm('Delete this loan application? This will also remove its documents.');">
                                            <input type="hidden" name="action" value="delete_loan">
                                            <input type="hidden" name="loan_id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
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
<?php include 'footer.php'; ?>
