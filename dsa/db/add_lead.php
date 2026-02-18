<?php
session_start();
include 'config.php';

if (!isset($_SESSION['dsa_id'])) {
    header('Location: ../index.php?err=Please login first');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../add-lead.php?err=Invalid request');
    exit; 
}

$dsa_id = (int)$_SESSION['dsa_id']; 

$permTbl = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_permissions'");
$userPermTbl = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_user_permissions'");
if ($permTbl && mysqli_num_rows($permTbl) > 0 && $userPermTbl && mysqli_num_rows($userPermTbl) > 0) {
    $permRes = mysqli_query($conn, "SELECT 1
        FROM dsa_user_permissions up
        INNER JOIN dsa_permissions p ON p.id = up.permission_id
        WHERE up.dsa_id = $dsa_id AND p.perm_key = 'dsa_lead_create'
        LIMIT 1");
    if (!$permRes || mysqli_num_rows($permRes) === 0) {
        header('Location: ../dashboard.php?err=Access denied');
        exit;
    } 
}

$hasDsaColumn = false;
$dsaColRes = mysqli_query($conn, "SHOW COLUMNS FROM loan_applications LIKE 'dsa_id'");
if ($dsaColRes && mysqli_num_rows($dsaColRes) > 0) {
    $hasDsaColumn = true;
}

$full_name = mysqli_real_escape_string($conn, trim((string)($_POST['full_name'] ?? '')));
$phone = mysqli_real_escape_string($conn, trim((string)($_POST['phone'] ?? '')));
$email = mysqli_real_escape_string($conn, trim((string)($_POST['email'] ?? '')));
$pan_plain = strtoupper(trim((string)($_POST['pan_number'] ?? '')));
$birth_date = trim((string)($_POST['birth_date'] ?? ''));
$employee_type = mysqli_real_escape_string($conn, trim((string)($_POST['employee_type'] ?? 'salaried')));
$company_name = mysqli_real_escape_string($conn, trim((string)($_POST['company_name'] ?? '')));
$monthly_income = (float)($_POST['monthly_income'] ?? 0);
$state = mysqli_real_escape_string($conn, trim((string)($_POST['state'] ?? '')));
$city = mysqli_real_escape_string($conn, trim((string)($_POST['city'] ?? '')));
$pin_code = mysqli_real_escape_string($conn, trim((string)($_POST['pin_code'] ?? '')));
$reference1_name = mysqli_real_escape_string($conn, trim((string)($_POST['reference1_name'] ?? '')));
$reference1_phone = mysqli_real_escape_string($conn, trim((string)($_POST['reference1_phone'] ?? '')));
$reference2_name = mysqli_real_escape_string($conn, trim((string)($_POST['reference2_name'] ?? '')));
$reference2_phone = mysqli_real_escape_string($conn, trim((string)($_POST['reference2_phone'] ?? '')));

$service_id = (int)($_POST['service_id'] ?? 0);
$requested_amount = (float)($_POST['requested_amount'] ?? 0);

if (
    $full_name === '' || $phone === '' || $email === '' || $pan_plain === '' || $birth_date === '' ||
    $state === '' || $city === '' || $pin_code === '' || $reference1_name === '' || $reference1_phone === '' ||
    $reference2_name === '' || $reference2_phone === '' || $service_id <= 0 || $requested_amount <= 0
) {
    header('Location: ../add-lead.php?err=Please fill all required fields');
    exit;
}

if (!preg_match('/^[6-9][0-9]{9}$/', $phone)) {
    header('Location: ../add-lead.php?err=Enter valid 10-digit mobile number');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../add-lead.php?err=Enter valid email address');
    exit;
}

if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $pan_plain)) {
    header('Location: ../add-lead.php?err=Enter valid PAN number');
    exit;
}

$dob_ts = strtotime($birth_date);
if (!$dob_ts) {
    header('Location: ../add-lead.php?err=Invalid birth date');
    exit;
}

$age_years = (int)date_diff(new DateTime(date('Y-m-d', $dob_ts)), new DateTime(date('Y-m-d')))->y;
if ($age_years < 18) {
    header('Location: ../add-lead.php?err=Applicant must be at least 18 years old');
    exit;
}

if (!in_array($employee_type, ['salaried', 'business', 'self_employed', 'professional'], true)) {
    $employee_type = 'salaried';
}

if ($employee_type === 'business' && $company_name === '') {
    header('Location: ../add-lead.php?err=Company or business name is required');
    exit;
}

if ($monthly_income < 0) {
    header('Location: ../add-lead.php?err=Monthly income cannot be negative');
    exit;
} 

if (!preg_match('/^[0-9]{6}$/', $pin_code)) {
    header('Location: ../add-lead.php?err=Enter valid 6-digit pin code');
    exit;
} 

if (!preg_match('/^[6-9][0-9]{9}$/', $reference1_phone) || !preg_match('/^[6-9][0-9]{9}$/', $reference2_phone)) {
    header('Location: ../add-lead.php?err=Enter valid reference mobile numbers');
    exit;
}

$r1p_norm = preg_replace('/\D+/', '', (string)($_POST['reference1_phone'] ?? ''));
$r2p_norm = preg_replace('/\D+/', '', (string)($_POST['reference2_phone'] ?? ''));

if ($r1p_norm !== '' && $r1p_norm === $r2p_norm) {
    header('Location: ../add-lead.php?err=Reference phone numbers must be different');
    exit;
}

$svcRes = mysqli_query($conn, "SELECT id FROM services WHERE id = $service_id LIMIT 1");
if (!$svcRes || mysqli_num_rows($svcRes) === 0) {
    header('Location: ../add-lead.php?err=Invalid loan product selected');
    exit; 
} 

mysqli_begin_transaction($conn); 

try { 
    $customer_id = 0;
    $custRes = mysqli_query($conn, "SELECT id FROM customers WHERE email = '$email' OR phone = '$phone' LIMIT 1");
    if ($custRes && mysqli_num_rows($custRes) > 0) {
        $customer = mysqli_fetch_assoc($custRes);
        $customer_id = (int)($customer['id'] ?? 0);
        mysqli_query($conn, "UPDATE customers SET full_name = '$full_name', email = '$email', phone = '$phone' WHERE id = $customer_id");
    } else {
        $plainPass = 'UDH' . random_int(100000, 999999) . '!';
        $hashPass = mysqli_real_escape_string($conn, password_hash($plainPass, PASSWORD_BCRYPT));
        mysqli_query($conn, "INSERT INTO customers (full_name, email, phone, password, status) VALUES ('$full_name', '$email', '$phone', '$hashPass', 'active')");
        $customer_id = (int)mysqli_insert_id($conn);
    }

    if ($customer_id <= 0) {
        throw new Exception('Unable to create customer'); 
    }

    mysqli_query($conn, "UPDATE enquiries SET customer_id = $customer_id WHERE customer_id IS NULL AND email = '$email'");

    $pan_encrypted = function_exists('uc_encrypt_sensitive')
        ? mysqli_real_escape_string($conn, uc_encrypt_sensitive($pan_plain))
        : mysqli_real_escape_string($conn, $pan_plain);

    $profileRes = mysqli_query($conn, "SELECT id FROM customer_profiles WHERE customer_id = $customer_id LIMIT 1");
    if ($profileRes && mysqli_num_rows($profileRes) > 0) {
        $profileId = (int)(mysqli_fetch_assoc($profileRes)['id'] ?? 0);
        if ($profileId > 0) {
            $profileSql = "UPDATE customer_profiles SET
                           pan_number = '$pan_encrypted',
                           birth_date = '$birth_date',
                           state = '$state',
                           city = '$city',
                           pin_code = '$pin_code',
                           employee_type = '$employee_type',
                           company_name = '$company_name',
                           monthly_income = $monthly_income,
                           reference1_name = '$reference1_name',
                           reference1_phone = '$reference1_phone',
                           reference2_name = '$reference2_name',
                           reference2_phone = '$reference2_phone'
                           WHERE customer_id = $customer_id";
            if (!mysqli_query($conn, $profileSql)) {
                throw new Exception('Unable to update customer profile');
            }
        }
    } else {
        $profileInsert = "INSERT INTO customer_profiles (
                            customer_id, pan_number, birth_date, state, city, pin_code,
                            employee_type, company_name, monthly_income,
                            reference1_name, reference1_phone, reference2_name, reference2_phone
                          ) VALUES (
                            $customer_id, '$pan_encrypted', '$birth_date', '$state', '$city', '$pin_code',
                            '$employee_type', '$company_name', $monthly_income,
                            '$reference1_name', '$reference1_phone', '$reference2_name', '$reference2_phone'
                          )";
        if (!mysqli_query($conn, $profileInsert)) {
            throw new Exception('Unable to save customer profile');
        }
    }

    $dsaInsertCol = $hasDsaColumn ? ', dsa_id' : '';
    $dsaInsertVal = $hasDsaColumn ? ", $dsa_id" : '';
    $loanSql = "INSERT INTO loan_applications (customer_id, service_id, requested_amount, tenure_years, emi_amount, status$dsaInsertCol)
                VALUES ($customer_id, $service_id, $requested_amount, 0, 0, 'pending'$dsaInsertVal)";
    if (!mysqli_query($conn, $loanSql)) {
        throw new Exception('Unable to create loan lead');
    }

    $loan_id = (int)mysqli_insert_id($conn);
    if ($loan_id <= 0) {
        throw new Exception('Loan creation failed');
    }

    if (!empty($_FILES['loan_docs']['name']) && is_array($_FILES['loan_docs']['name'])) {
        $allowedExt = ['pdf', 'jpg', 'jpeg', 'png'];
        $maxSize = 5 * 1024 * 1024;
        $uploadDir = realpath(__DIR__ . '/../../uploads/loans');
        if ($uploadDir === false) {
            throw new Exception('Upload directory not found');
        }

        foreach ($_FILES['loan_docs']['name'] as $key => $name) {
            if (empty($name)) {
                continue;
            }

            $ext = strtolower(pathinfo((string)$name, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExt, true)) {
                throw new Exception('Only PDF, JPG, JPEG, PNG files are allowed');
            }

            $size = (int)($_FILES['loan_docs']['size'][$key] ?? 0);
            if ($size <= 0 || $size > $maxSize) {
                throw new Exception('Each document must be less than 5 MB');
            }

            $safeKey = preg_replace('/[^A-Za-z0-9_-]/', '_', (string)$key);
            $newName = "loan_{$loan_id}_" . time() . "_{$safeKey}.{$ext}";
            $dest = $uploadDir . DIRECTORY_SEPARATOR . $newName;
            $tmp = (string)($_FILES['loan_docs']['tmp_name'][$key] ?? '');

            if ($tmp === '' || !move_uploaded_file($tmp, $dest)) {
                throw new Exception('Document upload failed');
            }

            $dbPath = "uploads/loans/$newName";
            $docTitle = mysqli_real_escape_string($conn, str_replace('_', ' ', (string)$key));
            mysqli_query($conn, "INSERT INTO loan_application_docs (loan_application_id, doc_name, doc_path, status)
                                 VALUES ($loan_id, '$docTitle', '$dbPath', 'pending')");
        }
    }

    mysqli_commit($conn);
    header('Location: ../add-lead.php?msg=Lead created successfully');
    exit;
} catch (Throwable $e) {
    mysqli_rollback($conn);
    header('Location: ../add-lead.php?err=' . urlencode($e->getMessage()));
    exit;
}
?>
