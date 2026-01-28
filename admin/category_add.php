<?php

include 'db/config.php';

$departments = mysqli_query($conn, "SELECT id, name FROM departments ORDER BY name ASC");
?>

<?php include 'header.php'; ?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-page">
<div class="content">
<div class="container-fluid pt-5">

<h2 class="fw-bold mb-2">New Category</h2>
<p class="text-muted mb-4">Create a new service category.</p>

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
           placeholder="e.g. Business Registration"
           required>
</div>

<div class="col-md-4 mb-4">
    <label class="form-label">Sequence</label>
    <input type="number" name="sequence" class="form-control" value="1" min="1">
</div>

<div class="col-md-4 mb-4">
    <label class="form-label">Status</label>
    <select name="active" class="form-select">
        <option value="1">Active</option>
        <option value="0">Inactive</option>
    </select>
</div>

<div class="col-12 pt-3 border-top">
    <button class="btn btn-submit">Create Category</button>
    <a href="category.php" class="btn btn-link text-muted ms-2">Cancel</a>
</div>

</div>
</form>

</div>
</div>

</div>
</div>
</div>

<?php include 'footer.php'; ?>
