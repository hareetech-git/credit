<?php
include 'db/config.php';
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

if (!hasAccess($conn, 'loan_view')) {
    header('Location: dashboard.php?err=Access denied');
    exit();
}

$staff_id = (int)$_SESSION['staff_id'];
$can_delete = hasAccess($conn, 'loan_delete');

$query = "SELECT l.*, c.full_name, c.phone, s.service_name 
          FROM loan_applications l
          JOIN customers c ON l.customer_id = c.id
          JOIN services s ON l.service_id = s.id
          WHERE l.assigned_staff_id = $staff_id
          ORDER BY l.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            <h4 class="mb-4">Assigned Loan Applications</h4>

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
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) == 0) { ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted p-4">No assigned loans yet.</td>
                                </tr>
                            <?php } ?>
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
                                    <td><?= date('d M, Y', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <a href="loan_view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Process</a>
                                        <?php if ($can_delete): ?>
                                            <form action="db/loan_handler.php" method="POST" class="d-inline-block ms-1" onsubmit="return confirm('Delete this loan application? This will also remove its documents.');">
                                                <input type="hidden" name="action" value="delete_loan">
                                                <input type="hidden" name="loan_id" value="<?= $row['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        <?php endif; ?>
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
