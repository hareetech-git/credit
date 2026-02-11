<?php
include 'db/config.php';

$message = '';

$result = mysqli_query($conn, "SELECT * FROM web_settings LIMIT 1");
$settings = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $phone   = mysqli_real_escape_string($conn, $_POST['site_phone']);
    $email   = mysqli_real_escape_string($conn, $_POST['site_email']);
    $hremail = mysqli_real_escape_string($conn, $_POST['hr_email']);
    $address = mysqli_real_escape_string($conn, $_POST['site_address']);

    mysqli_query($conn, "
        UPDATE web_settings 
        SET site_phone='$phone',
            site_email='$email',
            site_address='$address',
            hr_email='$hremail'
        WHERE id=".$settings['id']
    );

    $message = "Settings Updated Successfully!";

    $result = mysqli_query($conn, "SELECT * FROM web_settings LIMIT 1");
    $settings = mysqli_fetch_assoc($result);
}
?>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php include 'topbar.php'; ?>

<style>
:root {
    --slate-900: #0f172a;
    --slate-600: #475569;
    --slate-200: #e2e8f0;
    --blue-500: #3b82f6;
}

.content-page { background-color: #fcfcfd; }

.card-modern {
    border: 1px solid var(--slate-200);
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

.form-label {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--slate-600);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.form-control {
    border-radius: 10px;
    border-color: var(--slate-200);
}

.form-control:focus {
    border-color: var(--blue-500);
    box-shadow: none;
}

.btn-dark {
    border-radius: 10px;
    font-weight: 600;
}
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <!-- Page Header -->
            <div class="mb-4">
                <h2 class="fw-bold text-dark mb-1">Website Contact Settings</h2>
                <p class="text-muted small mb-0">
                    Manage website phone, emails and office address.
                </p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success border-0 shadow-sm py-3 mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Settings Card -->
            <div class="card card-modern">
                <div class="card-body p-4 p-lg-5">

                    <form method="POST" class="row g-4">

                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text"
                                   name="site_phone"
                                   class="form-control"
                                   value="<?= htmlspecialchars($settings['site_phone']); ?>"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Support Email</label>
                            <input type="email"
                                   name="site_email"
                                   class="form-control"
                                   value="<?= htmlspecialchars($settings['site_email']); ?>"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">HR Email</label>
                            <input type="email"
                                   name="hr_email"
                                   class="form-control"
                                   value="<?= htmlspecialchars($settings['hr_email']); ?>"
                                   required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Office Address</label>
                            <textarea name="site_address"
                                      class="form-control"
                                      rows="4"
                                      required><?= htmlspecialchars($settings['site_address']); ?></textarea>
                        </div>

                        <div class="col-12 d-flex justify-content-end pt-2">
                            <button type="submit" class="btn btn-dark px-4 py-2">
                                <i class="fas fa-save me-2"></i>
                                Update Settings
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
