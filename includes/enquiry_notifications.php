<?php
require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/loan_email_template.php';

function enquiryGetPayload(mysqli $conn, int $enquiry_id): ?array
{
    $enquiry_id = (int)$enquiry_id;
    if ($enquiry_id <= 0) {
        return null;
    }

    $sql = "SELECT e.id, e.full_name, e.email, e.phone, e.loan_type_name, e.query_message, e.created_at
            FROM enquiries e
            WHERE e.id = $enquiry_id
            LIMIT 1";
    $res = mysqli_query($conn, $sql);
    if (!$res || mysqli_num_rows($res) === 0) {
        return null;
    }

    return mysqli_fetch_assoc($res);
}

function enquiryNotifyStaffOnAssignment(mysqli $conn, int $enquiry_id, int $staff_id, string $assignedBy = 'Admin'): void
{
    $enquiry_id = (int)$enquiry_id;
    $staff_id = (int)$staff_id;
    if ($enquiry_id <= 0 || $staff_id <= 0) {
        return;
    }

    $staffRes = mysqli_query($conn, "SELECT name, email FROM staff WHERE id = $staff_id LIMIT 1");
    if (!$staffRes || mysqli_num_rows($staffRes) === 0) {
        return;
    }

    $staff = mysqli_fetch_assoc($staffRes);
    $staffName = trim((string)($staff['name'] ?? 'Staff'));
    $toEmail = trim((string)($staff['email'] ?? ''));
    if ($toEmail === '') {
        return;
    }

    $enquiry = enquiryGetPayload($conn, $enquiry_id);
    if (!$enquiry) {
        return;
    }

    $messagePreview = trim((string)($enquiry['query_message'] ?? ''));
    if ($messagePreview === '') {
        $messagePreview = 'No message provided.';
    }
    if (strlen($messagePreview) > 180) {
        $messagePreview = substr($messagePreview, 0, 177) . '...';
    }

    $subject = "Enquiry Assignment - Enquiry #E-" . (int)$enquiry['id'];
    $body = renderLoanEmailTemplate(
        "New Enquiry Assigned",
        "Hi {$staffName}, an enquiry has been assigned to you for follow-up.",
        [
            'Enquiry ID' => 'E-' . (int)$enquiry['id'],
            'Customer' => (string)($enquiry['full_name'] ?? ''),
            'Customer Email' => (string)($enquiry['email'] ?? ''),
            'Customer Phone' => (string)($enquiry['phone'] ?? ''),
            'Loan Type' => (string)($enquiry['loan_type_name'] ?? 'N/A'),
            'Submitted At' => (string)($enquiry['created_at'] ?? ''),
            'Assigned By' => $assignedBy,
        ],
        '<p style="margin:12px 0 0 0;color:#334155;"><strong>Message Preview:</strong><br>' . nl2br(htmlspecialchars($messagePreview)) . '</p>'
        . '<p style="margin:12px 0 0 0;color:#334155;">Please login to your staff dashboard and respond to this enquiry.</p>'
    );

    sendEnquiryEmail($toEmail, ($staffName !== '' ? $staffName : 'Staff'), $subject, $body);
}

