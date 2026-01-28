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

<div class="content-page">
<div class="content">
<div class="container-fluid pt-5">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Manage Subcategories</h2>
    <a href="subcategory_add.php" class="btn btn-submit">➕ New Subcategory</a>
</div>

<div class="card card-modern">
<div class="card-body p-4">

<table class="table table-hover align-middle">
<thead>
<tr>
    <th>#</th>
    <th>Subcategory</th>
    <th>Category</th>
    <th>Sequence</th>
    <th>Status</th>
    <th width="150">Action</th>
</tr>
</thead>
<tbody>

<?php if (mysqli_num_rows($result) > 0): ?>
<?php while ($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['sub_category_name']) ?></td>
    <td><?= htmlspecialchars($row['category_name']) ?></td>
    <td><?= $row['sequence'] ?></td>
    <td>
        <?= $row['status'] === 'active'
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-secondary">Inactive</span>' ?>
    </td>
    <td>
        <a href="subcategory_edit.php?id=<?= $row['id'] ?>"
           class="btn btn-sm btn-outline-primary">Edit</a>

        <a href="db/delete/subcategory_delete.php?id=<?= $row['id'] ?>"
           onclick="return confirm('Delete this subcategory?')"
           class="btn btn-sm btn-outline-danger">
           Delete
        </a>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="6" class="text-center text-muted">No subcategories found</td>
</tr>
<?php endif; ?>

</tbody>
</table>

</div>
</div>

</div>
</div>
</div>

<?php include 'footer.php'; ?>
