<?php

include 'db/config.php';

$res = mysqli_query($conn,
    "SELECT id, name, created_at FROM departments ORDER BY id DESC"
);
?>

<?php include 'header.php'; ?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark">Departments</h2>
                <a href="add-department.php" class="btn btn-submit">
                    ➕ New Department
                </a>
            </div>

            <div class="card card-modern">
                <div class="card-body p-4">

                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Department Name</th>
                                <th>Created</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php while ($row = mysqli_fetch_assoc($res)) { ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                              <td>
    <a href="add-department.php?id=<?= $row['id'] ?>"
       class="btn btn-sm btn-outline-primary">
        Edit
    </a>
<td>
    <a href="add-department.php?id=<?= $row['id'] ?>"
       class="btn btn-sm btn-outline-primary">
        Edit
    </a>

    <form action="db/department-delete.php"
          method="POST"
          style="display:inline"
          onsubmit="return confirm('Delete this department?')">

        <input type="hidden" name="id" value="<?= $row['id'] ?>">

        <button type="submit"
                class="btn btn-sm btn-outline-danger ms-1">
            Delete
        </button>
    </form>
</td>

</td>

                            </tr>
                        <?php } ?>

                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>
</div>



<?php include 'footer.php'; ?>
