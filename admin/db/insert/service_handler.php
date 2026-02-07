<?php
include '../../db/config.php';

/* -----------------------------------------------------------
| MASTER HANDLER FOR SERVICE FORMS (WIZARD FLOW)
| ------------------------------------------------------------
*/

$type       = $_POST['type'] ?? '';
$service_id = (int)($_POST['service_id'] ?? 0);
$base_url   = "../../service_add.php";

function clean($conn, $str) {
    return mysqli_real_escape_string($conn, trim($str));
}

function normalizeImageExt($filename) {
    $ext = strtolower((string)pathinfo($filename, PATHINFO_EXTENSION));
    if ($ext === 'acif') {
        $ext = 'avif';
    }
    return $ext;
}

function isAllowedImageExt($ext) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'jfif'];
    return in_array($ext, $allowed, true);
}

// HELPER: Batch Insert Loop
function batch_insert($conn, $keys, $values, $sql_template) {
    $count = 0;
    if (is_array($keys)) {
        for ($i = 0; $i < count($keys); $i++) {
            $k = clean($conn, $keys[$i]);
            $v = isset($values[$i]) ? clean($conn, $values[$i]) : '';

            if (!empty($k)) {
                $sql = str_replace(['{KEY}', '{VAL}'], ["'$k'", "'$v'"], $sql_template);
                mysqli_query($conn, $sql);
                $count++;
            }
        }
    }
    return $count;
}

switch ($type) {

    // ... [Cases 1-7 remain exactly the same as your previous code] ...
   /* =======================================================
       1. CREATE SERVICE -> GO TO OVERVIEW
    ======================================================= */
    case 'create_service':
        $category_id     = (int) $_POST['category_id'];
        $sub_category_id = (int) $_POST['sub_category_id'];
        
        // NEW FIELD
        $service_name    = clean($conn, $_POST['service_name']); 
        
        $title           = clean($conn, $_POST['title']);
        
        // Slug Logic
        $raw_slug    = $_POST['slug'] ?? '';
        $slug_source = empty($raw_slug) ? $title : $raw_slug;
        $slug        = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug_source)));

        $short_desc      = clean($conn, $_POST['short_description']);
        $long_desc       = clean($conn, $_POST['long_description']);
        // HERO IMAGE UPLOAD
$hero_image = '';
$card_img = '';

if (!empty($_FILES['hero_image']['name'])) {
    $upload_dir = "../../../uploads/services/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $ext = normalizeImageExt($_FILES['hero_image']['name']);
    if (!isAllowedImageExt($ext)) {
        header("Location: $base_url?error=Invalid hero image format. Allowed: jpg, jpeg, png, gif, webp, avif");
        exit;
    }
    $file_name = "service_" . time() . "_" . rand(100,999) . "." . $ext;
    $target = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $target)) {
        $hero_image = "uploads/services/" . $file_name; // DB path
    }
}

// CARD IMAGE UPLOAD
if (!empty($_FILES['card_img']['name'])) {
    $upload_dir = "../../../uploads/services/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $ext = normalizeImageExt($_FILES['card_img']['name']);
    if (!isAllowedImageExt($ext)) {
        header("Location: $base_url?error=Invalid card image format. Allowed: jpg, jpeg, png, gif, webp, avif");
        exit;
    }
    $file_name = "service_card_" . time() . "_" . rand(100,999) . "." . $ext;
    $target = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['card_img']['tmp_name'], $target)) {
        $card_img = "uploads/services/" . $file_name;
    }
}


        if ($category_id > 0 && $title !== '') {
            $sql = "INSERT INTO services 
(category_id, sub_category_id, service_name, title, slug, short_description, long_description, hero_image, card_img)

                    VALUES 
                    ($category_id, $sub_category_id, '$service_name', '$title', '$slug', '$short_desc', '$long_desc', '$hero_image', '$card_img')";
            
            if (mysqli_query($conn, $sql)) {
                $new_id = mysqli_insert_id($conn);
                header("Location: $base_url?service_id=$new_id&tab=overview&msg=Service Created. Next: Add Overview.");
                exit;
            }
        }
        header("Location: $base_url?error=creation_failed");
        break;
        
    case 'save_overview':
        // FIX: Added Title handling
        $title      = clean($conn, $_POST['title']); 
        $raw_keys   = $_POST['keys'] ?? [];
        $raw_values = $_POST['values'] ?? [];

        $clean_keys = []; 
        $clean_values = [];
        for ($i = 0; $i < count($raw_keys); $i++) {
            if (!empty(trim($raw_keys[$i]))) {
                $clean_keys[]   = trim($raw_keys[$i]);
                $clean_values[] = trim($raw_values[$i] ?? '');
            }
        }

        $keys_json   = clean($conn, json_encode($clean_keys, JSON_UNESCAPED_UNICODE));
        $values_json = clean($conn, json_encode($clean_values, JSON_UNESCAPED_UNICODE));

        mysqli_query($conn, "DELETE FROM service_overview WHERE service_id = $service_id");

        $sql = "INSERT INTO service_overview (service_id, title, `keys`, `values`) 
                VALUES ($service_id, '$title', '$keys_json', '$values_json')";

        mysqli_query($conn, $sql);
        header("Location: $base_url?service_id=$service_id&tab=features&msg=Overview Saved. Next: Add Features.");
        break;

    case 'add_feature':
        $titles = $_POST['title'] ?? [];
        $descs  = $_POST['description'] ?? [];
        $sql    = "INSERT INTO service_features (service_id, title, description) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $titles, $descs, $sql);
        header("Location: $base_url?service_id=$service_id&tab=eligibility&msg=Features Saved. Next: Eligibility.");
        break;

    case 'add_eligibility':
        $keys = $_POST['criteria_key'] ?? [];
        $vals = $_POST['criteria_value'] ?? [];
        $sql  = "INSERT INTO service_eligibility_criteria (service_id, criteria_key, criteria_value) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $keys, $vals, $sql);
        header("Location: $base_url?service_id=$service_id&tab=documents&msg=Eligibility Saved. Next: Documents.");
        break;

    case 'add_document':
        $names = $_POST['doc_name'] ?? [];
        $discs = $_POST['disclaimer'] ?? [];
        $sql   = "INSERT INTO service_documents (service_id, doc_name, disclaimer) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $names, $discs, $sql);
        header("Location: $base_url?service_id=$service_id&tab=fees&msg=Documents Saved. Next: Fees.");
        break;

    case 'add_fee':
        $keys = $_POST['fee_key'] ?? [];
        $vals = $_POST['fee_value'] ?? [];
        $sql  = "INSERT INTO service_fees_charges (service_id, fee_key, fee_value) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $keys, $vals, $sql);
        header("Location: $base_url?service_id=$service_id&tab=repayment&msg=Fees Saved. Next: Repayment.");
        break;

    case 'add_repayment':
        $titles = $_POST['title'] ?? [];
        $descs  = $_POST['description'] ?? [];
        $sql    = "INSERT INTO service_loan_repayment (service_id, title, description) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $titles, $descs, $sql);
        header("Location: $base_url?service_id=$service_id&tab=why&msg=Repayment Saved. Next: Why Choose Us.");
        break;

    /* =======================================================
       8. ADD WHY CHOOSE US (With Images)
    ======================================================= */
    case 'add_why':
        $titles = $_POST['title'] ?? [];
        $descs  = $_POST['description'] ?? [];
        
        // Handle Files
        $images = $_FILES['image'] ?? [];

        $upload_dir = "../../../uploads/why_choose_us/"; // Adjusted path relative to this file
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        for ($i = 0; $i < count($titles); $i++) {
            $title    = clean($conn, $titles[$i]);
            $desc     = clean($conn, $descs[$i] ?? '');
            $img_path = '';

            // Check if file exists at this index
            if (!empty($images['name'][$i])) {
                $ext = normalizeImageExt($images['name'][$i]);
                if (!isAllowedImageExt($ext)) {
                    continue;
                }
                $file_name = "why_" . time() . "_" . rand(100,999) . "." . $ext;
                $target = $upload_dir . $file_name;

                if (move_uploaded_file($images['tmp_name'][$i], $target)) {
                    $img_path = "uploads/why_choose_us/" . $file_name; // Path stored in DB
                }
            }

            if ($title !== '') {
                $sql = "INSERT INTO service_why_choose_us 
                        (service_id, image, title, description)
                        VALUES 
                        ($service_id, '$img_path', '$title', '$desc')";
                mysqli_query($conn, $sql);
            }
        }

        header("Location: $base_url?service_id=$service_id&tab=banks&msg=Why Choose Us Saved. Next: Banks.");
        break;

    /* =======================================================
       9. ADD BANKS
    ======================================================= */
   case 'add_bank':

    $keys   = $_POST['bank_key'] ?? [];
    $vals   = $_POST['bank_value'] ?? [];
    $images = $_FILES['image'] ?? [];

    // ✅ STORE INSIDE admin/assets/
    $upload_dir = "../../assets/banks/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    for ($i = 0; $i < count($keys); $i++) {

        $bank_key   = clean($conn, $keys[$i]);
        $bank_value = clean($conn, $vals[$i] ?? '');
        $img_path   = '';

        if (!empty($images['name'][$i])) {
            $ext = normalizeImageExt($images['name'][$i]);
            if (!isAllowedImageExt($ext)) {
                continue;
            }
            $file_name = "bank_" . time() . "_" . rand(100,999) . "." . $ext;
            $target = $upload_dir . $file_name;

            if (move_uploaded_file($images['tmp_name'][$i], $target)) {
                // ✅ Path saved in DB
                $img_path = "admin/assets/banks/" . $file_name;
            }
        }

        if ($bank_key !== '') {
            $sql = "INSERT INTO service_banks 
                    (service_id, bank_key, bank_value, bank_image)
                    VALUES 
                    ($service_id, '$bank_key', '$bank_value', '$img_path')";
            mysqli_query($conn, $sql);
        }
    }

    header("Location: ../../services.php?msg=Service Created Successfully");
    exit;


    default:
        header("Location: $base_url?error=unknown_type");
        exit;
}
?>
