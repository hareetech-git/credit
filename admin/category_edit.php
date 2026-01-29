<?php
include 'db/config.php';

$id = (int)($_GET['id'] ?? 0);

$category = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM service_categories WHERE id = $id")
);

if (!$category) {
    header("Location: category.php");
    exit;
}

$departments = mysqli_query($conn, "SELECT id, name FROM departments ORDER BY name ASC");
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
                        <h2 class="fw-bold text-dark mb-1">Update Category</h2>
                        <p class="text-muted small">Modify classification details for <span class="fw-bold text-dark">#<?= $category['id'] ?></span></p>
                    </div>

                    <div class="card card-modern">
                        <div class="card-body p-4 p-md-5">

                            <form method="POST" action="db/update/category_update.php">
                                <input type="hidden" name="id" value="<?= $category['id'] ?>">

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Assigned Department</label>
                                        <select name="department" class="form-select" required>
                                            <option value="">Select Department</option>
                                            <?php while ($d = mysqli_fetch_assoc($departments)): ?>
                                                <option value="<?= $d['id'] ?>"
                                                    <?= $category['department'] == $d['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($d['name']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Category Display Name</label>
                                        <input type="text"
                                               name="category_name"
                                               class="form-control"
                                               value="<?= htmlspecialchars($category['category_name']) ?>"
                                               required>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Sequence (Ordering)</label>
                                        <input type="number"
                                               name="sequence"
                                               class="form-control"
                                               value="<?= $category['sequence'] ?>"
                                               min="1">
                                        <small class="text-muted">Defines the sorting position on the frontend.</small>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Visibility Status</label>
                                        <select name="active" class="form-select">
                                            <option value="1" <?= $category['active'] ? 'selected' : '' ?>>Active (Visible)</option>
                                            <option value="0" <?= !$category['active'] ? 'selected' : '' ?>>Inactive (Hidden)</option>
                                        </select>
                                    </div>

                                    <div class="col-12 mt-2 pt-4 border-top d-flex align-items-center">
                                        <button type="submit" class="btn btn-submit-pro">
                                            <i class="fas fa-save me-2"></i> Update Category
                                        </button>
                                        <a href="category.php" class="btn-cancel ms-4">
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