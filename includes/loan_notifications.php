<?php
require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/loan_email_template.php';
require_once __DIR__ . '/format_helpers.php';

function loanGetApplicationPayload(mysqli $conn, int $loan_id): ?array {
    $sql = "SELECT l.id, l.requested_amount, l.status, l.rejection_note, l.tenure_years, l.interest_rate, l.created_at,
                   c.id AS customer_id, c.full_name, c.email, c.phone,
                   s.service_name
            FROM loan_applications l
            JOIN customers c ON l.customer_id = c.id
            JOIN services s ON l.service_id = s.id
            WHERE l.id = $loan_id
            LIMIT 1";
    $res = mysqli_query($conn, $sql);
    if (!$res || mysqli_num_rows($res) === 0) {
        return null;
    }
    return mysqli_fetch_assoc($res);
}

function loanNotifyAdminsOnNewApplication(mysqli $conn, int $loan_id): void {
    $loan = loanGetApplicationPayload($conn, $loan_id);
    if (!$loan) {
        return;
    }

    $admins = [];
    $adminRes = mysqli_query($conn, "SELECT email, name FROM admin WHERE email IS NOT NULL AND email != ''");
    if ($adminRes) {
        while ($row = mysqli_fetch_assoc($adminRes)) {
            $admins[] = $row;
        }
    }

    if (empty($admins)) {
        return;
    }

    $subject = "New Loan Application #L-" . (int)$loan['id'];
    $body = renderLoanEmailTemplate(
        "New Loan Application Received",
        "A new loan application has been submitted and is waiting for review.",
        [
            'Application ID' => 'L-' . (int)$loan['id'],
            'Applicant Name' => (string)$loan['full_name'],
            'Applicant Email' => (string)$loan['email'],
            'Phone' => (string)$loan['phone'],
            'Service' => (string)$loan['service_name'],
            'Amount' => 'INR ' . format_inr((float)$loan['requested_amount'], 2),
            'Submitted At' => (string)$loan['created_at'],
        ]
    );

    foreach ($admins as $admin) {
        $toEmail = trim((string)$admin['email']);
        if ($toEmail === '') {
            continue;
        }
        $toName = trim((string)($admin['name'] ?? 'Admin'));
        sendEnquiryEmail($toEmail, $toName, $subject, $body);
    }
}

function loanNotifyCustomerDocumentsVerified(mysqli $conn, int $loan_id): void {
    $loan = loanGetApplicationPayload($conn, $loan_id);
    if (!$loan || empty($loan['email'])) {
        return;
    }

    $subject = "Loan Documents Verified - Application #L-" . (int)$loan['id'];
    $body = renderLoanEmailTemplate(
        "Documents Verified",
        "Hi {$loan['full_name']}, your submitted loan documents have been verified successfully.",
        [
            'Application ID' => 'L-' . (int)$loan['id'],
            'Service' => (string)$loan['service_name'],
            'Status' => 'Documents Verified',
        ]
    );
    sendEnquiryEmail($loan['email'], $loan['full_name'], $subject, $body);
}

function loanNotifyCustomerDecision(mysqli $conn, int $loan_id, string $status, string $note = '', bool $sendCredentials = false): void {
    $loan = loanGetApplicationPayload($conn, $loan_id);
    if (!$loan || empty($loan['email'])) {
        return;
    }

    $status = strtolower(trim($status));
    if (!in_array($status, ['approved', 'rejected'], true)) {
        return;
    }

    $safeNote = trim($note);
    $noteHtml = $safeNote !== '' ? '<p style="margin:12px 0 0 0;color:#334155;"><strong>Note:</strong> ' . nl2br(htmlspecialchars($safeNote)) . '</p>' : '';
    $subject = '';
    $body = '';

    if ($status === 'approved') {
        $subject = "Loan Approved - Application #L-" . (int)$loan['id'];
        $extraHtml = $noteHtml;

        if ($sendCredentials) {
            $plainPassword = 'UDH' . random_int(100000, 999999) . '!';
            $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
            $customer_id = (int)$loan['customer_id'];
            mysqli_query($conn, "UPDATE customers SET password='" . mysqli_real_escape_string($conn, $hashedPassword) . "' WHERE id=$customer_id");

            $extraHtml .= '
                <div style="margin-top:14px;padding:14px;border:1px solid #dbeafe;background:#eff6ff;border-radius:10px;">
                    <div style="font-size:14px;font-weight:700;color:#1e3a8a;margin-bottom:6px;">Login Credentials</div>
                    <div style="font-size:13px;color:#0f172a;"><strong>Email:</strong> ' . htmlspecialchars($loan['email']) . '</div>
                    <div style="font-size:13px;color:#0f172a;"><strong>Password:</strong> ' . htmlspecialchars($plainPassword) . '</div>
                    <div style="font-size:13px;margin-top:8px;"><a href="http://localhost/credit/login.php">Login to your account</a></div>
                </div>
            ';
        }

        $body = renderLoanEmailTemplate(
            "Loan Approved",
            "Hi {$loan['full_name']}, your loan application has been approved.",
            [
                'Application ID' => 'L-' . (int)$loan['id'],
                'Service' => (string)$loan['service_name'],
                'Approved Amount' => 'INR ' . format_inr((float)$loan['requested_amount'], 2),
                'Tenure' => (int)$loan['tenure_years'] . ' years',
                'Interest Rate' => number_format((float)$loan['interest_rate'], 2) . '%',
                'Status' => 'Approved',
            ],
            $extraHtml
        );
    } else {
        $subject = "Loan Application Update - #L-" . (int)$loan['id'];
        $body = renderLoanEmailTemplate(
            "Loan Application Update",
            "Hi {$loan['full_name']}, your loan application has been reviewed.",
            [
                'Application ID' => 'L-' . (int)$loan['id'],
                'Service' => (string)$loan['service_name'],
                'Status' => 'Rejected',
            ],
            $noteHtml . '<p style="margin:12px 0 0 0;color:#334155;">You may re-apply after updating required details/documents.</p>'
        );
    }

    sendEnquiryEmail($loan['email'], $loan['full_name'], $subject, $body);
}

function loanNotifyStaffOnAssignment(mysqli $conn, int $loan_id, int $staff_id, string $assignedBy = 'Admin'): void {
    $loan_id = (int)$loan_id;
    $staff_id = (int)$staff_id;
    if ($loan_id <= 0 || $staff_id <= 0) {
        return;
    }

    $staffRes = mysqli_query($conn, "SELECT name, email FROM staff WHERE id = $staff_id LIMIT 1");
    if (!$staffRes || mysqli_num_rows($staffRes) === 0) {
        return;
    }
    $staff = mysqli_fetch_assoc($staffRes);
    $toEmail = trim((string)($staff['email'] ?? ''));
    if ($toEmail === '') {
        return;
    }

    $loan = loanGetApplicationPayload($conn, $loan_id);
    if (!$loan) {
        return;
    }

    $subject = "Loan Assignment - Application #L-" . (int)$loan['id'];
    $body = renderLoanEmailTemplate(
        "New Loan Assigned",
        "Hi {$staff['name']}, a loan application has been manually assigned to you for review.",
        [
            'Application ID' => 'L-' . (int)$loan['id'],
            'Customer' => (string)$loan['full_name'],
            'Customer Phone' => (string)$loan['phone'],
            'Service' => (string)$loan['service_name'],
            'Amount' => 'INR ' . format_inr((float)$loan['requested_amount'], 2),
            'Assigned By' => $assignedBy,
        ],
        '<p style="margin:12px 0 0 0;color:#334155;">Please login to your staff dashboard and process this application.</p>'
    );

    sendEnquiryEmail($toEmail, (string)$staff['name'], $subject, $body);
}
