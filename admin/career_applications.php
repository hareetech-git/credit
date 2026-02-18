<?php
include 'db/config.php';
include 'header.php';
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">';
include 'topbar.php';
include 'sidebar.php';

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS career_applications (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(191) NOT NULL,
    resume_path VARCHAR(255) NOT NULL,
    resume_original_name VARCHAR(255) NOT NULL,
    status ENUM('new','assigned','closed') NOT NULL DEFAULT 'new',
    assigned_staff_id BIGINT UNSIGNED DEFAULT NULL,
    assigned_by BIGINT UNSIGNED DEFAULT NULL,
    assigned_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_career_status (status),
    KEY idx_career_assigned_staff (assigned_staff_id),
    KEY idx_career_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$status_filter = trim((string)($_GET['status'] ?? ''));
$where = "1=1";
if (in_array($status_filter, ['new', 'assigned', 'closed'], true)) {
    $status_safe = mysqli_real_escape_string($conn, $status_filter);
    $where .= " AND c.status = '$status_safe'";
}

$query = "SELECT c.*, s.name AS assigned_staff_name, s.email AS assigned_staff_email
          FROM career_applications c
          LEFT JOIN staff s ON s.id = c.assigned_staff_id
          WHERE $where
          ORDER BY c.id DESC";
$result = mysqli_query($conn, $query);

$staff_list = [];
$staff_res = mysqli_query($conn, "SELECT id, name FROM staff WHERE status='active' ORDER BY name ASC");
if ($staff_res) {
    while ($s = mysqli_fetch_assoc($staff_res)) {
        $staff_list[] = $s;
    }
}
?>

<style>
    .card-modern {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .table-modern thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        padding: 14px 18px;
    }
    .table-modern tbody td {
        padding: 14px 18px;
        vertical-align: middle;
        border-top: 1px solid #e2e8f0;
    }
    .badge-soft {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        border: 1px solid transparent;
    }
    .badge-new { background: #ecfeff; color: #155e75; border-color: #bae6fd; }
    .badge-assigned { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .badge-closed { background: #f3f4f6; color: #374151; border-color: #e5e7eb; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Career Applications</h2>
                    <p class="text-muted small mb-0">Review applicants and assign to staff for screening.</p>
                </div>
            </div>

            <?php if (!empty($_GET['msg'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars((string)$_GET['msg']); ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['err'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars((string)$_GET['err']); ?></div>
            <?php endif; ?>

            <div class="card card-modern mb-3">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All</option>
                                <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>New</option>
                                <option value="assigned" <?php echo $status_filter === 'assigned' ? 'selected' : ''; ?>>Assigned</option>
                                <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-dark btn-sm w-100">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-modern">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th width="80">ID</th>
                                    <th>Email</th>
                                    <th>Resume</th>
                                    <th>Status</th>
                                    <th>Assigned Staff</th>
                                    <th width="80">Action</th>

                                    <th>Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td class="fw-bold text-muted">#<?php echo (int)$row['id']; ?></td>
                                            <td><?php echo htmlspecialchars((string)$row['email']); ?></td>
                                            <td>
                                                <a class="btn btn-sm btn-outline-primary"
                                                   target="_blank"
                                                   href="../<?php echo htmlspecialchars((string)$row['resume_path']); ?>">
                                                    <i class="fas fa-file-pdf me-1"></i> View PDF
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge-soft badge-<?php echo htmlspecialchars((string)$row['status']); ?>">
                                                    <?php echo ucfirst((string)$row['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="small fw-bold"><?php echo htmlspecialchars((string)($row['assigned_staff_name'] ?: 'Unassigned')); ?></div>
                                                <div class="small text-muted"><?php echo htmlspecialchars((string)($row['assigned_staff_email'] ?: '')); ?></div>
                                                <form method="POST" action="db/career_assign.php" class="mt-2">
                                                    <input type="hidden" name="career_id" value="<?php echo (int)$row['id']; ?>">
                                                    <div class="input-group input-group-sm">
                                                        <select name="staff_id" class="form-select form-select-sm">
                                                            <option value="0">Unassigned</option>
                                                            <?php foreach ($staff_list as $s): ?>
                                                                <option value="<?php echo (int)$s['id']; ?>" <?php echo ((int)$row['assigned_staff_id'] === (int)$s['id']) ? 'selected' : ''; ?>>
                                                                    <?php echo htmlspecialchars((string)$s['name']); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <button class="btn btn-sm btn-dark" type="submit">Assign</button>
                                                    </div>
                                                </form>
                                            </td>
                                                                                    <td>
    <a href="db/delete/delete_career_application.php?id=<?php echo (int)$row['id']; ?>"
       class="btn btn-sm btn-outline-danger"
       onclick="return confirm('Are you sure you want to delete this application?');">
        <i class="fas fa-trash"></i>
    </a>
</td>
                                            <td class="small text-muted">
                                                <?php echo date('d M Y', strtotime((string)$row['created_at'])); ?><br>
                                                <?php echo date('h:i A', strtotime((string)$row['created_at'])); ?>
                                            </td>
    

                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">No career applications found.</td>
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

