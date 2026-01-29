<?php
include '../config.php';

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

// HELPER: Batch Insert Loop
function batch_insert($conn, $keys, $values, $sql_template) {
    $count = 0;
    if (is_array($keys)) {
        for ($i = 0; $i < count($keys); $i++) {
            $k = clean($conn, $keys[$i]);
            // Value might be optional or in a second array
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
       1. CREATE SERVICE -> GO TO OVERVIEW
    ======================================================= */
    case 'create_service':
        $category_id     = (int) $_POST['category_id'];
        $sub_category_id = (int) $_POST['sub_category_id'];
        $title           = clean($conn, $_POST['title']);
        $short_desc      = clean($conn, $_POST['short_description']);
        $long_desc       = clean($conn, $_POST['long_description']);

        if ($category_id > 0 && $title !== '') {
            $sql = "INSERT INTO services 
                    (category_id, sub_category_id, title, short_description, long_description)
                    VALUES 
                    ($category_id, $sub_category_id, '$title', '$short_desc', '$long_desc')";
            
            if (mysqli_query($conn, $sql)) {
                $new_id = mysqli_insert_id($conn);
                // REDIRECT TO: Overview
                header("Location: $base_url?service_id=$new_id&tab=overview&msg=Service Created. Next: Add Overview.");
                exit;
            }
        }
        header("Location: $base_url?error=creation_failed");
        break;

    /* =======================================================
       2. SAVE OVERVIEW -> GO TO FEATURES
    ======================================================= */
    case 'save_overview':
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
        // REDIRECT TO: Features
        header("Location: $base_url?service_id=$service_id&tab=features&msg=Overview Saved. Next: Add Features.");
        break;

    /* =======================================================
       3. ADD FEATURES -> GO TO ELIGIBILITY
    ======================================================= */
    case 'add_feature':
        $titles = $_POST['title'] ?? [];
        $descs  = $_POST['description'] ?? [];
        $sql    = "INSERT INTO service_features (service_id, title, description) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $titles, $descs, $sql);
        // REDIRECT TO: Eligibility
        header("Location: $base_url?service_id=$service_id&tab=eligibility&msg=Features Saved. Next: Eligibility.");
        break;

    /* =======================================================
       4. ADD ELIGIBILITY -> GO TO DOCUMENTS
    ======================================================= */
    case 'add_eligibility':
        $keys = $_POST['criteria_key'] ?? [];
        $vals = $_POST['criteria_value'] ?? [];
        $sql  = "INSERT INTO service_eligibility_criteria (service_id, criteria_key, criteria_value) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $keys, $vals, $sql);
        // REDIRECT TO: Documents
        header("Location: $base_url?service_id=$service_id&tab=documents&msg=Eligibility Saved. Next: Documents.");
        break;

    /* =======================================================
       5. ADD DOCUMENTS -> GO TO FEES
    ======================================================= */
    case 'add_document':
        $names = $_POST['doc_name'] ?? [];
        $discs = $_POST['disclaimer'] ?? [];
        $sql   = "INSERT INTO service_documents (service_id, doc_name, disclaimer) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $names, $discs, $sql);
        // REDIRECT TO: Fees
        header("Location: $base_url?service_id=$service_id&tab=fees&msg=Documents Saved. Next: Fees.");
        break;

    /* =======================================================
       6. ADD FEES -> GO TO REPAYMENT
    ======================================================= */
    case 'add_fee':
        $keys = $_POST['fee_key'] ?? [];
        $vals = $_POST['fee_value'] ?? [];
        $sql  = "INSERT INTO service_fees_charges (service_id, fee_key, fee_value) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $keys, $vals, $sql);
        // REDIRECT TO: Repayment
        header("Location: $base_url?service_id=$service_id&tab=repayment&msg=Fees Saved. Next: Repayment.");
        break;

    /* =======================================================
       7. ADD REPAYMENT -> GO TO WHY CHOOSE US
    ======================================================= */
    case 'add_repayment':
        $titles = $_POST['title'] ?? [];
        $descs  = $_POST['description'] ?? [];
        $sql    = "INSERT INTO service_loan_repayment (service_id, title, description) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $titles, $descs, $sql);
        // REDIRECT TO: Why Choose Us
        header("Location: $base_url?service_id=$service_id&tab=why&msg=Repayment Saved. Next: Why Choose Us.");
        break;

    /* =======================================================
       8. ADD WHY CHOOSE US -> GO TO BANKS
    ======================================================= */
    case 'add_why':
        $titles = $_POST['title'] ?? [];
        $descs  = $_POST['description'] ?? [];
        $sql    = "INSERT INTO service_why_choose_us (service_id, title, description) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $titles, $descs, $sql);
        // REDIRECT TO: Banks
        header("Location: $base_url?service_id=$service_id&tab=banks&msg=Reasons Saved. Next: Banks.");
        break;

    /* =======================================================
       9. ADD BANKS -> STAY (COMPLETE)
    ======================================================= */
    case 'add_bank':
        $keys = $_POST['bank_key'] ?? [];
        $vals = $_POST['bank_value'] ?? [];
        $sql  = "INSERT INTO service_banks (service_id, bank_key, bank_value) VALUES ($service_id, {KEY}, {VAL})";
        batch_insert($conn, $keys, $vals, $sql);
        // REDIRECT TO: Self (Finished)
        header("Location: $base_url?service_id=$service_id&tab=banks&msg=Service Setup Complete!");
        break;

    default:
        header("Location: $base_url?error=unknown_type");
        exit;
}
?>