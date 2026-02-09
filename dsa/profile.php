<?php
include 'header.php';
dsaRequireAccess($conn, 'dsa_profile_manage');
include 'topbar.php';
include 'sidebar.php';

$dsa_id = (int)($_SESSION['dsa_id'] ?? 0);
$query = "SELECT d.*, dp.firm_name, dp.pan_number, dp.city, dp.state, dp.pin_code, dp.bank_name, dp.account_number, dp.ifsc_code
          FROM dsa d
          LEFT JOIN dsa_profiles dp ON dp.dsa_id = d.id
          WHERE d.id = $dsa_id
          LIMIT 1";
$res = mysqli_query($conn, $query);
$data = $res ? mysqli_fetch_assoc($res) : null;
?>
<div class="content-page"><div class="content"><div class="container-fluid pt-4">
    <div class="mb-4"><h2 class="fw-bold text-dark mb-1">My Profile</h2></div>
    <?php if ($data): ?>
    <form action="db/update_profile.php" method="POST" class="card border p-4">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Name</label><input type="text" class="form-control" value="<?= htmlspecialchars($data['name']) ?>" readonly></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="text" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" readonly></div>
            <div class="col-md-6"><label class="form-label">Phone</label><input type="text" class="form-control" value="<?= htmlspecialchars($data['phone']) ?>" readonly></div>
            <div class="col-md-6"><label class="form-label">Firm Name</label><input type="text" name="firm_name" class="form-control" value="<?= htmlspecialchars($data['firm_name'] ?? '') ?>"></div>
            <div class="col-md-4"><label class="form-label">PAN Number</label><input type="text" name="pan_number" class="form-control" value="<?= htmlspecialchars($data['pan_number'] ?? '') ?>"></div>
            <div class="col-md-4"><label class="form-label">City</label><input type="text" name="city" class="form-control" value="<?= htmlspecialchars($data['city'] ?? '') ?>"></div>
            <div class="col-md-4"><label class="form-label">State</label><input type="text" name="state" class="form-control" value="<?= htmlspecialchars($data['state'] ?? '') ?>"></div>
            <div class="col-md-4"><label class="form-label">Pin Code</label><input type="text" name="pin_code" class="form-control" value="<?= htmlspecialchars($data['pin_code'] ?? '') ?>"></div>
            <div class="col-md-4"><label class="form-label">Bank Name</label><input type="text" name="bank_name" class="form-control" value="<?= htmlspecialchars($data['bank_name'] ?? '') ?>"></div>
            <div class="col-md-4"><label class="form-label">Account Number</label><input type="text" name="account_number" class="form-control" value="<?= htmlspecialchars($data['account_number'] ?? '') ?>"></div>
            <div class="col-md-4"><label class="form-label">IFSC</label><input type="text" name="ifsc_code" class="form-control" value="<?= htmlspecialchars($data['ifsc_code'] ?? '') ?>"></div>
        </div>
        <div class="mt-3 text-end"><button type="submit" class="btn btn-dark">Update Profile</button></div>
    </form>
    <?php endif; ?>
</div></div></div>
<?php include 'footer.php'; ?>
