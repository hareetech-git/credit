<?php
require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/loan_email_template.php';
require_once __DIR__ . '/app_env.php';

function dsaGetRequestPayload(mysqli $conn, int $request_id): ?array {
    $sql = "SELECT r.*, c.full_name AS customer_name, c.email AS customer_email, c.phone AS customer_phone
            FROM dsa_requests r
            LEFT JOIN customers c ON c.id = r.customer_id
            WHERE r.id = $request_id
            LIMIT 1";
    $res = mysqli_query($conn, $sql);
    if (!$res || mysqli_num_rows($res) === 0) {
        return null;
    }
    return mysqli_fetch_assoc($res);
}

function dsaNotifyAdminsOnNewRequest(mysqli $conn, int $request_id): void {
    $request = dsaGetRequestPayload($conn, $request_id);
    if (!$request) {
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

    $subject = 'New DSA Registration Request #' . (int)$request['id'];
    $body = renderLoanEmailTemplate(
        'New DSA Agent Request',
        'A customer has submitted a request to become a DSA agent and is waiting for admin verification.',
        [
            'Request ID' => 'DSA-' . (int)$request['id'],
            'Applicant Name' => (string)$request['full_name'],
            'Email' => (string)$request['email'],
            'Phone' => (string)$request['phone'],
            'Firm Name' => (string)($request['firm_name'] ?? '-'),
            'Submitted At' => (string)$request['created_at'],
        ],
        '<p style="margin:12px 0 0 0;"><a href="' . htmlspecialchars(uc_base_url('/admin/dsa_requests.php')) . '">Review request in admin panel</a></p>'
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

function dsaNotifyApplicantDecision(mysqli $conn, int $request_id, string $status, string $note = '', ?string $dsaEmail = null, ?string $plainPassword = null): void {
    $request = dsaGetRequestPayload($conn, $request_id);
    if (!$request) {
        return;
    }

    $toEmail = trim((string)$request['email']);
    if ($toEmail === '') {
        return;
    }

    $toName = trim((string)$request['full_name']);
    $status = strtolower(trim($status));
    $safeNote = trim($note);
    $noteHtml = $safeNote !== '' ? '<p style="margin:12px 0 0 0;color:#334155;"><strong>Admin Note:</strong> ' . nl2br(htmlspecialchars($safeNote)) . '</p>' : '';

    if ($status === 'approved') {
        $subject = 'DSA Request Approved - Credentials Inside';

        $credentialHtml = '';
        if (!empty($dsaEmail) && !empty($plainPassword)) {
            $credentialHtml = '
                <div style="margin-top:14px;padding:14px;border:1px solid #dbeafe;background:#eff6ff;border-radius:10px;">
                    <div style="font-size:14px;font-weight:700;color:#1e3a8a;margin-bottom:6px;">Your DSA Login Credentials</div>
                    <div style="font-size:13px;color:#0f172a;"><strong>Login Email:</strong> ' . htmlspecialchars($dsaEmail) . '</div>
                    <div style="font-size:13px;color:#0f172a;"><strong>Password:</strong> ' . htmlspecialchars($plainPassword) . '</div>
                    <div style="font-size:13px;margin-top:8px;"><a href="' . htmlspecialchars(uc_base_url('/dsa/index.php')) . '">Login to DSA Portal</a></div>
                </div>
            ';
        }

        $body = renderLoanEmailTemplate(
            'DSA Request Approved',
            'Hi ' . $toName . ', your request to become a DSA agent has been approved.',
            [
                'Request ID' => 'DSA-' . (int)$request['id'],
                'Status' => 'Approved',
            ],
            $noteHtml . $credentialHtml
        );

        sendEnquiryEmail($toEmail, $toName, $subject, $body);
        return;
    }

    if ($status === 'rejected') {
        $subject = 'DSA Request Update';
        $body = renderLoanEmailTemplate(
            'DSA Request Update',
            'Hi ' . $toName . ', your request to become a DSA agent was not approved at this time.',
            [
                'Request ID' => 'DSA-' . (int)$request['id'],
                'Status' => 'Rejected',
            ],
            $noteHtml . '<p style="margin:12px 0 0 0;color:#334155;">You can submit a new request after correcting your details.</p>'
        );
        sendEnquiryEmail($toEmail, $toName, $subject, $body);
    }
}

function dsaNotifyCreatedByAdmin(string $toEmail, string $toName, string $plainPassword): bool {
    $toEmail = trim($toEmail);
    $toName = trim($toName);
    $plainPassword = trim($plainPassword);

    if ($toEmail === '' || $plainPassword === '') {
        return false;
    }

    $subject = 'Your DSA Account Has Been Created';
    $body = renderLoanEmailTemplate(
        'DSA Account Created',
        'Hi ' . ($toName !== '' ? $toName : 'DSA Partner') . ', your DSA account has been created by admin.',
        [
            'Account Status' => 'Active',
            'Login Email' => $toEmail,
        ],
        '
            <div style="margin-top:14px;padding:14px;border:1px solid #dbeafe;background:#eff6ff;border-radius:10px;">
                <div style="font-size:14px;font-weight:700;color:#1e3a8a;margin-bottom:6px;">Your DSA Login Credentials</div>
                <div style="font-size:13px;color:#0f172a;"><strong>Email:</strong> ' . htmlspecialchars($toEmail) . '</div>
                <div style="font-size:13px;color:#0f172a;"><strong>Password:</strong> ' . htmlspecialchars($plainPassword) . '</div>
                <div style="font-size:13px;margin-top:8px;"><a href="' . htmlspecialchars(uc_base_url('/dsa/index.php')) . '">Login to DSA Portal</a></div>
            </div>
        '
    );

    return sendEnquiryEmail($toEmail, ($toName !== '' ? $toName : 'DSA Partner'), $subject, $body);
}
?>
