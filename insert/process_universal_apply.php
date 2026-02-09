<?php
include '../includes/connection.php';
require_once '../includes/loan_notifications.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    exit;

$session_customer_id = $_SESSION['customer_id'] ?? null;
$cid = $session_customer_id;
$mode = $_POST['mode'] ?? 'apply';
$is_guest_apply = !$session_customer_id && $mode !== 'register';
mysqli_begin_transaction($conn);

try {
    if (!$cid) {
        $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
        $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
        if (!preg_match('/^[6-9][0-9]{9}$/', $phone)) {
            throw new Exception("Enter valid 10-digit mobile number starting with 6-9.");
        }
        $check = mysqli_query($conn, "SELECT id FROM customers WHERE email = '$email' OR phone = '$phone' LIMIT 1");
        $existing = $check ? mysqli_fetch_assoc($check) : null;

        if ($mode === 'register') {
            if ($existing) {
                throw new Exception("Email or Phone already registered.");
            }
            $password = trim($_POST['password'] ?? '');
            if ($password === '' || strlen($password) < 8) {
                throw new Exception("Password must be at least 8 characters.");
            }
            $hashedPass = password_hash($password, PASSWORD_BCRYPT);
            mysqli_query($conn, "INSERT INTO customers (full_name, email, phone, password, status) VALUES ('$full_name', '$email', '$phone', '$hashedPass', 'active')");
            $cid = mysqli_insert_id($conn);
        } else {
            if ($existing) {
                $cid = (int) $existing['id'];
                mysqli_query($conn, "UPDATE customers SET full_name='$full_name', email='$email', phone='$phone' WHERE id=$cid");
            } else {
                $generatedPassword = 'UDH' . random_int(100000, 999999) . '!';
                $hashedPass = password_hash($generatedPassword, PASSWORD_BCRYPT);
                mysqli_query($conn, "INSERT INTO customers (full_name, email, phone, password, status) VALUES ('$full_name', '$email', '$phone', '$hashedPass', 'active')");
                $cid = mysqli_insert_id($conn);
            }
        }

        // Link any existing enquiries by email to this customer
        mysqli_query($conn, "UPDATE enquiries SET customer_id = $cid WHERE customer_id IS NULL AND email = '$email'");

        $pan = strtoupper(mysqli_real_escape_string($conn, $_POST['pan_number']));
        $dob = mysqli_real_escape_string($conn, $_POST['birth_date']);
        $dob_ts = strtotime($dob);
        if (!$dob_ts) {
            throw new Exception("Invalid birth date.");
        }
        $age_years = (int) date_diff(new DateTime(date('Y-m-d', $dob_ts)), new DateTime(date('Y-m-d')))->y;
        if ($age_years < 18) {
            throw new Exception("Applicant must be at least 18 years old.");
        }
        $emp = mysqli_real_escape_string($conn, $_POST['employee_type']);
        $company_name = mysqli_real_escape_string($conn, trim($_POST['company_name'] ?? ''));
        $inc = (float) $_POST['monthly_income'];
        $state = mysqli_real_escape_string($conn, $_POST['state']);
        $city = mysqli_real_escape_string($conn, $_POST['city']);
        $pin = mysqli_real_escape_string($conn, $_POST['pin_code']);
        $r1n = mysqli_real_escape_string($conn, $_POST['reference1_name']);
        $r1p = mysqli_real_escape_string($conn, $_POST['reference1_phone']);
        $r2n = mysqli_real_escape_string($conn, $_POST['reference2_name']);
        $r2p = mysqli_real_escape_string($conn, $_POST['reference2_phone']);
        if (!preg_match('/^[6-9][0-9]{9}$/', $r1p) || !preg_match('/^[6-9][0-9]{9}$/', $r2p)) {
            throw new Exception("Reference mobile numbers must be valid 10-digit numbers starting with 6-9.");
        }
        if (trim($r1n) === '' || trim($r2n) === '') {
            throw new Exception("Both reference person names are required.");
        }
        $r1n_norm = strtolower(preg_replace('/\s+/', ' ', trim((string) ($_POST['reference1_name'] ?? ''))));
        $r2n_norm = strtolower(preg_replace('/\s+/', ' ', trim((string) ($_POST['reference2_name'] ?? ''))));
        $r1p_norm = preg_replace('/\D+/', '', (string) ($_POST['reference1_phone'] ?? ''));
        $r2p_norm = preg_replace('/\D+/', '', (string) ($_POST['reference2_phone'] ?? ''));
        if ($r1n_norm !== '' && $r1n_norm === $r2n_norm) {
            throw new Exception("Reference 1 and Reference 2 names must be different.");
        }
        if ($r1p_norm !== '' && $r1p_norm === $r2p_norm) {
            throw new Exception("Reference 1 and Reference 2 phone numbers must be different.");
        }

        $profileRes = mysqli_query($conn, "SELECT id FROM customer_profiles WHERE customer_id = $cid LIMIT 1");
        if ($profileRes && mysqli_num_rows($profileRes) > 0) {
            mysqli_query($conn, "UPDATE customer_profiles SET
                                 pan_number='$pan',
                                 birth_date='$dob',
                                 state='$state',
                                 city='$city',
                                 pin_code='$pin',
                                 employee_type='$emp',
                                 company_name='$company_name',
                                 monthly_income='$inc',
                                 reference1_name='$r1n',
                                 reference1_phone='$r1p',
                                 reference2_name='$r2n',
                                 reference2_phone='$r2p'
                                 WHERE customer_id=$cid");
        } else {
            mysqli_query($conn, "INSERT INTO customer_profiles (customer_id, pan_number, birth_date, state, city, pin_code, employee_type, company_name, monthly_income, reference1_name, reference1_phone, reference2_name, reference2_phone) 
                                 VALUES ($cid, '$pan', '$dob', '$state', '$city', '$pin', '$emp', '$company_name', '$inc', '$r1n', '$r1p', '$r2n', '$r2p')");
        }

        if ($mode === 'register') {
           
            $_SESSION['customer_id'] = $cid;
            $_SESSION['customer_name'] = $full_name;
            $_SESSION['customer_email'] = $email;
            $_SESSION['customer_phone'] = $phone;
            $_SESSION['reference1_name'] = $r1n;
            $_SESSION['reference2_name'] = $r2n;
        }
    }

    if ($mode === 'register') {
        mysqli_commit($conn);
        header("Location: ../customer/dashboard.php?msg=" . urlencode("Registration successful"));
        exit;
    } else {
        $sid = (int) $_POST['service_id'];
        $amt = (float) $_POST['requested_amount'];
        if ($sid <= 0 || $amt <= 0) {
            throw new Exception("Please select a valid loan service and amount.");
        }

        mysqli_query($conn, "INSERT INTO loan_applications (customer_id, service_id, requested_amount, tenure_years, emi_amount, status) VALUES ($cid, $sid, $amt, 0, 0, 'pending')");
        $loan_id = mysqli_insert_id($conn);

        if (!empty($_FILES['loan_docs']['name'])) {
            $allowed_exts = ['pdf', 'jpg', 'jpeg', 'png'];
            $max_size = 5 * 1024 * 1024; // 5 MB
            foreach ($_FILES['loan_docs']['name'] as $key => $val) {
                if (empty($val))
                    continue;
                $ext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed_exts, true)) {
                    throw new Exception("Only PDF, JPG, JPEG, PNG files are allowed.");
                }
                $file_size = (int) ($_FILES['loan_docs']['size'][$key] ?? 0);
                if ($file_size > $max_size) {
                    throw new Exception("Each document must be 5 MB or less.");
                }
                $new_name = "loan_{$loan_id}_" . time() . "_$key.$ext";
                if (move_uploaded_file($_FILES['loan_docs']['tmp_name'][$key], "../uploads/loans/$new_name")) {
                    $db_path = "uploads/loans/$new_name";
                    $title = str_replace('_', ' ', $key);
                    mysqli_query($conn, "INSERT INTO loan_application_docs (loan_application_id, doc_name, doc_path) VALUES ($loan_id, '$title', '$db_path')");
                }
            }
        }
        mysqli_commit($conn);
        loanNotifyAdminsOnNewApplication($conn, (int) $loan_id);

        if ($is_guest_apply) {
            header("Location: ../apply-loan.php?msg=" . urlencode("Application submitted successfully. Our team will review and contact you by email."));
        } else {
            header("Location: ../customer/dashboard.php?msg=" . urlencode("Application Submitted"));
        }
        exit;
    }
} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: ../apply-loan.php?err=" . urlencode($e->getMessage()));
}
