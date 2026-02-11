<?php
$current_page = basename($_SERVER['PHP_SELF']);

// 1. Existing Active Logic
$dept_active = in_array($current_page, ['add-department.php', 'departments.php']);

$customer_active = in_array($current_page, [
    'customer_add.php', 
    'customers.php', 
    'customer_edit.php', 
    'customer_view.php'
]);

$loan_active = in_array($current_page, [
    'loan_applications.php', 
    'loan_view.php',
    'manual_loan_assign.php',
    'rejected_loans.php'
]);

$cat_active      = in_array($current_page, ['category_add.php', 'category.php']);
$subcat_active   = in_array($current_page, ['subcategory_add.php', 'subcategory.php']);
$service_active  = in_array($current_page, ['service_add.php', 'services.php', 'service_edit.php', 'service_details.php', 'plan-work.php']);
$faq_active = ($current_page == 'faqs.php');
$career_active = ($current_page == 'career_applications.php');

// 2. NEW: Staff & Permissions Active Logic
$staff_active = in_array($current_page, [
    'staff_add.php', 
    'staff_list.php', 
    'manage_permissions.php'
]);
$dsa_active = in_array($current_page, [
    'dsa_add.php',
    'dsa_list.php',
    'dsa_requests.php',
    'manage_dsa_permissions.php'
]);
?>

<style>
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
        cursor: pointer;
    }

    .side-nav-link i {
        font-size: 1.1rem;
        margin-right: 12px;
        color: #64748b;
    }

    .has-arrow::after {
        content: "\ea4e"; 
        font-family: 'remixicon';
        margin-left: auto;
        transition: transform 0.3s;
    }

    .side-nav-item.active > .has-arrow::after {
        transform: rotate(180deg);
    }

    .side-nav-second-level {
        list-style: none;
        padding: 0;
        display: none; 
        background: rgba(255, 255, 255, 0.03);
    }

    .side-nav-item.active .side-nav-second-level {
        display: block;
    }

    .side-nav-second-level .side-nav-link {
        padding: 10px 20px 10px 45px !important; 
        font-size: 0.825rem !important;
        opacity: 0.7;
    }

    .side-nav-second-level .side-nav-link:hover {
        opacity: 1;
        background: var(--nav-hover-bg) !important;
    }

    .side-nav-item.active > .side-nav-link i {
        color: var(--accent-blue) !important;
    }
    
    .side-nav-title {
        padding: 15px 20px 5px;
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
</style>

<div class="leftside-menu">
    <a href="dashboard.php" class="logo text-center d-block">
        <span class="logo-lg">
            <img src="assets/udhaar_logo.png" alt="logo" style="max-height: 40px; margin: 20px 0; filter: brightness(0) invert(1);">
        </span>
    </a>

    <div class="h-100">
        <ul class="side-nav">
            
            <li class="side-nav-title">Main</li>
            <li class="side-nav-item <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                <a href="dashboard.php" class="side-nav-link">
                    <i class="ri-dashboard-2-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>

<li class="side-nav-item <?= $websettings_active ? 'active' : '' ?>">
    <a href="team_members.php" class="side-nav-link">
    <i class="fas fa-users"></i>

        <span>Manage Team </span>
    </a>
</li>
            <li class="side-nav-item <?= $staff_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-user-settings-line"></i>
                    <span>Staff Control</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="staff_add.php" class="side-nav-link"><i class="fas fa-plus"></i> Add Staff</a></li>
                    <li><a href="staff_list.php" class="side-nav-link"><i class="fas fa-users-cog"></i> View Staff</a></li>
                    <li><a href="manage_permissions.php" class="side-nav-link"><i class="fas fa-user-shield"></i> Manage Access</a></li>
                </ul>
            </li>

            <li class="side-nav-item <?= $dsa_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-user-star-line"></i>
                    <span>DSA Control</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="dsa_add.php" class="side-nav-link"><i class="fas fa-plus"></i> Add DSA</a></li>
                    <li><a href="dsa_list.php" class="side-nav-link"><i class="fas fa-users"></i> View DSA</a></li>
                    <li><a href="manage_dsa_permissions.php" class="side-nav-link"><i class="fas fa-key"></i> Manage Access</a></li>
                    <li><a href="dsa_requests.php" class="side-nav-link"><i class="fas fa-user-check"></i> DSA Requests</a></li>
                </ul>
            </li>

            <li class="side-nav-title">CRM & Finance</li>

            <li class="side-nav-item <?= $dept_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-building-line"></i>
                    <span>Manage Department</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="add-department.php" class="side-nav-link"><i class="fas fa-plus"></i> Create</a></li>
                    <li><a href="departments.php" class="side-nav-link"><i class="fas fa-eye"></i> View</a></li>
                </ul>
            </li>

            <li class="side-nav-item <?= $customer_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-user-star-line"></i>
                    <span>Manage Customer</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="customer_add.php" class="side-nav-link"><i class="fas fa-user-plus"></i> Add Customer</a></li>
                    <li><a href="customers.php" class="side-nav-link"><i class="fas fa-users"></i> View Customers</a></li>
                </ul>
            </li>

            <li class="side-nav-item <?= $loan_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-bank-card-2-line"></i>
                    <span>Loan Management</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="loan_applications.php" class="side-nav-link"><i class="fas fa-file-invoice-dollar"></i> All Applications</a></li>
                    <li><a href="manual_loan_assign.php" class="side-nav-link"><i class="fas fa-user-check"></i> Manual Assign</a></li>
                    <li><a href="loan_applications.php?status=rejected" class="side-nav-link"><i class="fas fa-ban"></i> Rejected Apps</a></li>
                </ul>
            </li>

            <li class="side-nav-title">Services & Categories</li>

            <li class="side-nav-item <?= $cat_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-folder-line"></i>
                    <span>Manage Category</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="category_add.php" class="side-nav-link"><i class="fas fa-plus"></i> Create</a></li>
                    <li><a href="category.php" class="side-nav-link"><i class="fas fa-eye"></i> View</a></li>
                </ul>
            </li>

            <li class="side-nav-item <?= $subcat_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-folder-shared-line"></i>
                    <span>Manage Subcategory</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="subcategory_add.php" class="side-nav-link"><i class="fas fa-plus"></i> Create</a></li>
                    <li><a href="subcategory.php" class="side-nav-link"><i class="fas fa-eye"></i> View</a></li>
                </ul>
            </li>

            <li class="side-nav-item <?= $service_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-customer-service-2-line"></i>
                    <span>Manage Service</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="service_add.php" class="side-nav-link"><i class="fas fa-plus"></i> Create</a></li>
                    <li><a href="services.php" class="side-nav-link"><i class="fas fa-eye"></i> View</a></li>
                    <!-- <li><a href="plan-work.php" class="side-nav-link"><i class="fas fa-file-alt"></i> Plan Work Format</a></li> -->
                </ul>
            </li>

            <li class="side-nav-item <?= ($current_page == 'enquiries.php') ? 'active' : '' ?>">
                <a href="enquiries.php" class="side-nav-link">
                    <i class="ri-question-answer-line"></i>
                    <span>Enquiry</span>
                </a>
            </li>

            <li class="side-nav-item <?= $career_active ? 'active' : '' ?>">
                <a href="career_applications.php" class="side-nav-link">
                    <i class="ri-briefcase-4-line"></i>
                    <span>Careers</span>
                </a>
            </li>

            <li class="side-nav-item <?= $faq_active ? 'active' : '' ?>">
                <a href="faqs.php" class="side-nav-link">
                    <i class="ri-question-line"></i>
                    <span>FAQs</span>
                </a>
            </li>
<li class="side-nav-item <?= $websettings_active ? 'active' : '' ?>">
    <a href="web_settings.php" class="side-nav-link">
        <i class="ri-settings-3-line"></i>
        <span>Web Settings</span>
    </a>
</li>
            <li class="side-nav-item">
                <a href="db/auth-logout.php" class="side-nav-link text-danger">
                    <i class="ri-logout-box-line"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>
