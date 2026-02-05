<?php
include 'header.php';

/* ------------------------------
   Dynamic Values (DB)
--------------------------------*/
$adminName = $_SESSION['staff_name'] ?? 'Staff';
$staff_id = (int)$_SESSION['staff_id'];

function getCountStaff($conn, $table, $where = '') {
    $sql = "SELECT COUNT(*) AS total FROM $table";
    if (!empty($where)) $sql .= " WHERE $where";
    $res = mysqli_query($conn, $sql);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    return (int)($row['total'] ?? 0);
}

$loanWhere = "assigned_staff_id = $staff_id";
$loanTotal = getCountStaff($conn, 'loan_applications', $loanWhere);
$loanPending = getCountStaff($conn, 'loan_applications', "$loanWhere AND status='pending'");
$loanApproved = getCountStaff($conn, 'loan_applications', "$loanWhere AND status='approved'");
$loanRejected = getCountStaff($conn, 'loan_applications', "$loanWhere AND status='rejected'");
$loanDisbursed = getCountStaff($conn, 'loan_applications', "$loanWhere AND status='disbursed'");

if (hasAccess($conn, 'enquiry_view_all')) {
    $enquiryWhere = "1=1";
} elseif (hasAccess($conn, 'enquiry_view_assigned')) {
    $enquiryWhere = "assigned_staff_id = $staff_id";
} else {
    $enquiryWhere = "1=0";
}
$enquiryCount = getCountStaff($conn, 'enquiries', $enquiryWhere);
$enquiryNew = getCountStaff($conn, 'enquiries', "$enquiryWhere AND status='new'");
$enquiryAssigned = getCountStaff($conn, 'enquiries', "$enquiryWhere AND status='assigned'");
$enquiryConversation = getCountStaff($conn, 'enquiries', "$enquiryWhere AND status='conversation'");
$enquiryConverted = getCountStaff($conn, 'enquiries', "$enquiryWhere AND status='converted'");
$enquiryClosed = getCountStaff($conn, 'enquiries', "$enquiryWhere AND status='closed'");

function dailyCountsStaff($conn, $table, $dateField, $where = '') {
    $labels = [];
    $data = [];
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
            if (isset($data[$row['d']])) {
                $data[$row['d']] = (int)$row['total'];
            }
        }
    }
    return [$labels, array_values($data)];
}

[$trendLabels, $trendLoans] = dailyCountsStaff($conn, 'loan_applications', 'created_at', $loanWhere);
[$_t1, $trendEnquiries] = dailyCountsStaff($conn, 'enquiries', 'created_at', $enquiryWhere);
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

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }

    .chart-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: 16px;
        padding: 20px;
        height: 100%;
    }

    .chart-title {
        font-weight: 700;
        color: var(--slate-900);
        margin-bottom: 12px;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px;
    }

    .quick-action-card {
        border: 1px solid var(--slate-200);
        border-radius: 14px;
        padding: 16px;
        background: #fff;
        text-decoration: none;
        color: inherit;
        display: flex;
        gap: 12px;
        align-items: center;
        transition: all 0.2s ease;
    }

    .quick-action-card:hover {
        border-color: var(--slate-900);
        box-shadow: 0 12px 20px -10px rgba(15, 23, 42, 0.25);
        transform: translateY(-2px);
    }

    .quick-action-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: var(--slate-900);
        font-size: 1rem;
        flex-shrink: 0;
    }

    .quick-action-meta {
        font-size: 0.78rem;
        color: var(--slate-600);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 600;
    }

    .quick-action-title {
        font-weight: 700;
        color: var(--slate-900);
        margin-bottom: 2px;
    }

    .quick-action-value {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--slate-900);
    }

    .quick-action-sub {
        font-size: 0.85rem;
        color: var(--slate-600);
    }</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="greeting-header">
                <h1>Welcome, <?= htmlspecialchars($adminName) ?></h1>
                <p>System performance and management overview.</p>
            </div>

            <div class="dashboard-grid">

                <a href="loan_applications.php" class="stat-card-link">
                    <div class="stat-card">
                        <span class="label">Assigned Loans</span>
                        <span class="value"><?= $loanTotal ?></span>
                        <div class="footer-link">View loans →</div>
                    </div>
                </a>

                <a href="loan_applications.php?status=new" class="stat-card-link">
                    <div class="stat-card">
                        <span class="label">New Applications</span>
                        <span class="value"><?= $loanPending ?></span>
                        <div class="footer-link">Awaiting decision</div>
                    </div>
                </a>

                <a href="loan_applications.php?status=approved" class="stat-card-link">
                    <div class="stat-card">
                        <span class="label">Approved Loans</span>
                        <span class="value"><?= $loanApproved ?></span>
                        <div class="footer-link">Approved by you</div>
                    </div>
                </a>

                <a href="loan_applications.php?status=rejected" class="stat-card-link">
                    <div class="stat-card">
                        <span class="label">Rejected Loans</span>
                        <span class="value"><?= $loanRejected ?></span>
                        <div class="footer-link">Needs follow-up</div>
                    </div>
                </a>

                <a href="enquiries.php" class="stat-card-link">
                    <div class="stat-card">
                        <span class="label">Enquiries</span>
                        <span class="value"><?= $enquiryCount ?></span>
                        <div class="footer-link">View enquiries →</div>
                    </div>
                </a>

                <a href="enquiries.php?status=new" class="stat-card-link">
                    <div class="stat-card">
                        <span class="label">New Enquiries</span>
                        <span class="value"><?= $enquiryNew ?></span>
                        <div class="footer-link">Needs response</div>
                    </div>
                </a>

                <a href="enquiries.php?status=conversation" class="stat-card-link">
                    <div class="stat-card">
                        <span class="label">In Conversation</span>
                        <span class="value"><?= $enquiryConversation ?></span>
                        <div class="footer-link">Active chats</div>
                    </div>
                </a>

                <a href="enquiries.php?status=resolved" class="stat-card-link">
                    <div class="stat-card">
                        <span class="label">Closed/Converted</span>
                        <span class="value"><?= ($enquiryClosed + $enquiryConverted) ?></span>
                        <div class="footer-link">Resolved</div>
                    </div>
                </a>

            </div>

            <div class="row mt-4 g-4">
                <div class="col-lg-8">
                    <div class="chart-card">
                        <div class="chart-title">7-Day Activity</div>
                        <canvas id="trendChart" height="110"></canvas>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="chart-card">
                        <div class="chart-title">Loan Status</div>
                        <canvas id="loanPie" height="220"></canvas>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="chart-card">
                        <div class="chart-title">Enquiry Status</div>
                        <canvas id="enquiryPie" height="220"></canvas>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="p-4 bg-white rounded-4 border h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Quick Actions</h5>
                            <span class="text-muted small">Shortcuts to your most used work queues</span>
                        </div>
                        <div class="quick-actions">
                            <a href="loan_applications.php" class="quick-action-card">
                                <span class="quick-action-icon"><i class="fas fa-clipboard-list"></i></span>
                                <div>
                                    <div class="quick-action-meta">Loans</div>
                                    <div class="quick-action-title">Assigned loans</div>
                                    <div class="quick-action-sub">Total assigned</div>
                                    <div class="quick-action-value"><?= $loanTotal ?></div>
                                </div>
                            </a>
                            <a href="loan_applications.php?status=new" class="quick-action-card">
                                <span class="quick-action-icon"><i class="fas fa-clock"></i></span>
                                <div>
                                    <div class="quick-action-meta">Applications</div>
                                    <div class="quick-action-title">Pending decisions</div>
                                    <div class="quick-action-sub">Need review</div>
                                    <div class="quick-action-value"><?= $loanPending ?></div>
                                </div>
                            </a>
                            <a href="enquiries.php?status=new" class="quick-action-card">
                                <span class="quick-action-icon"><i class="fas fa-headset"></i></span>
                                <div>
                                    <div class="quick-action-meta">Enquiries</div>
                                    <div class="quick-action-title">New enquiries</div>
                                    <div class="quick-action-sub">Fresh requests</div>
                                    <div class="quick-action-value"><?= $enquiryNew ?></div>
                                </div>
                            </a>
                            <a href="enquiries.php?status=conversation" class="quick-action-card">
                                <span class="quick-action-icon"><i class="fas fa-comments"></i></span>
                                <div>
                                    <div class="quick-action-meta">Conversations</div>
                                    <div class="quick-action-title">Active chats</div>
                                    <div class="quick-action-sub">In progress</div>
                                    <div class="quick-action-value"><?= $enquiryConversation ?></div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const trendLabels = <?= json_encode($trendLabels) ?>;
const trendLoans = <?= json_encode($trendLoans) ?>;
const trendEnquiries = <?= json_encode($trendEnquiries) ?>;

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trendLabels,
        datasets: [
            { label: 'Loans', data: trendLoans, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.15)', tension: 0.35, fill: true },
            { label: 'Enquiries', data: trendEnquiries, borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,0.15)', tension: 0.35, fill: true }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
});

new Chart(document.getElementById('loanPie'), {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Approved', 'Rejected', 'Disbursed'],
        datasets: [{
            data: [<?= $loanPending ?>, <?= $loanApproved ?>, <?= $loanRejected ?>, <?= $loanDisbursed ?>],
            backgroundColor: ['#fbbf24', '#34d399', '#f87171', '#60a5fa']
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('enquiryPie'), {
    type: 'doughnut',
    data: {
        labels: ['New', 'Assigned', 'Conversation', 'Converted', 'Closed'],
        datasets: [{
            data: [<?= $enquiryNew ?>, <?= $enquiryAssigned ?>, <?= $enquiryConversation ?>, <?= $enquiryConverted ?>, <?= $enquiryClosed ?>],
            backgroundColor: ['#a5b4fc', '#fbbf24', '#38bdf8', '#34d399', '#94a3b8']
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
</script>
<?php include 'footer.php'; ?>
