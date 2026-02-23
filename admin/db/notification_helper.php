<?php

if (!function_exists('adminNotificationColumnExists')) {
    function adminNotificationColumnExists(mysqli $conn, string $table, string $column): bool
    {
        static $cache = [];
        $key = $table . '.' . $column;
        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        $tableEsc = mysqli_real_escape_string($conn, $table);
        $columnEsc = mysqli_real_escape_string($conn, $column);
        $res = mysqli_query($conn, "SHOW COLUMNS FROM `$tableEsc` LIKE '$columnEsc'");
        $cache[$key] = ($res && mysqli_num_rows($res) > 0);
        return $cache[$key];
    }
}

if (!function_exists('adminNotificationsReady')) {
    function adminNotificationsReady(mysqli $conn): bool
    {
        return adminNotificationColumnExists($conn, 'loan_applications', 'is_read')
            && adminNotificationColumnExists($conn, 'enquiries', 'is_read');
    }
}

if (!function_exists('adminGetUnreadSummary')) {
    function adminGetUnreadSummary(mysqli $conn): array
    {
        $summary = [
            'loan' => 0,
            'enquiry' => 0,
            'total' => 0,
            'ready' => adminNotificationsReady($conn),
        ];

        if (!$summary['ready']) {
            return $summary;
        }

        $loanRes = mysqli_query($conn, "SELECT COUNT(*) AS c FROM loan_applications WHERE is_read = 0");
        $enqRes = mysqli_query($conn, "SELECT COUNT(*) AS c FROM enquiries WHERE is_read = 0");

        $summary['loan'] = $loanRes ? (int)(mysqli_fetch_assoc($loanRes)['c'] ?? 0) : 0;
        $summary['enquiry'] = $enqRes ? (int)(mysqli_fetch_assoc($enqRes)['c'] ?? 0) : 0;
        $summary['total'] = $summary['loan'] + $summary['enquiry'];

        return $summary;
    }
}

if (!function_exists('adminGetUnreadNotifications')) {
    function adminGetUnreadNotifications(mysqli $conn, int $limit = 20): array
    {
        if (!adminNotificationsReady($conn)) {
            return [];
        }

        $limit = max(1, min(100, $limit));
        $items = [];

        $sql = "
            (SELECT
                'loan' AS type,
                l.id AS ref_id,
                c.full_name AS full_name,
                c.phone AS phone,
                s.service_name AS subject,
                l.status AS status,
                l.created_at AS created_at,
                CONCAT('loan_view.php?id=', l.id) AS url
            FROM loan_applications l
            JOIN customers c ON c.id = l.customer_id
            JOIN services s ON s.id = l.service_id
            WHERE l.is_read = 0)
            UNION ALL
            (SELECT
                'enquiry' AS type,
                e.id AS ref_id,
                e.full_name AS full_name,
                e.phone AS phone,
                e.loan_type_name AS subject,
                e.status AS status,
                e.created_at AS created_at,
                CONCAT('enquiry_view.php?id=', e.id) AS url
            FROM enquiries e
            WHERE e.is_read = 0)
            ORDER BY created_at DESC
            LIMIT $limit
        ";

        $res = mysqli_query($conn, $sql);
        if (!$res) {
            return [];
        }

        while ($row = mysqli_fetch_assoc($res)) {
            $items[] = [
                'type' => (string)($row['type'] ?? ''),
                'ref_id' => (int)($row['ref_id'] ?? 0),
                'full_name' => (string)($row['full_name'] ?? ''),
                'phone' => (string)($row['phone'] ?? ''),
                'subject' => (string)($row['subject'] ?? ''),
                'status' => (string)($row['status'] ?? ''),
                'created_at' => (string)($row['created_at'] ?? ''),
                'url' => (string)($row['url'] ?? '#'),
            ];
        }

        return $items;
    }
}

if (!function_exists('adminMarkLoanAsRead')) {
    function adminMarkLoanAsRead(mysqli $conn, int $loan_id): void
    {
        $loan_id = (int)$loan_id;
        if ($loan_id <= 0 || !adminNotificationColumnExists($conn, 'loan_applications', 'is_read')) {
            return;
        }

        if (adminNotificationColumnExists($conn, 'loan_applications', 'read_at')) {
            mysqli_query($conn, "UPDATE loan_applications SET is_read = 1, read_at = NOW() WHERE id = $loan_id AND is_read = 0");
        } else {
            mysqli_query($conn, "UPDATE loan_applications SET is_read = 1 WHERE id = $loan_id AND is_read = 0");
        }
    }
}

if (!function_exists('adminMarkEnquiryAsRead')) {
    function adminMarkEnquiryAsRead(mysqli $conn, int $enquiry_id): void
    {
        $enquiry_id = (int)$enquiry_id;
        if ($enquiry_id <= 0 || !adminNotificationColumnExists($conn, 'enquiries', 'is_read')) {
            return;
        }

        if (adminNotificationColumnExists($conn, 'enquiries', 'read_at')) {
            mysqli_query($conn, "UPDATE enquiries SET is_read = 1, read_at = NOW() WHERE id = $enquiry_id AND is_read = 0");
        } else {
            mysqli_query($conn, "UPDATE enquiries SET is_read = 1 WHERE id = $enquiry_id AND is_read = 0");
        }
    }
}

