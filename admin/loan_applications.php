<?php
include 'db/config.php';
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

// Fetch Loans with Customer Name & Service Name
$query = "SELECT l.*, c.full_name, c.phone, s.service_name 
          FROM loan_applications l
          JOIN customers c ON l.customer_id = c.id
          JOIN services s ON l.service_id = s.id
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
                                <th>Status</th>
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
                                    <td>₹<?= number_format($row['requested_amount']) ?></td>
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