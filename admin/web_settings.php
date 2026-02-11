<?php
include 'db/config.php';

$message = '';

$result = mysqli_query($conn, "SELECT * FROM web_settings LIMIT 1");
$settings = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $phone = mysqli_real_escape_string($conn, $_POST['site_phone']);
    $email = mysqli_real_escape_string($conn, $_POST['site_email']);
     $hremail = mysqli_real_escape_string($conn, $_POST['hr_email']);
    $address = mysqli_real_escape_string($conn, $_POST['site_address']);

    mysqli_query($conn, "
        UPDATE web_settings 
        SET site_phone='$phone',
            site_email='$email',
            site_address='$address',
            hr_email = '$hremail'
        WHERE id=" . $settings['id']
    );

    $message = "Settings Updated Successfully!";
    
    $result = mysqli_query($conn, "SELECT * FROM web_settings LIMIT 1");
    $settings = mysqli_fetch_assoc($result);
}
?>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <h4 class="mb-4">Website Contact Settings</h4>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="card p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label>Phone Number</label>
                        <input type="text" name="site_phone" class="form-control"
                               value="<?php echo htmlspecialchars($settings['site_phone']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Email Address</label>
                        <input type="email" name="site_email" class="form-control"
                               value="<?php echo htmlspecialchars($settings['site_email']); ?>" required>
                    </div>
 <div class="mb-3">
                        <label> HR Email Address</label>
                        <input type="email" name="hr_email" class="form-control"
                               value="<?php echo htmlspecialchars($settings['hr_email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Office Address</label>
                        <textarea name="site_address" class="form-control" rows="4" required><?php echo htmlspecialchars($settings['site_address']); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Settings</button>
                </form>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
