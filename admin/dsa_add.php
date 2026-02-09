<?php
include 'db/config.php';
include 'header.php';

$isEdit = false;
$dsa_data = null;

if (isset($_GET['id'])) {
    $isEdit = true;
    $id = (int)$_GET['id'];
    $res = mysqli_query($conn, "SELECT * FROM dsa WHERE id = $id LIMIT 1");
    $dsa_data = $res ? mysqli_fetch_assoc($res) : null;
    if (!$dsa_data) {
        echo "<script>window.location='dsa_list.php';</script>";
        exit;
    }
}

$depts = mysqli_query($conn, "SELECT id, name FROM departments ORDER BY name ASC");
?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>
<div class="content-page"><div class="content"><div class="container-fluid pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0"><?= $isEdit ? 'Update DSA Agent' : 'Create DSA Agent' ?></h2>
            <p class="text-muted small"><?= $isEdit ? 'Modify DSA account details.' : 'Create a new DSA login account.' ?></p>
        </div>
        <a href="dsa_list.php" class="btn btn-outline-secondary px-4">Back to DSA List</a>
    </div>
    <form action="db/insert/dsa_handler.php" method="POST">
        <?php if($isEdit): ?><input type="hidden" name="dsa_id" value="<?= (int)$id ?>"><?php endif; ?>
        <div class="card border p-4">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($dsa_data['name'] ?? '') ?>"></div>
                <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($dsa_data['email'] ?? '') ?>"></div>
                <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" required maxlength="15" value="<?= htmlspecialchars($dsa_data['phone'] ?? '') ?>"></div>
                <div class="col-md-6"><label class="form-label">Password <?= $isEdit ? '<span class="text-primary">(leave blank to keep)</span>' : '' ?></label><input type="text" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?>></div>
                <div class="col-md-6"><label class="form-label">Assign Department</label><select name="department_id" class="form-select" required><option value="">Select Department</option><?php while($d = mysqli_fetch_assoc($depts)): ?><option value="<?= (int)$d['id'] ?>" <?= (($dsa_data['department_id'] ?? 0) == $d['id']) ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option><?php endwhile; ?></select></div>
                <div class="col-md-6"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active" <?= (($dsa_data['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option><option value="inactive" <?= (($dsa_data['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option></select></div>
            </div>
            <div class="mt-4 text-end"><button type="submit" name="save_dsa" class="btn btn-dark px-4"><?= $isEdit ? 'Update DSA' : 'Create DSA' ?></button></div>
        </div>
    </form>
</div></div></div>
<?php include 'footer.php'; ?>
