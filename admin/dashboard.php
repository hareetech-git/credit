<?php
include 'db/config.php';

/* --- LOGIC REMAINS IDENTICAL --- */
function getCount($conn, $table, $where = '') {
    $sql = "SELECT COUNT(*) AS total FROM $table";
    if (!empty($where)) { $sql .= " WHERE $where"; }
    $result = mysqli_query($conn, $sql);
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
$loanCount        = getCount($conn, 'loan_applications');

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

include 'header.php';
include 'topbar.php';
include 'sidebar.php';
?>

<style>
    :root {
        --slate-950: #020617;
        --slate-900: #0f172a;
        --slate-800: #1e293b;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
        --blue-600: #2563eb;
        --indigo-600: #4f46e5;
        --emerald-500: #10b981;
    }

    .content-page { background-color: #f8fafc; padding-bottom: 60px; }
    
    /* Header Animation */
    .greeting-header {
        padding: 40px 0 30px;
        background: linear-gradient(to right, #ffffff, #f8fafc);
        margin-bottom: 30px;
        border-bottom: 1px solid var(--slate-200);
    }

    .greeting-header h1 {
        font-size: 2rem;
        font-weight: 800;
        color: var(--slate-950);
        letter-spacing: -0.025em;
    }

    /* Modern Grid */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
    }

    .stat-card-link { text-decoration: none; outline: none; }
    
    .stat-card {
        background: #ffffff;
        border: 1px solid var(--slate-200);
        border-radius: 20px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        border-color: var(--indigo-600);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
    }

    .stat-card .label {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: var(--slate-600);
        display: block;
        margin-bottom: 8px;
    }

    .stat-card .value {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--slate-900);
        line-height: 1;
    }

    /* Icon Accents */
    .stat-card::after {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 80px;
        height: 80px;
        background: var(--slate-100);
        border-radius: 50%;
        z-index: 0;
        opacity: 0.5;
    }

    .stat-card-content { position: relative; z-index: 1; }

    /* Chart Cards */
    .chart-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .chart-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--slate-900);
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Action Buttons */
    .action-btn {
        background: var(--slate-950);
        color: white;
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 600;
        border: none;
        transition: 0.3s;
        display: inline-block;
    }

    .action-btn:hover {
        background: var(--indigo-600);
        box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        color: white;
        transform: translateY(-2px);
    }

    .action-btn-outline {
        border: 2px solid var(--slate-200);
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 600;
        color: var(--slate-900);
        transition: 0.3s;
        background: transparent;
    }

    .action-btn-outline:hover {
        border-color: var(--slate-900);
        background: var(--slate-50);
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="greeting-header">
                <h1>Hi, <?= htmlspecialchars($adminName) ?> 👋</h1>
                <p class="mb-0">Here's what's happening with your lending platform today.</p>
            </div>

            <div class="dashboard-grid">
                <?php
                $cards = [
                    ['label' => 'Service Categories', 'val' => $categoryCount, 'link' => 'category.php'],
                    ['label' => 'Loan Products', 'val' => $serviceCount, 'link' => 'services.php'],
                    ['label' => 'Total Customers', 'val' => $customerCount, 'link' => 'customers.php'],
                    ['label' => 'Active Staff', 'val' => $staffCount, 'link' => 'staff_list.php'],
                    ['label' => 'Total Enquiries', 'val' => $enquiryCount, 'link' => 'enquiries.php'],
                    ['label' => 'Loan Applications', 'val' => $loanCount, 'link' => 'loan_applications.php'],
                ];

                foreach($cards as $c): ?>
                <a href="<?= $c['link'] ?>" class="stat-card-link">
                    <div class="stat-card">
                        <div class="stat-card-content">
                            <span class="label"><?= $c['label'] ?></span>
                            <span class="value"><?= $c['val'] ?></span>
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
gradientBlue.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
gradientBlue.addColorStop(1, 'rgba(37, 99, 235, 0)');

new Chart(ctxTrend, {
    type: 'line',
    data: {
        labels: <?= json_encode($trendLabels) ?>,
        datasets: [
            { 
                label: 'Customers', 
                data: <?= json_encode($trendCustomers) ?>, 
                borderColor: '#2563eb', 
                borderWidth: 3,
                backgroundColor: gradientBlue,
                tension: 0.4, 
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderWidth: 2
            },
            { 
                label: 'Loans', 
                data: <?= json_encode($trendLoans) ?>, 
                borderColor: '#10b981', 
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
            backgroundColor: ['#f59e0b', '#10b981', '#ef4444', '#3b82f6'],
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
</script>
<?php include 'footer.php'; ?>