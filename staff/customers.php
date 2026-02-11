<?php
include 'db/config.php';
include 'header.php';

if (!hasAccess($conn, 'cust_read')) {
    echo "<script>alert('Access Denied: You do not have permission to view profile details.'); window.location='dashboard.php';</script>";
    exit();
}
// Ensure FontAwesome is loaded
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">';
include 'topbar.php';
include 'sidebar.php';

$search_query = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Build Query
$query = "SELECT c.*, cp.pan_number, 
          (SELECT COUNT(*) FROM loan_applications WHERE customer_id = c.id) as total_loans,
          (SELECT COUNT(*) FROM loan_applications WHERE customer_id = c.id AND status='pending') as pending_loans
          FROM customers c 
          LEFT JOIN customer_profiles cp ON c.id = cp.customer_id 
          WHERE 1=1";

if (!empty($status_filter)) {
    $status_safe = mysqli_real_escape_string($conn, $status_filter);
    $query .= " AND c.status = '$status_safe'";
}

$query .= " ORDER BY c.id DESC";
$result = mysqli_query($conn, $query);

$rows = [];
$searchNeedle = strtolower(trim((string)$search_query));
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $row['pan_number'] = uc_decrypt_sensitive((string)($row['pan_number'] ?? ''));

        if ($searchNeedle !== '') {
            $nameMatch = str_contains(strtolower((string)($row['full_name'] ?? '')), $searchNeedle);
            $phoneMatch = str_contains(strtolower((string)($row['phone'] ?? '')), $searchNeedle);
            $panMatch = str_contains(strtolower((string)($row['pan_number'] ?? '')), $searchNeedle);
            if (!$nameMatch && !$phoneMatch && !$panMatch) {
                continue;
            }
        }

        $rows[] = $row;
    }
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
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: var(--slate-600);
        padding: 16px 24px;
        border: none;
    }
    
    .table-modern tbody td {
        padding: 16px 24px;
        font-size: 0.9rem;
        color: var(--slate-900);
        border-bottom: 1px solid var(--slate-200);
        vertical-align: middle;
    }

    /* Avatar Branding */
    .avatar-slate {
        width: 40px; height: 40px;
        background: var(--slate-900);
        color: white;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.85rem;
    }

    /* Action Buttons */
    .btn-action {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; border: 1px solid var(--slate-200);
        background: white; color: var(--slate-600);
        transition: all 0.2s; text-decoration: none;
    }
    .btn-view:hover { background: var(--blue-500); color: white; border-color: var(--blue-500); }
    .btn-edit:hover { background: var(--slate-900); color: white; border-color: var(--slate-900); }
    .btn-delete:hover { background: #ef4444; color: white; border-color: #ef4444; }

    /* Custom Badges */
    .badge-soft {
        padding: 5px 10px; border-radius: 6px; font-weight: 600; font-size: 0.75rem;
        border: 1px solid transparent;
    }
    .badge-active { background: #f0fdf4; color: #16a34a; border-color: #dcfce7; }
    .badge-blocked { background: #fef2f2; color: #dc2626; border-color: #fee2e2; }
    .badge-loan-count { background: #eff6ff; color: #2563eb; border-color: #dbeafe; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Customer Management</h2>
                    <p class="text-muted small mb-0">Manage your borrower database and KYC status.</p>
                </div>
              <?php if (hasAccess($conn, 'cust_create')): ?>
    <a href="customer_add.php" class="btn btn-dark px-4 py-2 fw-bold" style="border-radius: 10px;">
        <i class="fas fa-plus me-2"></i> Add New Customer
    </a>
<?php endif; ?>
            </div>

            <div class="card card-modern mb-4">
                <div class="card-body p-3">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label text-muted small fw-bold">Search Database</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, Phone or PAN Number..." value="<?= htmlspecialchars($search_query) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Account Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Statuses</option>
                                <option value="active" <?= $status_filter == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="blocked" <?= $status_filter == 'blocked' ? 'selected' : '' ?>>Blocked</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button class="btn btn-sm btn-dark w-100"><i class="fas fa-filter me-1"></i> Filter Results</button>
                            <a href="customers.php" class="btn btn-sm btn-outline-secondary w-50">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (isset($_GET['msg'])) : ?>
                <div class="alert alert-success border-0 shadow-sm mb-4 py-3">
                    <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($_GET['msg']) ?>
                </div>
            <?php endif; ?>

            <div class="card card-modern">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th width="80">ID</th>
                                    <th>Customer Identity</th>
                                    <th>Account Status</th>
                                    <th>KYC / PAN</th>
                                    <th>Loan Activity</th>
                                    <th width="150" class="text-end">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($rows) > 0) : ?>
                                    <?php foreach ($rows as $row) : ?>
                                        <tr>
                                            <td class="text-muted fw-bold">#<?= $row['id'] ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-slate me-3"><?= strtoupper(substr($row['full_name'], 0, 1)) ?></div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['full_name']) ?></div>
                                                        <div class="text-muted small"><i class="fas fa-phone-alt me-1"></i> <?= htmlspecialchars($row['phone']) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($row['status'] == 'active') : ?>
                                                    <span class="badge-soft badge-active">Active</span>
                                                <?php else : ?>
                                                    <span class="badge-soft badge-blocked">Blocked</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($row['pan_number']) : ?>
                                                    <span class="text-dark fw-medium"><i class="fas fa-id-card me-1 text-muted"></i> <?= $row['pan_number'] ?></span>
                                                <?php else : ?>
                                                    <span class="text-warning small"><i class="fas fa-clock me-1"></i> Missing PAN</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($row['pending_loans'] > 0) : ?>
                                                    <span class="badge-soft" style="background: #fffbeb; color: #92400e; border-color: #fef3c7;">
                                                        <i class="fas fa-hourglass-half me-1"></i> <?= $row['pending_loans'] ?> Pending
                                                    </span>
                                                <?php elseif ($row['total_loans'] > 0) : ?>
                                                    <span class="badge-soft badge-loan-count">
                                                        <i class="fas fa-file-invoice-dollar me-1"></i> <?= $row['total_loans'] ?> Loans
                                                    </span>
                                                <?php else : ?>
                                                    <span class="text-muted small">No history</span>
                                                <?php endif; ?>
                                            </td>
       <td class="text-end">
    <div class="d-flex justify-content-end gap-2">
        
        <a href="customer_view.php?id=<?= $row['id'] ?>" class="btn-action btn-view"><i class="fas fa-eye"></i></a>

        <?php if (hasAccess($conn, 'cust_update')): ?>
            <a href="customer_add.php?id=<?= $row['id'] ?>" class="btn-action btn-edit"><i class="fas fa-edit"></i></a>
        <?php endif; ?>

<?php if (hasAccess($conn, 'cust_delete')): ?>
    <a href="db/delete/customer_delete.php?id=<?= $row['id'] ?>&action=delete" 
       class="btn-action btn-delete" 
       onclick="return confirm('Are you sure you want to delete this customer? This cannot be undone.')">
       <i class="fas fa-trash"></i>
    </a>
<?php endif; ?>
        
    </div>
</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-users-slash fa-3x mb-3 text-light"></i>
                                            <p>No customers found matching your criteria.</p>
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
