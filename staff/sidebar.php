<?php
$current_page = basename($_SERVER['PHP_SELF']);

// 1. Customer Active Logic
$customer_active = in_array($current_page, [
    'customer_add.php', 
    'customers.php', 
    'customer_view.php'
]);

// 2. Loan Active Logic
$loan_active = in_array($current_page, [
    'loan_applications.php', 
    'loan_view.php'
]);

// 3. Category & Service Active Logic
$cat_active      = in_array($current_page, ['category.php']);
$service_active  = in_array($current_page, ['services.php', 'service_details.php']);
?>

<style>
    /* Styles remain identical to maintain the Slate Theme */
    :root {
        --nav-bg: #000000;
        --nav-text: #ffffff;
        --nav-hover-bg: #1e293b;
        --accent-blue: #3b82f6;
    }
    .leftside-menu {
        background: var(--nav-bg) !important;
        width: 260px;
        position: fixed;
        height: 100%;
        box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        z-index: 1000;
        overflow-y: auto;
    }
    .side-nav-link {
        color: var(--nav-text) !important;
        padding: 12px 20px !important;
        display: flex !important;
        align-items: center;
        text-decoration: none !important;
        transition: 0.2s;
    }
    .side-nav-link i { font-size: 1.1rem; margin-right: 12px; color: #64748b; }
    .has-arrow::after {
        content: "\ea4e"; 
        font-family: 'remixicon';
        margin-left: auto;
        transition: transform 0.3s;
    }
    .side-nav-item.active > .has-arrow::after { transform: rotate(180deg); }
    .side-nav-second-level { list-style: none; padding: 0; display: none; background: rgba(255, 255, 255, 0.03); }
    .side-nav-item.active .side-nav-second-level { display: block; }
    .side-nav-second-level .side-nav-link { padding: 10px 20px 10px 45px !important; font-size: 0.825rem !important; opacity: 0.7; }
    .side-nav-item.active > .side-nav-link i { color: var(--accent-blue) !important; }
    .side-nav-title { padding: 15px 20px 5px; font-size: 0.7rem; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.5px; }
</style>

<div class="leftside-menu">
    <a href="dashboard.php" class="logo text-center d-block">
        <span class="logo-lg">
            <img src="../admin/uploads/logo-ravi.png" alt="logo" style="max-height: 40px; margin: 20px 0; filter: brightness(0) invert(1);">
        </span>
    </a>

    <div class="h-100">
        <ul class="side-nav">
            
            <li class="side-nav-title">Workplace</li>
            <li class="side-nav-item <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                <a href="dashboard.php" class="side-nav-link">
                    <i class="ri-dashboard-2-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <?php if (hasAccess($conn, 'cust_read') || hasAccess($conn, 'cust_create')): ?>
            <li class="side-nav-item <?= $customer_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-user-star-line"></i>
                    <span>Manage Customer</span>
                </a>
                <ul class="side-nav-second-level">
                    <?php if (hasAccess($conn, 'cust_create')): ?>
                        <li><a href="customer_add.php" class="side-nav-link"><i class="fas fa-plus"></i> Add New</a></li>
                    <?php endif; ?>
                    
                    <?php if (hasAccess($conn, 'cust_read')): ?>
                        <li><a href="customers.php" class="side-nav-link"><i class="fas fa-users"></i> View List</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <?php if (hasAccess($conn, 'loan_view')): ?>
            <li class="side-nav-item <?= $loan_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-bank-card-2-line"></i>
                    <span>Loan Management</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="loan_applications.php" class="side-nav-link"><i class="fas fa-file-invoice-dollar"></i> Assigned Applications</a></li>
                </ul>
            </li>
            <?php endif; ?>

            <li class="side-nav-title">Reference</li>

            <?php if (hasAccess($conn, 'service_read') || hasAccess($conn, 'cust_read')): ?>
            <li class="side-nav-item <?= $service_active ? 'active' : '' ?>">
                <a href="services.php" class="side-nav-link">
                    <i class="ri-customer-service-2-line"></i>
                    <span>Products/Services</span>
                </a>
            </li>
            <?php endif; ?>

            <li class="side-nav-item">
                <a href="db/auth-logout.php" class="side-nav-link text-danger">
                    <i class="ri-logout-box-line"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>
