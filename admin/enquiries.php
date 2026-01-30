<?php
include 'db/config.php';
include 'header.php';
// Ensure FontAwesome is loaded
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">';
include 'topbar.php';
include 'sidebar.php';

// --- FILTERS & SORTING LOGIC ---
$search_query = $_GET['search'] ?? '';
$sort_by      = $_GET['sort_by'] ?? 'id';
$sort_order   = $_GET['order'] ?? 'DESC';
$next_order   = ($sort_order === 'ASC') ? 'DESC' : 'ASC';

// Build Query
$query = "SELECT * FROM enquiries WHERE 1=1";

if (!empty($search_query)) {
    $search_safe = mysqli_real_escape_string($conn, $search_query);
    $query .= " AND (full_name LIKE '%$search_safe%' OR email LIKE '%$search_safe%' OR phone LIKE '%$search_safe%' OR loan_type_name LIKE '%$search_safe%')";
}

// Validate Sort Column
$allowed_sorts = ['id', 'full_name', 'loan_type_name', 'created_at'];
if (!in_array($sort_by, $allowed_sorts)) $sort_by = 'id';

$query .= " ORDER BY $sort_by $sort_order";
$result = mysqli_query($conn, $query);
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
        cursor: pointer;
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

    /* Action Buttons */
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
    .btn-mail { color: #0ea5e9; }
    .btn-mail:hover { background: #0ea5e9 !important; color: white !important; border-color: #0ea5e9; }

    .btn-delete-pro { color: #ef4444; }
    .btn-delete-pro:hover { background: #ef4444 !important; color: white !important; border-color: #ef4444; }

    .badge-soft {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid var(--slate-200);
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
    }

    .msg-box {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        color: var(--slate-600);
        font-size: 0.85rem;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">User Enquiries</h2>
           
                </div>
            </div>

            <div class="card card-modern mb-4">
                <div class="card-body p-3">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label text-muted small fw-bold">Quick Search</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email, phone or loan type..." value="<?= htmlspecialchars($search_query) ?>">
                            </div>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button class="btn btn-sm btn-dark w-100"><i class="fas fa-filter me-1"></i> Apply Filter</button>
                            <a href="enquiry_list.php" class="btn btn-sm btn-outline-secondary w-50">Reset</a>
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
                                    <th width="80">
                                        <a href="?sort_by=id&order=<?= $next_order ?>&search=<?= $search_query ?>">ID <i class="fas fa-sort float-end"></i></a>
                                    </th>
                                    <th>
                                        <a href="?sort_by=full_name&order=<?= $next_order ?>&search=<?= $search_query ?>">Applicant <i class="fas fa-sort float-end"></i></a>
                                    </th>
                                    <th>Contact Info</th>
                                    <th>
                                        <a href="?sort_by=loan_type_name&order=<?= $next_order ?>&search=<?= $search_query ?>">Loan Type <i class="fas fa-sort float-end"></i></a>
                                    </th>
                                    <th>Message</th>
                                    <th>
                                        <a href="?sort_by=created_at&order=<?= $next_order ?>&search=<?= $search_query ?>">Date <i class="fas fa-sort float-end"></i></a>
                                    </th>
                                    <th width="120" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr>
                                            <td class="text-muted fw-bold">#<?= $row['id'] ?></td>
                                            
                                            <td class="fw-semibold text-dark">
                                                <?= htmlspecialchars($row['full_name']) ?>
                                            </td>
                                            
                                            <td>
                                                <div class="small fw-bold"><?= htmlspecialchars($row['phone']) ?></div>
                                                <div class="text-muted small"><?= htmlspecialchars($row['email']) ?></div>
                                            </td>
                                            
                                            <td><span class="badge-soft text-primary"><?= htmlspecialchars($row['loan_type_name'] ?: 'N/A') ?></span></td>
                                            
                                            <td>
                                                <span class="msg-box" title="<?= htmlspecialchars($row['query_message']) ?>">
                                                    <?= htmlspecialchars($row['query_message']) ?>
                                                </span>
                                            </td>

                                            <td class="text-muted small">
                                                <?= date('M d, Y', strtotime($row['created_at'])) ?><br>
                                                <span class="text-light-emphasis"><?= date('h:i A', strtotime($row['created_at'])) ?></span>
                                            </td>
                                            
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="mailto:<?= $row['email'] ?>" class="btn-action btn-mail" title="Reply via Email">
                                                        <i class="fas fa-envelope"></i>
                                                    </a>
                                                    
                                                    <a href="db/delete/enquiry_delete.php?id=<?= $row['id'] ?>" 
                                                       class="btn-action btn-delete-pro" 
                                                       onclick="return confirm('Archive this enquiry?');"
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
                                            <div class="mb-2"><i class="fas fa-inbox fa-2x text-light"></i></div>
                                            No enquiries found.
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