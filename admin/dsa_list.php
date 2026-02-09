<?php
include 'db/config.php';
include 'header.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = $search ? "WHERE d.name LIKE '%$search%' OR d.email LIKE '%$search%' OR d.phone LIKE '%$search%'" : '';

$query = "SELECT d.*, dp.firm_name, dept.name AS department_name, a.name AS creator_name
          FROM dsa d
          LEFT JOIN dsa_profiles dp ON dp.dsa_id = d.id
          LEFT JOIN departments dept ON dept.id = d.department_id
          LEFT JOIN admin a ON a.id = d.created_by
          $where_clause
          ORDER BY d.id DESC";
$result = mysqli_query($conn, $query);
?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>
<div class="content-page"><div class="content"><div class="container-fluid pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">DSA Directory</h2>
            <p class="text-muted small">Manage all DSA agents and login accounts.</p>
        </div>
        <a href="dsa_add.php" class="btn btn-dark"><i class="fas fa-plus-circle me-1"></i> Add New DSA</a>
    </div>

    <div class="mb-4" style="background:#fff;border:1px solid #e2e8f0;padding:15px;border-radius:12px;">
        <form method="GET" class="row g-2">
            <div class="col-md-10"><input type="text" name="search" class="form-control" placeholder="Search by name, email, phone" value="<?= htmlspecialchars($search) ?>"></div>
            <div class="col-md-2"><button type="submit" class="btn btn-dark w-100">Search</button></div>
        </form>
    </div>

    <div class="card border"><div class="card-body p-0"><div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr><th class="ps-3">DSA Agent</th><th>Department</th><th>Firm</th><th>Status</th><th>Created By</th><th>Joined</th><th class="text-end pe-3">Actions</th></tr></thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td class="ps-3"><div class="fw-bold text-dark"><?= htmlspecialchars($row['name']) ?></div><div class="text-muted small"><?= htmlspecialchars($row['email']) ?> | <?= htmlspecialchars($row['phone']) ?></div></td>
                        <td><?= htmlspecialchars($row['department_name'] ?? 'Unassigned') ?></td>
                        <td><?= htmlspecialchars($row['firm_name'] ?? '-') ?></td>
                        <td><?php if($row['status'] === 'active'): ?><span class="badge rounded-pill bg-success-subtle text-success px-3">Active</span><?php else: ?><span class="badge rounded-pill bg-danger-subtle text-danger px-3">Inactive</span><?php endif; ?></td>
                        <td><span class="small text-muted"><?= htmlspecialchars($row['creator_name'] ?? 'System') ?></span></td>
                        <td class="small text-muted"><?= date('d M, Y', strtotime($row['created_at'])) ?></td>
                        <td class="text-end pe-3">
                            <a href="manage_dsa_permissions.php?dsa_id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-light border me-1" title="Manage Access"><i class="fas fa-key text-primary"></i></a>
                            <a href="dsa_add.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-light border me-1"><i class="fas fa-pen text-primary"></i></a>
                            <a href="db/delete/dsa_delete.php?id=<?= (int)$row['id'] ?>" onclick="return confirm('Delete this DSA account?')" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted">No DSA records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div></div></div>
</div></div></div>
<?php include 'footer.php'; ?>
