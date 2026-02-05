<?php
include 'header.php';

/* ------------------------------
   Static Values (No DB)
--------------------------------*/
$adminName        = 'Admin';
$categoryCount    = 12;
$subCategoryCount = 34;
$serviceCount     = 18;
$departmentCount  = 5;

$staff_id = (int)$_SESSION['staff_id'];
if (hasAccess($conn, 'enquiry_view_all')) {
    $count_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM enquiries");
} elseif (hasAccess($conn, 'enquiry_view_assigned')) {
    $count_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM enquiries WHERE assigned_staff_id = $staff_id");
} else {
    $count_res = false;
}
$enquiryCount = 0;
if ($count_res && ($row = mysqli_fetch_assoc($count_res))) {
    $enquiryCount = (int)$row['total'];
}
?>

<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
        --blue-600: #2563eb;
    }

    .content-page { background-color: #fcfcfd; }

    .greeting-header {
        padding: 40px 0;
        border-bottom: 1px solid var(--slate-200);
        margin-bottom: 40px;
    }

    .greeting-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--slate-900);
    }

    .greeting-header p {
        color: var(--slate-600);
    }

    .stat-card-link {
        text-decoration: none;
        display: block;
        transition: all 0.3s;
    }

    .stat-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: 16px;
        padding: 24px;
        height: 100%;
    }

    .stat-card-link:hover .stat-card {
        border-color: var(--slate-900);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
    }

    .label {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.1em;
        color: var(--slate-600);
    }

    .value {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--slate-900);
    }

    .footer-link {
        margin-top: 16px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--blue-600);
    }

    .action-btn {
        background: var(--slate-900);
        color: #fff;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
    }

    .action-btn-outline {
        border: 1px solid var(--slate-200);
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        color: var(--slate-900);
        text-decoration: none;
    }

    .action-btn-outline:hover {
        background: var(--slate-900);
        color: #fff;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="greeting-header">
                <h1>Welcome, <?= htmlspecialchars($adminName) ?></h1>
                <p>System performance and management overview.</p>
            </div>

            <div class="row">

                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <span class="label">Total Categories</span>
                        <span class="value"><?= $categoryCount ?></span>
                        <div class="footer-link">View all categories →</div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <span class="label">Sub Categories</span>
                        <span class="value"><?= $subCategoryCount ?></span>
                        <div class="footer-link">View subcategories →</div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <span class="label">Total Services</span>
                        <span class="value"><?= $serviceCount ?></span>
                        <div class="footer-link">Manage services →</div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <span class="label">Total Departments</span>
                        <span class="value"><?= $departmentCount ?></span>
                        <div class="footer-link">Manage departments →</div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <span class="label">Total Enquiries</span>
                        <span class="value"><?= $enquiryCount ?></span>
                        <div class="footer-link">View enquiries →</div>
                    </div>
                </div>

            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="p-4 bg-white rounded-4 border">
                        <h5 class="fw-bold mb-4">Control Panel</h5>
                        <div class="d-flex gap-3">
                            <a href="#" class="action-btn">Add Category</a>
                            <a href="#" class="action-btn-outline">New Service Entry</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
