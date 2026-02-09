<?php
include 'header.php';
dsaRequireAccess($conn, 'dsa_lead_view');
include 'topbar.php';
include 'sidebar.php';

$dsa_id = (int)($_SESSION['dsa_id'] ?? 0);
$query = "SELECT la.*, c.full_name, c.phone, s.service_name
          FROM loan_applications la
          INNER JOIN customers c ON la.customer_id = c.id
          INNER JOIN services s ON la.service_id = s.id
          WHERE la.dsa_id = $dsa_id
          ORDER BY la.created_at DESC";
$result = mysqli_query($conn, $query);
?>
<div class="content-page"><div class="content"><div class="container-fluid pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h2 class="fw-bold text-dark mb-1">My Leads</h2><p class="text-muted small mb-0">Loan applications submitted through your DSA account.</p></div>
        <?php if (dsaHasAccess($conn, 'dsa_lead_create')): ?>
        <a href="../apply-loan.php" class="btn btn-dark">Add New Lead</a>
        <?php endif; ?>
    </div>
    <div class="card border"><div class="card-body p-0"><div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr><th class="ps-3">Lead</th><th>Service</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td class="ps-3"><div class="fw-semibold"><?= htmlspecialchars($row['full_name']) ?></div><div class="small text-muted"><?= htmlspecialchars($row['phone']) ?></div></td>
                    <td><?= htmlspecialchars($row['service_name']) ?></td>
                    <td>Rs <?= format_inr($row['requested_amount']) ?></td>
                    <td><span class="badge bg-secondary-subtle text-dark"><?= htmlspecialchars(ucfirst($row['status'])) ?></span></td>
                    <td><?= date('d M, Y', strtotime($row['created_at'])) ?></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="5" class="text-center py-4 text-muted">No leads found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div></div></div>
</div></div></div>
<?php include 'footer.php'; ?>
