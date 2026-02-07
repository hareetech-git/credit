<?php
function renderEnquiryEmailTemplate($data) {
    $name = htmlspecialchars($data['name'] ?? 'Customer');
    $enquiryId = htmlspecialchars($data['enquiry_id'] ?? '');
    $loanType = htmlspecialchars($data['loan_type'] ?? '');
    $message = nl2br(htmlspecialchars($data['message'] ?? ''));
    $brand = htmlspecialchars($data['brand'] ?? 'Udhaar Capital');

    return '
    <div style="font-family: Arial, sans-serif; background:#f6f8fb; padding:24px;">
        <div style="max-width:640px; margin:0 auto; background:#ffffff; border:1px solid #e6eaf0; border-radius:12px; overflow:hidden;">
            <div style="background:#0f172a; color:#ffffff; padding:18px 24px; font-size:18px; font-weight:bold;">
                ' . $brand . '
            </div>
            <div style="padding:24px; color:#0f172a;">
                <p style="font-size:16px; margin:0 0 12px;">Hello ' . $name . ',</p>
                <p style="margin:0 0 16px; color:#475569;">
                    We’re following up on your enquiry 
                    regarding <strong>' . $loanType . '</strong>.
                </p>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:16px; border-radius:8px; color:#0f172a;">
                    ' . $message . '
                </div>
                <p style="margin:16px 0 0; color:#64748b; font-size:13px;">
                    If you need any more details, just reply to this email.
                </p>
            </div>
            <div style="padding:16px 24px; background:#f8fafc; color:#94a3b8; font-size:12px;">
                © ' . date('Y') . ' ' . $brand . '. All rights reserved.
            </div>
        </div>
    </div>';
}
