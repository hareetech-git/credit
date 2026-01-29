<?php
include 'db/config.php';
include 'header.php';
// Ensure FontAwesome is loaded
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">';
include 'topbar.php';
include 'sidebar.php';

// --- FILTERS & SORTING LOGIC ---
$search_title = $_GET['search_title'] ?? '';
$search_cat   = (int)($_GET['category'] ?? 0);
$search_sub   = (int)($_GET['sub_category'] ?? 0);

$sort_by    = $_GET['sort_by'] ?? 'id';
$sort_order = $_GET['order'] ?? 'DESC';
$next_order = ($sort_order === 'ASC') ? 'DESC' : 'ASC';

// Build Query
$query = "SELECT s.*, c.category_name, sub.sub_category_name 
          FROM services s
          LEFT JOIN service_categories c ON s.category_id = c.id
          LEFT JOIN services_subcategories sub ON s.sub_category_id = sub.id
          WHERE 1=1";

if (!empty($search_title)) {
    $title_safe = mysqli_real_escape_string($conn, $search_title);
    $query .= " AND (s.title LIKE '%$title_safe%' OR s.service_name LIKE '%$title_safe%')";
}
if ($search_cat > 0) {
    $query .= " AND s.category_id = $search_cat";
}
if ($search_sub > 0) {
    $query .= " AND s.sub_category_id = $search_sub";
}

// Validate Sort Column to prevent SQL Injection
$allowed_sorts = ['id', 'title', 'service_name', 'created_at'];
if (!in_array($sort_by, $allowed_sorts)) $sort_by = 'id';

$query .= " ORDER BY s.$sort_by $sort_order";

$result = mysqli_query($conn, $query);

// Fetch Categories for Dropdown
$cats = mysqli_query($conn, "SELECT id, category_name FROM service_categories WHERE active=1");
$subcats = [];
if ($search_cat > 0) {
    $subcats = mysqli_query($conn, "SELECT id, sub_category_name FROM services_subcategories WHERE category_id=$search_cat");
}
?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
    }
    .content-page { background-color: #fcfcfd; }
    
    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
    }

    /* Table Headers */
    .table-modern thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: var(--slate-600);
        padding: 16px 24px;
        border: none;
        cursor: pointer; /* Clickable headers */
    }
    
    .table-modern thead th a {
        color: inherit;
        text-decoration: none;
        display: block;
    }
    
    .table-modern tbody td {
        padding: 16px 24px;
        font-size: 0.9rem;
        color: var(--slate-900);
        border-bottom: 1px solid var(--slate-200);
        vertical-align: middle;
    }

    /* Fixed Button Styling */
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
        border: 1px solid var(--slate-200);
        background: white;
        text-decoration: none;
    }

    .btn-view { color: #0ea5e9; }
    .btn-view:hover { background: #0ea5e9 !important; color: white !important; border-color: #0ea5e9; }

    .btn-edit-pro { color: var(--slate-900); }
    .btn-edit-pro:hover { background: var(--slate-900) !important; color: white !important; border-color: var(--slate-900); }

    .btn-delete-pro { color: #ef4444; }
    .btn-delete-pro:hover { background: #ef4444 !important; color: white !important; border-color: #ef4444; }

    .btn-add-pro {
        background: var(--slate-900);
        color: white !important;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        transition: opacity 0.2s;
    }
    .btn-add-pro:hover { opacity: 0.9; color: white !important; }

    .badge-soft {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid var(--slate-200);
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Service Inventory</h2>
                    <p class="text-muted small mb-0">Browse and manage all registered financial services.</p>
                </div>
                <a href="service_add.php" class="btn-add-pro text-decoration-none">
                    <i class="fas fa-plus me-1"></i> Add New Service
                </a>
            </div>

            <div class="card card-modern mb-4">
                <div class="card-body p-3">
                    <form method="GET" class="row g-3 align-items-end">
                        <input type="hidden" name="sort_by" value="<?= $sort_by ?>">
                        <input type="hidden" name="order" value="<?= $sort_order ?>">

                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Search</label>
                            <input type="text" name="search_title" class="form-control form-control-sm" placeholder="Title or Service Name" value="<?= htmlspecialchars($search_title) ?>">
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Category</label>
                            <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <?php while($c = mysqli_fetch_assoc($cats)) { ?>
                                    <option value="<?= $c['id'] ?>" <?= $search_cat == $c['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['category_name']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Subcategory</label>
                            <select name="sub_category" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Subcategories</option>
                                <?php if($search_cat > 0) { 
                                    while($s = mysqli_fetch_assoc($subcats)) { ?>
                                    <option value="<?= $s['id'] ?>" <?= $search_sub == $s['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['sub_category_name']) ?>
                                    </option>
                                <?php } } ?>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button class="btn btn-sm btn-dark w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                            <a href="services.php" class="btn btn-sm btn-outline-secondary w-50">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (isset($_GET['msg'])) { ?>
                <div class="alert alert-success border-0 shadow-sm mb-4 py-3">
                    <i class="fas fa-check-circle me-1"></i> <?= htmlspecialchars($_GET['msg']) ?>
                </div>
            <?php } ?>

            <div class="card card-modern">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th width="80">
                                        <a href="?sort_by=id&order=<?= $next_order ?>">ID <i class="fas fa-sort float-end"></i></a>
                                    </th>
                                    <th>
                                        <a href="?sort_by=service_name&order=<?= $next_order ?>">Service Name <i class="fas fa-sort float-end"></i></a>
                                    </th>
                                    <th>
                                        <a href="?sort_by=title&order=<?= $next_order ?>">Display Title <i class="fas fa-sort float-end"></i></a>
                                    </th>
                                    <th>Category</th>
                                    <th>Subcategory</th>
                                    <th>
                                        <a href="?sort_by=created_at&order=<?= $next_order ?>">Added <i class="fas fa-sort float-end"></i></a>
                                    </th>
                                    <th width="150" class="text-center">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr id="row-<?= $row['id'] ?>">
                                            <td class="text-muted fw-bold">#<?= $row['id'] ?></td>
                                            
                                            <td class="fw-semibold text-dark"><?= htmlspecialchars($row['service_name']) ?></td>
                                            
                                            <td class="text-primary"><?= htmlspecialchars($row['title']) ?></td>
                                            
                                            <td><span class="badge-soft"><?= htmlspecialchars($row['category_name']) ?></span></td>
                                            <td><span class="badge-soft"><?= htmlspecialchars($row['sub_category_name']) ?></span></td>
                                            <td class="text-muted small"><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                                            
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="service_details.php?id=<?= $row['id'] ?>" class="btn-action btn-view" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <a href="service_edit.php?service_id=<?= $row['id'] ?>" class="btn-action btn-edit-pro" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <a href="db/delete/service_delete.php?id=<?= $row['id'] ?>" 
                                                       class="btn-action btn-delete-pro" 
                                                       onclick="return confirm('This will permanently delete this service and all its details. Continue?');"
                                                       title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <div class="mb-2"><i class="fas fa-box-open fa-2x text-light"></i></div>
                                            No services found matching your criteria.
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
</div>

<?php include 'footer.php'; ?>