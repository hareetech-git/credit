<?php
include 'db/config.php';
session_start();

$categoryCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM categories"))['total'];
$serviceCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM services"))['total'];
$sliderCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM slider_images"))['total'];
$adminName = $_SESSION['admin_name'] ?? 'Admin';
?>

<?php include 'header.php'; ?>
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
    
    /* Elegant Greeting */
    .greeting-header {
        padding: 40px 0;
        border-bottom: 1px solid var(--slate-200);
        margin-bottom: 40px;
    }
    .greeting-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--slate-900);
        letter-spacing: -0.02em;
    }
    .greeting-header p {
        color: var(--slate-600);
        font-size: 1rem;
    }

    /* Premium Card Design */
    .stat-card-link {
        text-decoration: none !important;
        display: block;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid var(--slate-200);
        border-radius: 16px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    /* Subtle hover: Deep border and soft shadow */
    .stat-card-link:hover .stat-card {
        border-color: var(--slate-900);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
    }

    .stat-card .label {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.1em;
        color: var(--slate-600);
        margin-bottom: 8px;
        display: block;
    }

    .stat-card .value {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--slate-900);
        display: block;
    }

    .stat-card .footer-link {
        margin-top: 16px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--blue-600);
        display: flex;
        align-items: center;
    }

    .stat-card .footer-link i {
        margin-left: 4px;
        transition: transform 0.2s;
    }

    .stat-card-link:hover .footer-link i {
        transform: translateX(4px);
    }

    /* Quick Action Buttons */
    .action-btn {
        background: var(--slate-900);
        color: white;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        border: none;
        transition: opacity 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    
    .action-btn-outline {
        background: transparent;
        border: 1px solid var(--slate-200);
        color: var(--slate-900);
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
    }

    .action-btn-outline:hover {
        background: var(--slate-900);
        color: white;
        border-color: var(--slate-900);
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="greeting-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h1>Welcome, <?= htmlspecialchars($adminName) ?></h1>
                        <p class="mb-0">System performance and management overview.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                
                <div class="col-md-4 mb-4">
                    <a href="manage-categories.php" class="stat-card-link">
                        <div class="stat-card">
                            <span class="label">Total Categories</span>
                            <span class="value"><?= $categoryCount ?></span>
                            <div class="footer-link">
                                View all categories <i class="ri-arrow-right-line"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 mb-4">
                    <a href="manage-services.php" class="stat-card-link">
                        <div class="stat-card">
                            <span class="label">Services Active</span>
                            <span class="value"><?= $serviceCount ?></span>
                            <div class="footer-link">
                                Manage services <i class="ri-arrow-right-line"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 mb-4">
                    <a href="slider-images.php" class="stat-card-link">
                        <div class="stat-card">
                            <span class="label">Media Assets</span>
                            <span class="value"><?= $sliderCount ?></span>
                            <div class="footer-link">
                                Update slider <i class="ri-arrow-right-line"></i>
                            </div>
                        </div>
                    </a>
                </div>

            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="p-4 bg-white rounded-4 border">
                        <h5 class="fw-bold mb-4">Control Panel</h5>
                        <div class="d-flex gap-3">
                            <a href="add-category.php" class="action-btn">
                                Add Category
                            </a>
                            <a href="add-service.php" class="action-btn-outline">
                                New Service Entry
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>