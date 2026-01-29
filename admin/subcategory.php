<?php
include 'db/config.php';

// --- FILTERS & SORTING LOGIC ---
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Sorting logic
$sort_by    = $_GET['sort_by'] ?? 'sequence'; 
$sort_order = $_GET['order'] ?? 'ASC';
$next_order = ($sort_order === 'ASC') ? 'DESC' : 'ASC';

// Helper function for sort URLs
function getSortUrl($column, $current_sort_by, $current_sort_order, $search_term) {
    $order = ($column === $current_sort_by && $current_sort_order === 'ASC') ? 'DESC' : 'ASC';
    return "?" . http_build_query(['search' => $search_term, 'sort_by' => $column, 'order' => $order]);
}

$where_clause = $search ? "WHERE sc.sub_category_name LIKE '%$search%'" : "";

// Validate Sort Column & Map to correct SQL field
$allowed_sorts = ['id', 'sub_category_name', 'category_name', 'sequence'];
if (!in_array($sort_by, $allowed_sorts)) {
    $sort_by = 'sequence';
}

// Logic to sort by the Category Name from the joined table
$order_sql = match($sort_by) {
    'category_name'     => "c.category_name $sort_order",
    'sub_category_name' => "sc.sub_category_name $sort_order",
    'id'                => "sc.id $sort_order",
    default             => "sc.sequence $sort_order",
};

$query = "
SELECT sc.id, sc.sub_category_name, sc.sequence, sc.status,
       c.category_name
FROM services_subcategories sc
LEFT JOIN service_categories c ON c.id = sc.category_id
$where_clause
ORDER BY $order_sql
";

$result = mysqli_query($conn, $query);
?>

<?php include 'header.php'; ?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
        --slate-50: #f8fafc;
    }
    .content-page { background-color: #fcfcfd; min-height: 100vh; }
    
    .breadcrumb-item a { color: var(--slate-600); text-decoration: none; }
    .breadcrumb-item.active { color: var(--slate-900); font-weight: 700; }

    .filter-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
    }

    /* Interactive Table Headers */
    .table-modern thead th {
        background: var(--slate-50);
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--slate-600);
        padding: 0;
        border-bottom: 1px solid var(--slate-200);
    }

    .table-modern thead th a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 24px;
        color: inherit;
        text-decoration: none;
        width: 100%;
        transition: background 0.2s;
    }

    .table-modern thead th a:hover { background: #f1f5f9; }
    
    .sort-icon { font-size: 0.7rem; opacity: 0.3; }
    .active-sort { opacity: 1 !important; color: var(--slate-900); }

    .badge-cat {
        background: #f1f5f9;
        color: var(--slate-600);
        font-size: 0.7rem;
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid var(--slate-200);
    }

    .btn-submit-pro {
        background: var(--slate-900);
        color: #ffffff !important;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
    }
    .btn-submit-pro :hover {
        background: var(--slate-600);
        color: #ffffff !important;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            
            <div class="">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Subcategories</li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-0">Service Subcategories</h2>
                       
                    </div>
                    <a href="subcategory_add.php" class="btn btn-dark text-decoration-none">
                        <i class="fas fa-plus-circle me-1"></i> New Subcategory
                    </a>
                </div>
            </div>

            <div class="filter-card">
                <form method="GET" class="row g-3 align-items-center">
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" 
                                   placeholder="Search subcategory names..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-dark w-100 fw-bold">Apply Filter</button>
                    </div>
                    <?php if($search || $sort_by != 'sequence'): ?>
                    <div class="col-md-2">
                        <a href="subcategories.php" class="btn btn-link text-muted px-0 text-decoration-none small">Clear All</a>
                    </div>
                    <?php endif; ?>
                </form>
            </div>

            <div class="card card-modern">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th width="100">
                                        <a href="<?= getSortUrl('id', $sort_by, $sort_order, $search) ?>">
                                            # <i class="fas <?= $sort_by == 'id' ? ($sort_order == 'ASC' ? 'fa-sort-up active-sort' : 'fa-sort-down active-sort') : 'fa-sort sort-icon' ?>"></i>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="<?= getSortUrl('sub_category_name', $sort_by, $sort_order, $search) ?>">
                                            Subcategory Name <i class="fas <?= $sort_by == 'sub_category_name' ? ($sort_order == 'ASC' ? 'fa-sort-up active-sort' : 'fa-sort-down active-sort') : 'fa-sort sort-icon' ?>"></i>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="<?= getSortUrl('category_name', $sort_by, $sort_order, $search) ?>">
                                            Parent Category <i class="fas <?= $sort_by == 'category_name' ? ($sort_order == 'ASC' ? 'fa-sort-up active-sort' : 'fa-sort-down active-sort') : 'fa-sort sort-icon' ?>"></i>
                                        </a>
                                    </th>
                                    <th width="120">
                                        <a href="<?= getSortUrl('sequence', $sort_by, $sort_order, $search) ?>">
                                            Order <i class="fas <?= $sort_by == 'sequence' ? ($sort_order == 'ASC' ? 'fa-sort-up active-sort' : 'fa-sort-down active-sort') : 'fa-sort sort-icon' ?>"></i>
                                        </a>
                                    </th>
                                    <th width="120" class="ps-4">Status</th>
                                    <th width="180" class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="ps-4 text-muted fw-bold">#<?= $row['id'] ?></td>
                                        <td class="fw-semibold text-dark"><?= htmlspecialchars($row['sub_category_name']) ?></td>
                                        <td>
                                            <span class="badge-cat text-uppercase fw-bold">
                                                <?= htmlspecialchars($row['category_name'] ?? 'Unassigned') ?>
                                            </span>
                                        </td>
                                        <td class="ps-4">
                                            <span class="badge bg-light text-dark border px-3"><?= $row['sequence'] ?></span>
                                        </td>
                                        <td>
                                            <?php if($row['status'] === 'active'): ?>
                                                <span class="badge rounded-pill bg-success-subtle text-success px-3" style="background-color: #ecfdf5; border: 1px solid #10b98133;">Active</span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill bg-secondary-subtle text-muted px-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="subcategory_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-light border me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="db/delete/subcategory_delete.php?id=<?= $row['id'] ?>" 
                                               onclick="return confirm('Delete this subcategory?')" 
                                               class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted small">
                                        No subcategories matched your search.
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