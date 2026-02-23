<?php
require_once __DIR__ . '/db/notification_helper.php';
$adminNoticeSummary = adminGetUnreadSummary($conn);
$adminUnreadNotifications = adminGetUnreadNotifications($conn, 12);
$adminUnreadTotal = (int)($adminNoticeSummary['total'] ?? 0);
?>

<style>
    .admin-noti-count {
        position: absolute;
        top: 19px;
        right: 6px;
        min-width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #850d0d;
        color: #fff;
        font-size: 10px;
        line-height: 18px;
        text-align: center;
        font-weight: 700;
        padding: 0 4px;
    }
    .admin-noti-scroll {
        max-height: 360px;
        overflow-y: auto;
    }
    .admin-noti-item {
        border-bottom: 1px solid #f1f5f9;
        white-space: normal;
    }
    .admin-noti-item:last-child {
        border-bottom: 0;
    }
    .admin-noti-item .title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 2px 0;
        line-height: 1.2;
    }
    .admin-noti-item .meta {
        font-size: 0.74rem;
        color: #64748b;
        line-height: 1.25;
    }
    .admin-noti-item .icon-chip {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .admin-noti-item.loan .icon-chip {
        background: #eff6ff;
        color: #1d4ed8;
    }
    .admin-noti-item.enquiry .icon-chip {
        background: #ecfeff;
        color: #0e7490;
    }
</style>

<!-- ========== Topbar Start ========== -->
<div class="navbar-custom">
    <div class="topbar container-fluid">
        <div class="d-flex align-items-center gap-1">

            <!-- Topbar Brand Logo -->
            <div class="logo-topbar">
                <!-- Logo light -->
                <a href="index.php" class="logo-light">
                    <span class="logo-lg">
                        <img src="assets/udhaar_logo.png" alt="logo">
                    </span>
                    <span class="logo-sm">
                        <img src="assets/udhaar_logo.png" alt="small logo">
                    </span>
                </a>

                <!-- Logo Dark -->
                <a href="index.php" class="logo-dark">
                    <span class="logo-lg">
                        <img src="assets/udhaar_logo.png" alt="dark logo">
                    </span>
                    <span class="logo-sm">
                        <img src="assets/udhaar_logo.png" alt="small logo">
                    </span>
                </a>
            </div>

            <!-- Sidebar Menu Toggle Button -->
            <button class="button-toggle-menu">
                <i class="mdi mdi-menu"></i>
            </button>

            <!-- Page Title -->
            <h4 class="page-title d-none d-sm-block">Dashboards</h4>
        </div>

        <ul class="topbar-menu d-flex align-items-center gap-3">
            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle arrow-none position-relative" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="ri-notification-3-line fs-22"></span>
                    <?php if ($adminUnreadTotal > 0): ?>
                        <span class="admin-noti-count"><?= $adminUnreadTotal > 99 ? '99+' : $adminUnreadTotal ?></span>
                    <?php endif; ?>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated p-0" style="min-width: 360px;">
                    <div class="px-3 py-2 border-bottom bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">Unread Notifications</h6>
                            <span class="badge bg-danger-subtle text-danger"><?= $adminUnreadTotal ?> Unread</span>
                        </div>
                    </div>

                    <div class="admin-noti-scroll">
                        <?php if (empty($adminNoticeSummary['ready'])): ?>
                            <div class="px-3 py-3 text-muted small">
                                Run read-flag migration SQL to enable unread notifications.
                            </div>
                        <?php elseif (empty($adminUnreadNotifications)): ?>
                            <div class="px-3 py-3 text-muted small">
                                No unread loan/enquiry notifications.
                            </div>
                        <?php else: ?>
                            <?php foreach ($adminUnreadNotifications as $item): ?>
                                <?php
                                    $type = ($item['type'] === 'loan') ? 'loan' : 'enquiry';
                                    $icon = ($type === 'loan') ? 'ri-bank-card-line' : 'ri-chat-3-line';
                                    $titlePrefix = ($type === 'loan') ? 'Loan #' : 'Enquiry #';
                                    $displayName = trim((string)($item['full_name'] ?? ''));
                                    $displaySubject = trim((string)($item['subject'] ?? ''));
                                ?>
                                <a href="<?= htmlspecialchars($item['url']) ?>" class="dropdown-item admin-noti-item <?= $type ?>">
                                    <div class="d-flex gap-2 align-items-start">
                                        <span class="icon-chip"><i class="<?= $icon ?>"></i></span>
                                        <div class="flex-grow-1">
                                            <p class="title mb-0"><?= $titlePrefix . (int)$item['ref_id'] ?> - <?= htmlspecialchars($displayName !== '' ? $displayName : 'Customer') ?></p>
                                            <p class="meta mb-1"><?= htmlspecialchars($displaySubject !== '' ? $displaySubject : 'New notification') ?></p>
                                            <p class="meta mb-0"><?= htmlspecialchars(date('d M, h:i A', strtotime((string)$item['created_at']))) ?></p>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="px-2 py-2 border-top text-center">
                        <a href="dashboard.php#admin-notice-panel" class="btn btn-sm btn-outline-dark w-100">Open Notice Panel</a>
                    </div>
                </div>
            </li>

            <li class="d-none d-sm-inline-block">
                <a class="nav-link" data-bs-toggle="offcanvas" href="#theme-settings-offcanvas">
                    <span class="ri-settings-3-line fs-22"></span>
                </a>
            </li>

            <li class="d-none d-sm-inline-block">
                <div class="nav-link" id="light-dark-mode">
                    <i class="ri-moon-line fs-22"></i>
                </div>
            </li>

            <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none nav-user" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    
                    <span class="d-lg-block d-none">
                        <h5 class="my-0 fw-normal">Admin<i class="ri-arrow-down-s-line fs-22 d-none d-sm-inline-block align-middle"></i></h5>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                    <a href="db/auth-logout.php" class="dropdown-item">
                        <i class="ri-logout-circle-r-line align-middle me-1"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</div>
<!-- ========== Topbar End ========== -->
