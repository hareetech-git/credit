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

<div class="content-page">
<div class="content">
<div class="container-fluid pt-5">

<h2 class="fw-bold mb-2">Update Subcategory</h2>
<p class="text-muted mb-4">Edit subcategory details.</p>

<div class="card card-modern">
<div class="card-body p-4 p-md-5">

<form method="POST" action="db/update/subcategory_update.php">

<input type="hidden" name="id" value="<?= $subcategory['id'] ?>">

<div class="row">

<!-- ✅ DEPARTMENT (READ-ONLY) -->
<div class="col-md-6 mb-4">
    <label class="form-label">Department</label>
    <select class="form-select" disabled>
        <?php while ($d = mysqli_fetch_assoc($departments)) { ?>
            <option value="<?= $d['id'] ?>"
                <?= $selected_department == $d['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($d['name']) ?>
            </option>
        <?php } ?>
    </select>
</div>

<!-- ✅ CATEGORY (FILTERED BY DEPARTMENT) -->
<div class="col-md-6 mb-4">
    <label class="form-label">Category</label>
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
           value="<?= $subcategory['sequence'] ?>">
</div>

<div class="col-md-3 mb-4">
    <label class="form-label">Status</label>
    <select name="status" class="form-select">
        <option value="active" <?= $subcategory['status']=='active'?'selected':'' ?>>Active</option>
        <option value="inactive" <?= $subcategory['status']=='inactive'?'selected':'' ?>>Inactive</option>
    </select>
</div>

<div class="col-12 pt-3 border-top">
    <button class="btn btn-submit">Save Changes</button>
    <a href="subcategory.php" class="btn btn-link text-muted ms-2">Cancel</a>
</div>

</div>
</form>

</div>
</div>

</div>
</div>
</div>

<?php include 'footer.php'; ?>
