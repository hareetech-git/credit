
<include 'header.php'; ?>
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