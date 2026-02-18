<?php
$current_page = basename($_SERVER['PHP_SELF']);
$testimonial_active = in_array($current_page, ['testimonial_add.php', 'testimonials.php', 'testimonial_edit.php'], true);
$certificate_active = in_array($current_page, ['certificate_add.php', 'certificates.php', 'certificate_edit.php'], true);
$brand_active = in_array($current_page, ['brand_add.php', 'brands.php', 'brand_edit.php'], true);
$blog_active = in_array($current_page, ['blogs.php', 'blog_add.php', 'blog_edit.php'], true);
$dept_active = in_array($current_page, ['add-department.php', 'departments.php'], true);
$customer_active = in_array($current_page, ['customer_add.php', 'customers.php', 'customer_edit.php', 'customer_view.php'], true);
$loan_active = in_array($current_page, ['loan_applications.php', 'loan_view.php', 'manual_loan_assign.php', 'rejected_loans.php'], true);

$active_loan_category_id = 0;
if (isset($_GET['cat_id'])) {
    $active_loan_category_id = (int) $_GET['cat_id'];
} elseif (isset($_GET['category_id'])) {
    $active_loan_category_id = (int) $_GET['category_id'];
}

$loan_categories = [];
if (isset($conn) && $conn instanceof mysqli) {
    $loanCatRes = mysqli_query(
        $conn,
        "SELECT id, category_name
         FROM service_categories
         WHERE active = 1
         ORDER BY sequence ASC, category_name ASC"
    );
    if ($loanCatRes) {
        while ($catRow = mysqli_fetch_assoc($loanCatRes)) {
            $loan_categories[] = $catRow;
        }
    }
}

$cat_active = in_array($current_page, ['category_add.php', 'category.php'], true);
$subcat_active = in_array($current_page, ['subcategory_add.php', 'subcategory.php', 'subcategory_edit.php'], true);
$service_active = in_array($current_page, ['service_add.php', 'services.php', 'service_edit.php', 'service_details.php', 'service_view.php', 'plan-work.php'], true);
$faq_active = ($current_page === 'faqs.php');
$career_active = ($current_page === 'career_applications.php');
$staff_active = in_array($current_page, ['staff_add.php', 'staff_list.php', 'manage_permissions.php'], true);
$dsa_active = in_array($current_page, ['dsa_add.php', 'dsa_list.php', 'dsa_requests.php', 'manage_dsa_permissions.php'], true);
$enquiry_active = in_array($current_page, ['enquiries.php', 'enquiry_view.php', 'enquiry_email.php'], true);
$team_active = ($current_page === 'team_members.php');
$websettings_active = ($current_page === 'web_settings.php');
$admin_sidebar_name = trim((string) ($_SESSION['admin_name'] ?? 'Administrator'));
$admin_sidebar_role = ucfirst((string) ($_SESSION['admin_role'] ?? 'admin'));
$admin_sidebar_initial = strtoupper(substr($admin_sidebar_name, 0, 1));

$loan_rejected_active = ($current_page === 'loan_applications.php' && ($_GET['status'] ?? '') === 'rejected');
$loan_all_active = ($current_page === 'loan_applications.php' && $active_loan_category_id === 0 && !$loan_rejected_active);
?>

<style>
    :root {
        --admin-nav-bg: #000000;
        --admin-nav-card: #0a0a0a;
        --admin-nav-card-hover: #121212;
        --admin-nav-border: #1f1f1f;
        --admin-nav-text: #f5f5f5;
        --admin-nav-muted: #9ca3af;
        --admin-nav-accent: #ffffff;
        --admin-nav-accent-soft: rgba(255, 255, 255, 0.08);
    }

    .leftside-menu {
        background: var(--admin-nav-bg) !important;
        width: 260px;
        position: fixed;
        height: 100%;
        box-shadow: 12px 0 30px rgba(2, 6, 23, 0.42);
        z-index: 1000;
        overflow-y: auto;
        border-right: 1px solid var(--admin-nav-border);
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .leftside-menu::-webkit-scrollbar {
        width: 0;
        height: 0;
        display: none;
    }

    .leftside-menu::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 10% 10%, rgba(255, 255, 255, 0.05), transparent 35%),
            radial-gradient(circle at 90% 95%, rgba(255, 255, 255, 0.04), transparent 40%);
        pointer-events: none;
    }

    .side-nav {
        padding: 8px 14px 20px;
    }

    .side-nav-title {
        margin-top: 16px;
        padding: 0 10px;
        font-size: 0.67rem;
        font-weight: 700;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        color: var(--admin-nav-muted);
    }

    .side-nav-item {
        margin: 5px 0;
    }

    .side-nav-link {
        color: var(--admin-nav-text) !important;
        padding: 10px 12px !important;
        border-radius: 12px;
        display: flex !important;
        align-items: center;
        gap: 10px;
        text-decoration: none !important;
        transition: all 0.22s ease;
        border: 1px solid var(--admin-nav-border);
        background: var(--admin-nav-card);
        box-shadow: none;
        position: relative;
        overflow: hidden;
    }

    .side-nav-link i {
        width: 22px;
        min-width: 22px;
        text-align: center;
        font-size: 1.02rem;
        color: #bdbdbd;
        transition: transform 0.25s ease, color 0.25s ease;
    }

    .side-nav-link::before {
        content: "";
        position: absolute;
        left: -100%;
        top: 0;
        height: 100%;
        width: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.08), transparent);
        transition: left 0.5s ease;
    }

    .side-nav-link:hover {
        background: var(--admin-nav-card-hover) !important;
        border-color: var(--admin-nav-border);
        transform: translateY(-2px) scale(1.01);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.5);
    }

    .side-nav-link:hover::before {
        left: 100%;
    }

    .side-nav-link:hover i {
        color: #ffffff;
        transform: translateX(1px);
    }

    .side-nav-item.active>.side-nav-link {
        background: linear-gradient(135deg, var(--admin-nav-accent-soft), #121212);
        border-color: #3b3b3b;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
    }

    .side-nav-item.active>.side-nav-link i {
        color: var(--admin-nav-accent) !important;
    }

    .side-nav-link.has-arrow::after {
        content: "\ea4e";
        font-family: 'remixicon';
        margin-left: auto;
        transition: transform 0.3s ease;
        color: var(--admin-nav-muted);
        font-size: 1rem;
    }

    .side-nav-item.active>.side-nav-link.has-arrow::after {
        transform: rotate(180deg);
        color: var(--admin-nav-accent);
    }

    .side-nav-second-level {
        list-style: none;
        padding: 6px 0 2px 14px;
        display: block;
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        margin: 6px 0 2px;
        transition: max-height 0.3s ease, opacity 0.25s ease;
    }

    .side-nav-item.active .side-nav-second-level {
        max-height: 520px;
        opacity: 1;
    }

    .side-nav-second-level .side-nav-link {
        padding: 8px 12px !important;
        font-size: 0.82rem !important;
        color: #cbd5e1 !important;
        border-radius: 10px;
        box-shadow: none;
        border-color: #2a2a2a;
        background: #101010;
    }

    .side-nav-second-level .side-nav-link i {
        font-size: 0.86rem;
        color: #a3a3a3;
    }

    .side-nav-second-level .side-nav-link.active {
        background: rgba(255, 255, 255, 0.1) !important;
        border: 1px solid #3f3f46;
    }

    .sidebar-brand {
        margin: 16px 14px 12px;
        padding: 14px;
        border-radius: 16px;
        border: 1px solid #1f1f1f;
        background: linear-gradient(155deg, #0a0a0a, #121212);
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.5);
    }

    .sidebar-brand-logo {
        max-height: 34px;
        filter: brightness(0) invert(1);
    }

    .sidebar-brand-subtitle {
        margin-top: 8px;
        font-size: 0.72rem;
        color: var(--admin-nav-muted);
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .sidebar-profile {
        margin: 8px 14px;
        padding: 12px;
        border-radius: 14px;
        border: 1px solid #1f1f1f;
        background: #0d0d0d;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: none;
        transition: border-color 0.25s ease, transform 0.2s ease;
    }

    .sidebar-profile:hover {
        border-color: #2f2f2f;
        transform: translateY(-1px);
    }

    .sidebar-avatar {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #000000;
        font-weight: 700;
        background: linear-gradient(135deg, #f3f4f6, #d1d5db);
        font-size: 0.9rem;
    }

    .sidebar-profile-name {
        margin: 0;
        font-size: 0.83rem;
        font-weight: 600;
        color: #f8fafc;
        line-height: 1.2;
    }

    .sidebar-profile-role {
        margin: 0;
        font-size: 0.72rem;
        color: var(--admin-nav-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .side-nav-badge {
        margin-left: auto;
        background: rgba(255, 255, 255, 0.12);
        color: #f5f5f5;
        border: 1px solid #3f3f46;
        border-radius: 999px;
        padding: 2px 8px;
        font-size: 0.67rem;
        font-weight: 700;
    }

    .side-nav-link.text-danger {
        color: #fecaca !important;
    }

    .side-nav-link.text-danger i {
        color: #fca5a5;
    }

    .side-nav-link.text-danger:hover {
        background: rgba(239, 68, 68, 0.18) !important;
        border-color: rgba(239, 68, 68, 0.4);
        box-shadow: none;
    }

    @media (max-width: 991.98px) {

        .leftside-menu,
        .leftside-menu::before {
            width: 100%;
            max-width: 300px;
        }
    }
</style>

<div class="leftside-menu">
    <div class="sidebar-brand">
        <a href="dashboard.php" class="logo text-center d-block m-0">
            <span class="logo-lg">
                <img src="assets/udhaar_logo.png" alt="logo" class="sidebar-brand-logo">
            </span>
        </a>
        <div class="sidebar-brand-subtitle">Admin Control Panel</div>
    </div>

    <div class="sidebar-profile">
        <span class="sidebar-avatar"><?= htmlspecialchars($admin_sidebar_initial) ?></span>
        <div>
            <p class="sidebar-profile-name"><?= htmlspecialchars($admin_sidebar_name) ?></p>
            <p class="sidebar-profile-role"><?= htmlspecialchars($admin_sidebar_role) ?></p>
        </div>
    </div>

    <div class="h-100">
        <ul class="side-nav">
            <li class="side-nav-title">Main</li>
            <li class="side-nav-item <?= ($current_page === 'dashboard.php') ? 'active' : '' ?>">
                <a href="dashboard.php" class="side-nav-link">
                    <i class="ri-dashboard-2-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="side-nav-item <?= $team_active ? 'active' : '' ?>">
                <a href="team_members.php" class="side-nav-link">
                    <i class="fas fa-users"></i>
                    <span>Manage Team</span>
                </a>
            </li>

            <li class="side-nav-item <?= $staff_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-user-settings-line"></i>
                    <span>Staff Control</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="staff_add.php"
                            class="side-nav-link <?= ($current_page === 'staff_add.php') ? 'active' : '' ?>"><i
                                class="fas fa-plus"></i> Add Staff</a></li>
                    <li><a href="staff_list.php"
                            class="side-nav-link <?= ($current_page === 'staff_list.php') ? 'active' : '' ?>"><i
                                class="fas fa-users-cog"></i> View Staff</a></li>
                    <li><a href="manage_permissions.php"
                            class="side-nav-link <?= ($current_page === 'manage_permissions.php') ? 'active' : '' ?>"><i
                                class="fas fa-user-shield"></i> Manage Access</a></li>
                </ul>
            </li>

            <li class="side-nav-item <?= $dsa_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-user-star-line"></i>
                    <span>DSA Control</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="dsa_add.php"
                            class="side-nav-link <?= ($current_page === 'dsa_add.php') ? 'active' : '' ?>"><i
                                class="fas fa-plus"></i> Add DSA</a></li>
                    <li><a href="dsa_list.php"
                            class="side-nav-link <?= ($current_page === 'dsa_list.php') ? 'active' : '' ?>"><i
                                class="fas fa-users"></i> View DSA</a></li>
                    <li><a href="manage_dsa_permissions.php"
                            class="side-nav-link <?= ($current_page === 'manage_dsa_permissions.php') ? 'active' : '' ?>"><i
                                class="fas fa-key"></i> Manage Access</a></li>
                    <li><a href="dsa_requests.php"
                            class="side-nav-link <?= ($current_page === 'dsa_requests.php') ? 'active' : '' ?>"><i
                                class="fas fa-user-check"></i> DSA Requests</a></li>
                </ul>
            </li>

            <li class="side-nav-title">CRM & Finance</li>

            <li class="side-nav-item <?= $dept_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-building-line"></i>
                    <span>Manage Department</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="add-department.php"
                            class="side-nav-link <?= ($current_page === 'add-department.php') ? 'active' : '' ?>"><i
                                class="fas fa-plus"></i> Create</a></li>
                    <li><a href="departments.php"
                            class="side-nav-link <?= ($current_page === 'departments.php') ? 'active' : '' ?>"><i
                                class="fas fa-eye"></i> View</a></li>
                </ul>
            </li>

            <li class="side-nav-item <?= $customer_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-user-heart-line"></i>
                    <span>Manage Customer</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="customer_add.php"
                            class="side-nav-link <?= ($current_page === 'customer_add.php') ? 'active' : '' ?>"><i
                                class="fas fa-user-plus"></i> Add Customer</a></li>
                    <li><a href="customers.php"
                            class="side-nav-link <?= ($current_page === 'customers.php' || $current_page === 'customer_view.php') ? 'active' : '' ?>"><i
                                class="fas fa-users"></i> View Customers</a></li>
                </ul>
            </li>

            <li class="side-nav-item <?= $loan_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-bank-card-2-line"></i>
                    <span>Loan Management</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="loan_applications.php" class="side-nav-link <?= $loan_all_active ? 'active' : '' ?>"><i
                                class="fas fa-file-invoice-dollar"></i> All Applications</a></li>
                    <?php foreach ($loan_categories as $loanCat): ?>
                        <?php $catId = (int) $loanCat['id']; ?>
                        <li>
                            <a href="loan_applications.php?cat_id=<?= $catId ?>"
                                class="side-nav-link <?= ($current_page === 'loan_applications.php' && $active_loan_category_id === $catId) ? 'active' : '' ?>">
                                <i class="fas fa-angle-right"></i>
                                <?= htmlspecialchars((string) $loanCat['category_name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li><a href="manual_loan_assign.php"
                            class="side-nav-link <?= ($current_page === 'manual_loan_assign.php') ? 'active' : '' ?>"><i
                                class="fas fa-user-check"></i> Manual Assign</a></li>
                    <li><a href="loan_applications.php?status=rejected"
                            class="side-nav-link <?= $loan_rejected_active ? 'active' : '' ?>"><i
                                class="fas fa-ban"></i> Rejected Apps</a></li>
                </ul>
            </li>

            <li class="side-nav-title">Services & Categories</li>

            <li class="side-nav-item <?= $cat_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-folder-line"></i>
                    <span>Manage Category</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="category_add.php"
                            class="side-nav-link <?= ($current_page === 'category_add.php') ? 'active' : '' ?>"><i
                                class="fas fa-plus"></i> Create</a></li>
                    <li><a href="category.php"
                            class="side-nav-link <?= ($current_page === 'category.php' || $current_page === 'category_edit.php') ? 'active' : '' ?>"><i
                                class="fas fa-eye"></i> View</a></li>
                </ul>
            </li>

            <li class="side-nav-item <?= $subcat_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-folder-shared-line"></i>
                    <span>Manage Subcategory</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="subcategory_add.php"
                            class="side-nav-link <?= ($current_page === 'subcategory_add.php') ? 'active' : '' ?>"><i
                                class="fas fa-plus"></i> Create</a></li>
                    <li><a href="subcategory.php"
                            class="side-nav-link <?= ($current_page === 'subcategory.php' || $current_page === 'subcategory_edit.php') ? 'active' : '' ?>"><i
                                class="fas fa-eye"></i> View</a></li>
                </ul>
            </li>

            <li class="side-nav-item <?= $service_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-customer-service-2-line"></i>
                    <span>Manage Service</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="service_add.php"
                            class="side-nav-link <?= ($current_page === 'service_add.php') ? 'active' : '' ?>"><i
                                class="fas fa-plus"></i> Create</a></li>
                    <li><a href="services.php"
                            class="side-nav-link <?= ($current_page === 'services.php' || $current_page === 'service_edit.php' || $current_page === 'service_view.php' || $current_page === 'service_details.php') ? 'active' : '' ?>"><i
                                class="fas fa-eye"></i> View</a></li>
                </ul>
            </li>
            <li class="side-nav-item <?= $testimonial_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-star-line"></i>
                    <span>Manage Testimonials</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="testimonial_add.php"
                            class="side-nav-link <?= ($current_page === 'testimonial_add.php') ? 'active' : '' ?>"><i
                                class="fas fa-plus"></i> Add Testimonial</a></li>
                    <li><a href="testimonials.php"
                            class="side-nav-link <?= ($current_page === 'testimonials.php' || $current_page === 'testimonial_edit.php') ? 'active' : '' ?>"><i
                                class="fas fa-eye"></i> View Testimonials</a></li>
                </ul>
            </li>


            <li class="side-nav-item <?= $certificate_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-award-line"></i>
                    <span>Manage Certificates</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="certificate_add.php"
                            class="side-nav-link <?= ($current_page === 'certificate_add.php') ? 'active' : '' ?>"><i
                                class="fas fa-plus"></i> Add Certificate</a></li>
                    <li><a href="certificates.php"
                            class="side-nav-link <?= ($current_page === 'certificates.php' || $current_page === 'certificate_edit.php') ? 'active' : '' ?>"><i
                                class="fas fa-eye"></i> View Certificates</a>
                    </li>




                    <li class="side-nav-item <?= $brand_active ? 'active' : '' ?>">
                        <a href="javascript:void(0);" class="side-nav-link has-arrow">
                            <i class="ri-building-2-line"></i>
                            <span>Manage Brands</span>
                        </a>
                        <ul class="side-nav-second-level">
                            <li><a href="brand_add.php"
                                    class="side-nav-link <?= ($current_page === 'brand_add.php') ? 'active' : '' ?>"><i
                                        class="fas fa-plus"></i> Add Brand</a></li>
                            <li><a href="brands.php"
                                    class="side-nav-link <?= ($current_page === 'brands.php' || $current_page === 'brand_edit.php') ? 'active' : '' ?>"><i
                                        class="fas fa-eye"></i> View Brands</a></li>
                        </ul>
                    </li>
                    <li class="side-nav-item <?= $enquiry_active ? 'active' : '' ?>">
                        <a href="enquiries.php" class="side-nav-link">
                            <i class="ri-question-answer-line"></i>
                            <span>Enquiries</span>
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


                </ul>
            </li>
            <li class="side-nav-item <?= $blog_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="side-nav-link has-arrow">
                    <i class="ri-article-line"></i>
                    <span>Manage Blogs</span>
                </a>
                <ul class="side-nav-second-level">
                    <li><a href="blog_add.php"
                            class="side-nav-link <?= ($current_page === 'blog_add.php') ? 'active' : '' ?>"><i
                                class="fas fa-plus"></i> Add Blog</a></li>
                    <li><a href="blogs.php"
                            class="side-nav-link <?= ($current_page === 'blogs.php' || $current_page === 'blog_edit.php') ? 'active' : '' ?>"><i
                                class="fas fa-eye"></i> View Blogs</a></li>
                </ul>
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