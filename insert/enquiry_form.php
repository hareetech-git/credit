<?php
// insert/enquiry_form.php
session_start();

// Include database connection
include '../includes/connection.php';

// Form submission handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_enquiry'])) {
    $form_errors = [];
    
    // Sanitize and validate inputs
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $loan_type_id = $_POST['loan_type'] ?? '';
    $query_message = trim($_POST['query_message'] ?? '');
    $customer_id = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
    
    // Validation
    if (empty($full_name)) {
        $form_errors[] = "Full name is required";
    }
    
    if (empty($phone) || !preg_match('/^[0-9]{10}$/', $phone)) {
        $form_errors[] = "Valid 10-digit phone number is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_errors[] = "Valid email address is required";
    }
    
    if (empty($loan_type_id)) {
        $form_errors[] = "Please select a loan type";
    }
    
    if (empty($query_message)) {
        $form_errors[] = "Please enter your query/message";
    }
    
    // If no errors, insert into database
    if (empty($form_errors) && isset($conn)) {
        // Escape inputs
        $full_name_clean = mysqli_real_escape_string($conn, $full_name);
        $phone_clean = mysqli_real_escape_string($conn, $phone);
        $email_clean = mysqli_real_escape_string($conn, $email);
        $loan_type_id_clean = (int)$loan_type_id;
        $query_message_clean = mysqli_real_escape_string($conn, $query_message);
        
        // Get loan type name from database
        $loan_name_query = "SELECT sub_category_name 
                           FROM services_subcategories 
                           WHERE id = $loan_type_id_clean AND status = 'active' AND live = 1 
                           LIMIT 1";
        
        $loan_name_result = mysqli_query($conn, $loan_name_query);
        $loan_type_name = '';
        
        if ($loan_name_result && mysqli_num_rows($loan_name_result) > 0) {
            $loan_row = mysqli_fetch_assoc($loan_name_result);
            $loan_type_name = mysqli_real_escape_string($conn, $loan_row['sub_category_name']);
        }
        
        // Insert enquiry
        $customer_id_sql = $customer_id ? $customer_id : "NULL";
        $insert_query = "INSERT INTO enquiries 
                        (customer_id, full_name, phone, email, loan_type_id, loan_type_name, query_message) 
                        VALUES 
                        ($customer_id_sql, '$full_name_clean', '$phone_clean', '$email_clean', $loan_type_id_clean, '$loan_type_name', '$query_message_clean')";
        
        if (mysqli_query($conn, $insert_query)) {
            // Store success message in session
            $_SESSION['success_message'] = "Thank you! Your enquiry has been submitted successfully. We'll contact you shortly.";

            // Redirect to same page to prevent resubmission (PRG Pattern)
            $redirect_to = $_POST['redirect_to'] ?? '../index.php#loanForm';
            if (strpos($redirect_to, '://') !== false || str_starts_with($redirect_to, '//')) {
                $redirect_to = '../index.php#loanForm';
            }
            header("Location: " . $redirect_to);
            exit();
        } else {
            $form_errors[] = "Something went wrong. Please try again.";
        }
    }
    
    // If there are errors, store them in session and redirect
    if (!empty($form_errors)) {
        $_SESSION['errors'] = $form_errors;
        $redirect_to = $_POST['redirect_to'] ?? '../index.php#loanForm';
        if (strpos($redirect_to, '://') !== false || str_starts_with($redirect_to, '//')) {
            $redirect_to = '../index.php#loanForm';
        }
        header("Location: " . $redirect_to);
        exit();
    }
} else {
    // If accessed directly without POST, redirect to home
    header("Location: ../index.php");
    exit();
}
?>
