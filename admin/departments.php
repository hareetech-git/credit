<?php
include 'db/config.php';

$res = mysqli_query($conn,
    "SELECT id, name, created_at FROM departments ORDER BY id DESC"
);
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

    /* Fixed Button Hover States */
    .btn-submit-pro {
        background: var(--slate-900);
        color: #ffffff !important;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
    }
    .btn-submit-pro:hover {
        background: #334155 !important;
        color: #ffffff !important;
    }

    .btn-action-edit {
        color: var(--slate-900);
        border: 1px solid var(--slate-200);
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-action-edit:hover {
        background: #f1f5f9 !important;
        border-color: var(--slate-900);
        color: var(--slate-900) !important;
    }

    .btn-action-delete {
        color: #ef4444;
        font-weight: 600;
        transition: all 0.2s;
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
                    <h2 class="fw-bold text-dark mb-1">Departments</h2>
                    <p class="text-muted small mb-0">Manage high-level service divisions.</p>
                </div>
                <a href="add-department.php" class="btn btn-submit-pro">
                    <i class="fas fa-plus-circle me-1"></i> New Department
                </a>
            </div>

            <div class="card card-modern">
                <div class="card-body p-0">

                    <div class="table-responsive">
                        <table class="table table-modern mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th width="80">#</th>
                                    <th>Department Name</th>
                                    <th>Created Date</th>
                                    <th width="200" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php if(mysqli_num_rows($res) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($res)) { ?>
                                    <tr>
                                        <td class="text-muted fw-bold">#<?= $row['id'] ?></td>
                                        <td class="fw-semibold text-dark"><?= htmlspecialchars($row['name']) ?></td>
                                        <td class="text-muted"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                        <td class="text-center">
                                            <a href="add-department.php?id=<?= $row['id'] ?>"
                                               class="btn btn-sm btn-action-edit me-2">
                                               <i class="fas fa-edit me-1"></i> Edit
                                            </a>

                                            <form action="db/department-delete.php"
                                                  method="POST"
                                                  style="display:inline"
                                                  onsubmit="return confirm('Delete this department?')">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-action-delete border-0">
                                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">No departments found.</td>
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