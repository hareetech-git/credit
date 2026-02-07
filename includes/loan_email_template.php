<?php
function renderLoanEmailTemplate(string $title, string $intro, array $rows = [], string $extraHtml = ''): string {
    $rowsHtml = '';
    foreach ($rows as $label => $value) {
        $rowsHtml .= '
            <tr>
                <td style="padding:10px 0;border-bottom:1px solid #eef2f7;color:#475569;font-size:14px;">' . htmlspecialchars((string)$label) . '</td>
                <td style="padding:10px 0;border-bottom:1px solid #eef2f7;color:#0f172a;font-size:14px;font-weight:700;text-align:right;">' . htmlspecialchars((string)$value) . '</td>
            </tr>
        ';
    }

    return '
    <div style="margin:0;padding:24px;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">
            <tr>
                <td style="padding:20px 24px;background:#0f172a;color:#ffffff;">
                    <div style="font-size:18px;font-weight:700;">Udhaar Capital</div>
                    <div style="font-size:12px;opacity:0.85;margin-top:4px;">Loan Application Notification</div>
                </td>
            </tr>
            <tr>
                <td style="padding:24px;">
                    <h2 style="margin:0 0 12px 0;font-size:20px;line-height:1.3;">' . htmlspecialchars($title) . '</h2>
                    <p style="margin:0 0 18px 0;font-size:14px;line-height:1.6;color:#334155;">' . nl2br(htmlspecialchars($intro)) . '</p>
                    ' . ($rowsHtml !== '' ? '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:16px;">' . $rowsHtml . '</table>' : '') . '
                    ' . $extraHtml . '
                </td>
            </tr>
            <tr>
                <td style="padding:16px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;color:#64748b;font-size:12px;">
                    This is an automated email from Udhaar Capital.
                </td>
            </tr>
        </table>
    </div>';
}
