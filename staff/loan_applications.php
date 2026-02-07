<?php
include 'db/config.php';
include 'header.php';

// Ensure FontAwesome is loaded
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">';
include 'topbar.php';
include 'sidebar.php';

if (!hasAccess($conn, 'loan_view')) {
    header('Location: dashboard.php?err=Access denied');
    exit();
}

$staff_id = (int)$_SESSION['staff_id'];
$can_delete = hasAccess($conn, 'loan_delete');

// --- FILTERS & SORTING ---
$sort_column = $_GET['sort'] ?? 'l.created_at';
$sort_order = $_GET['order'] ?? 'DESC';
$next_order = ($sort_order == 'ASC') ? 'DESC' : 'ASC';

$status_param = $_GET['status'] ?? '';
if ($status_param === 'new') {
    $status_param = 'pending';
}
$allowed_statuses = ['pending', 'approved', 'rejected', 'disbursed'];
$status_filter = '';
if (!empty($status_param) && in_array($status_param, $allowed_statuses, true)) {
    $status_filter = " AND l.status = '$status_param'";
}

// Build Query
$query = "SELECT l.*, c.full_name, c.phone, s.service_name 
          FROM loan_applications l
          JOIN customers c ON l.customer_id = c.id
          JOIN services s ON l.service_id = s.id
          WHERE l.assigned_staff_id = $staff_id$status_filter
          ORDER BY $sort_column $sort_order";
$result = mysqli_query($conn, $query);

// Serial Counter
$sr_no = 1;

// Helper for Sort Links
function sortLink($col, $label, $current_col, $current_order, $next_order, $status_param) {
    $icon = ($current_col == $col) ? ($current_order == 'ASC' ? ' <i class="fas fa-sort-up"></i>' : ' <i class="fas fa-sort-down"></i>') : ' <i class="fas fa-sort text-muted opacity-50"></i>';
    $status_q = !empty($status_param) ? '&status='.urlencode($status_param) : '';
    return '<a href="?sort='.$col.'&order='.$next_order.$status_q.'" class="sort-link text-decoration-none">' . $label . $icon . '</a>';
}
?>
<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
        --blue-600: #2563eb;
    }
    .content-page { background-color: #fcfcfd; }
    
    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
    }

    /* Table Styling */
    .table-modern thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: var(--slate-600);
        padding: 16px 20px;
        border: none;
    }
    
    .table-modern tbody td {
        padding: 16px 20px;
        font-size: 0.88rem;
        color: var(--slate-900);
        border-bottom: 1px solid var(--slate-200);
        vertical-align: middle;
    }

    .sort-link { color: inherit; }
    .sort-link:hover { color: var(--blue-600); }

    /* Avatar Initial Circle */
    .avatar-init {
        width: 38px; height: 38px;
        background: var(--slate-900);
        color: white;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.85rem;
    }

    /* Status Pill Badges */
    .st-badge {
        padding: 5px 12px; border-radius: 50px; font-weight: 700; font-size: 0.65rem;
        text-transform: uppercase; border: 1px solid transparent;
    }
    .st-pending { background: #fffbeb; color: #92400e; border-color: #fef3c7; }
    .st-approved { background: #f0fdf4; color: #16a34a; border-color: #dcfce7; }
    .st-rejected { background: #fef2f2; color: #dc2626; border-color: #fee2e2; }
    .st-disbursed { background: #eff6ff; color: #1d4ed8; border-color: #dbeafe; }

    /* Action Buttons */
    .btn-action {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; border: 1px solid var(--slate-200);
        background: white; color: var(--slate-600);
        transition: 0.2s; text-decoration: none;
    }
    .btn-action:hover { background: var(--slate-900); color: white; border-color: var(--slate-900); }
    .btn-delete:hover { background: #ef4444; border-color: #ef4444; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Assigned Workload</h2>
                    <p class="text-muted small mb-0">Manage and process loan applications assigned to your desk.</p>
                </div>
                <div class="bg-white border rounded-pill px-3 py-1 shadow-sm small fw-bold">
                    <i class="fas fa-user-tie me-1 text-primary"></i> Staff Dashboard
                </div>
            </div>

            <div class="card card-modern">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th width="60">Sr.</th>
                                    <th><?= sortLink('c.full_name', 'Customer', $sort_column, $sort_order, $next_order, $status_param) ?></th>
                                    <th>Service</th>
                                    <th><?= sortLink('l.requested_amount', 'Amount', $sort_column, $sort_order, $next_order, $status_param) ?></th>
                                    <th>Interest</th>
                                    <th>Status</th>
                                    <th><?= sortLink('l.created_at', 'Date Applied', $sort_column, $sort_order, $next_order, $status_param) ?></th>
                                    <th width="100" class="text-end">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) == 0) : ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                            <p>No loan applications are currently assigned to you.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                    <tr>
                                        <td class="fw-bold text-muted"><?= $sr_no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-init me-3"><?= strtoupper(substr($row['full_name'], 0, 1)) ?></div>
                                                <div>
                                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['full_name']) ?></div>
                                                    <div class="small text-muted"><?= htmlspecialchars($row['phone']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="text-secondary fw-medium"><?= htmlspecialchars($row['service_name']) ?></span></td>
                                        <td class="fw-bold text-dark">₹<?= format_inr($row['requested_amount']) ?></td>
                                        <td class="text-muted"><?= number_format((float)$row['interest_rate'], 2) ?>%</td>
                                        <td>
                                            <span class="st-badge st-<?= $row['status'] ?>">
                                                <?= ucfirst($row['status']) ?>
                                            </span>
                                        </td>
                                        <td class="small text-muted"><?= date('d M, Y', strtotime($row['created_at'])) ?></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="loan_view.php?id=<?= $row['id'] ?>" class="btn-action" title="Process Application">
                                                    <i class="fas fa-file-signature"></i>
                                                </a>
                                                <?php if ($can_delete) : ?>
                                                    <form action="db/loan_handler.php" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this application?');">
                                                        <input type="hidden" name="action" value="delete_loan">
                                                        <input type="hidden" name="loan_id" value="<?= $row['id'] ?>">
                                                        <button type="submit" class="btn-action btn-delete" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>GET['status'] ?? '';
if ($status_param === 'new') {
    $status_param = 'pending';
}
$allowed_statuses = ['pending', 'approved', 'rejected', 'disbursed'];
$status_filter = '';
if (!empty($status_param) && in_array($status_param, $allowed_statuses, true)) {
    $status_filter = " AND l.status = '$status_param'";
}

// Build Query
$query = "SELECT l.*, c.full_name, c.phone, s.service_name 
          FROM loan_applications l
          JOIN customers c ON l.customer_id = c.id
          JOIN services s ON l.service_id = s.id
          WHERE l.assigned_staff_id = $staff_id$status_filter
          ORDER BY $sort_column $sort_order";
$result = mysqli_query($conn, $query);

// Serial Counter
$sr_no = 1;

// Helper for Sort Links
function sortLink($col, $label, $current_col, $current_order, $next_order, $status_param) {
    $icon = ($current_col == $col) ? ($current_order == 'ASC' ? ' <i class="fas fa-sort-up"></i>' : ' <i class="fas fa-sort-down"></i>') : ' <i class="fas fa-sort text-muted opacity-50"></i>';
    $status_q = !empty($status_param) ? '&status='.urlencode($status_param) : '';
    return '<a href="?sort='.$col.'&order='.$next_order.$status_q.'" class="sort-link text-decoration-none">' . $label . $icon . '</a>';
}
?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
        --blue-600: #2563eb;
    }
    .content-page { background-color: #fcfcfd; }
    
    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
    }

    /* Table Styling */
    .table-modern thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: var(--slate-600);
        padding: 16px 20px;
        border: none;
    }
    
    .table-modern tbody td {
        padding: 16px 20px;
        font-size: 0.88rem;
        color: var(--slate-900);
        border-bottom: 1px solid var(--slate-200);
        vertical-align: middle;
    }

    .sort-link { color: inherit; }
    .sort-link:hover { color: var(--blue-600); }

    /* Avatar Initial Circle */
    .avatar-init {
        width: 38px; height: 38px;
        background: var(--slate-900);
        color: white;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.85rem;
    }

    /* Status Pill Badges */
    .st-badge {
        padding: 5px 12px; border-radius: 50px; font-weight: 700; font-size: 0.65rem;
        text-transform: uppercase; border: 1px solid transparent;
    }
    .st-pending { background: #fffbeb; color: #92400e; border-color: #fef3c7; }
    .st-approved { background: #f0fdf4; color: #16a34a; border-color: #dcfce7; }
    .st-rejected { background: #fef2f2; color: #dc2626; border-color: #fee2e2; }
    .st-disbursed { background: #eff6ff; color: #1d4ed8; border-color: #dbeafe; }

    /* Action Buttons */
    .btn-action {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; border: 1px solid var(--slate-200);
        background: white; color: var(--slate-600);
        transition: 0.2s; text-decoration: none;
    }
    .btn-action:hover { background: var(--slate-900); color: white; border-color: var(--slate-900); }
    .btn-delete:hover { background: #ef4444; border-color: #ef4444; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Assigned Workload</h2>
                    <p class="text-muted small mb-0">Manage and process loan applications assigned to your desk.</p>
                </div>
                <div class="bg-white border rounded-pill px-3 py-1 shadow-sm small fw-bold">
                    <i class="fas fa-user-tie me-1 text-primary"></i> Staff Dashboard
                </div>
            </div>

            <div class="card card-modern">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th width="60">Sr.</th>
                                    <th><?= sortLink('c.full_name', 'Customer', $sort_column, $sort_order, $next_order, $status_param) ?></th>
                                    <th>Service</th>
                                    <th><?= sortLink('l.requested_amount', 'Amount', $sort_column, $sort_order, $next_order, $status_param) ?></th>
                                    <th>Interest</th>
                                    <th>Status</th>
                                    <th><?= sortLink('l.created_at', 'Date Applied', $sort_column, $sort_order, $next_order, $status_param) ?></th>
                                    <th width="100" class="text-end">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) == 0) : ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                            <p>No loan applications are currently assigned to you.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                    <tr>
                                        <td class="fw-bold text-muted"><?= $sr_no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-init me-3"><?= strtoupper(substr($row['full_name'], 0, 1)) ?></div>
                                                <div>
                                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['full_name']) ?></div>
                                                    <div class="small text-muted"><?= htmlspecialchars($row['phone']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="text-secondary fw-medium"><?= htmlspecialchars($row['service_name']) ?></span></td>
                                        <td class="fw-bold text-dark">₹<?= format_inr($row['requested_amount']) ?></td>
                                        <td class="text-muted"><?= number_format((float)$row['interest_rate'], 2) ?>%</td>
                                        <td>
                                            <span class="st-badge st-<?= $row['status'] ?>">
                                                <?= ucfirst($row['status']) ?>
                                            </span>
                                        </td>
                                        <td class="small text-muted"><?= date('d M, Y', strtotime($row['created_at'])) ?></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="loan_view.php?id=<?= $row['id'] ?>" class="btn-action" title="Process Application">
                                                    <i class="fas fa-file-signature"></i>
                                                </a>
                                                <?php if ($can_delete) : ?>
                                                    <form action="db/loan_handler.php" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this application?');">
                                                        <input type="hidden" name="action" value="delete_loan">
                                                        <input type="hidden" name="loan_id" value="<?= $row['id'] ?>">
                                                        <button type="submit" class="btn-action btn-delete" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
