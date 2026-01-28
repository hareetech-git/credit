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

<div class="content-page">
<div class="content">
<div class="container-fluid pt-5">

<h2 class="fw-bold mb-2">New Subcategory</h2>
<p class="text-muted mb-4">
    Select department → category → create subcategory
</p>

<div class="card card-modern">
<div class="card-body p-4 p-md-5">

<!-- STEP 1: SELECT DEPARTMENT -->
<form method="GET" class="mb-4">
    <label class="form-label">Department</label>
    <select name="department" class="form-select" onchange="this.form.submit()" required>
        <option value="">Select Department</option>
        <?php while ($d = mysqli_fetch_assoc($departments)) { ?>
            <option value="<?= $d['id'] ?>" <?= $selected_department == $d['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($d['name']) ?>
            </option>
        <?php } ?>
    </select>
</form>

<!-- STEP 2: SELECT CATEGORY -->
<?php if ($selected_department > 0) { ?>
<form method="GET" class="mb-4">
    <input type="hidden" name="department" value="<?= $selected_department ?>">
    <label class="form-label">Category</label>
    <select name="category" class="form-select" onchange="this.form.submit()" required>
        <option value="">Select Category</option>
        <?php while ($c = mysqli_fetch_assoc($categories)) { ?>
            <option value="<?= $c['id'] ?>" <?= $selected_category == $c['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['category_name']) ?>
            </option>
        <?php } ?>
    </select>
</form>
<?php } ?>

<!-- STEP 3: CREATE SUBCATEGORY -->
<?php if ($selected_category > 0) { ?>
<form method="POST" action="db/insert/subcategory_insert.php">

    <input type="hidden" name="category_id" value="<?= $selected_category ?>">

    <div class="row">

        <div class="col-md-6 mb-4">
            <label class="form-label">Subcategory Name</label>
            <input type="text" name="sub_category_name" class="form-control" required>
        </div>

        <div class="col-md-3 mb-4">
            <label class="form-label">Sequence</label>
            <input type="number" name="sequence" class="form-control" value="1">
        </div>

        <div class="col-md-3 mb-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="col-12 pt-3 border-top">
            <button class="btn btn-submit">Create Subcategory</button>
            <a href="subcategory.php" class="btn btn-link text-muted ms-2">Cancel</a>
        </div>

    </div>
</form>
<?php } ?>

</div>
</div>

</div>
</div>
</div>

<?php include 'footer.php'; ?>
