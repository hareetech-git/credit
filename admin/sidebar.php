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
    }

    .side-nav-link {
        color: var(--nav-text) !important;
        padding: 12px 20px !important;
        display: flex !important;
        align-items: center;
        text-decoration: none !important;
        transition: 0.2s;
    }

    .side-nav-link i {
        font-size: 1.1rem;
        margin-right: 12px;
        color: #64748b;
    }

    /* Submenu Arrow logic */
    .has-arrow::after {
        content: "\ea4e"; 
        font-family: 'remixicon';
        margin-left: auto;
        transition: transform 0.3s;
    }

    .active .has-arrow::after {
        transform: rotate(180deg);
    }

    /* Submenu Container */
    .side-nav-second-level {
        list-style: none;
        padding: 0;
        display: none; 
        background: rgba(255, 255, 255, 0.03);
    }

    /* Show submenu if parent is active */
    .side-nav-item.active .side-nav-second-level {
        display: block;
    }

    /* Sub-item Styling with indentation */
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
</style>
<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Active State Logic
$dept_active     = in_array($current_page, ['add-department.php', 'departments.php']);
$customer_active = in_array($current_page, ['add-customer.php', 'customers.php']);
$cat_active      = in_array($current_page, ['category_add.php', 'category.php']);
$subcat_active   = in_array($current_page, ['subcategory_add.php', 'subcategory.php']);
$service_active  = in_array($current_page, ['add-service.php', 'services.php', 'plan-work.php']);
?>

<div class="leftside-menu">
    <a href="dashboard.php" class="logo text-center d-block">
        <span class="logo-lg">
            <img src="uploads/logo-ravi.png" alt="logo" style="max-height: 40px; margin: 20px 0; filter: brightness(0) invert(1);">
        </span>
    </a>

    <div data-simplebar class="h-100">
        <ul class="side-nav">
            
            <li class="side-nav-title">Main</li>
            <li class="side-nav-item <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                <a href="dashboard.php" class="side-nav-link">
                    <i class="ri-dashboard-2-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>

  
            <li class="side-nav-title">CRM</li>


            
            <li class="side-nav-item <?= $customer_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-user-settings-line"></i>
                    <span>Manage Customer</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="add-customer.php" class="side-nav-link">➕ Create Customer</a></li>
                    <li><a href="customers.php" class="side-nav-link">👁 View Customers</a></li>
                </ul>
            </li>



                      <li class="side-nav-item <?= $dept_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-building-line"></i>
                    <span>Manage Department</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="add-department.php" class="side-nav-link">➕ Create</a></li>
                    <li><a href="departments.php" class="side-nav-link">👁 View</a></li>
                </ul>
            </li>

            <li class="side-nav-title">Services & Categories</li>

            <li class="side-nav-item <?= $cat_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-folder-line"></i>
                    <span>Manage Category</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="category_add.php" class="side-nav-link">➕ Create</a></li>
                    <li><a href="category.php" class="side-nav-link">👁 View</a></li>
                </ul>
            </li>

            <li class="side-nav-item <?= $subcat_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-folder-shared-line"></i>
                    <span>Manage Subcategory</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="subcategory_add.php" class="side-nav-link">➕ Create</a></li>
                    <li><a href="subcategory.php" class="side-nav-link">👁 View</a></li>
                </ul>
            </li>

            <li class="side-nav-item <?= $service_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-customer-service-2-line"></i>
                    <span>Manage Service</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="add-service.php" class="side-nav-link">➕ Create</a></li>
                    <li><a href="services.php" class="side-nav-link">👁 View</a></li>
                    <li><a href="plan-work.php" class="side-nav-link">📄 Plan Work Formate</a></li>
                </ul>
            </li>

            <li class="side-nav-item">
                <a href="logout.php" class="side-nav-link text-danger">
                    <i class="ri-logout-box-line"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>