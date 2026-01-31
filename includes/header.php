<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isCustomerLoggedIn = isset($_SESSION['customer_id']);
$customerName = $_SESSION['customer_name'] ?? '';

include 'includes/connection.php'; 

$current_page = basename($_SERVER['PHP_SELF']);

// --- 1. FETCH MENU STRUCTURE (UPDATED) ---
// Changed JOIN to LEFT JOIN to show Categories even if they have no services yet.
$menu_sql = "SELECT 
                c.id as cat_id, c.category_name,
                s.id as sub_id, s.sub_category_name,
                srv.id as service_id, srv.service_name, srv.slug
            FROM service_categories c
            LEFT JOIN services_subcategories s ON c.id = s.category_id AND s.status = 'active'
            LEFT JOIN services srv ON s.id = srv.sub_category_id
            WHERE c.active = 1 
            ORDER BY c.sequence ASC, s.sequence ASC, srv.id ASC";

$menu_res = mysqli_query($conn, $menu_sql);

$menuTree = [];

while ($row = mysqli_fetch_assoc($menu_res)) {
    $cat_id = $row['cat_id'];
    $sub_id = $row['sub_id'];
    $srv_id = $row['service_id'];

    // 1. Build Category
    if (!isset($menuTree[$cat_id])) {
        $menuTree[$cat_id] = [
            'name' => $row['category_name'],
            'subcategories' => []
        ];
    }

    // 2. Build Subcategory (Only if it exists)
    if ($sub_id) {
        if (!isset($menuTree[$cat_id]['subcategories'][$sub_id])) {
            $menuTree[$cat_id]['subcategories'][$sub_id] = [
                'name' => $row['sub_category_name'],
                'services' => []
            ];
        }

        // 3. Add Service (Only if it exists)
        if ($srv_id) {
            $final_slug = !empty($row['slug']) ? $row['slug'] : 'service-'.$srv_id;
            $menuTree[$cat_id]['subcategories'][$sub_id]['services'][] = [
                'name' => $row['service_name'],
                'slug' => $final_slug
            ];
        }
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Udhar Capital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="includes/css/header.css">
</head>
<body>

<header class="header-custom">
    <div class="header-container">
        <a href="index.php" class="logo-custom">
            <img src="includes/assets/udhaar_logo.png" alt="Udhar Capital Logo" class="logo-img">
        </a>

        <ul class="nav-menu-custom">
            <li class="nav-item-custom">
                <a href="index.php" class="nav-link-custom <?= ($current_page == 'index.php') ? 'active' : '' ?>">Home</a>
            </li>
            
            <li class="nav-item-custom">
                <a href="#" class="nav-link-custom">Become DSA Partner</a>
            </li>
            
            <?php foreach($menuTree as $catId => $category): 
                // Check if this category actually has subcategories with data
                $hasSub = !empty($category['subcategories']);
            ?>
                <li class="nav-item-custom" id="menu-cat-<?= $catId ?>">
                    <?php if($hasSub): ?>
                        <span class="nav-link-custom">
                            <?= htmlspecialchars($category['name']) ?> <i class="fas fa-chevron-down"></i>
                        </span>
                        
                        <div class="mega-dropdown">
                            <div class="mega-menu-categories">
                                <?php 
                                $firstSub = true;
                                foreach($category['subcategories'] as $subId => $subcat): 
                                    $isActive = $firstSub ? 'active' : '';
                                ?>
                                    <div class="mega-category-item <?= $isActive ?>" data-target="sub-panel-<?= $subId ?>">
                                        <?= htmlspecialchars($subcat['name']) ?> <i class="fas fa-chevron-right"></i>
                                    </div>
                                <?php 
                                    $firstSub = false; 
                                endforeach; 
                                ?>
                            </div>
                            
                            <?php 
                            $firstPanel = true;
                            foreach($category['subcategories'] as $subId => $subcat): 
                                $isActivePanel = $firstPanel ? 'active' : '';
                                $serviceCount = count($subcat['services']);
                            ?>
                                <div class="mega-menu-services <?= $isActivePanel ?>" id="sub-panel-<?= $subId ?>">
                                    <div class="mega-service-header"><?= htmlspecialchars($subcat['name']) ?></div>
                                    
                                    <?php if($serviceCount === 0): ?>
                                        <div class="p-3 text-muted small">Coming Soon</div>

                                    <?php elseif($serviceCount === 1): 
                                        // SINGLE SERVICE: Show 1 Big Link
                                        $srv = $subcat['services'][0];
                                    ?>
                                        <a href="services.php?slug=<?= $srv['slug'] ?>" class="mega-service-item">
                                            <strong><?= htmlspecialchars($srv['name']) ?></strong>
                                            <small>Click to view details</small>
                                        </a>

                                    <?php else: 
                                        // MULTIPLE SERVICES: Show List
                                        foreach($subcat['services'] as $srv): ?>
                                            <a href="services.php?slug=<?= $srv['slug'] ?>" class="mega-service-item">
                                                <strong><?= htmlspecialchars($srv['name']) ?></strong>
                                                <small>View details</small>
                                            </a>
                                    <?php endforeach; 
                                    endif; ?>
                                </div>
                            <?php 
                                $firstPanel = false;
                            endforeach; 
                            ?>
                        </div>

                    <?php else: ?>
                        <a href="#" class="nav-link-custom"><?= htmlspecialchars($category['name']) ?></a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>

            <li class="nav-item-custom"><a href="#" class="nav-link-custom">Insurance</a></li>
            <li class="nav-item-custom"><a href="contact.php" class="nav-link-custom">Contact Us</a></li>
        </ul>

   <div class="nav-buttons-custom">

    <?php if ($isCustomerLoggedIn): ?>
        <!-- Logged-in customer -->
        <div class="dropdown">
            <a href="#" class="btn-login-custom dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-user-circle me-1"></i>
                <?= htmlspecialchars($customerName) ?>
            </a>

            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="customer/dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="customer/profile.php">
                        <i class="fas fa-user me-2"></i>My Profile
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="customer/db/auth-logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </div>

    <?php else: ?>
        <!-- Guest -->
        <a href="login.php" class="btn-apply-custom">Login Now</a>
        
    <?php endif; ?>

</div>
        

        <div class="mobile-toggle-custom" id="mobileToggle">
            <span></span><span></span><span></span>
        </div>
    </div>
</header>

<div class="mobile-overlay-custom" id="mobileOverlay"></div>

<div class="mobile-menu-custom" id="mobileMenu">
    <div style="flex: 1; overflow-y: auto;">
        <div class="mobile-nav-item-custom"><a href="index.php" class="mobile-nav-link-custom">Home</a></div>
        <div class="mobile-nav-item-custom"><a href="#" class="mobile-nav-link-custom">Become DSA Partner</a></div>
        
        <?php foreach($menuTree as $catId => $category): 
             $hasSub = !empty($category['subcategories']);
        ?>
            <div class="mobile-nav-item-custom">
                <?php if($hasSub): ?>
                    <span class="mobile-nav-link-custom" onclick="toggleMobileDropdown('mobile-cat-<?= $catId ?>', this)">
                        <?= htmlspecialchars($category['name']) ?> <i class="fas fa-chevron-down"></i>
                    </span>
                    
                    <div class="mobile-dropdown-custom" id="mobile-cat-<?= $catId ?>">
                        <?php foreach($category['subcategories'] as $subId => $subcat): 
                            $serviceCount = count($subcat['services']);
                        ?>
                            
                            <?php if($serviceCount === 1): 
                                // Single Service -> Link directly using Subcategory Name
                                $srv = $subcat['services'][0];    
                            ?>
                                <a href="services.php?slug=<?= $srv['slug'] ?>" class="mobile-dropdown-item-custom fw-bold">
                                    <?= htmlspecialchars($subcat['name']) ?>
                                </a>

                            <?php elseif($serviceCount > 1): 
                                // Multiple Services -> Header + Links
                            ?>
                                <div class="mobile-category-header text-muted mt-2 mb-1 ps-3" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:1px;">
                                    <?= htmlspecialchars($subcat['name']) ?>
                                </div>
                                
                                <?php foreach($subcat['services'] as $srv): ?>
                                    <a href="services.php?slug=<?= $srv['slug'] ?>" class="mobile-dropdown-item-custom ps-4">
                                        <i class="fas fa-angle-right me-2 small"></i> <?= htmlspecialchars($srv['name']) ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <a href="#" class="mobile-nav-link-custom"><?= htmlspecialchars($category['name']) ?></a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        
        <div class="mobile-nav-item-custom"><a href="#" class="mobile-nav-link-custom">Insurance</a></div>
        <div class="mobile-nav-item-custom"><a href="contact.php" class="mobile-nav-link-custom">Contact Us</a></div>
    </div>
    
    <div class="mobile-buttons-custom">
        <a href="login.php" class="btn-login-custom">Login Now</a>
        <a href="apply-loan.php" class="btn-apply-custom" onclick="scrollToForm()">Apply Now</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const megaCategoryItems = document.querySelectorAll('.mega-category-item');
    
    megaCategoryItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            const parentDropdown = this.closest('.mega-dropdown');
            const targetId = this.getAttribute('data-target');

            parentDropdown.querySelectorAll('.mega-category-item').forEach(cat => cat.classList.remove('active'));
            parentDropdown.querySelectorAll('.mega-menu-services').forEach(panel => panel.classList.remove('active'));

            this.classList.add('active');
            const targetPanel = document.getElementById(targetId);
            if(targetPanel) targetPanel.classList.add('active');
        });
    });

    const mobileToggle = document.getElementById('mobileToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileOverlay = document.getElementById('mobileOverlay');

    function toggleMenu() {
        mobileToggle.classList.toggle('active');
        mobileMenu.classList.toggle('active');
        mobileOverlay.classList.toggle('active');
    }

    mobileToggle.addEventListener('click', toggleMenu);
    mobileOverlay.addEventListener('click', toggleMenu);

    function toggleMobileDropdown(id, triggerElement) {
        const dropdown = document.getElementById(id);
        dropdown.classList.toggle('active');
        const icon = triggerElement.querySelector('i');
        if(icon) icon.style.transform = dropdown.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0)';
    }

    function scrollToForm() {
        const form = document.getElementById('loanForm');
        if (form) form.scrollIntoView({ behavior: 'smooth', block: 'center' });
        toggleMenu(); 
    }
</script>
