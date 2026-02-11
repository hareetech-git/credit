<?php
include 'db/config.php';
include 'header.php';

// Ensure FontAwesome is loaded
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">';
include 'topbar.php';
include 'sidebar.php';

// --- FILTERS & SORTING LOGIC ---
$search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
$status = mysqli_real_escape_string($conn, $_GET['status'] ?? '');
$staff  = mysqli_real_escape_string($conn, $_GET['staff'] ?? '');

// Sorting Logic
$sort_column = $_GET['sort'] ?? 'l.id'; 
$sort_order = $_GET['order'] ?? 'DESC'; 
$next_order = ($sort_order == 'ASC') ? 'DESC' : 'ASC';

// Staff list for filter/assignment (only staff with loan processing access)
$staff_list = [];
$staff_res = mysqli_query($conn, "
    SELECT DISTINCT s.id, s.name
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
    ORDER BY s.name
");
while ($s = mysqli_fetch_assoc($staff_res)) {
    $staff_list[] = $s;
}

// Build Query
$query = "SELECT l.*, c.full_name, c.phone, s.service_name, st.name AS staff_name,
                 d.id AS dsa_agent_id, d.name AS dsa_agent_name, d.phone AS dsa_agent_phone
          FROM loan_applications l
          JOIN customers c ON l.customer_id = c.id
          JOIN services s ON l.service_id = s.id
          LEFT JOIN staff st ON l.assigned_staff_id = st.id
          LEFT JOIN dsa d ON l.dsa_id = d.id
          WHERE 1=1";

if ($search != '') {
    $query .= " AND (c.full_name LIKE '%$search%' OR c.phone LIKE '%$search%' OR l.id LIKE '%$search%')";
}
if ($status != '') {
    $query .= " AND l.status = '$status'";
}
if ($staff != '') {
    $query .= " AND l.assigned_staff_id = '$staff'";
}

$query .= " ORDER BY $sort_column $sort_order";
$result = mysqli_query($conn, $query);

// Serial Number Counter
$sr_no = 1;

// Helper function for sort links
function getSortUrl($col, $next_order, $search, $status, $staff) {
    return "loan_applications.php?sort=$col&order=$next_order&search=" . urlencode($search) . "&status=" . urlencode($status) . "&staff=" . urlencode($staff);
}
?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
        --blue-500: #3b82f6;
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

    .sort-link { color: var(--slate-600); text-decoration: none; display: flex; align-items: center; gap: 5px; }
    .sort-link:hover { color: var(--slate-900); }
    
    .table-modern tbody td {
        padding: 16px 20px;
        font-size: 0.88rem;
        color: var(--slate-900);
        border-bottom: 1px solid var(--slate-200);
        vertical-align: middle;
    }

    /* Status Badges */
    .badge-soft {
        padding: 5px 12px; border-radius: 6px; font-weight: 700; font-size: 0.7rem;
        text-transform: uppercase; border: 1px solid transparent;
    }
    .badge-pending { background: #fffbeb; color: #92400e; border-color: #fef3c7; }
    .badge-approved { background: #f0fdf4; color: #16a34a; border-color: #dcfce7; }
    .badge-rejected { background: #fef2f2; color: #dc2626; border-color: #fee2e2; }
    .badge-disbursed { background: #eff6ff; color: #1d4ed8; border-color: #dbeafe; }

    /* Action Buttons */
    .btn-action {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; border: 1px solid var(--slate-200);
        background: white; color: var(--slate-600);
        transition: all 0.2s; text-decoration: none;
    }
    .btn-view:hover { background: var(--blue-500); color: white; border-color: var(--blue-500); }
    .btn-delete:hover { background: #ef4444; color: white; border-color: #ef4444; }

    .avatar-init {
        width: 35px; height: 35px;
        background: var(--slate-200);
        color: var(--slate-900);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.8rem;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Loan Applications</h2>
                    <p class="text-muted small mb-0">Review and process incoming loan requests.</p>
                </div>
            </div>

            <?php if (!empty($_GET['msg'])) : ?>
                <div class="alert alert-success border-0 shadow-sm mb-4 py-3">
                    <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($_GET['msg']) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($_GET['err'])) : ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4 py-3">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($_GET['err']) ?>
                </div>
            <?php endif; ?>

            <div class="card card-modern mb-4">
                <div class="card-body p-3">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold uppercase">Search</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, Phone or ID..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold uppercase">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Status</option>
                                <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="approved" <?= $status == 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="rejected" <?= $status == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                <option value="disbursed" <?= $status == 'disbursed' ? 'selected' : '' ?>>Disbursed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold uppercase">Assigned Staff</label>
                            <select name="staff" class="form-select form-select-sm">
                                <option value="">All Staff</option>
                                <?php foreach ($staff_list as $st) : ?>
                                    <option value="<?= $st['id'] ?>" <?= $staff == $st['id'] ? 'selected' : '' ?>><?= $st['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button class="btn btn-sm btn-dark w-100 shadow-sm"><i class="fas fa-filter me-1"></i> Apply Filters</button>
                            <a href="loan_applications.php" class="btn btn-sm btn-outline-secondary w-50">Reset</a>
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
                                    <th width="70">Sr.</th>
                                    <th>
                                        <a href="<?= getSortUrl('full_name', $next_order, $search, $status, $staff) ?>" class="sort-link">
                                            Customer <?= $sort_column == 'full_name' ? ($sort_order == 'ASC' ? '↑' : '↓') : '' ?>
                                        </a>
                                    </th>
                                    <th>Service</th>
                                    <th>
                                        <a href="<?= getSortUrl('requested_amount', $next_order, $search, $status, $staff) ?>" class="sort-link">
                                            Amount <?= $sort_column == 'requested_amount' ? ($sort_order == 'ASC' ? '↑' : '↓') : '' ?>
                                        </a>
                                    </th>
                                    <th>Status</th>
                                    <th>Lead Source</th>
                                    <th>Assigned To</th>
                                    <th class="text-end">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0) : ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                        <tr>
                                            <td class="text-muted fw-bold"><?= $sr_no++ ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-init me-2"><?= strtoupper(substr($row['full_name'], 0, 1)) ?></div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['full_name']) ?></div>
                                                        <div class="text-muted" style="font-size: 0.75rem;"><i class="fas fa-phone-alt"></i> <?= $row['phone'] ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="text-muted fw-medium"><?= htmlspecialchars($row['service_name']) ?></span></td>
                                            <td class="fw-bold text-dark">₹<?= format_inr($row['requested_amount']) ?></td>
                                            <td>
                                                <span class="badge-soft badge-<?= $row['status'] ?>">
                                                    <?= ucfirst($row['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($row['dsa_agent_id'])): ?>
                                                    <div class="small fw-bold text-dark">DSA: <?= htmlspecialchars((string)$row['dsa_agent_name']) ?></div>
                                                    <a href="dsa_view.php?id=<?= (int)$row['dsa_agent_id'] ?>" class="small text-decoration-none">
                                                        View DSA Details
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted small">Direct / Website Lead</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <form action="db/loan_handler.php" method="POST" class="d-flex gap-1 align-items-center">
                                                    <input type="hidden" name="action" value="assign_staff">
                                                    <input type="hidden" name="loan_id" value="<?= $row['id'] ?>">
                                                    <select name="staff_id" class="form-select form-select-sm py-0" style="font-size: 0.75rem; width: 110px; height: 28px;">
                                                        <option value="0">Unassigned</option>
                                                        <?php foreach ($staff_list as $st_opt) : ?>
                                                            <option value="<?= $st_opt['id'] ?>" <?= ($row['assigned_staff_id'] == $st_opt['id']) ? 'selected' : '' ?>>
                                                                <?= $st_opt['name'] ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <button class="btn btn-dark btn-sm p-0" style="width:28px; height:28px;"><i class="fas fa-check" style="font-size: 0.6rem;"></i></button>
                                                </form>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="loan_view.php?id=<?= $row['id'] ?>" class="btn-action btn-view" title="Process"><i class="fas fa-eye"></i></a>
                                                    <form action="db/loan_handler.php" method="POST" onsubmit="return confirm('Delete application?');">
                                                        <input type="hidden" name="action" value="delete_loan">
                                                        <input type="hidden" name="loan_id" value="<?= $row['id'] ?>">
                                                        <button type="submit" class="btn-action btn-delete"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="fas fa-file-invoice-dollar fa-3x mb-3 text-light"></i>
                                            <p>No loan applications found.</p>
                                        </td>
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
