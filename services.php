<?php
// ==========================================
// BACKEND LOGIC (Do not modify)
// ==========================================
require_once 'includes/header.php';

// Initialize variables
$service = null;
$error = null;

// Check if slug is provided in the URL
if(isset($_GET['slug']) && !empty($_GET['slug'])) {
    $slug = trim($_GET['slug']);
    
    // Validate slug format
    if(!preg_match('/^[a-z0-9\-]+$/', $slug)) {
        $error = "Invalid service URL format.";
    } else {
        // Include database connection
        if(!file_exists('includes/connection.php')) {
            $error = "Database configuration not found.";
        } else {
            include('includes/connection.php');
            
            // Check if connection exists
            if(!isset($conn)) {
                $error = "Database connection is not properly configured.";
            } else {
                // Prepare SQL query
                $query = "SELECT `id`, `category_id`, `sub_category_id`, `service_name`, `title`, `slug`, `short_description`, `long_description`, `created_at`, `updated_at` 
                          FROM `services` 
                          WHERE `slug` = ? 
                          AND `slug` IS NOT NULL 
                          AND `slug` != '' 
                          LIMIT 1";
                
                if($stmt = mysqli_prepare($conn, $query)) {
                    // Bind parameters
                    mysqli_stmt_bind_param($stmt, "s", $slug);
                    
                    // Execute query
                    if(mysqli_stmt_execute($stmt)) {
                        $result = mysqli_stmt_get_result($stmt);
                        
                        if(mysqli_num_rows($result) > 0) {
                            $service = mysqli_fetch_assoc($result);
                            // Convert NULL values to empty strings
                            $service = array_map(function($value) {
                                return $value === null ? '' : $value;
                            }, $service);
                        } else {
                            $error = "The service you're looking for doesn't exist or has been moved.";
                        }
                    } else {
                        $error = "Unable to fetch service details at the moment.";
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $error = "Database query preparation failed.";
                }
                mysqli_close($conn);
            }
        }
    }
} else {
    $error = "No service selected. Please choose a service from our list.";
}
?>

<!-- Only content after header inclusion -->
<style>
    /* Custom styles for services page only - uses your header's root colors */
    .services-hero-section {
        padding: 140px 0 60px;
        background: linear-gradient(rgba(11, 8, 27, 0.9), rgba(11, 8, 27, 0.8)), 
                    url('https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
        background-size: cover;
        background-position: center;
        color: white;
    }
    
    .services-hero-title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 20px;
        line-height: 1.2;
    }
    
    .services-hero-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 30px;
        max-width: 600px;
    }
    
    .services-btn-custom {
        background: var(--accent-teal);
        color: var(--primary-color);
        padding: 12px 35px;
        font-weight: 600;
        border-radius: 8px;
        border: none;
        transition: all 0.3s ease;
        display: inline-block;
        text-decoration: none;
    }
    
    .services-btn-custom:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 212, 170, 0.3);
        color: var(--primary-color);
    }
    
    .services-features-section {
        padding: 80px 0;
        background: #f8fafc;
    }
    
    .services-section-title {
        color: var(--primary-color);
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 50px;
        text-align: center;
    }
    
    .services-feature-card {
        background: white;
        border-radius: 15px;
        padding: 40px 30px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
        height: 100%;
        border: 2px solid transparent;
    }
    
    .services-feature-card:hover {
        transform: translateY(-10px);
        border-color: var(--accent-teal);
        box-shadow: 0 15px 40px rgba(0, 212, 170, 0.1);
    }
    
    .services-feature-icon {
        font-size: 2.5rem;
        color: var(--accent-teal);
        margin-bottom: 20px;
    }
    
    .services-feature-title {
        color: var(--primary-color);
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .services-feature-text {
        color: #64748b;
        font-size: 1rem;
        line-height: 1.6;
    }
    
    .services-content-section {
        padding: 80px 0;
        background: white;
    }
    
    .services-content-card {
        background: white;
        border-radius: 15px;
        padding: 50px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    
    .services-content-title {
        color: var(--primary-color);
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .services-short-desc {
        font-size: 1.2rem;
        color: #64748b;
        line-height: 1.8;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f1f5f9;
    }
    
    .services-long-desc {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #475569;
    }
    
    .services-long-desc p {
        margin-bottom: 20px;
    }
    
    .services-error-container {
        padding: 100px 20px;
        text-align: center;
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .services-error-box {
        max-width: 600px;
        padding: 50px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    
    .services-error-title {
        color: #ef4444;
        font-size: 2rem;
        margin-bottom: 20px;
    }
    
    .services-error-message {
        color: #64748b;
        font-size: 1.1rem;
        margin-bottom: 30px;
    }
    
    .services-cta-section {
        padding: 80px 0;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        text-align: center;
    }
    
    .services-cta-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .services-cta-text {
        font-size: 1.2rem;
        max-width: 700px;
        margin: 0 auto 40px;
        opacity: 0.9;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .services-hero-section {
            padding: 120px 0 40px;
            text-align: center;
        }
        
        .services-hero-title {
            font-size: 2.2rem;
        }
        
        .services-content-card {
            padding: 30px 20px;
        }
        
        .services-section-title {
            font-size: 2rem;
        }
    }
</style>

<main>
    <?php if(isset($error) && !empty($error) && $service === null): ?>
        <!-- Error Display -->
        <div class="services-error-container">
            <div class="services-error-box">
                <i class="fas fa-exclamation-circle text-danger fa-4x mb-4"></i>
                <h2 class="services-error-title">Oops!</h2>
                <p class="services-error-message"><?php echo htmlspecialchars($error); ?></p>
                <a href="index.php" class="btn btn-primary">Back to Home</a>
            </div>
        </div>
    <?php elseif($service): ?>
        <!-- Service Details Page -->
        <!-- Hero Section -->
        <section class="services-hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8 mx-auto text-center">
                        <h1 class="services-hero-title">
                            <?php echo htmlspecialchars($service['title']); ?>
                        </h1>
                        
                        <?php if(!empty($service['short_description'])): ?>
                            <p class="services-hero-subtitle">
                                <?php echo htmlspecialchars($service['short_description']); ?>
                            </p>
                        <?php else: ?>
                            <p class="services-hero-subtitle">
                                ARE YOU READY TO TAKE YOUR BUSINESS TO GREATER HEIGHTS? 
                                APPLY FOR LOANS ONLINE AT LOW-INTEREST RATES THROUGH UDHAR CAPITAL.
                            </p>
                        <?php endif; ?>
                        
                        <a href="#apply-now" class="services-btn-custom">
                            <i class="fas fa-file-contract me-2"></i> Apply Now
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="services-features-section">
            <div class="container">
                <h2 class="services-section-title">Choose Secured or Unsecured Business Loan</h2>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="services-feature-card">
                            <div class="services-feature-icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <h3 class="services-feature-title">Rate of Interest Starting from 14%</h3>
                            <p class="services-feature-text">Competitive interest rates designed to help your business grow without burden.</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="services-feature-card">
                            <div class="services-feature-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h3 class="services-feature-title">Minimal Documentation</h3>
                            <p class="services-feature-text">Quick and easy application process with minimal paperwork required.</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="services-feature-card">
                            <div class="services-feature-icon">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <h3 class="services-feature-title">Flexible Loan Options</h3>
                            <p class="services-feature-text">Choose between secured or unsecured business loans based on your needs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Content Section -->
        <section class="services-content-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="services-content-card">
                            <h2 class="services-content-title"><?php echo htmlspecialchars($service['service_name']); ?></h2>
                            
                            <?php if(!empty($service['short_description'])): ?>
                                <div class="services-short-desc">
                                    <?php echo htmlspecialchars($service['short_description']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($service['long_description'])): ?>
                                <div class="services-long-desc">
                                    <?php 
                                    $description = htmlspecialchars($service['long_description']);
                                    $paragraphs = explode("\n", $description);
                                    foreach($paragraphs as $paragraph) {
                                        $trimmed = trim($paragraph);
                                        if(!empty($trimmed)) {
                                            echo '<p>' . $trimmed . '</p>';
                                        }
                                    }
                                    ?>
                                </div>
                            <?php else: ?>
                                <div class="services-long-desc">
                                    <p>Our business loans are designed to help you grow your business with flexible options and competitive rates.</p>
                                    <h3>Key Features:</h3>
                                    <ul>
                                        <li>Loan amount up to ₹2 Crores</li>
                                        <li>Repayment tenure up to 5 years</li>
                                        <li>Quick processing and approval</li>
                                        <li>Transparent pricing with no hidden charges</li>
                                        <li>Dedicated relationship manager</li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="services-cta-section" id="apply-now">
            <div class="container">
                <h2 class="services-cta-title">Ready to Grow Your Business?</h2>
                <p class="services-cta-text">Take the first step towards achieving your business goals. Apply for a business loan today.</p>
                <a href="tel:+919569408620" class="services-btn-custom">
                    <i class="fas fa-phone-alt me-2"></i> Call Now: +91 95694 08620
                </a>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>

<script>
    // Smooth scroll to apply section
    document.querySelector('a[href="#apply-now"]')?.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('apply-now').scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    });
</script>