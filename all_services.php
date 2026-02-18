<?php
session_start();

// Include database connection
require_once 'includes/header.php';
include 'includes/connection.php';

if (!function_exists('limitWords')) {
    function limitWords($text, $limit = 10) {
        $text = trim((string)$text);
        if ($text === '') return $text;
        $words = preg_split('/\s+/', $text);
        if (count($words) <= $limit) {
            return $text;
        }
        return implode(' ', array_slice($words, 0, $limit)) . '...';
    }
}

// Fetch service cards dynamically from services table
$service_cards = [];
if (isset($conn)) {
    $service_res = mysqli_query(
        $conn,
        "SELECT id, service_name, title, slug, short_description, hero_image, card_img
         FROM services
         WHERE slug IS NOT NULL AND slug != ''
         ORDER BY id DESC
         "
    );

    if ($service_res && mysqli_num_rows($service_res) > 0) {
        while ($row = mysqli_fetch_assoc($service_res)) {
            $service_cards[] = $row;
        }
    }
}
?>

<style>
/* Breadcrumb Styles - With Background Image and Centered Navigation */
.breadcrumb-section {
    position: relative;
    padding: 80px 0;
    background: url('includes/assets/services.jpg') no-repeat center center;
    background-size: cover;
    background-attachment: ;
    text-align: center;
    isolation: isolate;
}

/* Dark overlay for better text readability */
.breadcrumb-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(32, 32, 32, 0.7) 0%, rgba(87, 85, 85, 0.5) 100%);
    z-index: 1;
    pointer-events: none;
}

.breadcrumb-container {
    position: relative;
    z-index: 2;
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

.breadcrumb-header {
    margin-bottom: 15px;
}

.breadcrumb-subtitle {
    color: rgba(255,255,255,0.9);
    font-size: 1rem;
    font-weight: 500;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 10px;
    display: inline-block;
    background: rgba(200,16,46,0.2);
    padding: 5px 15px;
    border-radius: 30px;
    backdrop-filter: blur(5px);
    border: 1px solid rgba(255,255,255,0.1);
}

.breadcrumb-title {
    font-size: 3.2rem;
    font-weight: 800;
    color: white;
    margin: 0 0 20px 0;
    line-height: 1.2;
    text-shadow: 0 4px 15px rgba(82, 71, 71, 0.5);
    letter-spacing: -0.02em;
}

.breadcrumb-title span {
    color: var(--primary-color);
    position: relative;
    display: inline-block;
}

.breadcrumb-title span::after {
    content: '';
    position: absolute;
    bottom: 5px;
    left: 0;
    width: 100%;
    height: 8px;
    background: rgba(200,16,46,0.3);
    z-index: -1;
    border-radius: 4px;
}

.breadcrumb-nav {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    padding: 12px 25px;
    border-radius: 50px;
    border: 1px solid rgba(255,255,255,0.2);
    display: inline-flex;
    margin: 0 auto;
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    justify-content: center;
}

.breadcrumb-item {
    font-size: 1rem;
    font-weight: 500;
}

.breadcrumb-item a {
    color: rgba(255,255,255,0.9);
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 5px 10px;
    border-radius: 30px;
}

.breadcrumb-item a:hover {
    color: white;
    background: rgba(255,255,255,0.1);
    transform: translateY(-2px);
}

.breadcrumb-item.active {
    color: white;
    font-weight: 600;
    padding: 5px 10px;
}

.breadcrumb-item.active i {
    color: var(--primary-color);
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: rgba(255,255,255,0.5);
    font-size: 1.4rem;
    line-height: 1;
    padding: 0 5px;
}

.breadcrumb-icon {
    width: 35px;
    height: 35px;
    background: rgba(200,16,46,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    margin-right: 10px;
    border: 1px solid rgba(255,255,255,0.2);
}

/* Responsive */
@media (max-width: 768px) {
    .breadcrumb-section {
        padding: 60px 0;
    }
    
    .breadcrumb-title {
        font-size: 2.2rem;
    }
    
    .breadcrumb-nav {
        padding: 10px 20px;
    }
    
    .breadcrumb-item {
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .breadcrumb-title {
        font-size: 1.8rem;
    }
    
    .breadcrumb-nav {
        flex-direction: column;
        border-radius: 20px;
        padding: 15px;
    }
}

/* Services Section Styles */
.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}
</style>

<!-- Breadcrumb Section - With Background Image and Centered Navigation -->
<section class="breadcrumb-section">
    <div class="container">
        <div class="breadcrumb-container">
            <div class="breadcrumb-header">
                <span class="breadcrumb-subtitle">
                    <i class="fas fa-tag me-2"></i>Our Services
                </span>
            </div>
            
            <h1 class="breadcrumb-title">
                <span>All Services</span>
            </h1>
            
            <div class="breadcrumb-nav">
                <span class="breadcrumb-icon">
                    <i class="fas fa-compass"></i>
                </span>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="index.php">
                                <i class="fas fa-home"></i> 
                                <span class="d-none d-sm-inline">Home</span>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-cog"></i> 
                            <span>All Services</span>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-5" style="background-color: #f8fafc;">
    <div class="container py-4">
        <div class="text-center mb-5 animate-up">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2">Our Services</span>
            <h2 class="fw-bold mb-2">Tailored Financial Solutions</h2>
            <p class="text-muted">Choose the product that fits your life goals</p>
        </div>
        
        <div class="row g-4">
            <?php
            $card_colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
            $card_icons = ['fa-user', 'fa-briefcase', 'fa-user-md', 'fa-home', 'fa-credit-card', 'fa-car'];
?>
            <?php if (!empty($service_cards)): ?>
                <?php foreach ($service_cards as $index => $service_card):
                    $color = $card_colors[$index % count($card_colors)];
                    $icon = $card_icons[$index % count($card_icons)];
                    $card_title = !empty($service_card['service_name']) ? $service_card['service_name'] : $service_card['title'];
                    $card_desc = !empty($service_card['short_description']) ? limitWords($service_card['short_description'], 8) : 'Explore this service';
                    $card_image = !empty($service_card['card_img'])
                        ? $service_card['card_img']
                        : 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=600';
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card bg-white border-0 shadow-sm h-100 card-hover rounded-4 overflow-hidden">
                        <div style="height: 200px; overflow: hidden;">
                            <img src="<?= htmlspecialchars($card_image) ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?= htmlspecialchars($card_title) ?>">
                        </div>
                        
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-<?= $color ?> bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="fas <?= $icon ?> fs-4 text-<?= $color ?>"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($card_title) ?></h5>
                                    <p class="text-muted small mb-0"><?= htmlspecialchars($card_desc) ?></p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top border-light">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Service</small>
                                    <span class="text-<?= $color ?> fw-bold">Available</span>
                                </div>
                                <a href="services.php?slug=<?= urlencode($service_card['slug']) ?>" class="btn btn-sm btn-outline-<?= $color ?> rounded-pill px-3">
                                    Check Eligibility
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-light border text-center mb-0">No services available right now.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>