<?php
// backend/service_backend.php
// ==========================================
// BACKEND LOGIC - ALL DATABASE OPERATIONS
// ==========================================

function getServiceData($slug) {
    // Initialize variables
    $data = [
        'service' => null,
        'overview' => null,    
        'features' => [], 
        'why_choose' => [],    
        'eligibility' => [], 
        'documents' => [],     
        'fees' => [], 
        'banks' => [],         
        'repayments' => [],
        'error' => null,
        'pageTitle' => 'Service Details'
    ];

    // Validate slug
    if(empty($slug) || !preg_match('/^[a-z0-9\-]+$/', $slug)) {
        $data['error'] = "Invalid service URL.";
        return $data;
    }

    // Database connection
    $connFile = __DIR__ . '/../includes/connection.php';
    if(!file_exists($connFile)) {
        $data['error'] = "Database configuration not found.";
        return $data;
    }

    include($connFile);
    
    if(!isset($conn)) {
        $data['error'] = "Database connection failed.";
        return $data;
    }

    // 1. Fetch Service Details
    $query = "SELECT `id`, `category_id`, `sub_category_id`, `service_name`, `title`, `slug`, `short_description`, `long_description`,`hero_image`,  `created_at`, `updated_at` 
              FROM `services` 
              WHERE `slug` = ? AND `slug` IS NOT NULL AND `slug` != '' LIMIT 1";
    
    if($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $slug);
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result) > 0) {
                $service = mysqli_fetch_assoc($result);
                $service = array_map(function($value) { return $value === null ? '' : $value; }, $service);
                $data['service'] = $service;
                $data['pageTitle'] = $service['title'];
                $service_id = $service['id'];

                // 2. Fetch Service Overview
                $o_query = "SELECT `title`, `keys`, `values` FROM `service_overview` WHERE `service_id` = ? LIMIT 1";
                if($o_stmt = mysqli_prepare($conn, $o_query)) {
                    mysqli_stmt_bind_param($o_stmt, "i", $service_id);
                    if(mysqli_stmt_execute($o_stmt)) {
                        $o_result = mysqli_stmt_get_result($o_stmt);
                        if(mysqli_num_rows($o_result) > 0) {
                            $row = mysqli_fetch_assoc($o_result);
                            $keys = json_decode($row['keys'], true);
                            $values = json_decode($row['values'], true);
                            
                            if(is_array($keys) && is_array($values) && count($keys) === count($values)) {
                                $data['overview'] = [
                                    'intro' => $row['title'],
                                    'data' => array_combine($keys, $values)
                                ];
                            }
                        }
                    }
                    mysqli_stmt_close($o_stmt);
                }

                // 3. Fetch Service Features
                $f_query = "SELECT `title`, `description` FROM `service_features` WHERE `service_id` = ?";
                if($f_stmt = mysqli_prepare($conn, $f_query)) {
                    mysqli_stmt_bind_param($f_stmt, "i", $service_id);
                    if(mysqli_stmt_execute($f_stmt)) {
                        $f_result = mysqli_stmt_get_result($f_stmt);
                        while($row = mysqli_fetch_assoc($f_result)) { 
                            $data['features'][] = $row; 
                        }
                    }
                    mysqli_stmt_close($f_stmt);
                }

                // 4. Fetch Why Choose Us
                $w_query = "SELECT `image`, `title`, `description` FROM `service_why_choose_us` WHERE `service_id` = ?";
                if($w_stmt = mysqli_prepare($conn, $w_query)) {
                    mysqli_stmt_bind_param($w_stmt, "i", $service_id);
                    if(mysqli_stmt_execute($w_stmt)) {
                        $w_result = mysqli_stmt_get_result($w_stmt);
                        while($row = mysqli_fetch_assoc($w_result)) { 
                            $data['why_choose'][] = $row; 
                        }
                    }
                    mysqli_stmt_close($w_stmt);
                }

                // 5. Fetch Eligibility
                $e_query = "SELECT `criteria_key`, `criteria_value` FROM `service_eligibility_criteria` WHERE `service_id` = ?";
                if($e_stmt = mysqli_prepare($conn, $e_query)) {
                    mysqli_stmt_bind_param($e_stmt, "i", $service_id);
                    if(mysqli_stmt_execute($e_stmt)) {
                        $e_result = mysqli_stmt_get_result($e_stmt);
                        while($row = mysqli_fetch_assoc($e_result)) { 
                            $data['eligibility'][] = $row; 
                        }
                    }
                    mysqli_stmt_close($e_stmt);
                }

                // 6. Fetch Documents
                $d_query = "SELECT `doc_name`, `disclaimer` FROM `service_documents` WHERE `service_id` = ?";
                if($d_stmt = mysqli_prepare($conn, $d_query)) {
                    mysqli_stmt_bind_param($d_stmt, "i", $service_id);
                    if(mysqli_stmt_execute($d_stmt)) {
                        $d_result = mysqli_stmt_get_result($d_stmt);
                        while($row = mysqli_fetch_assoc($d_result)) { 
                            $data['documents'][] = $row; 
                        }
                    }
                    mysqli_stmt_close($d_stmt);
                }

                // 7. Fetch Fees
                $fee_query = "SELECT `fee_key`, `fee_value` FROM `service_fees_charges` WHERE `service_id` = ?";
                if($fee_stmt = mysqli_prepare($conn, $fee_query)) {
                    mysqli_stmt_bind_param($fee_stmt, "i", $service_id);
                    if(mysqli_stmt_execute($fee_stmt)) {
                        $fee_result = mysqli_stmt_get_result($fee_stmt);
                        while($row = mysqli_fetch_assoc($fee_result)) { 
                            $data['fees'][] = $row; 
                        }
                    }
                    mysqli_stmt_close($fee_stmt);
                }

                // 8. Fetch Banks
            $bank_query = "SELECT `bank_key`, `bank_value`, `bank_image` 
               FROM `service_banks` 
               WHERE `service_id` = ?";

                if($bank_stmt = mysqli_prepare($conn, $bank_query)) {
                    mysqli_stmt_bind_param($bank_stmt, "i", $service_id);
                    if(mysqli_stmt_execute($bank_stmt)) {
                        $bank_result = mysqli_stmt_get_result($bank_stmt);
                        while($row = mysqli_fetch_assoc($bank_result)) { 
                            $data['banks'][] = $row; 
                        }
                    }
                    mysqli_stmt_close($bank_stmt);
                }

                // 9. Fetch Repayment
                $repayment_query = "SELECT `title`, `description` FROM `service_loan_repayment` WHERE `service_id` = ?";
                if($r_stmt = mysqli_prepare($conn, $repayment_query)) {
                    mysqli_stmt_bind_param($r_stmt, "i", $service_id);
                    if(mysqli_stmt_execute($r_stmt)) {
                        $r_result = mysqli_stmt_get_result($r_stmt);
                        while($row = mysqli_fetch_assoc($r_result)) { 
                            $data['repayments'][] = $row; 
                        }
                    }
                    mysqli_stmt_close($r_stmt);
                }

            } else {
                $data['error'] = "Service not found.";
            }
        } else {
            $data['error'] = "Unable to fetch details.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $data['error'] = "Database query failed.";
    }
    
    mysqli_close($conn);
    return $data;
}
?>
