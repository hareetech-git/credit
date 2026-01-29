<?php
include 'db/config.php';

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

    /* Fixed Button Styling */
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
        <div class="container-fluid ">

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <div class="mb-4 text-center text-md-start">
                        <h2 class="fw-bold text-dark mb-1">New Category</h2>
                        <p class="text-muted small">Create a grouping for your services under a specific department.</p>
                    </div>

                    <div class="card card-modern">
                        <div class="card-body p-4 p-md-5">

                            <form method="POST" action="db/insert/category_insert.php">
                                <div class="row">

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Department</label>
                                        <select name="department" class="form-select" required>
                                            <option value="">Select Department</option>
                                            <?php while ($d = mysqli_fetch_assoc($departments)): ?>
                                                <option value="<?= $d['id'] ?>">
                                                    <?= htmlspecialchars($d['name']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Category Name</label>
                                        <input type="text"
                                               name="category_name"
                                               class="form-control"
                                               placeholder="e.g. GST Registration"
                                               required>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Sequence (Order)</label>
                                        <input type="number" name="sequence" class="form-control" value="1" min="1">
                                        <small class="text-muted">Controls the display order of categories.</small>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Visibility Status</label>
                                        <select name="active" class="form-select">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>

                                    <div class="col-12 mt-2 pt-4 border-top d-flex align-items-center">
                                        <button type="submit" class="btn btn-submit-pro">
                                            <i class="fas fa-check-circle me-2"></i> Create Category
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