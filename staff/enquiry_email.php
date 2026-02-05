<?php
include 'header.php';
include 'topbar.php';
include 'sidebar.php';
include 'db/enquiry_helpers.php';

$enquiry_id = (int)($_GET['id'] ?? 0);
$staff_id = (int)$_SESSION['staff_id'];

if ($enquiry_id <= 0) {
    header("Location: enquiries.php?err=Invalid enquiry");
    exit;
}

if (!staffCanAccessEnquiry($conn, $staff_id, $enquiry_id)) {
    header("Location: enquiries.php?err=Access denied");
    exit;
}

$enquiry_sql = "SELECT e.*, c.full_name AS customer_name 
                FROM enquiries e
                LEFT JOIN customers c ON e.customer_id = c.id
                WHERE e.id = $enquiry_id
                LIMIT 1";
$enquiry_res = mysqli_query($conn, $enquiry_sql);
if (!$enquiry_res || mysqli_num_rows($enquiry_res) === 0) {
    header("Location: enquiries.php?err=Enquiry not found");
    exit;
}
$enquiry = mysqli_fetch_assoc($enquiry_res);

$subject_default = "Regarding your enquiry #{$enquiry_id}";
$message_default = "Thanks for reaching out. We’re reviewing your request and will get back to you shortly.";
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Send Email</h2>
                    <div class="text-muted small">Enquiry #<?= $enquiry_id ?></div>
                </div>
                <a href="enquiry_view.php?id=<?= $enquiry_id ?>" class="btn btn-outline-secondary btn-sm">Back</a>
            </div>

            <div class="card card-modern">
                <div class="card-body">
                    <form method="POST" action="db/enquiry_send_email.php">
                        <input type="hidden" name="enquiry_id" value="<?= $enquiry_id ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">To (Email)</label>
                                <input type="email" name="to_email" class="form-control" value="<?= htmlspecialchars($enquiry['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">To (Name)</label>
                                <input type="text" name="to_name" class="form-control" value="<?= htmlspecialchars($enquiry['full_name']) ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Subject</label>
                                <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars($subject_default) ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message</label>
                                <textarea name="message" class="form-control" rows="5" required><?= htmlspecialchars($message_default) ?></textarea>
                            </div>
                        </div>
                        <button class="btn btn-dark mt-3">Send Email</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
