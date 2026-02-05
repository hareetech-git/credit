<?php
include 'db/config.php';
include 'header.php';

$mode = $_GET['mode'] ?? 'individual'; 
$selected_id = isset($_GET['staff_id']) ? (int)$_GET['staff_id'] : 0;
$current_perms = [];

if ($mode == 'bulk') {
    // Mode Bulk: Fetch permissions linked to Role ID 1 (All Staff)
    $res = mysqli_query($conn, "SELECT permission_id FROM role_permissions WHERE role_id = 1");
    while ($row = mysqli_fetch_assoc($res)) {
        $current_perms[] = $row['permission_id'];
    }
} else {
    // Mode Individual: Fetch permissions linked directly to specific user
    if ($selected_id > 0) {
        $res = mysqli_query($conn, "SELECT permission_id FROM staff_permissions WHERE staff_id = $selected_id");
        while ($row = mysqli_fetch_assoc($res)) {
            $current_perms[] = $row['permission_id'];
        }
    }
}

$all_perms = mysqli_query($conn, "SELECT id, perm_key, description FROM permissions ORDER BY description ASC");
$staff_res = mysqli_query($conn, "SELECT id, name, email FROM staff ORDER BY name ASC");

function permissionGroup($perm_key) {
    if (strpos($perm_key, 'cust_') === 0) return 'Customers';
    if (strpos($perm_key, 'loan_') === 0) return 'Loans';
    if (strpos($perm_key, 'enquiry_') === 0) return 'Enquiries';
    if (strpos($perm_key, 'service_') === 0) return 'Services';
    return 'Other';
}

$perm_groups = [];
mysqli_data_seek($all_perms, 0);
while ($p = mysqli_fetch_assoc($all_perms)) {
    $group = permissionGroup($p['perm_key']);
    if (!isset($perm_groups[$group])) $perm_groups[$group] = [];
    $perm_groups[$group][] = $p;
}
?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            <div class="mb-4">
                <h2 class="fw-bold text-dark">Access Control Center</h2>
                <p class="text-muted">Manage permissions globally or for specific individuals. Each permission has a plain‑English meaning.</p>
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

                <style>
                    .perm-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 12px; }
                    .perm-item {
                        border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 14px;
                        display: flex; gap: 10px; align-items: flex-start; background: #fff;
                    }
                    .perm-item:hover { border-color: #94a3b8; }
                    .perm-group {
                        border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px; margin-bottom: 18px; background: #f8fafc;
                    }
                    .perm-group h6 { margin: 0 0 12px; font-weight: 800; color: #0f172a; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.08em; }
                    .perm-help { font-size: 0.75rem; color: #64748b; margin-top: 4px; }
                    .perm-code { font-size: 0.65rem; color: #2563eb; background: #eff6ff; padding: 2px 6px; border-radius: 6px; display: inline-block; }
                </style>

                <div class="card card-modern p-4 <?= $mode == 'bulk' ? 'border-danger' : '' ?>">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold"><?= $mode == 'bulk' ? 'Global Permissions (Affects Everyone)' : 'User Permissions' ?></h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label small fw-bold" for="selectAll">Check All</label>
                        </div>
                    </div>

                    <?php foreach ($perm_groups as $group_name => $perms): ?>
                        <div class="perm-group">
                            <h6><?= htmlspecialchars($group_name) ?></h6>
                            <div class="perm-grid">
                                <?php foreach ($perms as $p): ?>
                                    <label class="perm-item">
                                        <input type="checkbox" name="perms[]" value="<?= $p['id'] ?>" class="form-check-input perm-check" <?= in_array($p['id'], $current_perms) ? 'checked' : '' ?>>
                                        <div>
                                            <div class="fw-bold text-dark small"><?= htmlspecialchars($p['description']) ?></div>
                                            <div class="perm-help">Controls: <?= str_replace('_', ' ', $p['perm_key']) ?></div>
                                            <span class="perm-code"><?= htmlspecialchars($p['perm_key']) ?></span>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

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
