<?php
include 'db/config.php';
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

// Fetch all services with Category and Subcategory names
$query = "SELECT s.*, c.category_name, sub.sub_category_name 
          FROM services s
          LEFT JOIN service_categories c ON s.category_id = c.id
          LEFT JOIN services_subcategories sub ON s.sub_category_id = sub.id
          ORDER BY s.id DESC";
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
    }
    
    /* Table Body */
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
                    <i class="ri-add-line me-1"></i> Add New Service
                </a>
            </div>

            <?php if (isset($_GET['msg'])) { ?>
                <div class="alert alert-success border-0 shadow-sm mb-4 py-3">
                    <i class="ri-checkbox-circle-line me-1"></i> <?= htmlspecialchars($_GET['msg']) ?>
                </div>
            <?php } ?>

            <div class="card card-modern">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th width="80">ID</th>
                                    <th>Service Details</th>
                                    <th>Category</th>
                                    <th>Subcategory</th>
                                    <th>Date Added</th>
                                    <th width="150" class="text-center">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr id="row-<?= $row['id'] ?>">
                                            <td class="text-muted fw-bold">#<?= $row['id'] ?></td>
                                            <td>
                                                <span class="fw-bold text-dark d-block"><?= htmlspecialchars($row['title']) ?></span>
                                            </td>
                                            <td><span class="badge-soft"><?= htmlspecialchars($row['category_name']) ?></span></td>
                                            <td><span class="badge-soft"><?= htmlspecialchars($row['sub_category_name']) ?></span></td>
                                            <td class="text-muted small"><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                                            
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="service_details.php?id=<?= $row['id'] ?>" class="btn-action btn-view" title="View Details">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                    
                                                    <a href="service_edit.php?service_id=<?= $row['id'] ?>" class="btn-action btn-edit-pro" title="Edit">
                                                        <i class="ri-edit-line"></i>
                                                    </a>

                                                    <a href="db/delete/service_delete.php?id=<?= $row['id'] ?>" 
                                                       class="btn-action btn-delete-pro" 
                                                       onclick="return confirm('This will permanently delete this service. Continue?');"
                                                       title="Delete">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            No services found. Click "Add New Service" to get started.
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