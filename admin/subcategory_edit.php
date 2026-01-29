<?php
include 'db/config.php';

$id = (int)($_GET['id'] ?? 0);

/*
|--------------------------------------------------------------------------
| Fetch subcategory + its department (via category)
|--------------------------------------------------------------------------
*/
$subcategory = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT sc.*, c.department
         FROM services_subcategories sc
         LEFT JOIN service_categories c ON c.id = sc.category_id
         WHERE sc.id = $id"
    )
);

if (!$subcategory) {
    header("Location: subcategory.php");
    exit;
}

$selected_department = (int)$subcategory['department'];
$selected_category   = (int)$subcategory['category_id'];

/*
|--------------------------------------------------------------------------
| Load departments
|--------------------------------------------------------------------------
*/
$departments = mysqli_query(
    $conn,
    "SELECT id, name FROM departments ORDER BY name"
);

/*
|--------------------------------------------------------------------------
| Load categories ONLY for selected department
|--------------------------------------------------------------------------
*/
$categories = mysqli_query(
    $conn,
    "SELECT id, category_name
     FROM service_categories
     WHERE department = $selected_department
     ORDER BY category_name"
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
    }

    .form-label {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: var(--slate-600);
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e1;
        transition: all 0.2s;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--slate-900);
        box-shadow: none;
    }

    .form-select:disabled {
        background-color: #f8fafc;
        border-color: var(--slate-200);
        color: #94a3b8;
    }

    /* Fixed Button Hover States */
    .btn-submit-pro {
        background: var(--slate-900);
        color: #ffffff !important;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
    }

    .btn-submit-pro:hover {
        background: #334155 !important;
        color: #ffffff !important;
    }

    .btn-cancel {
        font-weight: 600;
        color: var(--slate-600);
        text-decoration: none;
        transition: color 0.2s;
    }

    .btn-cancel:hover {
        color: var(--slate-900);
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <div class="mb-4 text-center text-md-start">
                        <h2 class="fw-bold text-dark mb-1">Update Subcategory</h2>
                        <p class="text-muted small">Refine details for <span class="fw-bold text-dark">#<?= $subcategory['id'] ?></span> within its assigned hierarchy.</p>
                    </div>

                    <div class="card card-modern">
                        <div class="card-body p-4 p-md-5">

                            <form method="POST" action="db/update/subcategory_update.php">
                                <input type="hidden" name="id" value="<?= $subcategory['id'] ?>">

                                <div class="row">

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Parent Department</label>
                                        <select class="form-select" disabled>
                                            <?php while ($d = mysqli_fetch_assoc($departments)) { ?>
                                                <option value="<?= $d['id'] ?>"
                                                    <?= $selected_department == $d['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($d['name']) ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <small class="text-muted">Department is fixed via the Category selection.</small>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Assigned Category</label>
                                        <select name="category_id" class="form-select" required>
                                            <?php while ($c = mysqli_fetch_assoc($categories)) { ?>
                                                <option value="<?= $c['id'] ?>"
                                                    <?= $selected_category == $c['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($c['category_name']) ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Subcategory Name</label>
                                        <input type="text"
                                               name="sub_category_name"
                                               class="form-control"
                                               value="<?= htmlspecialchars($subcategory['sub_category_name']) ?>"
                                               required>
                                    </div>

                                    <div class="col-md-3 mb-4">
                                        <label class="form-label">Sequence</label>
                                        <input type="number"
                                               name="sequence"
                                               class="form-control"
                                               value="<?= $subcategory['sequence'] ?>"
                                               min="1">
                                    </div>

                                    <div class="col-md-3 mb-4">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="active" <?= $subcategory['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="inactive" <?= $subcategory['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </div>

                                    <div class="col-12 mt-2 pt-4 border-top d-flex align-items-center">
                                        <button type="submit" class="btn btn-submit-pro">
                                            <i class="fas fa-save me-2"></i> Save Changes
                                        </button>
                                        <a href="subcategory.php" class="btn-cancel ms-4">
                                            Cancel
                                        </a>
                                    </div>

                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>