<?php
include '../config.php';

/* -----------------------------------------------------------
| MASTER HANDLER FOR SERVICE FORMS (BATCH INSERT SUPPORT)
| ------------------------------------------------------------
*/

$type       = $_POST['type'] ?? '';
$service_id = (int)($_POST['service_id'] ?? 0);
$base_url   = "../../service_add.php";

function clean($conn, $str) {
    return mysqli_real_escape_string($conn, trim($str));
}

// HELPER: Batch Insert Loop
// loops through array inputs and runs the query for each valid row
function batch_insert($conn, $keys, $values, $sql_template) {
    $count = 0;
    if (is_array($keys)) {
        for ($i = 0; $i < count($keys); $i++) {
            $k = clean($conn, $keys[$i]);
            // Value might be optional or in a second array
            $v = isset($values[$i]) ? clean($conn, $values[$i]) : '';

            if (!empty($k)) {
                // Replace placeholders in SQL (e.g., '{KEY}', '{VAL}')
                $sql = str_replace(['{KEY}', '{VAL}'], ["'$k'", "'$v'"], $sql_template);
                mysqli_query($conn, $sql);
                $count++;
            }
        }
    }
    return $count;
}

switch ($type) {

    // 1. CREATE SERVICE (Single Entry - No Change)
    case 'create_service':
        $category_id     = (int) $_POST['category_id'];
        $sub_category_id = (int) $_POST['sub_category_id'];
        $title           = clean($conn, $_POST['title']);
        $short_desc      = clean($conn, $_POST['short_description']);
        $long_desc       = clean($conn, $_POST['long_description']);

        if ($category_id > 0 && $title !== '') {
            $sql = "INSERT INTO services (category_id, sub_category_id, title, short_description, long_description)
                    VALUES ($category_id, $sub_category_id, '$title', '$short_desc', '$long_desc')";
            if (mysqli_query($conn, $sql)) {
                $new_id = mysqli_insert_id($conn);
                header("Location: $base_url?service_id=$new_id&tab=overview&msg=created");
                exit;
            }
        }
        header("Location: $base_url?error=creation_failed");
        break;

    // 2. OVERVIEW (Key/Value - Replaces Existing)
    case 'save_overview':
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

        mysqli_query($conn, "DELETE FROM service_overview WHERE service_id = $service_id");
        $sql = "INSERT INTO service_overview (service_id, title, `keys`, `values`) VALUES ($service_id, '$title', '$keys_json', '$values_json')";
        mysqli_query($conn, $sql);
        
        // Redirect back to Overview (or Features if you prefer)
        header("Location: $base_url?service_id=$service_id&tab=overview&msg=saved");
        break;

    // 3. FEATURES (Batch Insert)
    case 'add_feature':
        $titles = $_POST['title'] ?? [];
        $descs  = $_POST['description'] ?? [];
        $sql    = "INSERT INTO service_features (service_id, title, description) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $titles, $descs, $sql);
        header("Location: $base_url?service_id=$service_id&tab=features&msg=features_added");
        break;

    // 4. ELIGIBILITY (Batch Insert)
    case 'add_eligibility':
        $keys = $_POST['criteria_key'] ?? [];
        $vals = $_POST['criteria_value'] ?? [];
        $sql  = "INSERT INTO service_eligibility_criteria (service_id, criteria_key, criteria_value) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $keys, $vals, $sql);
        header("Location: $base_url?service_id=$service_id&tab=eligibility&msg=criteria_added");
        break;

    // 5. DOCUMENTS (Batch Insert)
    case 'add_document':
        $names = $_POST['doc_name'] ?? [];
        $discs = $_POST['disclaimer'] ?? [];
        $sql   = "INSERT INTO service_documents (service_id, doc_name, disclaimer) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $names, $discs, $sql);
        header("Location: $base_url?service_id=$service_id&tab=documents&msg=docs_added");
        break;

    // 6. FEES (Batch Insert)
    case 'add_fee':
        $keys = $_POST['fee_key'] ?? [];
        $vals = $_POST['fee_value'] ?? [];
        $sql  = "INSERT INTO service_fees_charges (service_id, fee_key, fee_value) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $keys, $vals, $sql);
        header("Location: $base_url?service_id=$service_id&tab=fees&msg=fees_added");
        break;

    // 7. REPAYMENT (Batch Insert)
    case 'add_repayment':
        $titles = $_POST['title'] ?? [];
        $descs  = $_POST['description'] ?? [];
        $sql    = "INSERT INTO service_loan_repayment (service_id, title, description) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $titles, $descs, $sql);
        header("Location: $base_url?service_id=$service_id&tab=repayment&msg=repayment_added");
        break;

    // 8. WHY CHOOSE US (Batch Insert)
    case 'add_why':
        $titles = $_POST['title'] ?? [];
        $descs  = $_POST['description'] ?? [];
        $sql    = "INSERT INTO service_why_choose_us (service_id, title, description) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $titles, $descs, $sql);
        header("Location: $base_url?service_id=$service_id&tab=why&msg=reasons_added");
        break;

    // 9. BANKS (Batch Insert)
    case 'add_bank':
        $keys = $_POST['bank_key'] ?? [];
        $vals = $_POST['bank_value'] ?? [];
        $sql  = "INSERT INTO service_banks (service_id, bank_key, bank_value) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $keys, $vals, $sql);
        header("Location: $base_url?service_id=$service_id&tab=banks&msg=banks_added");
        break;

    default:
        header("Location: $base_url?error=unknown_type");
        exit;
}
?>