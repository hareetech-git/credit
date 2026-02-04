<style>
    :root {
        --nav-bg: #000000; 
        --nav-text: #94a3b8; 
        --nav-active: #ffffff; 
        --nav-hover-bg: #171717; 
    }

    .leftside-menu {
        background: var(--nav-bg) !important;
        box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        width: 260px;
    }

    .side-nav-title {
        color: #4b5563 !important; 
        font-size: 0.65rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.15em !important;
        font-weight: 800 !important;
        padding: 24px 20px 10px !important;
    }

    .side-nav-link {
        color: var(--nav-text) !important;
        font-weight: 500 !important;
        font-size: 0.85rem !important;
        padding: 12px 20px !important;
        transition: all 0.3s ease !important;
        display: flex !important;
        align-items: center !important;
        text-decoration: none !important;
    }

    .side-nav-link i {
        font-size: 1.2rem !important;
        margin-right: 14px !important;
        color: #3f3f46 !important; 
        transition: color 0.3s ease;
    }

    .side-nav-item:hover .side-nav-link {
        background: var(--nav-hover-bg);
        color: var(--nav-active) !important;
    }

    .side-nav-item:hover i {
        color: #ffffff !important;
    }

    .side-nav-item.active .side-nav-link {
        color: var(--nav-active) !important;
        background: var(--nav-hover-bg);
        border-right: 3px solid #ffffff;
    }

    .side-nav-item.active i {
        color: #ffffff !important; 
    }

    .logo-lg {
        padding: 30px 20px;
        display: block;
        text-decoration: none;
    }
    
    .logo-text {
        color: white;
        font-weight: 900;
        font-size: 1.4rem;
        letter-spacing: -1px;
        font-style: italic;
    }

    .logo-sub {
        color: #525252;
        font-weight: 300;
        font-style: normal;
    }
</style>

<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="leftside-menu">

    <a href="dashboard.php" class="logo-lg">
        <span class="logo-text">UDHAR <span class="logo-sub">CAPITAL</span></span>
    </a>

    <div data-simplebar class="h-100">
        <ul class="side-nav">
            
            <li class="side-nav-title">Navigation</li>

            <li class="side-nav-item <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                <a href="dashboard.php" class="side-nav-link">
                    <i class="ri-dashboard-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="side-nav-title">Personal</li>

            <li class="side-nav-item <?= ($current_page == 'profile.php') ? 'active' : '' ?>">
                <a href="profile.php" class="side-nav-link">
                    <i class="ri-user-settings-line"></i>
                    <span>My Profile</span>
                </a>
            </li>

            <li class="side-nav-item <?= ($current_page == 'documents.php') ? 'active' : '' ?>">
                <a href="documents.php" class="side-nav-link">
                    <i class="ri-folder-shield-2-line"></i>
                    <span>E-Vault Documents</span>
                </a>
            </li>

            <li class="side-nav-title">Loan Desk</li>

            <li class="side-nav-item <?= ($current_page == 'loan-application.php' || $current_page == 'apply-loan.php') ? 'active' : '' ?>">
                <a href="../apply-loan.php" class="side-nav-link">
                    <i class="ri-file-edit-line"></i>
                    <span>New Application</span>
                </a>
            </li>

            <li class="side-nav-item <?= ($current_page == 'my-applications.php' || $current_page == 'view-application-detail.php') ? 'active' : '' ?>">
                <a href="my-applications.php" class="side-nav-link">
                    <i class="ri-bank-card-line"></i>
                    <span>My Applications</span>
                </a>
            </li>

            <li class="side-nav-item <?= ($current_page == 'repayments.php') ? 'active' : '' ?>">
                <a href="repayments.php" class="side-nav-link">
                    <i class="ri-history-line"></i>
                    <span>Active & Repay</span>
                </a>
            </li>

            <li class="side-nav-title">System</li>

            <li class="side-nav-item">
                <a href="db/auth-logout.php" class="side-nav-link" style="color: #ef4444 !important;">
                    <i class="ri-shut-down-line" style="color: #ef4444 !important;"></i>
                    <span>Logout</span>
                </a>
            </li>

        </ul>
    </div>
</div>