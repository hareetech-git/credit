<?php
include 'db/config.php';
include 'header.php';

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
$search = mysqli_real_escape_string($conn, trim((string)($_GET['search'] ?? '')));
$status_param = trim((string)($_GET['status'] ?? ''));
if ($status_param === 'new') {
    $status_param = 'pending';
}
$allowed_statuses = ['pending', 'approved', 'rejected', 'disbursed'];
if (!in_array($status_param, $allowed_statuses, true)) {
    $status_param = '';
}

// Supports both cat_id (new) and category_id (legacy).
$category_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : (isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0);
$active_category_name = '';
$category_list = [];

$catListRes = mysqli_query($conn, "SELECT id, category_name FROM service_categories WHERE active = 1 ORDER BY sequence ASC, category_name ASC");
if ($catListRes) {
    while ($catRow = mysqli_fetch_assoc($catListRes)) {
        $category_list[] = $catRow;
    }
}
if ($category_id > 0) {
    $catNameRes = mysqli_query($conn, "SELECT category_name FROM service_categories WHERE id = $category_id LIMIT 1");
    if ($catNameRes && mysqli_num_rows($catNameRes) > 0) {
        $active_category_name = (string)(mysqli_fetch_assoc($catNameRes)['category_name'] ?? '');
    }
}

$allowed_sort_columns = ['c.full_name', 'l.requested_amount', 'l.created_at'];
$sort_column = (string)($_GET['sort'] ?? 'l.created_at');
if (!in_array($sort_column, $allowed_sort_columns, true)) {
    $sort_column = 'l.created_at';
}
$sort_order = strtoupper((string)($_GET['order'] ?? 'DESC')) === 'ASC' ? 'ASC' : 'DESC';
$next_order = ($sort_order === 'ASC') ? 'DESC' : 'ASC';

$status_filter_sql = $status_param !== '' ? " AND l.status = '" . mysqli_real_escape_string($conn, $status_param) . "'" : '';
$category_filter_sql = $category_id > 0 ? " AND s.category_id = $category_id" : '';
$search_filter_sql = '';
if ($search !== '') {
    $search_filter_sql = " AND (c.full_name LIKE '%$search%' OR c.phone LIKE '%$search%' OR l.id LIKE '%$search%')";
}

$query = "SELECT l.*, c.full_name, c.phone, s.service_name
          FROM loan_applications l
          JOIN customers c ON l.customer_id = c.id
          JOIN services s ON l.service_id = s.id
          WHERE l.assigned_staff_id = $staff_id$status_filter_sql$category_filter_sql$search_filter_sql
          ORDER BY $sort_column $sort_order";
$result = mysqli_query($conn, $query);
$sr_no = 1;

function sortLink($col, $label, $current_col, $current_order, $next_order, $status_param, $category_id, $search) {
    $icon = ($current_col === $col)
        ? ($current_order === 'ASC' ? ' <i class="fas fa-sort-up"></i>' : ' <i class="fas fa-sort-down"></i>')
        : ' <i class="fas fa-sort text-muted opacity-50"></i>';

    $params = [
        'sort' => $col,
        'order' => $next_order,
    ];
    if ($status_param !== '') {
        $params['status'] = $status_param;
    }
    if ($category_id > 0) {
        $params['cat_id'] = $category_id;
    }
    if ($search !== '') {
        $params['search'] = $search;
    }

    return '<a href="?' . http_build_query($params) . '" class="sort-link text-decoration-none">' . $label . $icon . '</a>';
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

    .avatar-init {
        width: 38px; height: 38px;
        background: var(--slate-900);
        color: white;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.85rem;
    }

    .st-badge {
        padding: 5px 12px; border-radius: 50px; font-weight: 700; font-size: 0.65rem;
        text-transform: uppercase; border: 1px solid transparent;
    }
    .st-pending { background: #fffbeb; color: #92400e; border-color: #fef3c7; }
    .st-approved { background: #f0fdf4; color: #16a34a; border-color: #dcfce7; }
    .st-rejected { background: #fef2f2; color: #dc2626; border-color: #fee2e2; }
    .st-disbursed { background: #eff6ff; color: #1d4ed8; border-color: #dbeafe; }

    .btn-action {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; border: 1px solid var(--slate-200);
        background: white; color: var(--slate-600);
        transition: 0.2s; text-decoration: none;
    }
    .btn-action:hover { background: var(--slate-900); color: white; border-color: var(--slate-900); }
    .btn-delete:hover { background: #ef4444; border-color: #ef4444; }

    .active-filter-badge {
        background: var(--blue-600);
        color: white;
        padding: 2px 10px;
        border-radius: 4px;
        font-size: 0.7rem;
        margin-left: 10px;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">
                        Assigned Workload
                        <?php if ($category_id > 0 && $active_category_name !== ''): ?>
                            <span class="active-filter-badge"><?= htmlspecialchars($active_category_name) ?></span>
                        <?php endif; ?>
                    </h2>
                    <p class="text-muted small mb-0">
                        <?php if ($category_id > 0 && $active_category_name !== ''): ?>
                            Manage and process <?= htmlspecialchars($active_category_name) ?> applications assigned to your desk.
                        <?php else: ?>
                            Manage and process loan applications assigned to your desk.
                        <?php endif; ?>
                    </p>
                </div>
                <div class="bg-white border rounded-pill px-3 py-1 shadow-sm small fw-bold">
                    <i class="fas fa-user-tie me-1 text-primary"></i> Staff Dashboard
                </div>
            </div>

            <div class="card card-modern mb-3">
                <div class="card-body p-3">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold uppercase">Search</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, Phone or ID..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold uppercase">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Status</option>
                                <option value="pending" <?= $status_param === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="approved" <?= $status_param === 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="rejected" <?= $status_param === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                <option value="disbursed" <?= $status_param === 'disbursed' ? 'selected' : '' ?>>Disbursed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-2 d-flex gap-2">
                            <button class="btn btn-sm btn-dark w-100 shadow-sm"><i class="fas fa-filter me-1"></i> Apply</button>
                            <a href="loan_applications.php" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                                    <th width="60">Sr.</th>
                                    <th><?= sortLink('c.full_name', 'Customer', $sort_column, $sort_order, $next_order, $status_param, $category_id, $search) ?></th>
                                    <th>Service</th>
                                    <th><?= sortLink('l.requested_amount', 'Amount', $sort_column, $sort_order, $next_order, $status_param, $category_id, $search) ?></th>
                                    <th>Interest</th>
                                    <th>Status</th>
                                    <th><?= sortLink('l.created_at', 'Date Applied', $sort_column, $sort_order, $next_order, $status_param, $category_id, $search) ?></th>
                                    <th width="100" class="text-end">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$result || mysqli_num_rows($result) === 0) : ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                            <?php if ($category_id > 0 && $active_category_name !== ''): ?>
                                                <p>No <?= htmlspecialchars($active_category_name) ?> applications are currently assigned to you.</p>
                                            <?php else: ?>
                                                <p>No loan applications are currently assigned to you.</p>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php if ($result): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                        <tr>
                                            <td class="fw-bold text-muted"><?= $sr_no++ ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-init me-3"><?= strtoupper(substr((string)$row['full_name'], 0, 1)) ?></div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= htmlspecialchars((string)$row['full_name']) ?></div>
                                                        <div class="small text-muted"><?= htmlspecialchars((string)$row['phone']) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="text-secondary fw-medium"><?= htmlspecialchars((string)$row['service_name']) ?></span></td>
                                            <td class="fw-bold text-dark">₹<?= format_inr($row['requested_amount']) ?></td>
                                            <td class="text-muted"><?= number_format((float)$row['interest_rate'], 2) ?>%</td>
                                            <td>
                                                <span class="st-badge st-<?= htmlspecialchars((string)$row['status']) ?>">
                                                    <?= ucfirst((string)$row['status']) ?>
                                                </span>
                                            </td>
                                            <td class="small text-muted"><?= date('d M, Y', strtotime((string)$row['created_at'])) ?></td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="loan_view.php?id=<?= (int)$row['id'] ?>" class="btn-action" title="Process Application">
                                                        <i class="fas fa-file-signature"></i>
                                                    </a>
                                                    <?php if ($can_delete) : ?>
                                                        <form action="db/loan_handler.php" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this application?');">
                                                            <input type="hidden" name="action" value="delete_loan">
                                                            <input type="hidden" name="loan_id" value="<?= (int)$row['id'] ?>">
                                                            <button type="submit" class="btn-action btn-delete" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
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
