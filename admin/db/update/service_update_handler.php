<?php
include '../../db/config.php'; 

/* -----------------------------------------------------------
| MASTER HANDLER FOR SERVICE UPDATES
| ------------------------------------------------------------
*/

$type       = $_POST['type'] ?? '';
$service_id = (int)($_POST['service_id'] ?? 0);
$base_url   = "../../service_edit.php"; 

if ($service_id === 0) {
    header("Location: ../../services.php?error=invalid_id");
    exit;
}

function clean($conn, $str) {
    return mysqli_real_escape_string($conn, trim($str));
}

// HELPER: Wipe Old Data & Batch Insert New
function update_child_table($conn, $service_id, $table, $keys, $values, $sql_template) {
    // 1. Wipe existing rows for this service in this table
    mysqli_query($conn, "DELETE FROM $table WHERE service_id = $service_id");

    // 2. Insert new set
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


 /* =======================================================
       1. UPDATE SERVICE INFO (Tab 1)
    ======================================================= */
    case 'update_service':
        $category_id     = (int) $_POST['category_id'];
        $sub_category_id = (int) $_POST['sub_category_id'];
        
        // 1. Capture Service Name
        $service_name    = clean($conn, $_POST['service_name']);
        
        $title           = clean($conn, $_POST['title']);

        // 2. Slug Logic (Auto-generate if empty)
        $raw_slug    = $_POST['slug'] ?? '';
        $slug_source = empty($raw_slug) ? $title : $raw_slug;
        $slug        = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug_source)));

        $short_desc      = clean($conn, $_POST['short_description']);
        $long_desc       = clean($conn, $_POST['long_description']);
// HERO IMAGE LOGIC
$hero_image = clean($conn, $_POST['existing_hero_image'] ?? '');

if (!empty($_FILES['hero_image']['name'])) {

    $upload_dir = "../../../uploads/services/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $ext = pathinfo($_FILES['hero_image']['name'], PATHINFO_EXTENSION);
    $file_name = "service_" . time() . "_" . rand(100,999) . "." . $ext;
    $target = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $target)) {

        // OPTIONAL: delete old image
        if (!empty($hero_image) && file_exists("../../../".$hero_image)) {
            unlink("../../../".$hero_image);
        }

        $hero_image = "uploads/services/" . $file_name;
    }
}

        // 3. Update Database
        $sql = "UPDATE services SET 
    category_id = $category_id,
    sub_category_id = $sub_category_id,
    service_name = '$service_name',
    title = '$title',
    slug = '$slug',
    short_description = '$short_desc',
    long_description = '$long_desc',
    hero_image = '$hero_image',
    updated_at = NOW()
WHERE id = $service_id
    ";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: $base_url?service_id=$service_id&tab=info&msg=Info Updated Successfully");
        } else {
            // Debugging: echo mysqli_error($conn); 
            header("Location: $base_url?service_id=$service_id&tab=info&error=Update Failed");
        }
        break;

    /* =======================================================
       2. UPDATE OVERVIEW (Tab 2)
    ======================================================= */
    case 'update_overview':
        $title      = clean($conn, $_POST['title']);
        $raw_keys   = $_POST['keys'] ?? [];
        $raw_values = $_POST['values'] ?? [];

        $clean_keys = []; $clean_values = [];
        for ($i = 0; $i < count($raw_keys); $i++) {
            if (!empty(trim($raw_keys[$i]))) {
                $clean_keys[]   = trim($raw_keys[$i]);
                $clean_values[] = trim($raw_values[$i] ?? '');
            }
        }

        $keys_json   = clean($conn, json_encode($clean_keys, JSON_UNESCAPED_UNICODE));
        $values_json = clean($conn, json_encode($clean_values, JSON_UNESCAPED_UNICODE));

        // Delete old overview
        mysqli_query($conn, "DELETE FROM service_overview WHERE service_id = $service_id");

        // Insert new
        $sql = "INSERT INTO service_overview (service_id, title, `keys`, `values`) 
                VALUES ($service_id, '$title', '$keys_json', '$values_json')";
        
        mysqli_query($conn, $sql);
        header("Location: $base_url?service_id=$service_id&tab=overview&msg=Overview Updated");
        break;

    /* =======================================================
       3. UPDATE FEATURES (Tab 3)
    ======================================================= */
    case 'update_feature':
        $sql_template = "INSERT INTO service_features (service_id, title, description) VALUES ($service_id, {KEY}, {VAL})";
        update_child_table($conn, $service_id, 'service_features', $_POST['title']??[], $_POST['description']??[], $sql_template);
        header("Location: $base_url?service_id=$service_id&tab=features&msg=Features Updated");
        break;

    /* =======================================================
       4. UPDATE ELIGIBILITY (Tab 4)
    ======================================================= */
    case 'update_eligibility':
        $sql_template = "INSERT INTO service_eligibility_criteria (service_id, criteria_key, criteria_value) VALUES ($service_id, {KEY}, {VAL})";
        update_child_table($conn, $service_id, 'service_eligibility_criteria', $_POST['criteria_key']??[], $_POST['criteria_value']??[], $sql_template);
        header("Location: $base_url?service_id=$service_id&tab=eligibility&msg=Eligibility Updated");
        break;

    /* =======================================================
       5. UPDATE DOCUMENTS (Tab 5)
    ======================================================= */
    case 'update_document':
        $sql_template = "INSERT INTO service_documents (service_id, doc_name, disclaimer) VALUES ($service_id, {KEY}, {VAL})";
        update_child_table($conn, $service_id, 'service_documents', $_POST['doc_name']??[], $_POST['disclaimer']??[], $sql_template);
        header("Location: $base_url?service_id=$service_id&tab=documents&msg=Documents Updated");
        break;

    /* =======================================================
       6. UPDATE FEES (Tab 6)
    ======================================================= */
    case 'update_fee':
        $sql_template = "INSERT INTO service_fees_charges (service_id, fee_key, fee_value) VALUES ($service_id, {KEY}, {VAL})";
        update_child_table($conn, $service_id, 'service_fees_charges', $_POST['fee_key']??[], $_POST['fee_value']??[], $sql_template);
        header("Location: $base_url?service_id=$service_id&tab=fees&msg=Fees Updated");
        break;

    /* =======================================================
       7. UPDATE REPAYMENT (Tab 7)
    ======================================================= */
    case 'update_repayment':
        $sql_template = "INSERT INTO service_loan_repayment (service_id, title, description) VALUES ($service_id, {KEY}, {VAL})";
        update_child_table($conn, $service_id, 'service_loan_repayment', $_POST['title']??[], $_POST['description']??[], $sql_template);
        header("Location: $base_url?service_id=$service_id&tab=repayment&msg=Repayment Updated");
        break;

    /* =======================================================
       8. UPDATE WHY CHOOSE US (Tab 8 - With Images)
    ======================================================= */
    case 'update_why':
        $titles     = $_POST['title'] ?? [];
        $descs      = $_POST['description'] ?? [];
        $old_images = $_POST['existing_image'] ?? []; // Paths of existing images
        $new_images = $_FILES['image'] ?? [];

        $upload_dir = "../../../uploads/why_choose_us/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // 1. Wipe Old Data
        mysqli_query($conn, "DELETE FROM service_why_choose_us WHERE service_id = $service_id");

        // 2. Insert New Data
        for ($i = 0; $i < count($titles); $i++) {
            $title = clean($conn, $titles[$i]);
            $desc  = clean($conn, $descs[$i] ?? '');
            
            // Logic: Default to old image path
            $img_path = clean($conn, $old_images[$i] ?? '');

            // Check if a NEW file was uploaded for this index
            if (!empty($new_images['name'][$i])) {
                $ext = pathinfo($new_images['name'][$i], PATHINFO_EXTENSION);
                $file_name = "why_" . time() . "_" . rand(100,999) . "." . $ext;
                $target = $upload_dir . $file_name;

                if (move_uploaded_file($new_images['tmp_name'][$i], $target)) {
                    $img_path = "uploads/why_choose_us/" . $file_name;
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
        header("Location: $base_url?service_id=$service_id&tab=why&msg=Reasons Updated");
        break;

    /* =======================================================
       9. UPDATE BANKS (Tab 9)
    ======================================================= */
   case 'update_bank':

    $keys   = $_POST['bank_key'] ?? [];
    $vals   = $_POST['bank_value'] ?? [];
    $oldImg = $_POST['existing_image'] ?? [];
    $files  = $_FILES['image'] ?? [];

    $upload_dir = "../../assets/banks/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // delete old rows
    mysqli_query($conn, "DELETE FROM service_banks WHERE service_id = $service_id");

    for ($i = 0; $i < count($keys); $i++) {

        $bank_key   = clean($conn, $keys[$i]);
        $bank_value = clean($conn, $vals[$i] ?? '');
        $img_path   = $oldImg[$i] ?? '';

        // new image uploaded?
        if (!empty($files['name'][$i])) {
            $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $file = "bank_" . time() . "_" . rand(100,999) . "." . $ext;

            if (move_uploaded_file($files['tmp_name'][$i], $upload_dir.$file)) {
                $img_path = "admin/assets/banks/" . $file;
            }
        }

        if ($bank_key !== '') {
            mysqli_query($conn, "
                INSERT INTO service_banks 
                (service_id, bank_key, bank_value, bank_image)
                VALUES 
                ($service_id, '$bank_key', '$bank_value', '$img_path')
            ");
        }
    }

    header("Location: $base_url?service_id=$service_id&tab=banks&msg=Banks Updated Successfully");
    exit;


    default:
        header("Location: $base_url?error=unknown_type");
        exit;
}
?>