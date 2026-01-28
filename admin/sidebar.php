<style>
    :root {
        --nav-bg: black; /* Deep Midnight */
        --nav-text: white; /* Slate gray */
        --nav-active: #ffffff;
        --nav-hover-bg: #1e293b;
    }

    .leftside-menu {
        background: var(--nav-bg) !important;
        box-shadow: 4px 0 10px rgba(0,0,0,0.05);
    }

    .side-nav-title {
        color: #475569 !important;
        font-size: 0.7rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.1em !important;
        font-weight: 700 !important;
        padding: 20px 20px 10px !important;
    }

    .side-nav-link {
        color: var(--nav-text) !important;
        font-weight: 500 !important;
        font-size: 0.875rem !important;
        padding: 12px 20px !important;
        transition: all 0.2s ease !important;
        display: flex !important;
        align-items: center !important;
    }

    .side-nav-link i {
        font-size: 1.1rem !important;
        margin-right: 12px !important;
        color: #64748b !important;
    }

    .side-nav-item:hover .side-nav-link {
        background: var(--nav-hover-bg);
        color: var(--nav-active) !important;
    }

    .side-nav-item.active .side-nav-link {
        color: var(--nav-active) !important;
        background: var(--nav-hover-bg);
    }

    .side-nav-item.active i {
        color: #3b82f6 !important; /* Premium Blue Accent for Active Icon */
    }

    .logo-lg img {
        max-height: 40px;
        margin: 20px 0;
        filter: brightness(0) invert(1); /* Forces logo to white if it's dark */
    }
</style>

<?php
// Simple logic to highlight active page
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="leftside-menu">

    <a href="dashboard.php" class="logo text-center d-block">
        <span class="logo-lg">
            <img src="uploads/logo-ravi.png" alt="logo">
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

            <li class="side-nav-title">Categories</li>

            <li class="side-nav-item <?= ($current_page == 'add-category.php') ? 'active' : '' ?>">
                <a href="add-category.php" class="side-nav-link">
                    <i class="ri-add-circle-line"></i>
                    <span>Add Category</span>
                </a>
            </li>

            <li class="side-nav-item <?= ($current_page == 'categories.php') ? 'active' : '' ?>">
                <a href="categories.php" class="side-nav-link">
                    <i class="ri-list-check-2"></i>
                    <span>View Categories</span>
                </a>
            </li>

            <li class="side-nav-title">Services</li>

            <li class="side-nav-item <?= ($current_page == 'add-service.php') ? 'active' : '' ?>">
                <a href="add-service.php" class="side-nav-link">
                    <i class="ri-paint-brush-line"></i>
                    <span>Add Service</span>
                </a>
            </li>

            <li class="side-nav-item <?= ($current_page == 'services.php') ? 'active' : '' ?>">
                <a href="services.php" class="side-nav-link">
                    <i class="ri-stack-line"></i>
                    <span>View Services</span>
                </a>
            </li>

            <li class="side-nav-title">Management</li>

            <li class="side-nav-item <?= ($current_page == 'slider-images.php') ? 'active' : '' ?>">
                <a href="slider-images.php" class="side-nav-link">
                    <i class="ri-image-2-line"></i>
                    <span>Home Sliders</span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="logout.php" class="side-nav-link text-danger-hover">
                    <i class="ri-logout-box-line"></i>
                    <span>Logout</span>
                </a>
            </li>

        </ul>
    </div>
</div>