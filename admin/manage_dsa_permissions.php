<?php
include 'db/config.php';
include 'header.php';

$selected_id = isset($_GET['dsa_id']) ? (int)$_GET['dsa_id'] : 0;
$current_perms = [];
$tableReady = false;

$permTbl = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_permissions'");
$userPermTbl = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_user_permissions'");
if ($permTbl && mysqli_num_rows($permTbl) > 0 && $userPermTbl && mysqli_num_rows($userPermTbl) > 0) {
    $tableReady = true;
}

if ($tableReady && $selected_id > 0) {
    $res = mysqli_query($conn, "SELECT permission_id FROM dsa_user_permissions WHERE dsa_id = $selected_id");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $current_perms[] = (int)$row['permission_id'];
        }
    }
}

$all_perms = $tableReady ? mysqli_query($conn, "SELECT id, perm_key, description FROM dsa_permissions ORDER BY description ASC") : false;
$dsa_res = mysqli_query($conn, "SELECT id, name, email FROM dsa ORDER BY name ASC");

function dsaPermissionGroup($perm_key) {
    if (strpos($perm_key, 'dsa_dashboard_') === 0) return 'Dashboard';
    if (strpos($perm_key, 'dsa_profile_') === 0) return 'Profile';
    if (strpos($perm_key, 'dsa_lead_') === 0) return 'Lead Management';
    return 'Other';
}

$perm_groups = [];
if ($all_perms) {
    mysqli_data_seek($all_perms, 0);
    while ($p = mysqli_fetch_assoc($all_perms)) {
        $group = dsaPermissionGroup($p['perm_key']);
        if (!isset($perm_groups[$group])) {
            $perm_groups[$group] = [];
        }
        $perm_groups[$group][] = $p;
    }
}
?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            <div class="mb-4">
                <h2 class="fw-bold text-dark">DSA Access Control</h2>
                <p class="text-muted">Grant or revoke DSA feature-level permissions for each DSA account.</p>
            </div>

            <?php if (!empty($_GET['msg'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['err'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['err']) ?></div>
            <?php endif; ?>

            <?php if (!$tableReady): ?>
                <div class="alert alert-warning">Permission tables are missing. Run `dsa_migration.sql` first.</div>
            <?php endif; ?>

            <div class="card card-modern p-4 mb-4">
                <form method="GET">
                    <label class="small fw-bold text-muted mb-2">SELECT DSA AGENT</label>
                    <select name="dsa_id" class="form-select form-select-lg" onchange="this.form.submit()">
                        <option value="">-- Choose DSA Agent --</option>
                        <?php if ($dsa_res): while($s = mysqli_fetch_assoc($dsa_res)): ?>
                            <option value="<?= (int)$s['id'] ?>" <?= $selected_id === (int)$s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['email']) ?>)</option>
                        <?php endwhile; endif; ?>
                    </select>
                </form>
            </div>

            <?php if ($tableReady && $selected_id > 0): ?>
            <form action="db/update/dsa_permission_handler.php" method="POST">
                <input type="hidden" name="dsa_id" value="<?= $selected_id ?>">

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

                <div class="card card-modern p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">DSA Permissions</h5>
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
                                        <input type="checkbox" name="perms[]" value="<?= (int)$p['id'] ?>" class="form-check-input perm-check" <?= in_array((int)$p['id'], $current_perms, true) ? 'checked' : '' ?>>
                                        <div>
                                            <div class="fw-bold text-dark small"><?= htmlspecialchars($p['description']) ?></div>
                                            <div class="perm-help">Controls: <?= htmlspecialchars(str_replace('_', ' ', $p['perm_key'])) ?></div>
                                            <span class="perm-code"><?= htmlspecialchars($p['perm_key']) ?></span>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="mt-4 text-end">
                        <button type="submit" name="save_dsa_perms" class="btn btn-dark btn-lg px-5">Update DSA Access</button>
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
