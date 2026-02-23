<?php
include 'db/config.php';
require_once __DIR__ . '/db/notification_helper.php';
include 'header.php';

// Ensure FontAwesome is loaded
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">';
include 'topbar.php';
include 'sidebar.php';

// --- FILTERS & SORTING LOGIC ---
$search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
$status = mysqli_real_escape_string($conn, $_GET['status'] ?? '');
$staff  = mysqli_real_escape_string($conn, $_GET['staff'] ?? '');
// Category filter supports both cat_id and legacy category_id.
$category_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : (isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0);
$active_category_name = '';
$category_list = [];
if ($category_id > 0) {
    $catNameRes = mysqli_query($conn, "SELECT category_name FROM service_categories WHERE id = $category_id LIMIT 1");
    if ($catNameRes && mysqli_num_rows($catNameRes) > 0) {
        $active_category_name = (string)(mysqli_fetch_assoc($catNameRes)['category_name'] ?? '');
    }
}
// Category list for filter dropdown.
$catListRes = mysqli_query($conn, "SELECT id, category_name FROM service_categories WHERE active = 1 ORDER BY sequence ASC, category_name ASC");
if ($catListRes) {
    while ($catRow = mysqli_fetch_assoc($catListRes)) {
        $category_list[] = $catRow;
    }
}

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
// NEW: Add category filter
if ($category_id > 0) {
    $query .= " AND s.category_id = $category_id";
}

$query .= " ORDER BY $sort_column $sort_order";
$result = mysqli_query($conn, $query);
$readFlagsReady = adminNotificationsReady($conn);

// Serial Number Counter
$sr_no = 1;

// Helper function for sort links.
function getSortUrl($col, $next_order, $search, $status, $staff, $category_id) {
    $params = [
        'sort' => $col,
        'order' => $next_order,
        'search' => $search,
        'status' => $status,
        'staff' => $staff
    ];
    // Keep category filter in URL.
    if ($category_id > 0) {
        $params['cat_id'] = $category_id;
    }
    return "loan_applications.php?" . http_build_query($params);
}
?>

<style>
    /* ... (keep all your existing styles) ... */
    
    /* NEW: Add a style to show active filter indicator */
    .active-filter-badge {
        background: var(--blue-500);
        color: white;
        padding: 2px 10px;
        border-radius: 4px;
        font-size: 0.7rem;
        margin-left: 10px;
    }

    .row-unread {
        background: #eff6ff;
    }
    .notice-pill {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 2px 8px;
        border-radius: 999px;
        margin-top: 4px;
    }
    .notice-pill.unread {
        background: #dbeafe;
        color: #1d4ed8;
        border: 1px solid #93c5fd;
    }
    .notice-pill.read {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">
                        Loan Applications
                        <?php if ($category_id > 0 && $active_category_name !== ''): ?>
                            <span class="active-filter-badge"><?= htmlspecialchars($active_category_name) ?></span>
                        <?php endif; ?>
                    </h2>
                    <p class="text-muted small mb-0">
                        <?php if ($category_id > 0 && $active_category_name !== ''): ?>
                            Showing only <?= htmlspecialchars($active_category_name) ?> applications
                        <?php else: ?>
                            Review and process incoming loan requests.
                        <?php endif; ?>
                    </p>
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
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold uppercase">Assigned Staff</label>
                            <select name="staff" class="form-select form-select-sm">
                                <option value="">All Staff</option>
                                <?php foreach ($staff_list as $st) : ?>
                                    <option value="<?= $st['id'] ?>" <?= $staff == $st['id'] ? 'selected' : '' ?>><?= $st['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold uppercase">Category</label>
                            <select name="cat_id" class="form-select form-select-sm">
                                <option value="">All Categories</option>
                                <?php foreach ($category_list as $cat): ?>
                                    <option value="<?= (int)$cat['id'] ?>" <?= ($category_id === (int)$cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string)$cat['category_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button class="btn btn-sm btn-dark w-100 shadow-sm"><i class="fas fa-filter me-1"></i> Apply Filters</button>
                            <?php if ($category_id > 0): ?>
                                <a href="loan_applications.php?cat_id=<?= $category_id ?>" class="btn btn-sm btn-outline-secondary w-50">Reset</a>
                            <?php else: ?>
                                <a href="loan_applications.php" class="btn btn-sm btn-outline-secondary w-50">Reset</a>
                            <?php endif; ?>
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
                                        <a href="<?= getSortUrl('full_name', $next_order, $search, $status, $staff, $category_id) ?>" class="sort-link">
                                            Customer <?= $sort_column == 'full_name' ? ($sort_order == 'ASC' ? '↑' : '↓') : '' ?>
                                        </a>
                                    </th>
                                    <th>Service</th>
                                    <th>
                                        <a href="<?= getSortUrl('requested_amount', $next_order, $search, $status, $staff, $category_id) ?>" class="sort-link">
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
                                        <?php $isUnread = $readFlagsReady && ((int)($row['is_read'] ?? 0) === 0); ?>
                                        <tr class="<?= $isUnread ? 'row-unread' : '' ?>">
                                            <td class="text-muted fw-bold"><?= $sr_no++ ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-init me-2"><?= strtoupper(substr($row['full_name'], 0, 1)) ?></div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['full_name']) ?></div>
                                                        <div class="text-muted" style="font-size: 0.75rem;"><i class="fas fa-phone-alt"></i> <?= $row['phone'] ?></div>
                                                        <?php if ($readFlagsReady): ?>
                                                            <span class="notice-pill <?= $isUnread ? 'unread' : 'read' ?>">
                                                                <?= $isUnread ? 'Unread' : 'Read' ?>
                                                            </span>
                                                        <?php endif; ?>
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
                                            <?php if ($category_id > 0 && $active_category_name !== ''): ?>
                                                <p>No <?= htmlspecialchars($active_category_name) ?> applications found.</p>
                                            <?php else: ?>
                                                <p>No loan applications found.</p>
                                            <?php endif; ?>
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
