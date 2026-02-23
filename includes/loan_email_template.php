<?php
function renderLoanEmailTemplate(string $title, string $intro, array $rows = [], string $extraHtml = ''): string {
    $rowsHtml = '';
    foreach ($rows as $label => $value) {
        $rowsHtml .= '
            <tr>
                <td style="padding:12px 0;border-bottom:1px solid #e2e8f0;">
                    <div style="font-size:12px;line-height:1.4;color:#64748b;font-weight:700;letter-spacing:0.2px;">' . htmlspecialchars((string)$label) . '</div>
                    <div style="font-size:14px;line-height:1.6;color:#0f172a;font-weight:700;word-break:break-word;">' . htmlspecialchars((string)$value) . '</div>
                </td>
            </tr>
        ';
    }

    return '<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>' . htmlspecialchars($title) . '</title>
    <style>
        body, table, td { margin: 0; padding: 0; }
        table { border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { border: 0; line-height: 100%; outline: none; text-decoration: none; }
        @media only screen and (max-width: 640px) {
            .uc-shell { width: 100% !important; }
            .uc-pad { padding: 18px !important; }
            .uc-header { padding: 18px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <center style="width:100%;background:#f8fafc;">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f8fafc;">
            <tr>
                <td align="center" style="padding:16px 8px;">
                    <!--[if mso]>
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="640">
                        <tr>
                            <td>
                    <![endif]-->
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" class="uc-shell" style="width:100%;max-width:640px;background:#ffffff;border:1px solid #e2e8f0;">
                        <tr>
                            <td class="uc-header" style="padding:20px 24px;background:#0f172a;color:#ffffff;">
                                <div style="font-size:18px;font-weight:700;line-height:1.3;">Udhaar Capital</div>
                                <div style="font-size:12px;line-height:1.4;opacity:0.9;margin-top:4px;">Loan Application Notification</div>
                            </td>
                        </tr>
                        <tr>
                            <td class="uc-pad" style="padding:24px;">
                                <h2 style="margin:0 0 12px 0;font-size:20px;line-height:1.3;color:#0f172a;">' . htmlspecialchars($title) . '</h2>
                                <p style="margin:0 0 18px 0;font-size:14px;line-height:1.6;color:#334155;">' . nl2br(htmlspecialchars($intro)) . '</p>
                                ' . ($rowsHtml !== '' ? '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:16px;">' . $rowsHtml . '</table>' : '') . '
                                ' . ($extraHtml !== '' ? '<div style="font-size:14px;line-height:1.6;color:#334155;">' . $extraHtml . '</div>' : '') . '
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:14px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;color:#64748b;font-size:12px;line-height:1.5;">
                                This is an automated email from Udhaar Capital.
                            </td>
                        </tr>
                    </table>
                    <!--[if mso]>
                            </td>
                        </tr>
                    </table>
                    <![endif]-->
                </td>
            </tr>
        </table>
    </center>
</body>
</html>';
}
