<?php
include 'db/config.php';
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

if (!hasAccess($conn, 'loan_manual_assign')) {
    header('Location: dashboard.php?err=Access denied');
    exit();
}
$can_assign_others = hasAccess($conn, 'loan_manual_assign_others');
$current_staff_id = (int)($_SESSION['staff_id'] ?? 0);

$search = mysqli_real_escape_string($conn, trim((string)($_GET['search'] ?? '')));
$status = mysqli_real_escape_string($conn, trim((string)($_GET['status'] ?? '')));

$staff_list = [];
if ($can_assign_others) {
    $staff_res = mysqli_query($conn, "
    SELECT DISTINCT s.id, s.name, s.email
    FROM staff s
    WHERE s.status='active'
      AND (
        EXISTS (
            SELECT 1
            FROM role_permissions rp
            INNER JOIN permissions p ON p.id = rp.permission_id
            WHERE rp.role_id = s.role_id AND p.perm_key = 'loan_process'
        )
        OR EXISTS (
            SELECT 1
            FROM staff_permissions sp
            INNER JOIN permissions p2 ON p2.id = sp.permission_id
            WHERE sp.staff_id = s.id AND p2.perm_key = 'loan_process'
        )
      )
    ORDER BY s.name
");
    if ($staff_res) {
        while ($row = mysqli_fetch_assoc($staff_res)) {
            $staff_list[] = $row;
        }
    }
} else {
    $selfRes = mysqli_query($conn, "SELECT id, name, email FROM staff WHERE id = $current_staff_id LIMIT 1");
    if ($selfRes && mysqli_num_rows($selfRes) > 0) {
        $staff_list[] = mysqli_fetch_assoc($selfRes);
    }
}

$customers = [];
$custRes = mysqli_query($conn, "SELECT id, full_name, email, phone FROM customers WHERE status='active' ORDER BY full_name ASC");
if ($custRes) {
    while ($row = mysqli_fetch_assoc($custRes)) {
        $customers[] = $row;
    }
}

$services = [];
$srvRes = mysqli_query($conn, "SELECT id, service_name FROM services ORDER BY service_name ASC");
if ($srvRes) {
    while ($row = mysqli_fetch_assoc($srvRes)) {
        $services[] = $row;
    }
}

$query = "SELECT l.id, l.status, l.requested_amount, l.created_at,
                 c.full_name, c.phone,
                 sv.service_name,
                 st.name AS assigned_staff_name, st.id AS assigned_staff_id
          FROM loan_applications l
          INNER JOIN customers c ON c.id = l.customer_id
          INNER JOIN services sv ON sv.id = l.service_id
          LEFT JOIN staff st ON st.id = l.assigned_staff_id
          WHERE 1=1";

if ($search !== '') {
    $query .= " AND (c.full_name LIKE '%$search%' OR c.phone LIKE '%$search%' OR l.id LIKE '%$search%')";
}
if ($status !== '') {
    $query .= " AND l.status = '$status'";
}
$query .= " ORDER BY l.id DESC";

$loans = mysqli_query($conn, $query);
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Manual Loan Assign</h2>
                    <p class="text-muted small mb-0">Re-assign loans when you have manual assignment access.</p>
                </div>
            </div>

            <?php if (!empty($_GET['msg'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['err'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['err']) ?></div>
            <?php endif; ?>
      

            <div class="card border mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Create Manual Loan Assignment</h5>
                </div>
                <div class="card-body">
                    <form action="db/loan_handler.php" method="POST" class="row g-3 align-items-end" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="manual_create_assign">

                        <div class="col-md-6">
                            <label class="form-label">Customer</label>
                            <select name="customer_id" class="form-select" required>
                                <option value="">Select customer</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?= (int)$c['id'] ?>">
                                        <?= htmlspecialchars($c['full_name']) ?> (<?= htmlspecialchars($c['phone']) ?> | <?= htmlspecialchars($c['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Loan Service</label>
                            <select name="service_id" class="form-select" required>
                                <option value="">Select service</option>
                                <?php foreach ($services as $sv): ?>
                                    <option value="<?= (int)$sv['id'] ?>"><?= htmlspecialchars($sv['service_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Requested Amount</label>
                            <input type="number" step="0.01" min="1" name="requested_amount" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <div id="doc_container" class="row g-3"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tenure (Months)</label>
                            <input type="number" min="0" name="tenure_months" class="form-control" value="12" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Interest %</label>
                            <input type="number" step="0.01" min="0" name="interest_rate" class="form-control" value="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Interest Type</label>
                            <select name="interest_type" class="form-select">
                                <option value="year" selected>Yearly</option>
                                <option value="month">Monthly</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="pending" selected>Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="disbursed">Disbursed</option>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Assign Staff</label>
                            <?php if ($can_assign_others): ?>
                                <select name="staff_id" class="form-select" required>
                                    <option value="">Select staff</option>
                                    <?php foreach ($staff_list as $s): ?>
                                        <option value="<?= (int)$s['id'] ?>" <?= (int)$s['id'] === $current_staff_id ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="hidden" name="staff_id" value="<?= $current_staff_id ?>">
                                <input type="text" class="form-control" value="Self Assignment" readonly>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-dark w-100">Create And Assign</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border mb-3">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Loan ID, customer, phone" value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                <option value="disbursed" <?= $status === 'disbursed' ? 'selected' : '' ?>>Disbursed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-dark w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="manual_loan_assign.php" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-3">Loan</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Current Staff</th>
                                    <th class="pe-3">Assign</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if ($loans && mysqli_num_rows($loans) > 0): ?>
                                <?php while ($loan = mysqli_fetch_assoc($loans)): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold">#L-<?= (int)$loan['id'] ?></div>
                                            <div class="small text-muted"><?= date('d M Y', strtotime($loan['created_at'])) ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold"><?= htmlspecialchars($loan['full_name']) ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($loan['phone']) ?></div>
                                        </td>
                                        <td><?= htmlspecialchars($loan['service_name']) ?></td>
                                        <td>INR <?= format_inr((float)$loan['requested_amount'], 2) ?></td>
                                        <td><span class="badge bg-light text-dark text-capitalize"><?= htmlspecialchars($loan['status']) ?></span></td>
                                        <td><?= htmlspecialchars($loan['assigned_staff_name'] ?: 'Unassigned') ?></td>
                                        <td class="pe-3" style="min-width:260px;">
                                            <?php if ($can_assign_others): ?>
                                                <form action="db/loan_handler.php" method="POST" class="d-flex gap-2">
                                                    <input type="hidden" name="action" value="assign_staff">
                                                    <input type="hidden" name="loan_id" value="<?= (int)$loan['id'] ?>">
                                                    <input type="hidden" name="redirect_to" value="manual">
                                                    <select name="staff_id" class="form-select form-select-sm" required>
                                                        <option value="0">Unassign</option>
                                                        <?php foreach ($staff_list as $s): ?>
                                                            <option value="<?= (int)$s['id'] ?>" <?= (int)$loan['assigned_staff_id'] === (int)$s['id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['email']) ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-dark">Save</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="small text-muted">No reassignment access</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No loan applications found.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function validateLoanDocs() {
        const container = document.getElementById('doc_container');
        if (!container) return true;
        const fileInputs = container.querySelectorAll('input[type="file"][name^="loan_docs["]');
        if (!fileInputs || fileInputs.length === 0) return true;

        let valid = true;
        const maxBytes = 5 * 1024 * 1024;
        const allowedExt = ['pdf', 'jpg', 'jpeg', 'png'];

        fileInputs.forEach((input) => {
            input.setCustomValidity('');
            input.classList.remove('is-invalid');
            if (!input.files || input.files.length === 0) return;
            const file = input.files[0];
            const ext = ((file.name || '').split('.').pop() || '').toLowerCase();
            if (!allowedExt.includes(ext)) {
                input.setCustomValidity('Only PDF, JPG, JPEG, PNG files are allowed.');
                input.classList.add('is-invalid');
                valid = false;
                return;
            }
            if (file.size > maxBytes) {
                input.setCustomValidity('File size must be 5 MB or less.');
                input.classList.add('is-invalid');
                valid = false;
            }
        });
        return valid;
    }

    async function fetchDocs(serviceId) {
        const container = document.getElementById('doc_container');
        if (!container) return;
        container.innerHTML = '';
        if (!serviceId) return;
        try {
            const res = await fetch(`../api/get_service_docs.php?service_id=${serviceId}`);
            const docs = await res.json();
            docs.forEach(d => {
                const key = (d.doc_name || '').replace(/ /g, '_');
                container.innerHTML += `
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-white text-center">
                            <label class="form-label small">${d.doc_name}</label>
                            <input type="file" name="loan_docs[${key}]" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted d-block mt-1">Allowed: PDF/JPG/JPEG/PNG, max 5 MB</small>
                        </div>
                    </div>`;
            });
            container.querySelectorAll('input[type="file"]').forEach((input) => {
                input.addEventListener('change', validateLoanDocs);
            });
        } catch (e) {
            console.error('Error fetching docs', e);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const serviceSelect = document.querySelector('select[name="service_id"]');
        if (serviceSelect) {
            serviceSelect.addEventListener('change', (e) => fetchDocs(e.target.value));
            if (serviceSelect.value) {
                fetchDocs(serviceSelect.value);
            }
        }
        const form = document.querySelector('form[action="db/loan_handler.php"]');
        if (form) {
            form.addEventListener('submit', (e) => {
                if (!validateLoanDocs()) {
                    e.preventDefault();
                }
            });
        }
    });
</script>

<?php include 'footer.php'; ?>
