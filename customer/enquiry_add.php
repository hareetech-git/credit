<?php
include 'header.php';
include 'topbar.php';
include 'sidebar.php';
require_once __DIR__ . '/../includes/enquiry_notifications.php';

$error = '';
$success = '';
$prefill_name = '';
$prefill_email = '';
$prefill_phone = '';
$selected_loan_type = isset($_GET['loan_type']) ? (int)$_GET['loan_type'] : 0;

$customer_id = (int)$_SESSION['customer_id'];
$cust_res = mysqli_query($conn, "SELECT full_name, email, phone FROM customers WHERE id = $customer_id LIMIT 1");
if ($cust_res && mysqli_num_rows($cust_res) > 0) {
    $cust = mysqli_fetch_assoc($cust_res);
    $prefill_name = $cust['full_name'] ?? '';
    $prefill_email = $cust['email'] ?? '';
    $prefill_phone = $cust['phone'] ?? '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $loan_type_id = (int)($_POST['loan_type'] ?? 0);
    $query_message = trim($_POST['query_message'] ?? '');
    $customer_id = (int)$_SESSION['customer_id'];

    if ($full_name === '' || $phone === '' || $email === '' || $loan_type_id <= 0 || $query_message === '') {
        $error = 'All fields are required.';
    } else {
        $full_name_clean = mysqli_real_escape_string($conn, $full_name);
        $phone_clean = mysqli_real_escape_string($conn, $phone);
        $email_clean = mysqli_real_escape_string($conn, $email);
        $query_message_clean = mysqli_real_escape_string($conn, $query_message);

        $loan_name_query = "SELECT sub_category_name 
                            FROM services_subcategories 
                            WHERE id = $loan_type_id AND status = 'active' AND live = 1 
                            LIMIT 1";
        $loan_name_result = mysqli_query($conn, $loan_name_query);
        $loan_type_name = '';
        if ($loan_name_result && mysqli_num_rows($loan_name_result) > 0) {
            $loan_row = mysqli_fetch_assoc($loan_name_result);
            $loan_type_name = mysqli_real_escape_string($conn, $loan_row['sub_category_name']);
        }

        $insert_query = "INSERT INTO enquiries 
                         (customer_id, full_name, phone, email, loan_type_id, loan_type_name, query_message) 
                         VALUES 
                         ($customer_id, '$full_name_clean', '$phone_clean', '$email_clean', $loan_type_id, '$loan_type_name', '$query_message_clean')";
        if (mysqli_query($conn, $insert_query)) {
            $enquiry_id = (int)mysqli_insert_id($conn);
            if ($enquiry_id > 0) {
                enquiryNotifyAdminsOnNewEnquiry($conn, $enquiry_id);
            }
            $success = 'Enquiry submitted successfully.';
        } else {
            $error = 'Something went wrong. Please try again.';
        }
    }
}

$loan_types = [];
$applied_types = [];

$applied_res = mysqli_query($conn, "SELECT DISTINCT s.sub_category_id, sc.sub_category_name
    FROM loan_applications la
    JOIN services s ON la.service_id = s.id
    JOIN services_subcategories sc ON s.sub_category_id = sc.id
    WHERE la.customer_id = $customer_id AND sc.status='active' AND sc.live=1
    ORDER BY sc.sub_category_name ASC");
if ($applied_res) {
    while ($row = mysqli_fetch_assoc($applied_res)) {
        $applied_types[] = $row;
    }
}

$other_res = mysqli_query($conn, "SELECT id AS sub_category_id, sub_category_name
    FROM services_subcategories 
    WHERE status='active' AND live=1
    ORDER BY sub_category_name ASC");
if ($other_res) {
    while ($row = mysqli_fetch_assoc($other_res)) {
        $loan_types[] = $row;
    }
}
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">New Enquiry</h2>
                    <div class="text-muted small">Submit an enquiry from your account</div>
                </div>
            </div>

            <style>
                .card-modern {
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    background: #ffffff;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
                    overflow: hidden;
                }
            </style>

            <div class="card card-modern">
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php elseif ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row g-3">
                            <input type="hidden" name="full_name" value="<?= htmlspecialchars($prefill_name) ?>">
                            <input type="hidden" name="phone" value="<?= htmlspecialchars($prefill_phone) ?>">
                            <input type="hidden" name="email" value="<?= htmlspecialchars($prefill_email) ?>">
                            <div class="col-md-6">
                                <label class="form-label">Loan Type</label>
                            <?php if ($selected_loan_type > 0): ?>
                                <input type="hidden" name="loan_type" value="<?= $selected_loan_type ?>">
                                <select class="form-select" disabled>
                                    <?php foreach ($loan_types as $lt): ?>
                                        <option value="<?= $lt['sub_category_id'] ?>" <?= ((int)$selected_loan_type === (int)$lt['sub_category_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($lt['sub_category_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <select name="loan_type" class="form-select" required>
                                    <option value="">Select</option>
                                    <?php if (!empty($applied_types)): ?>
                                        <optgroup label="From My Applications">
                                            <?php foreach ($applied_types as $lt): ?>
                                                <option value="<?= $lt['sub_category_id'] ?>"><?= htmlspecialchars($lt['sub_category_name']) ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                    <optgroup label="Other Loan Types">
                                        <?php foreach ($loan_types as $lt): ?>
                                            <option value="<?= $lt['sub_category_id'] ?>"><?= htmlspecialchars($lt['sub_category_name']) ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            <?php endif; ?>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message</label>
                                <textarea name="query_message" class="form-control" rows="4" required></textarea>
                            </div>
                        </div>
                        <button class="btn btn-dark mt-3">Submit Enquiry</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
