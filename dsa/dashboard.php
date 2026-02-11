<?php
include 'header.php';
dsaRequireAccess($conn, 'dsa_dashboard_view');
include 'topbar.php';
include 'sidebar.php';

$dsa_id = (int)($_SESSION['dsa_id'] ?? 0);
$dsa_name = $_SESSION['dsa_name'] ?? 'DSA Agent';

$stats = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'disbursed' => 0];
if ($dsa_id > 0) {
    $res = mysqli_query($conn, "SELECT COUNT(*) AS total, SUM(status='pending') AS pending, SUM(status='approved') AS approved, SUM(status='rejected') AS rejected, SUM(status='disbursed') AS disbursed FROM loan_applications WHERE dsa_id = $dsa_id");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        if ($row) {
            $stats = $row;
        }
    }
}

$today_count = 0;
$todayRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM loan_applications WHERE dsa_id = $dsa_id AND DATE(created_at) = CURDATE()");
if ($todayRes) {
    $today_count = (int)(mysqli_fetch_assoc($todayRes)['total'] ?? 0);
}

$week_count = 0;
$weekRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM loan_applications WHERE dsa_id = $dsa_id AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
if ($weekRes) {
    $week_count = (int)(mysqli_fetch_assoc($weekRes)['total'] ?? 0);
}

$approved_value = (int)($stats['approved'] ?? 0);
$total_value = (int)($stats['total'] ?? 0);
$conversion_rate = $total_value > 0 ? round(($approved_value * 100) / $total_value, 1) : 0;

$trendLabels = [];
$trendValues = [];
for ($i = 6; $i >= 0; $i--) {
    $d = new DateTime();
    $d->modify("-$i days");
    $trendLabels[] = $d->format('M d');
    $trendValues[$d->format('Y-m-d')] = 0;
}
$trendRes = mysqli_query($conn, "SELECT DATE(created_at) AS d, COUNT(*) AS total
                                 FROM loan_applications
                                 WHERE dsa_id = $dsa_id
                                   AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                                 GROUP BY DATE(created_at)");
if ($trendRes) {
    while ($r = mysqli_fetch_assoc($trendRes)) {
        $k = (string)($r['d'] ?? '');
        if (isset($trendValues[$k])) {
            $trendValues[$k] = (int)($r['total'] ?? 0);
        }
    }
}
$trendData = array_values($trendValues);

$recentLeads = mysqli_query($conn, "SELECT la.id, la.requested_amount, la.status, la.created_at, c.full_name, s.service_name
                                    FROM loan_applications la
                                    INNER JOIN customers c ON c.id = la.customer_id
                                    INNER JOIN services s ON s.id = la.service_id
                                    WHERE la.dsa_id = $dsa_id
                                    ORDER BY la.id DESC
                                    LIMIT 5");
?>
<style>
    :root {
        --slate-950: #020617;
        --slate-900: #0f172a;
        --slate-800: #1e293b;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
        --indigo-600: #4f46e5;
    }
    .content-page { background-color: #f8fafc; padding-bottom: 60px; }
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
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }
    .stat-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: 18px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.25s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        border-color: var(--indigo-600);
        box-shadow: 0 14px 20px -8px rgba(0,0,0,0.1);
    }
    .stat-card .label {
        font-size: 0.72rem;
        text-transform: uppercase;
        color: var(--slate-600);
        font-weight: 700;
        letter-spacing: 0.06em;
        display: block;
        margin-bottom: 8px;
    }
    .stat-card .value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--slate-900);
        line-height: 1.1;
    }
    .chart-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: 20px;
        padding: 20px;
    }
    .chart-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--slate-900);
        margin-bottom: 16px;
    }
    .table-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: 20px;
        overflow: hidden;
    }
    .table-card .table thead th {
        background: #f8fafc;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--slate-600);
        letter-spacing: 0.05em;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="greeting-header d-flex justify-content-between align-items-center">
                <div>
                    <h1>Welcome, <?= htmlspecialchars($dsa_name) ?></h1>
                    <p class="mb-0 text-muted">Interactive overview of your leads and performance.</p>
                </div>
                <?php if (dsaHasAccess($conn, 'dsa_lead_create')): ?>
                    <a href="add-lead.php" class="btn btn-dark">Add New Lead</a>
                <?php endif; ?>
            </div>

            <div class="dashboard-grid mb-4">
                <div class="stat-card"><span class="label">Total Leads</span><span class="value"><?= (int)($stats['total'] ?? 0) ?></span></div>
                <div class="stat-card"><span class="label">Pending</span><span class="value"><?= (int)($stats['pending'] ?? 0) ?></span></div>
                <div class="stat-card"><span class="label">Approved</span><span class="value"><?= (int)($stats['approved'] ?? 0) ?></span></div>
                <div class="stat-card"><span class="label">Rejected</span><span class="value"><?= (int)($stats['rejected'] ?? 0) ?></span></div>
                <div class="stat-card"><span class="label">Disbursed</span><span class="value"><?= (int)($stats['disbursed'] ?? 0) ?></span></div>
                <div class="stat-card"><span class="label">Today's Leads</span><span class="value"><?= $today_count ?></span></div>
                <div class="stat-card"><span class="label">Last 7 Days</span><span class="value"><?= $week_count ?></span></div>
                <div class="stat-card"><span class="label">Approval Rate</span><span class="value"><?= $conversion_rate ?>%</span></div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="chart-card">
                        <div class="chart-title">Lead Trend (Last 7 Days)</div>
                        <canvas id="leadTrendChart" height="95"></canvas>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="chart-card">
                        <div class="chart-title">Lead Status Mix</div>
                        <canvas id="leadStatusChart" height="220"></canvas>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-white">
                    <h5 class="mb-0 fw-bold">Recent Leads</h5>
                    <a href="my-applications.php" class="small">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="ps-3">Lead</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentLeads && mysqli_num_rows($recentLeads) > 0): while ($lead = mysqli_fetch_assoc($recentLeads)): ?>
                                <tr>
                                    <td class="ps-3"><?= htmlspecialchars((string)$lead['full_name']) ?></td>
                                    <td><?= htmlspecialchars((string)$lead['service_name']) ?></td>
                                    <td>Rs <?= format_inr((float)$lead['requested_amount'], 2) ?></td>
                                    <td><span class="badge bg-secondary-subtle text-dark"><?= htmlspecialchars(ucfirst((string)$lead['status'])) ?></span></td>
                                    <td><?= date('d M, Y', strtotime((string)$lead['created_at'])) ?></td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">No leads found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
 </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const trendCtx = document.getElementById('leadTrendChart').getContext('2d');
const trendGradient = trendCtx.createLinearGradient(0, 0, 0, 260);
trendGradient.addColorStop(0, 'rgba(79, 70, 229, 0.25)');
trendGradient.addColorStop(1, 'rgba(79, 70, 229, 0.02)');

new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($trendLabels) ?>,
        datasets: [{
            label: 'Leads',
            data: <?= json_encode($trendData) ?>,
            borderColor: '#4f46e5',
            borderWidth: 3,
            backgroundColor: trendGradient,
            tension: 0.35,
            fill: true,
            pointRadius: 4,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#4f46e5',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: { grid: { display: false } },
            y: { grid: { color: '#e2e8f0' }, beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});

new Chart(document.getElementById('leadStatusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Approved', 'Rejected', 'Disbursed'],
        datasets: [{
            data: [
                <?= (int)($stats['pending'] ?? 0) ?>,
                <?= (int)($stats['approved'] ?? 0) ?>,
                <?= (int)($stats['rejected'] ?? 0) ?>,
                <?= (int)($stats['disbursed'] ?? 0) ?>
            ],
            backgroundColor: ['#f59e0b', '#10b981', '#ef4444', '#3b82f6'],
            borderWidth: 0
        }]
    },
    options: {
        cutout: '68%',
        plugins: {
            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 18 } }
        }
    }
});
</script>

<?php include 'footer.php'; ?>
