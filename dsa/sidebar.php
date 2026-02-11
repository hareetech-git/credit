<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
:root { --nav-bg:#000; --nav-text:#fff; --nav-hover-bg:#1e293b; --accent-blue:#3b82f6; }
.leftside-menu { background: var(--nav-bg) !important; width:260px; position:fixed; height:100%; box-shadow:4px 0 10px rgba(0,0,0,0.1); z-index:1000; overflow-y:auto; }
.side-nav-link { color: var(--nav-text) !important; padding:12px 20px !important; display:flex !important; align-items:center; text-decoration:none !important; }
.side-nav-link i { font-size:1.1rem; margin-right:12px; color:#64748b; }
.side-nav-item.active > .side-nav-link i { color:var(--accent-blue) !important; }
.side-nav-title { padding:15px 20px 5px; font-size:0.7rem; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.5px; }
</style>
<div class="leftside-menu">
    <a href="dashboard.php" class="logo text-center d-block">
        <span class="logo-lg"><img src="../admin/assets/udhaar_logo.png" alt="logo" style="max-height:40px; margin:20px 0; filter:brightness(0) invert(1);"></span>
    </a>
    <div class="h-100">
        <ul class="side-nav">
            <li class="side-nav-title">Main</li>
            <?php if (dsaHasAccess($conn, 'dsa_dashboard_view')): ?>
            <li class="side-nav-item <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                <a href="dashboard.php" class="side-nav-link"><i class="ri-dashboard-2-line"></i><span>Dashboard</span></a>
            </li>
            <?php endif; ?>

            <?php if (dsaHasAccess($conn, 'dsa_lead_view')): ?>
            <li class="side-nav-item <?= ($current_page == 'my-applications.php') ? 'active' : '' ?>">
                <a href="my-applications.php" class="side-nav-link"><i class="ri-file-list-3-line"></i><span>My Leads</span></a>
            </li>
            <?php endif; ?>

            <?php if (dsaHasAccess($conn, 'dsa_profile_manage')): ?>
            <li class="side-nav-item <?= ($current_page == 'profile.php') ? 'active' : '' ?>">
                <a href="profile.php" class="side-nav-link"><i class="ri-user-settings-line"></i><span>My Profile</span></a>
            </li>
            <?php endif; ?>

            <?php if (dsaHasAccess($conn, 'dsa_lead_create')): ?>
            <li class="side-nav-item <?= ($current_page == 'add-lead.php') ? 'active' : '' ?>">
                <a href="add-lead.php" class="side-nav-link"><i class="ri-add-circle-line"></i><span>Add New Lead</span></a>
            </li>
            <?php endif; ?>

            <li class="side-nav-item">
                <a href="db/auth-logout.php" class="side-nav-link text-danger"><i class="ri-logout-box-line"></i><span>Logout</span></a>
            </li>
        </ul>
    </div>
</div>
