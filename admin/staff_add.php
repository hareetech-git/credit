<?php
include 'db/config.php';
include 'header.php';

$isEdit = false;
$staff_data = null;
$current_perms = [];

// 1. Logic for Edit Mode
if (isset($_GET['id'])) {
    $isEdit = true;
    $id = (int)$_GET['id'];

    // Fetch Staff Basic Info
    $res = mysqli_query($conn, "SELECT * FROM staff WHERE id = $id");
    $staff_data = mysqli_fetch_assoc($res);

    if (!$staff_data) {
        echo "<script>window.location='staff_list.php';</script>";
        exit;
    }

    // Fetch Existing Permissions for this user
    $p_res = mysqli_query($conn, "SELECT permission_id FROM staff_permissions WHERE staff_id = $id");
    while ($row = mysqli_fetch_assoc($p_res)) {
        $current_perms[] = $row['permission_id'];
    }
}

// 2. Fetch Data for Selects/Checkboxes
$depts = mysqli_query($conn, "SELECT id, name FROM departments ORDER BY name ASC");
$perms = mysqli_query($conn, "SELECT id, perm_key, description FROM permissions ORDER BY description ASC");
?>

<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
    }
    .content-page { background-color: #fcfcfd; min-height: 100vh; }
    .card-modern { border: 1px solid var(--slate-200); border-radius: 12px; background: #ffffff; }
    
    .perm-card {
        border: 1px solid var(--slate-200);
        border-radius: 8px;
        padding: 12px;
        transition: all 0.2s;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        height: 100%;
    }
    .perm-card:hover { background: #f8fafc; border-color: var(--slate-900); }
    .form-check-input:checked + div .perm-title { color: var(--slate-900); font-weight: 700; }
    
    .btn-submit-pro {
        background: var(--slate-900);
        color: #ffffff !important;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-0"><?= $isEdit ? 'Update Staff Member' : 'Create New Staff' ?></h2>
                    <p class="text-muted small"><?= $isEdit ? 'Modify access and details for ' . htmlspecialchars($staff_data['name']) : 'Register a new user and assign custom access levels.' ?></p>
                </div>
                <a href="staff_list.php" class="btn btn-outline-secondary px-4">Back to Directory</a>
            </div>

            <form action="db/insert/staff_handler.php" method="POST">
                <?php if($isEdit): ?>
                    <input type="hidden" name="staff_id" value="<?= $id ?>">
                <?php endif; ?>

                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card card-modern p-4 shadow-sm">
                            <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-user-circle me-2"></i>Account Details</h5>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">FULL NAME</label>
                                <input type="text" name="name" class="form-control" required 
                                       value="<?= $isEdit ? htmlspecialchars($staff_data['name']) : '' ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">EMAIL ADDRESS</label>
                                <input type="email" name="email" class="form-control" required 
                                       value="<?= $isEdit ? htmlspecialchars($staff_data['email']) : '' ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">
                                    PASSWORD <?= $isEdit ? '<span class="text-primary">(Leave blank to keep current)</span>' : '' ?>
                                </label>
                                <input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?>>
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted">ASSIGN DEPARTMENT</label>
                                <select name="department_id" class="form-select" required>
                                    <option value="">Select Department</option>
                                    <?php while($d = mysqli_fetch_assoc($depts)): ?>
                                        <option value="<?= $d['id'] ?>" 
                                            <?= ($isEdit && $staff_data['department_id'] == $d['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($d['name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card card-modern p-4 shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-shield-alt me-2"></i>Access Permissions</h5>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label small fw-bold" for="selectAll">Select All</label>
                                </div>
                            </div>
                            
                            <div class="row g-3">
                                <?php while($p = mysqli_fetch_assoc($perms)): ?>
                                <div class="col-md-6">
                                    <label class="perm-card">
                                        <input type="checkbox" name="perms[]" value="<?= $p['id'] ?>" 
                                               class="form-check-input perm-check"
                                               <?= (in_array($p['id'], $current_perms)) ? 'checked' : '' ?>>
                                        <div>
                                            <div class="small fw-bold text-dark perm-title"><?= $p['description'] ?></div>
                                            <div style="font-size: 0.65rem;" class="text-muted"><?= $p['perm_key'] ?></div>
                                        </div>
                                    </label>
                                </div>
                                <?php endwhile; ?>
                            </div>

                            <div class="mt-5 pt-4 border-top d-flex justify-content-between align-items-center">
                                <p class="text-muted small mb-0">Note: User permissions take effect immediately upon login.</p>
                                <button type="submit" name="save_staff" class="btn btn-submit-pro px-5">
                                    <i class="fas fa-save me-2"></i> <?= $isEdit ? 'Update Staff Member' : 'Create Staff Member' ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle Select All
document.getElementById('selectAll').addEventListener('change', function() {
    const checks = document.querySelectorAll('.perm-check');
    checks.forEach(c => c.checked = this.checked);
});
</script>

<?php include 'footer.php'; ?>