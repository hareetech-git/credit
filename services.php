<?php
// Include necessary files
require_once 'insert/service_detail.php';

// Get service_id from URL
$service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;

// Redirect if no service_id
if ($service_id <= 0) {
    header('Location: services.php');
    exit;
}

// Initialize service detail class
$serviceDetail = new ServiceDetail();

// Get all service data
$data = $serviceDetail->getAllServiceData($service_id);

// Check if service exists
if (!$data['service']) {
    header('Location: services.php');
    exit;
}

// Extract service info
$service = $data['service'];
$overview = $data['overview'];
$documents = $data['documents'];
$features = $data['features'];
$eligibility = $data['eligibility'];
$fees = $data['fees'];
$repayment = $data['repayment'];
$banks = $data['banks'];
$why_choose = $data['why_choose'];

// Include header
require_once 'includes/header.php';
?>

<style>
    /* Global Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        background-color: #ffffff;
    }
    
    /* Hero Section */
    .service-hero {
        background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
        padding: 80px 32px 60px;
        position: relative;
        overflow: hidden;
    }
    
    .service-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, transparent 100%);
        border-radius: 50%;
    }
    
    .hero-container {
        max-width: 1280px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }
    
    .hero-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
        font-size: 14px;
        color: #64748b;
    }
    
    .hero-breadcrumb a {
        color: #2563eb;
        text-decoration: none;
        transition: color 0.2s;
    }
    
    .hero-breadcrumb a:hover {
        color: #1d4ed8;
    }
    
    .hero-breadcrumb i {
        font-size: 10px;
    }
    
    .service-category {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #dbeafe;
        color: #2563eb;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    
    .service-title {
        font-size: 3rem;
        color: #0f172a;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin-bottom: 20px;
        line-height: 1.1;
    }
    
    .service-description {
        font-size: 1.125rem;
        color: #64748b;
        line-height: 1.7;
        max-width: 800px;
        margin-bottom: 32px;
    }
    
    .hero-stats {
        display: flex;
        gap: 32px;
        margin-top: 32px;
        flex-wrap: wrap;
    }
    
    .stat-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        background: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        font-size: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .stat-content h4 {
        color: #0f172a;
        font-size: 1.25rem;
        font-weight: 700;
    }
    
    .stat-content p {
        color: #64748b;
        font-size: 14px;
    }
    
    /* Main Content Section */
    .service-content {
        padding: 60px 32px;
    }
    
    .content-container {
        max-width: 1280px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 60px;
    }
    
    /* Left Column Sections */
    .content-section {
        background: white;
        border-radius: 16px;
        padding: 40px;
        margin-bottom: 32px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid #f1f5f9;
    }
    
    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }
    
    .section-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        font-size: 18px;
    }
    
    .section-header h2 {
        color: #0f172a;
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .section-content {
        color: #64748b;
        line-height: 1.7;
        font-size: 15px;
    }
    
    /* Lists */
    .requirements-list,
    .deliverables-list {
        display: grid;
        gap: 16px;
    }
    
    .list-item {
        display: flex;
        gap: 12px;
        padding: 16px;
        background: #f8fafc;
        border-radius: 10px;
        transition: all 0.2s ease;
    }
    
    .list-item:hover {
        background: #eff6ff;
        transform: translateX(4px);
    }
    
    .list-item-icon {
        width: 24px;
        height: 24px;
        background: #2563eb;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        flex-shrink: 0;
        margin-top: 2px;
    }
    
    .list-item-content h4 {
        color: #0f172a;
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 4px;
    }
    
    .list-item-content p {
        color: #64748b;
        font-size: 14px;
    }
    
    /* Process Flow */
    .process-timeline {
        position: relative;
        padding-left: 32px;
    }
    
    .process-timeline::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 32px;
        bottom: 32px;
        width: 2px;
        background: linear-gradient(to bottom, #2563eb, #93c5fd);
    }
    
    .process-step {
        position: relative;
        margin-bottom: 32px;
        padding-left: 24px;
    }
    
    .process-step:last-child {
        margin-bottom: 0;
    }
    
    .process-number {
        position: absolute;
        left: -32px;
        top: 0;
        width: 24px;
        height: 24px;
        background: #2563eb;
        border: 3px solid white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 0 0 4px #eff6ff;
    }
    
    .process-content h4 {
        color: #0f172a;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 8px;
    }
    
    .process-content p {
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }
    
    /* Timeline Table */
    .timeline-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
    }
    
    .timeline-table th {
        text-align: left;
        padding: 12px 16px;
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .timeline-table td {
        padding: 16px;
        background: #f8fafc;
        color: #64748b;
        font-size: 14px;
    }
    
    .timeline-table tr {
        transition: all 0.2s ease;
    }
    
    .timeline-table tr:hover td {
        background: #eff6ff;
    }
    
    .timeline-phase {
        font-weight: 600;
        color: #0f172a;
    }
    
    .timeline-duration {
        color: #2563eb;
        font-weight: 600;
    }
    
    /* Add-on Services */
    .addon-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    
    .addon-card {
        padding: 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    
    .addon-card:hover {
        border-color: #2563eb;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        transform: translateY(-2px);
    }
    
    .addon-card h4 {
        color: #0f172a;
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .addon-card h4 i {
        color: #2563eb;
        font-size: 16px;
    }
    
    .addon-card p {
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }
    
    /* FAQ Section */
    .faq-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .faq-item {
        background: #f8fafc;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    
    .faq-question {
        padding: 20px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
    }
    
    .faq-question:hover {
        background: #eff6ff;
    }
    
    .faq-question h4 {
        color: #0f172a;
        font-size: 15px;
        font-weight: 600;
        flex: 1;
    }
    
    .faq-toggle {
        width: 24px;
        height: 24px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        font-size: 14px;
        transition: transform 0.3s ease;
    }
    
    .faq-item.active .faq-toggle {
        transform: rotate(180deg);
    }
    
    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }
    
    .faq-item.active .faq-answer {
        max-height: 300px;
    }
    
    .faq-answer-content {
        padding: 0 20px 20px;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }
    
    /* Why Choose Us */
    .why-choose-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .why-choose-item {
        padding: 24px;
        background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
        border-radius: 12px;
        border: 1px solid #dbeafe;
    }
    
    .why-choose-item h4 {
        color: #0f172a;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .why-choose-item h4 i {
        color: #2563eb;
        font-size: 18px;
    }
    
    .why-choose-item h4 img {
        width: 24px;
        height: 24px;
        object-fit: contain;
    }
    
    .why-choose-item p {
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }
    
    /* Right Sidebar */
    .sidebar {
        position: sticky;
        top: 100px;
        height: fit-content;
    }
    
    .sidebar-card {
        background: white;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #f1f5f9;
        margin-bottom: 24px;
    }
    
    .sidebar-card h3 {
        color: #0f172a;
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .sidebar-features {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .sidebar-feature {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #f8fafc;
        border-radius: 8px;
    }
    
    .sidebar-feature i {
        color: #10b981;
        font-size: 16px;
    }
    
    .sidebar-feature span {
        color: #334155;
        font-size: 14px;
        font-weight: 500;
    }
    
    .apply-button {
        width: 100%;
        background: #2563eb;
        color: white;
        padding: 16px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-bottom: 16px;
    }
    
    .apply-button:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
    }
    
    .contact-button {
        width: 100%;
        background: white;
        color: #2563eb;
        padding: 16px;
        border: 2px solid #2563eb;
        border-radius: 10px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .contact-button:hover {
        background: #eff6ff;
    }
    
    .help-box {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
        padding: 24px;
        border-radius: 12px;
        text-align: center;
    }
    
    .help-box h4 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 12px;
    }
    
    .help-box p {
        font-size: 14px;
        margin-bottom: 16px;
        opacity: 0.9;
    }
    
    .help-phone {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 1.25rem;
        font-weight: 700;
        text-decoration: none;
        color: white;
    }
    
    .help-phone:hover {
        opacity: 0.9;
    }
    
    /* Responsive */
    @media (max-width: 1024px) {
        .content-container {
            grid-template-columns: 1fr;
        }
        
        .sidebar {
            position: static;
        }
        
        .why-choose-grid,
        .addon-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .service-title {
            font-size: 2rem;
        }
        
        .hero-stats {
            flex-direction: column;
            gap: 16px;
        }
        
        .content-section {
            padding: 24px;
        }
    }
</style>

<!-- Hero Section -->
<section class="service-hero">
    <div class="hero-container">
        <div class="hero-breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="services.php">Services</a>
            <i class="fas fa-chevron-right"></i>
            <span><?php echo htmlspecialchars($service['title']); ?></span>
        </div>
        
        <div class="service-category">
            <i class="fas fa-briefcase"></i>
            <?php echo htmlspecialchars($service['sub_category_name'] ?? 'Loan Services'); ?>
        </div>
        
        <h1 class="service-title"><?php echo htmlspecialchars($service['title']); ?></h1>
        
        <div class="service-description">
            <?php echo nl2br(htmlspecialchars($service['short_description'] ?? $service['long_description'] ?? '')); ?>
        </div>
        
        <div class="hero-stats">
            <?php 
            // Display overview stats
            if (!empty($overview)) {
                foreach ($overview as $index => $item) {
                    $overviewData = parseOverviewData($item);
                    if (!empty($overviewData)) {
                        foreach ($overviewData as $key => $value) {
                            ?>
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-<?php echo $index === 0 ? 'money-bill-wave' : ($index === 1 ? 'percentage' : 'clock'); ?>"></i>
                                </div>
                                <div class="stat-content">
                                    <h4><?php echo htmlspecialchars($value); ?></h4>
                                    <p><?php echo htmlspecialchars($key); ?></p>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }
            } else {
                // Default stats if no overview data
                ?>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-content">
                        <h4>Up to ₹25 Lakhs</h4>
                        <p>Loan Amount</p>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-content">
                        <h4>10.5% p.a.</h4>
                        <p>Starting Interest</p>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h4>Quick Approval</h4>
                        <p>Fast Processing</p>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="service-content">
    <div class="content-container">
        <!-- Left Column -->
        <div class="main-content">
            <?php if (!empty($documents)): ?>
            <!-- Requirements Section -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h2>Required Documents</h2>
                </div>
                <div class="requirements-list">
                    <?php foreach ($documents as $doc): ?>
                    <div class="list-item">
                        <div class="list-item-icon">
                            <i class="fas <?php echo getDocumentIcon($doc['doc_name']); ?>"></i>
                        </div>
                        <div class="list-item-content">
                            <h4><?php echo htmlspecialchars($doc['doc_name']); ?></h4>
                            <?php if ($doc['disclaimer']): ?>
                            <p><?php echo htmlspecialchars($doc['disclaimer']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($features)): ?>
            <!-- Features/Deliverables Section -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h2>Key Features</h2>
                </div>
                <div class="deliverables-list">
                    <?php foreach ($features as $feature): ?>
                    <div class="list-item">
                        <div class="list-item-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="list-item-content">
                            <h4><?php echo htmlspecialchars($feature['title']); ?></h4>
                            <?php if ($feature['description']): ?>
                            <p><?php echo htmlspecialchars($feature['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($eligibility)): ?>
            <!-- Eligibility Criteria / Process Flow Section -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2>Eligibility Criteria</h2>
                </div>
                <div class="process-timeline">
                    <?php foreach ($eligibility as $index => $criteria): ?>
                    <div class="process-step">
                        <div class="process-number"><?php echo $index + 1; ?></div>
                        <div class="process-content">
                            <h4><?php echo htmlspecialchars($criteria['criteria_key']); ?></h4>
                            <p><?php echo htmlspecialchars($criteria['criteria_value']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($fees)): ?>
            <!-- Fees and Charges Section -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <h2>Fees & Charges</h2>
                </div>
                <table class="timeline-table">
                    <thead>
                        <tr>
                            <th>Fee Type</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fees as $fee): ?>
                        <tr>
                            <td class="timeline-phase"><?php echo htmlspecialchars($fee['fee_key']); ?></td>
                            <td class="timeline-duration"><?php echo htmlspecialchars($fee['fee_value']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($repayment)): ?>
            <!-- Loan Repayment Section -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h2>Repayment Options</h2>
                </div>
                <div class="addon-grid">
                    <?php foreach ($repayment as $option): ?>
                    <div class="addon-card">
                        <h4><i class="fas fa-hand-holding-usd"></i> <?php echo htmlspecialchars($option['title']); ?></h4>
                        <?php if ($option['description']): ?>
                        <p><?php echo htmlspecialchars($option['description']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($banks)): ?>
            <!-- Partner Banks / Add-on Services -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <h2>Partner Banks & Add-ons</h2>
                </div>
                <div class="addon-grid">
                    <?php foreach ($banks as $bank): ?>
                    <div class="addon-card">
                        <h4><i class="fas fa-plus-circle"></i> <?php echo htmlspecialchars($bank['bank_key']); ?></h4>
                        <p><?php echo htmlspecialchars($bank['bank_value']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($why_choose)): ?>
            <!-- Why Choose Us Section -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h2>Why Choose Us</h2>
                </div>
                <div class="why-choose-grid">
                    <?php foreach ($why_choose as $reason): ?>
                    <div class="why-choose-item">
                        <h4>
                            <?php if ($reason['image']): ?>
                                <img src="<?php echo htmlspecialchars($reason['image']); ?>" alt="">
                            <?php else: ?>
                                <i class="fas fa-check-circle"></i>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($reason['title']); ?>
                        </h4>
                        <?php if ($reason['description']): ?>
                        <p><?php echo htmlspecialchars($reason['description']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($service['long_description'])): ?>
            <!-- Detailed Description Section -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h2>About This Service</h2>
                </div>
                <div class="section-content">
                    <?php echo nl2br(htmlspecialchars($service['long_description'])); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Right Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-card">
                <h3>Service Highlights</h3>
                <div class="sidebar-features">
                    <?php
                    // Display first 5 features as highlights
                    $highlightFeatures = array_slice($features, 0, 5);
                    foreach ($highlightFeatures as $feature):
                    ?>
                    <div class="sidebar-feature">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo htmlspecialchars($feature['title']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="apply-button" onclick="window.location.href='apply.php?service_id=<?php echo $service_id; ?>'">
                    <i class="fas fa-paper-plane"></i>
                    Apply Now
                </button>
                <button class="contact-button" onclick="window.location.href='contact.php'">
                    <i class="fas fa-phone"></i>
                    Contact Us
                </button>
            </div>
            
            <div class="sidebar-card help-box">
                <h4>Need Help?</h4>
                <p>Our loan experts are ready to assist you</p>
                <a href="tel:+919569408620" class="help-phone">
                    <i class="fas fa-phone-alt"></i>
                    +91 95694 08620
                </a>
            </div>
        </aside>
    </div>
</section>

<script>
    function toggleFAQ(element) {
        const faqItem = element.parentElement;
        const allFAQs = document.querySelectorAll('.faq-item');
        
        // Close all other FAQs
        allFAQs.forEach(item => {
            if (item !== faqItem) {
                item.classList.remove('active');
            }
        });
        
        // Toggle current FAQ
        faqItem.classList.toggle('active');
    }
</script>

<?php
// Include footer
require_once 'includes/footer.php';
?>