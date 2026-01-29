<?php
include 'db/config.php';

$query = "
SELECT sc.id, sc.sub_category_name, sc.sequence, sc.status,
       c.category_name
FROM services_subcategories sc
LEFT JOIN service_categories c ON c.id = sc.category_id
ORDER BY sc.sequence ASC
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

    /* Badge Styling */
    .badge-modern {
        padding: 5px 10px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    /* Button Styles with Fixed Hover */
    .btn-submit-pro {
        background: var(--slate-900);
        color: #ffffff !important;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
        display: inline-block;
    }
    .btn-submit-pro:hover {
        background: #334155 !important;
        color: #ffffff !important;
    }

    .btn-action-edit {
        color: var(--slate-900) !important;
        border: 1px solid var(--slate-200);
        font-weight: 600;
        transition: all 0.2s;
        text-decoration: none;
        background: transparent;
    }
    .btn-action-edit:hover {
        background: #f1f5f9 !important;
        border-color: var(--slate-900);
        color: var(--slate-900) !important;
    }

    .btn-action-delete {
        color: #ef4444 !important;
        font-weight: 600;
        transition: all 0.2s;
        text-decoration: none;
        background: transparent;
        border: none;
    }
    .btn-action-delete:hover {
        text-decoration: underline;
        color: #b91c1c !important;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Service Subcategories</h2>
                    <p class="text-muted small mb-0">Manage detailed classifications for your categories.</p>
                </div>
                <a href="subcategory_add.php" class="btn btn-submit-pro text-decoration-none">
                    <i class="fas fa-plus-circle me-1"></i> New Subcategory
                </a>
            </div>

            <div class="card card-modern">
                <div class="card-body p-0">

                    <div class="table-responsive">
                        <table class="table table-modern mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th width="80">#</th>
                                    <th>Subcategory Name</th>
                                    <th>Parent Category</th>
                                    <th width="100">Order</th>
                                    <th width="120">Status</th>
                                    <th width="180" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="text-muted fw-bold">#<?= $row['id'] ?></td>
                                        <td class="fw-semibold text-dark"><?= htmlspecialchars($row['sub_category_name']) ?></td>
                                        <td>
                                            <span class="text-muted small text-uppercase fw-bold">
                                                <?= htmlspecialchars($row['category_name'] ?? 'Unassigned') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?= $row['sequence'] ?></span>
                                        </td>
                                        <td>
                                            <?php if($row['status'] === 'active'): ?>
                                                <span class="badge badge-modern bg-soft-success text-success" style="background-color: #ecfdf5;">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-modern bg-soft-secondary text-muted" style="background-color: #f1f5f9;">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="subcategory_edit.php?id=<?= $row['id'] ?>"
                                               class="btn btn-sm btn-action-edit me-2 py-1 px-3">
                                               <i class="fas fa-edit me-1"></i> Edit
                                            </a>

                                            <a href="db/delete/subcategory_delete.php?id=<?= $row['id'] ?>"
                                               onclick="return confirm('Permanently delete this subcategory?')"
                                               class="btn btn-sm btn-action-delete py-1">
                                               <i class="fas fa-trash-alt me-1"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No subcategories found. Click "New Subcategory" to begin.</td>
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