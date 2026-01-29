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
        $title           = clean($conn, $_POST['title']);
        $short_desc      = clean($conn, $_POST['short_description']);
        $long_desc       = clean($conn, $_POST['long_description']);

        $sql = "UPDATE services SET 
                category_id = $category_id,
                sub_category_id = $sub_category_id,
                title = '$title',
                short_description = '$short_desc',
                long_description = '$long_desc',
                updated_at = NOW()
                WHERE id = $service_id";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: $base_url?service_id=$service_id&tab=info&msg=Info Updated");
        } else {
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
       8. UPDATE WHY CHOOSE US (Tab 8)
    ======================================================= */
    case 'update_why':
        $sql_template = "INSERT INTO service_why_choose_us (service_id, title, description) VALUES ($service_id, {KEY}, {VAL})";
        update_child_table($conn, $service_id, 'service_why_choose_us', $_POST['title']??[], $_POST['description']??[], $sql_template);
        header("Location: $base_url?service_id=$service_id&tab=why&msg=Reasons Updated");
        break;

    /* =======================================================
       9. UPDATE BANKS (Tab 9)
    ======================================================= */
    case 'update_bank':
        $sql_template = "INSERT INTO service_banks (service_id, bank_key, bank_value) VALUES ($service_id, {KEY}, {VAL})";
        update_child_table($conn, $service_id, 'service_banks', $_POST['bank_key']??[], $_POST['bank_value']??[], $sql_template);
        header("Location: $base_url?service_id=$service_id&tab=banks&msg=Banks Updated");
        break;

    default:
        header("Location: $base_url?error=unknown_type");
        exit;
}
?>