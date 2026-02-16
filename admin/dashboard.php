<?php
include 'db/config.php';

/* --- LOGIC REMAINS IDENTICAL --- */
function getCount($conn, $table, $where = '') {
    $sql = "SELECT COUNT(*) AS total FROM $table";
    if (!empty($where)) { $sql .= " WHERE $where"; }
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return 0;
    }
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

$categoryCount    = getCount($conn, 'service_categories');
$subCategoryCount = getCount($conn, 'services_subcategories');
$serviceCount     = getCount($conn, 'services'); 
$enquiryCount     = getCount($conn, 'enquiries');
$customerCount    = getCount($conn,'customers' );
$departmentCount  = getCount ($conn,'departments');
$staffCount       = getCount($conn, 'staff');
$dsaCount         = getCount($conn, 'dsa');
$pendingDsaRequests = getCount($conn, 'dsa_requests', "status='pending'");
$loanCount        = getCount($conn, 'loan_applications');
$teamCount = getCount($conn, 'team_members');

// Status specific counts
$loanPending = getCount($conn, 'loan_applications', "status='pending'");
$loanApproved = getCount($conn, 'loan_applications', "status='approved'");
$loanRejected = getCount($conn, 'loan_applications', "status='rejected'");
$loanDisbursed = getCount($conn, 'loan_applications', "status='disbursed'");

$enquiryNew = getCount($conn, 'enquiries', "status='new'");
$enquiryAssigned = getCount($conn, 'enquiries', "status='assigned'");
$enquiryConversation = getCount($conn, 'enquiries', "status='conversation'");
$enquiryConverted = getCount($conn, 'enquiries', "status='converted'");
$enquiryClosed = getCount($conn, 'enquiries', "status='closed'");

function dailyCounts($conn, $table, $dateField, $where = '') {
    $labels = []; $data = [];
    for ($i = 6; $i >= 0; $i--) {
        $d = new DateTime();
        $d->modify("-$i days");
        $key = $d->format('Y-m-d');
        $labels[] = $d->format('M d');
        $data[$key] = 0;
    }
    $whereSql = $where ? "WHERE $where" : "";
    $res = mysqli_query($conn, "SELECT DATE($dateField) AS d, COUNT(*) AS total FROM $table $whereSql GROUP BY DATE($dateField)");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            if (isset($data[$row['d']])) { $data[$row['d']] = (int)$row['total']; }
        }
    }
    return [$labels, array_values($data)];
}

[$trendLabels, $trendCustomers] = dailyCounts($conn, 'customers', 'created_at');
[$_t1, $trendLoans] = dailyCounts($conn, 'loan_applications', 'created_at');
[$_t2, $trendEnquiries] = dailyCounts($conn, 'enquiries', 'created_at');
$adminName = $_SESSION['admin_name'] ?? 'Admin';

$portfolioBase = max(1, $customerCount + $loanCount + $enquiryCount + $staffCount + $dsaCount + $teamCount);
$approvalRate = $loanCount > 0 ? (int) round(($loanApproved / $loanCount) * 100) : 0;
$enquiryConversionRate = $enquiryCount > 0 ? (int) round(($enquiryConverted / $enquiryCount) * 100) : 0;
$kpis = [
    [
        'label' => 'Customers',
        'val' => $customerCount,
        'link' => 'customers.php',
        'icon' => 'ri-user-heart-line',
        'tone' => 'sky',
        'meta' => 'Registered borrowers',
        'badge' => 'Portfolio',
        'pct' => (int) round(($customerCount / $portfolioBase) * 100),
    ],
    [
        'label' => 'Loan Applications',
        'val' => $loanCount,
        'link' => 'loan_applications.php',
        'icon' => 'ri-bank-card-2-line',
        'tone' => 'indigo',
        'meta' => 'Pipeline volume',
        'badge' => 'Core KPI',
        'pct' => (int) round(($loanCount / $portfolioBase) * 100),
    ],
    [
        'label' => 'Enquiries',
        'val' => $enquiryCount,
        'link' => 'enquiries.php',
        'icon' => 'ri-question-answer-line',
        'tone' => 'amber',
        'meta' => 'Lead interactions',
        'badge' => 'Lead Flow',
        'pct' => (int) round(($enquiryCount / $portfolioBase) * 100),
    ],
    [
        'label' => 'Staff Members',
        'val' => $staffCount,
        'link' => 'staff_list.php',
        'icon' => 'ri-user-settings-line',
        'tone' => 'teal',
        'meta' => 'Operations team',
        'badge' => 'Workforce',
        'pct' => (int) round(($staffCount / $portfolioBase) * 100),
    ],
    [
        'label' => 'DSA Partners',
        'val' => $dsaCount,
        'link' => 'dsa_list.php',
        'icon' => 'ri-user-star-line',
        'tone' => 'rose',
        'meta' => $pendingDsaRequests . ' pending requests',
        'badge' => 'Partners',
        'pct' => (int) round(($dsaCount / $portfolioBase) * 100),
    ],
    [
        'label' => 'Loan Products',
        'val' => $serviceCount,
        'link' => 'services.php',
        'icon' => 'ri-customer-service-2-line',
        'tone' => 'violet',
        'meta' => $categoryCount . ' active categories',
        'badge' => 'Products',
        'pct' => (int) round(($serviceCount / max(1, $serviceCount + $categoryCount)) * 100),
    ],
];

include 'header.php';
include 'topbar.php';
include 'sidebar.php';
?>

<style>
    :root {
        --ink-950: #101323;
        --ink-900: #171a2e;
        --ink-700: #2a2f52;
        --ink-500: #636a91;
        --ink-300: #d8dcf2;
        --ink-100: #f5f6ff;
        --bg-1: #eef2ff;
        --bg-2: #fff7ed;
        --bg-3: #f0fdf4;
        --cyan: #06b6d4;
        --violet: #7c3aed;
        --rose: #e11d48;
        --amber: #f59e0b;
        --emerald: #10b981;
        --blue: #3b82f6;
    }

    .content-page {
        background:
            radial-gradient(circle at 8% 10%, rgba(124, 58, 237, 0.15), rgba(255, 255, 255, 0) 35%),
            radial-gradient(circle at 92% 8%, rgba(6, 182, 212, 0.14), rgba(255, 255, 255, 0) 35%),
            linear-gradient(180deg, #f8faff, #f4f6ff);
        padding-bottom: 60px;
    }

    .greeting-header {
        padding: 30px;
        background: linear-gradient(140deg, #ffffff, #f7f8ff);
        margin-bottom: 28px;
        border-radius: 24px;
        border: 1px solid #e5e7ff;
        box-shadow: 0 14px 36px rgba(36, 41, 84, 0.12);
        animation: fadeInUp 0.55s ease both;
    }

    .greeting-header h1 {
        font-size: 2rem;
        font-weight: 800;
        color: var(--ink-900);
        letter-spacing: -0.02em;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 18px;
    }

    .dashboard-grid .stat-card-link {
        grid-column: span 4;
    }

    .dashboard-grid .stat-card-link:nth-child(1),
    .dashboard-grid .stat-card-link:nth-child(2) {
        grid-column: span 6;
    }

    @media (max-width: 1199.98px) {
        .dashboard-grid .stat-card-link,
        .dashboard-grid .stat-card-link:nth-child(1),
        .dashboard-grid .stat-card-link:nth-child(2) {
            grid-column: span 6;
        }
    }

    @media (max-width: 767.98px) {
        .dashboard-grid .stat-card-link,
        .dashboard-grid .stat-card-link:nth-child(1),
        .dashboard-grid .stat-card-link:nth-child(2) {
            grid-column: span 12;
        }
    }

    .greeting-strips {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
    }

    .greeting-strip {
        background: white;
        color: var(--ink-700);
        border: 1px solid #e5e7ff;
        border-radius: 999px;
        padding: 7px 13px;
        font-size: 0.75rem;
        font-weight: 600;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .greeting-strip:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 18px rgba(40, 50, 110, 0.12);
    }

    .greeting-strip.warning {
        background: #fff7ed;
        border-color: #fed7aa;
        color: #9a3412;
    }

    .greeting-strip.success {
        background: #f0fdf4;
        border-color: #bbf7d0;
        color: #166534;
    }

    .spotlight-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
        margin: 0 0 22px;
    }

    .spotlight-card {
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 16px 28px rgba(32, 38, 84, 0.18);
        position: relative;
        overflow: hidden;
        transition: transform 0.35s ease, box-shadow 0.35s ease;
        animation: fadeInUp 0.65s ease both;
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    .spotlight-card:hover {
        transform: translateY(-8px) rotate(-0.3deg);
        box-shadow: 0 24px 34px rgba(32, 38, 84, 0.22);
    }

    .spotlight-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: -120%;
        width: 75%;
        height: 100%;
        background: linear-gradient(100deg, transparent, rgba(255, 255, 255, 0.28), transparent);
        transform: skewX(-18deg);
        animation: sweep 4s ease-in-out infinite;
    }

    .spotlight-card::after {
        content: "";
        position: absolute;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        top: -80px;
        right: -50px;
        opacity: 0.35;
    }

    .spotlight-primary {
        background: linear-gradient(145deg, #4f46e5, #7c3aed);
    }

    .spotlight-primary::after {
        background: #22d3ee;
    }

    .spotlight-secondary {
        background: linear-gradient(145deg, #0ea5e9, #14b8a6);
    }

    .spotlight-secondary::after {
        background: #facc15;
    }

    .spotlight-title,
    .spotlight-value,
    .spotlight-meta {
        color: #f8fafc;
    }

    .spotlight-title {
        font-size: 0.76rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        font-weight: 700;
        opacity: 0.92;
    }

    .spotlight-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.05;
        margin: 8px 0 10px;
    }

    .spotlight-meta {
        font-size: 0.82rem;
        font-weight: 500;
        opacity: 0.92;
    }

    .stat-card-link {
        text-decoration: none;
        outline: none;
        display: block;
    }

    .stat-card {
        border-radius: 22px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        animation: fadeInUp 0.6s ease both;
        border: 1px solid #e3e6ff;
        box-shadow: 0 12px 24px rgba(45, 56, 128, 0.12);
    }

    .stat-card.sky {
        background: linear-gradient(160deg, #ecfeff, #eff6ff 55%, #f8fafc);
    }

    .stat-card.indigo {
        background: linear-gradient(160deg, #eef2ff, #f5f3ff 55%, #f8fafc);
    }

    .stat-card.amber {
        background: linear-gradient(160deg, #fffbeb, #fff7ed 55%, #fefce8);
    }

    .stat-card.teal {
        background: linear-gradient(160deg, #ecfeff, #ecfdf5 55%, #f0fdfa);
    }

    .stat-card.rose {
        background: linear-gradient(160deg, #fff1f2, #fdf2f8 55%, #f8fafc);
    }

    .stat-card.violet {
        background: linear-gradient(160deg, #f5f3ff, #eef2ff 55%, #f8fafc);
    }

    .stat-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(125deg, rgba(255, 255, 255, 0.38), transparent 45%, rgba(99, 102, 241, 0.09));
        pointer-events: none;
    }

    .stat-card::after {
        content: "";
        position: absolute;
        width: 128px;
        height: 128px;
        border-radius: 50%;
        right: -28px;
        bottom: -46px;
        background: radial-gradient(circle, rgba(79, 70, 229, 0.16), rgba(79, 70, 229, 0));
        animation: pulseOrb 3.5s ease-in-out infinite;
        pointer-events: none;
    }

    .stat-card:hover {
        transform: translateY(-9px) scale(1.01);
        box-shadow: 0 24px 34px rgba(45, 56, 128, 0.18);
        border-color: #c7d2fe;
    }

    .stat-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        border: 1px solid transparent;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .stat-icon i {
        transition: transform 0.35s ease;
    }

    .stat-card:hover .stat-icon {
        transform: translateY(-2px) scale(1.06);
        box-shadow: 0 9px 16px rgba(53, 64, 145, 0.18);
    }

    .stat-card:hover .stat-icon i {
        transform: rotate(-7deg) scale(1.05);
    }

    .stat-icon.sky { background: #cffafe; color: #0e7490; border-color: #67e8f9; }
    .stat-icon.indigo { background: #e0e7ff; color: #4338ca; border-color: #c7d2fe; }
    .stat-icon.amber { background: #ffedd5; color: #b45309; border-color: #fdba74; }
    .stat-icon.teal { background: #ccfbf1; color: #0f766e; border-color: #5eead4; }
    .stat-icon.rose { background: #ffe4e6; color: #be123c; border-color: #fda4af; }
    .stat-icon.violet { background: #ede9fe; color: #6d28d9; border-color: #c4b5fd; }

    .stat-share {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        border-radius: 999px;
        padding: 4px 9px;
        background: var(--ink-900);
        color: #f5f5f5;
        animation: floatChip 3s ease-in-out infinite;
    }

    .stat-card .label {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: var(--ink-500);
        display: block;
        margin-bottom: 8px;
    }

    .stat-card .value {
        font-size: 2.45rem;
        font-weight: 800;
        color: var(--ink-900);
        line-height: 1.05;
    }

    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-weight: 700;
        color: #334155;
        background: rgba(255, 255, 255, 0.75);
        border: 1px solid #dbeafe;
    }

    .stat-meta {
        margin-top: 9px;
        font-size: 0.82rem;
        color: #475569;
        font-weight: 500;
    }

    .stat-progress {
        margin-top: 14px;
        width: 100%;
        height: 8px;
        border-radius: 999px;
        background: rgba(99, 102, 241, 0.16);
        overflow: hidden;
    }

    .stat-progress > span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, var(--blue), var(--violet), var(--rose));
        width: 0;
        transition: width 1.2s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .stat-foot {
        margin-top: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.78rem;
        color: #334155;
    }

    .stat-foot strong {
        font-weight: 700;
        color: #4f46e5;
    }

    .stat-card-content {
        position: relative;
        z-index: 1;
    }

    .chart-card {
        background: linear-gradient(170deg, #ffffff, #f7f8ff);
        border: 1px solid #e3e6ff;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 12px 26px rgba(45, 56, 128, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        animation: fadeInUp 0.7s ease both;
    }

    .chart-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 18px 30px rgba(45, 56, 128, 0.16);
    }

    .chart-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--ink-900);
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .bg-indigo-soft {
        background: rgba(79, 70, 229, 0.14) !important;
    }

    .text-indigo {
        color: #4338ca !important;
    }

    .action-btn {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 600;
        border: none;
        transition: 0.3s;
        display: inline-block;
    }

    .action-btn:hover {
        background: linear-gradient(135deg, #3730a3, #6d28d9);
        box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.35);
        color: white;
        transform: translateY(-2px);
    }

    .action-btn-outline {
        border: 2px solid #c7d2fe;
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 600;
        color: #3730a3;
        transition: 0.3s;
        background: #eef2ff;
    }

    .action-btn-outline:hover {
        border-color: #6366f1;
        background: #e0e7ff;
        color: #312e81;
    }

    .dashboard-grid .stat-card-link:nth-child(1) .stat-card { animation-delay: 0.06s; }
    .dashboard-grid .stat-card-link:nth-child(2) .stat-card { animation-delay: 0.12s; }
    .dashboard-grid .stat-card-link:nth-child(3) .stat-card { animation-delay: 0.18s; }
    .dashboard-grid .stat-card-link:nth-child(4) .stat-card { animation-delay: 0.24s; }
    .dashboard-grid .stat-card-link:nth-child(5) .stat-card { animation-delay: 0.30s; }
    .dashboard-grid .stat-card-link:nth-child(6) .stat-card { animation-delay: 0.36s; }

    .card-tilt {
        transform-style: preserve-3d;
        will-change: transform;
    }

    @keyframes sweep {
        0%, 35% { left: -120%; }
        75%, 100% { left: 140%; }
    }

    @keyframes pulseOrb {
        0%, 100% { transform: scale(1); opacity: 0.45; }
        50% { transform: scale(1.25); opacity: 0.75; }
    }

    @keyframes floatChip {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-2px); }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(16px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .spotlight-card,
        .stat-card,
        .chart-card,
        .greeting-header {
            animation: none !important;
            transition: none !important;
        }
        .spotlight-card::before,
        .stat-card::after,
        .stat-share {
            animation: none !important;
        }
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="greeting-header">
                <h1>Hi, <?= htmlspecialchars($adminName) ?> <i class="ri-hand-heart-line"></i></h1>
                <p class="mb-0">Here's what's happening with your lending platform today.</p>
                <div class="greeting-strips">
                    <span class="greeting-strip">Total Departments: <?= number_format($departmentCount) ?></span>
                    <span class="greeting-strip warning">Pending DSA Requests: <?= number_format($pendingDsaRequests) ?></span>
                    <span class="greeting-strip success">Team Members: <?= number_format($teamCount) ?></span>
                </div>
            </div>

            <div class="spotlight-grid">
                <div class="spotlight-card spotlight-primary card-tilt">
                    <div class="spotlight-title">Loan Approval Rate</div>
                    <div class="spotlight-value"><?= $approvalRate ?>%</div>
                    <div class="spotlight-meta"><?= number_format($loanApproved) ?> approved from <?= number_format($loanCount) ?> total applications</div>
                </div>
                <div class="spotlight-card spotlight-secondary card-tilt">
                    <div class="spotlight-title">Enquiry Conversion</div>
                    <div class="spotlight-value"><?= $enquiryConversionRate ?>%</div>
                    <div class="spotlight-meta"><?= number_format($enquiryConverted) ?> converted from <?= number_format($enquiryCount) ?> total enquiries</div>
                </div>
            </div>

            <div class="dashboard-grid">
                <?php foreach ($kpis as $c): ?>
                <a href="<?= $c['link'] ?>" class="stat-card-link">
                    <div class="stat-card card-tilt <?= htmlspecialchars($c['tone']) ?>">
                        <div class="stat-card-content">
                            <div class="stat-card-top">
                                <span class="stat-icon <?= htmlspecialchars($c['tone']) ?>"><i class="<?= htmlspecialchars($c['icon']) ?>"></i></span>
                                <span class="stat-share"><?= (int) $c['pct'] ?>%</span>
                            </div>
                            <span class="label"><?= $c['label'] ?></span>
                            <span class="value"><?= number_format((int) $c['val']) ?></span>
                            <span class="stat-badge"><?= htmlspecialchars($c['badge']) ?></span>
                            <div class="stat-meta"><?= htmlspecialchars($c['meta']) ?></div>
                            <div class="stat-progress"><span data-progress="<?= max(8, (int) $c['pct']) ?>"></span></div>
                            <div class="stat-foot">
                                <span>Distribution</span>
                                <strong><?= (int) $c['pct'] ?>% share</strong>
                            </div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <div class="row mt-4 g-4">
                <div class="col-lg-12">
                    <div class="chart-card">
                        <div class="chart-title">
                            <span>Growth Trend</span>
                            <span class="badge bg-indigo-soft text-indigo small">Last 7 Days</span>
                        </div>
                        <canvas id="trendChart" height="80"></canvas>
                    </div>
                </div>
            </div>

            <div class="row mt-4 g-4">
                <div class="col-lg-4">
                    <div class="chart-card">
                        <div class="chart-title">Loan Pipeline</div>
                        <canvas id="loanPie" height="250"></canvas>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="chart-card">
                        <div class="chart-title">Enquiry Volume</div>
                        <canvas id="enquiryPie" height="250"></canvas>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="chart-card h-100 d-flex flex-column justify-content-center align-items-center text-center">
                        <div class="mb-4">
                            <h5 class="fw-bold">Administrative Tasks</h5>
                            <p class="text-muted small">Quickly manage system configurations.</p>
                        </div>
                        <div class="d-grid gap-2 w-100 px-3">
                            <a href="category_add.php" class="action-btn">Add New Category</a>
                            <a href="service_add.php" class="action-btn-outline">Register New Service</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const ctxTrend = document.getElementById('trendChart').getContext('2d');
const gradientBlue = ctxTrend.createLinearGradient(0, 0, 0, 400);
gradientBlue.addColorStop(0, 'rgba(79, 70, 229, 0.28)');
gradientBlue.addColorStop(1, 'rgba(79, 70, 229, 0.02)');

new Chart(ctxTrend, {
    type: 'line',
    data: {
        labels: <?= json_encode($trendLabels) ?>,
        datasets: [
            { 
                label: 'Customers', 
                data: <?= json_encode($trendCustomers) ?>, 
                borderColor: '#4f46e5', 
                borderWidth: 3,
                backgroundColor: gradientBlue,
                tension: 0.4, 
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderWidth: 2,
                pointBorderColor: '#4f46e5'
            },
            { 
                label: 'Loans', 
                data: <?= json_encode($trendLoans) ?>, 
                borderColor: '#14b8a6', 
                borderWidth: 3,
                tension: 0.4,
                pointRadius: 0
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: true, position: 'top', align: 'end' } },
        scales: {
            x: { grid: { display: false } },
            y: { border: { dash: [5, 5] }, grid: { color: '#e2e8f0' } }
        }
    }
});

// Pie Chart Options
const pieOptions = {
    cutout: '70%',
    plugins: {
        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
    }
};

new Chart(document.getElementById('loanPie'), {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Approved', 'Rejected', 'Disbursed'],
        datasets: [{
            data: [<?= $loanPending ?>, <?= $loanApproved ?>, <?= $loanRejected ?>, <?= $loanDisbursed ?>],
            backgroundColor: ['#f59e0b', '#10b981', '#ef4444', '#4f46e5'],
            borderWidth: 0
        }]
    },
    options: pieOptions
});

new Chart(document.getElementById('enquiryPie'), {
    type: 'doughnut',
    data: {
        labels: ['New', 'Assigned', 'Conversation', 'Converted', 'Closed'],
        datasets: [{
            data: [<?= $enquiryNew ?>, <?= $enquiryAssigned ?>, <?= $enquiryConversation ?>, <?= $enquiryConverted ?>, <?= $enquiryClosed ?>],
            backgroundColor: ['#6366f1', '#f59e0b', '#06b6d4', '#10b981', '#64748b'],
            borderWidth: 0
        }]
    },
    options: pieOptions
});

// Animate KPI progress bars when visible and add subtle mouse-tilt interaction.
(function () {
    var progressBars = document.querySelectorAll('.stat-progress > span[data-progress]');
    if ('IntersectionObserver' in window) {
        var barObserver = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                var bar = entry.target;
                var target = parseInt(bar.getAttribute('data-progress') || '0', 10);
                requestAnimationFrame(function () {
                    bar.style.width = Math.max(8, target) + '%';
                });
                observer.unobserve(bar);
            });
        }, { threshold: 0.45 });
        progressBars.forEach(function (bar) { barObserver.observe(bar); });
    } else {
        progressBars.forEach(function (bar) {
            var target = parseInt(bar.getAttribute('data-progress') || '0', 10);
            bar.style.width = Math.max(8, target) + '%';
        });
    }

    var tiltCards = document.querySelectorAll('.card-tilt');
    tiltCards.forEach(function (card) {
        card.addEventListener('mousemove', function (e) {
            var rect = card.getBoundingClientRect();
            var x = (e.clientX - rect.left) / rect.width;
            var y = (e.clientY - rect.top) / rect.height;
            var rotateY = (x - 0.5) * 5;
            var rotateX = (0.5 - y) * 5;
            card.style.transform = 'perspective(900px) rotateX(' + rotateX.toFixed(2) + 'deg) rotateY(' + rotateY.toFixed(2) + 'deg) translateY(-6px)';
        });
        card.addEventListener('mouseleave', function () {
            card.style.transform = '';
        });
    });
})();
</script>
<?php include 'footer.php'; ?>
