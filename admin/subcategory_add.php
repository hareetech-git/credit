<?php
include 'db/config.php';

// Selected values (persist on reload)
$selected_department = isset($_GET['department']) ? (int)$_GET['department'] : 0;
$selected_category   = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Load hierarchy helpers
include 'pair_indie/get_departments.php';
include 'pair_indie/get_categories.php';
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

    /* Step Indicators */
    .step-box {
        border-left: 3px solid var(--slate-200);
        padding-left: 20px;
        margin-bottom: 30px;
        position: relative;
    }
    .step-box.active {
        border-left-color: var(--slate-900);
    }
    .step-number {
        font-size: 0.7rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        margin-bottom: 5px;
        display: block;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid ">

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <div class="mb-4 text-center text-md-start">
                        <h2 class="fw-bold text-dark mb-1">New Subcategory</h2>
                        <p class="text-muted small">Follow the hierarchy to create a specific service grouping.</p>
                    </div>

                    <div class="card card-modern">
                        <div class="card-body p-4 p-md-5">

                            <div class="step-box <?= ($selected_department == 0) ? 'active' : '' ?>">
                                <span class="step-number">Step 01</span>
                                <form method="GET">
                                    <label class="form-label">Primary Department</label>
                                    <select name="department" class="form-select" onchange="this.form.submit()" required>
                                        <option value="">Select Division</option>
                                        <?php while ($d = mysqli_fetch_assoc($departments)) { ?>
                                            <option value="<?= $d['id'] ?>" <?= $selected_department == $d['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($d['name']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </form>
                            </div>

                            <?php if ($selected_department > 0) { ?>
                            <div class="step-box <?= ($selected_category == 0) ? 'active' : '' ?>">
                                <span class="step-number">Step 02</span>
                                <form method="GET">
                                    <input type="hidden" name="department" value="<?= $selected_department ?>">
                                    <label class="form-label">Parent Category</label>
                                    <select name="category" class="form-select" onchange="this.form.submit()" required>
                                        <option value="">Select Category</option>
                                        <?php while ($c = mysqli_fetch_assoc($categories)) { ?>
                                            <option value="<?= $c['id'] ?>" <?= $selected_category == $c['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($c['category_name']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </form>
                            </div>
                            <?php } ?>

                            <?php if ($selected_category > 0) { ?>
                            <div class="step-box active mb-0 border-0">
                                <span class="step-number">Step 03</span>
                                <h5 class="fw-bold text-dark mb-4">Subcategory Details</h5>
                                <form method="POST" action="db/insert/subcategory_insert.php">
                                    <input type="hidden" name="category_id" value="<?= $selected_category ?>">

                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Subcategory Name</label>
                                            <input type="text" name="sub_category_name" class="form-control" placeholder="e.g. New Pan Card" required>
                                        </div>

                                        <div class="col-md-3 mb-4">
                                            <label class="form-label">Display Order</label>
                                            <input type="number" name="sequence" class="form-control" value="1" min="1">
                                        </div>

                                        <div class="col-md-3 mb-4">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>

                                        <div class="col-12 pt-4 mt-2 border-top d-flex align-items-center">
                                            <button type="submit" class="btn btn-submit-pro">
                                                <i class="fas fa-check-circle me-2"></i> Create Subcategory
                                            </button>
                                            <a href="subcategory.php" class="btn-cancel ms-4">Cancel</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>