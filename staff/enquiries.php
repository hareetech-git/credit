<?php
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

$staff_id = (int)$_SESSION['staff_id'];
$can_view_all = hasAccess($conn, 'enquiry_view_all');
$can_view_assigned = hasAccess($conn, 'enquiry_view_assigned');
$can_delete = hasAccess($conn, 'enquiry_delete');
$can_change_status = hasAccess($conn, 'enquiry_status_change');

$search_query = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

$query = "SELECT e.*, s.name AS assigned_staff_name, s.email AS assigned_staff_email 
          FROM enquiries e 
          LEFT JOIN staff s ON e.assigned_staff_id = s.id
          WHERE 1=1";

if ($can_view_all) {
    // no extra filter
} elseif ($can_view_assigned) {
    $query .= " AND e.assigned_staff_id = $staff_id";
} else {
    $query .= " AND 1=0";
}

if (!empty($search_query)) {
    $search_safe = mysqli_real_escape_string($conn, $search_query);
    $query .= " AND (e.full_name LIKE '%$search_safe%' OR e.email LIKE '%$search_safe%' OR e.phone LIKE '%$search_safe%' OR e.loan_type_name LIKE '%$search_safe%')";
}

$allowed_status = ['new','assigned','conversation','converted','closed'];
if (!empty($status_filter) && in_array($status_filter, $allowed_status, true)) {
    $status_safe = mysqli_real_escape_string($conn, $status_filter);
    $query .= " AND e.status = '$status_safe'";
}

$query .= " ORDER BY e.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
    }
    .content-page { background-color: #fcfcfd; }
    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .table-modern thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: var(--slate-600);
        padding: 16px 24px;
        border: none;
    }
    .table-modern tbody td {
        padding: 16px 24px;
        font-size: 0.9rem;
        color: var(--slate-900);
        border-bottom: 1px solid var(--slate-200);
        vertical-align: middle;
    }
    .badge-soft {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid var(--slate-200);
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
    }
    .msg-box {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        color: var(--slate-600);
        font-size: 0.85rem;
    }
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
        border: 1px solid var(--slate-200);
        background: white;
        text-decoration: none;
    }
    .btn-delete-pro { color: #ef4444; }
    .btn-delete-pro:hover { background: #ef4444 !important; color: white !important; border-color: #ef4444; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Enquiries</h2>
                    <div class="text-muted small">
                        <?= $can_view_all ? 'Viewing all enquiries' : ($can_view_assigned ? 'Viewing assigned enquiries' : 'No enquiry access') ?>
                    </div>
                </div>
            </div>

            <div class="card card-modern mb-4">
                <div class="card-body p-3">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-7">
                            <label class="form-label text-muted small fw-bold">Quick Search</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email, phone or loan type..." value="<?= htmlspecialchars($search_query) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All</option>
                                <?php foreach (['new','assigned','conversation','converted','closed'] as $s): ?>
                                    <option value="<?= $s ?>" <?= ($status_filter === $s) ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button class="btn btn-sm btn-dark w-100"><i class="fas fa-filter me-1"></i> Apply</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-modern">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th width="80">ID</th>
                                    <th>Applicant</th>
                                    <th>Contact Info</th>
                                    <th>Loan Type</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Assigned</th>
                                    <th>Date</th>
                                    <th width="120" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr>
                                            <td class="text-muted fw-bold">#<?= $row['id'] ?></td>
                                            <td class="fw-semibold text-dark"><?= htmlspecialchars($row['full_name']) ?></td>
                                            <td>
                                                <div class="small fw-bold"><?= htmlspecialchars($row['phone']) ?></div>
                                                <div class="text-muted small"><?= htmlspecialchars($row['email']) ?></div>
                                            </td>
                                            <td><span class="badge-soft text-primary"><?= htmlspecialchars($row['loan_type_name'] ?: 'N/A') ?></span></td>
                                            <td>
                                                <span class="msg-box" title="<?= htmlspecialchars($row['query_message']) ?>">
                                                    <?= htmlspecialchars($row['query_message']) ?>
                                                </span>
                                            </td>
                                            <td><span class="badge-soft"><?= ucfirst($row['status'] ?? 'new') ?></span></td>
                                            <td>
                                                <div class="small fw-bold"><?= htmlspecialchars($row['assigned_staff_name'] ?: 'Unassigned') ?></div>
                                                <div class="text-muted small"><?= htmlspecialchars($row['assigned_staff_email'] ?: '') ?></div>
                                            </td>
                                            <td class="text-muted small">
                                                <?= date('M d, Y', strtotime($row['created_at'])) ?><br>
                                                <span class="text-light-emphasis"><?= date('h:i A', strtotime($row['created_at'])) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="enquiry_view.php?id=<?= $row['id'] ?>" class="btn-action" title="Open Conversation">
                                                        <i class="fas fa-comments"></i>
                                                    </a>
                                                    <?php if ($can_change_status): ?>
                                                        <a href="enquiry_email.php?id=<?= $row['id'] ?>" class="btn-action btn-mail" title="Send Email">
                                                            <i class="fas fa-envelope"></i>
                                                        </a>
                                                        <?php
                                                            $wa_phone = preg_replace('/\\D+/', '', $row['phone'] ?? '');
                                                            $wa_text = rawurlencode("Hello {$row['full_name']}, can you provide more information about your enquiry #{$row['id']}?");
                                                            $wa_link = "https://api.whatsapp.com/send/?phone={$wa_phone}&text={$wa_text}&type=phone_number&app_absent=0";
                                                        ?>
                                                        <?php if (!empty($wa_phone)): ?>
                                                            <a href="<?= $wa_link ?>" target="_blank" class="btn-action" title="WhatsApp">
                                                                <i class="fab fa-whatsapp"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                    <?php if ($can_delete): ?>
                                                    <a href="db/delete/enquiry_delete.php?id=<?= $row['id'] ?>" 
                                                       class="btn-action btn-delete-pro" 
                                                       onclick="return confirm('Delete this enquiry permanently?');"
                                                       title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <div class="mb-2"><i class="fas fa-inbox fa-2x text-light"></i></div>
                                            No enquiries found.
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
