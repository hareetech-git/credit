<?php
include 'db/config.php';
include 'header.php';

// 1. Fetch all staff for the dropdown
$staff_res = mysqli_query($conn, "SELECT id, name, email FROM staff ORDER BY name ASC");

// 2. Identify if we are in "Bulk" or "Individual" mode
$mode = $_GET['mode'] ?? 'individual'; 
$selected_id = isset($_GET['staff_id']) ? (int)$_GET['staff_id'] : 0;

$current_perms = [];

// 3. If individual mode, fetch their specific current permissions
if ($mode == 'individual' && $selected_id > 0) {
    $res = mysqli_query($conn, "SELECT permission_id FROM staff_permissions WHERE staff_id = $selected_id");
    while ($row = mysqli_fetch_assoc($res)) {
        $current_perms[] = $row['permission_id'];
    }
}

$all_perms = mysqli_query($conn, "SELECT id, perm_key, description FROM permissions ORDER BY description ASC");
?>

<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            <div class="mb-4">
                <h2 class="fw-bold text-dark">Access Control Center</h2>
                <p class="text-muted">Manage permissions globally or for specific individuals.</p>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="btn-group w-100 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        <a href="?mode=individual" class="btn btn-lg <?= $mode == 'individual' ? 'btn-dark' : 'btn-light border' ?>">Specific User</a>
                        <a href="?mode=bulk" class="btn btn-lg <?= $mode == 'bulk' ? 'btn-danger' : 'btn-light border' ?>">All Staff (Global)</a>
                    </div>
                </div>
            </div>

            <?php if ($mode == 'individual'): ?>
                <div class="card card-modern p-4 mb-4">
                    <form method="GET">
                        <input type="hidden" name="mode" value="individual">
                        <label class="small fw-bold text-muted mb-2">SELECT STAFF MEMBER</label>
                        <select name="staff_id" class="form-select form-select-lg" onchange="this.form.submit()">
                            <option value="">-- Choose User --</option>
                            <?php mysqli_data_seek($staff_res, 0); while($s = mysqli_fetch_assoc($staff_res)): ?>
                                <option value="<?= $s['id'] ?>" <?= $selected_id == $s['id'] ? 'selected' : '' ?>><?= $s['name'] ?> (<?= $s['email'] ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($mode == 'bulk' || ($mode == 'individual' && $selected_id > 0)): ?>
            <form action="db/update/permission_handler.php" method="POST">
                <input type="hidden" name="update_type" value="<?= $mode ?>">
                <input type="hidden" name="staff_id" value="<?= $selected_id ?>">

                <div class="card card-modern p-4 <?= $mode == 'bulk' ? 'border-danger' : '' ?>">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold"><?= $mode == 'bulk' ? 'Global Permissions (Affects Everyone)' : 'User Permissions' ?></h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label small fw-bold" for="selectAll">Check All</label>
                        </div>
                    </div>

                    <div class="perm-grid">
                        <?php mysqli_data_seek($all_perms, 0); while($p = mysqli_fetch_assoc($all_perms)): ?>
                            <label class="perm-item">
                                <input type="checkbox" name="perms[]" value="<?= $p['id'] ?>" class="form-check-input perm-check" <?= in_array($p['id'], $current_perms) ? 'checked' : '' ?>>
                                <div>
                                    <div class="fw-bold text-dark small"><?= $p['description'] ?></div>
                                    <code class="text-primary" style="font-size: 0.6rem;"><?= $p['perm_key'] ?></code>
                                </div>
                            </label>
                        <?php endwhile; ?>
                    </div>

                    <div class="mt-5 text-end">
                        <button type="submit" name="save_perms" class="btn <?= $mode == 'bulk' ? 'btn-danger' : 'btn-submit-pro' ?> btn-lg px-5">
                            <?= $mode == 'bulk' ? 'Overwrite All Staff Access' : 'Update User Access' ?>
                        </button>
                    </div>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.perm-check').forEach(c => c.checked = this.checked);
});
</script>
<?php include 'footer.php'; ?>