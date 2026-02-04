<?php
include 'db/config.php';
include 'header.php';

// Search logic
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = $search ? "WHERE s.name LIKE '%$search%' OR s.email LIKE '%$search%'" : "";

// Fetch Staff with Joins (No foreign keys, just matching IDs)
$query = "
    SELECT 
        s.*, 
        d.name AS department_name, 
        a.name AS creator_name 
    FROM staff s
    LEFT JOIN departments d ON s.department_id = d.id
    LEFT JOIN admin a ON s.created_by = a.id
    $where_clause
    ORDER BY s.id DESC
";

$result = mysqli_query($conn, $query);
?>

<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-0">Staff Directory</h2>
                    <p class="text-muted small">View and manage all staff members and their basic roles.</p>
                </div>
                <a href="staff_add.php" class="btn btn-submit-pro">
                    <i class="fas fa-plus-circle me-1"></i> Add New Staff
                </a>
            </div>

            <div class="filter-card mb-4" style="background: #fff; border: 1px solid var(--slate-200); padding: 15px; border-radius: 12px;">
                <form method="GET" class="row g-2">
                    <div class="col-md-10">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark w-100">Search</button>
                    </div>
                </form>
            </div>

            <div class="card card-modern">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-4">Staff Name</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Joined Date</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($row['name']) ?></div>
                                                <div class="text-muted small"><?= htmlspecialchars($row['email']) ?></div>
                                            </td>
                                            <td>
                                                <span class="badge-dept"><?= htmlspecialchars($row['department_name'] ?? 'Unassigned') ?></span>
                                            </td>
                                            <td>
                                                <?php if($row['status'] == 'active'): ?>
                                                    <span class="badge rounded-pill bg-success-subtle text-success px-3">Active</span>
                                                <?php else: ?>
                                                    <span class="badge rounded-pill bg-danger-subtle text-danger px-3">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="small fw-semibold text-muted">
                                                    <i class="fas fa-user-shield me-1"></i> <?= htmlspecialchars($row['creator_name'] ?? 'System') ?>
                                                </div>
                                            </td>
                                            <td class="text-muted small">
                                                <?= date('d M, Y', strtotime($row['created_at'])) ?>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="manage_permissions.php?staff_id=<?= $row['id'] ?>" class="btn btn-sm btn-light border me-1" title="Manage Access">
                                                    <i class="fas fa-key text-primary"></i>
                                                </a>
                                                <a href="db/delete/staff_delete.php?id=<?= $row['id'] ?>" 
                                                   onclick="return confirm('Are you sure you want to delete this staff member? All their specific permissions will also be removed.')" 
                                                   class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">No staff members found.</td>
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