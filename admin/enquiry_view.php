<?php
include 'db/config.php';
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

$enquiry_id = (int)($_GET['id'] ?? 0);
if ($enquiry_id <= 0) {
    header("Location: enquiries.php?err=Invalid enquiry");
    exit;
}

$enquiry_sql = "SELECT e.*, 
                       s.name AS assigned_staff_name, s.email AS assigned_staff_email,
                       c.full_name AS customer_name, c.email AS customer_email, c.phone AS customer_phone
                FROM enquiries e
                LEFT JOIN staff s ON e.assigned_staff_id = s.id
                LEFT JOIN customers c ON e.customer_id = c.id
                WHERE e.id = $enquiry_id
                LIMIT 1";
$enquiry_res = mysqli_query($conn, $enquiry_sql);
if (!$enquiry_res || mysqli_num_rows($enquiry_res) === 0) {
    header("Location: enquiries.php?err=Enquiry not found");
    exit;
}
$enquiry = mysqli_fetch_assoc($enquiry_res);
$is_closed_or_converted = in_array($enquiry['status'], ['closed','converted'], true);
$wa_phone = preg_replace('/\D+/', '', $enquiry['phone'] ?? '');
$wa_text = rawurlencode("Hello {$enquiry['full_name']}, can you provide more information about your enquiry #{$enquiry_id}?");
$wa_link = "https://api.whatsapp.com/send/?phone={$wa_phone}&text={$wa_text}&type=phone_number&app_absent=0";

// Staff list for assignment
$staff_list = [];
$staff_res = mysqli_query($conn, "SELECT id, name, email FROM staff WHERE status = 'active' ORDER BY name ASC");
if ($staff_res) {
    while ($s = mysqli_fetch_assoc($staff_res)) {
        $staff_list[] = $s;
    }
}

// Conversation + messages
$conv_res = mysqli_query($conn, "SELECT id FROM enquiry_conversations WHERE enquiry_id = $enquiry_id LIMIT 1");
$conversation_id = 0;
if ($conv_res && mysqli_num_rows($conv_res) > 0) {
    $conv = mysqli_fetch_assoc($conv_res);
    $conversation_id = (int)$conv['id'];
}

$messages = [];
if ($conversation_id > 0) {
    $msg_sql = "SELECT m.*, 
                       a.name AS admin_name,
                       st.name AS staff_name,
                       cu.full_name AS customer_name
                FROM enquiry_messages m
                LEFT JOIN admin a ON (m.sender_role='admin' AND m.sender_id = a.id)
                LEFT JOIN staff st ON (m.sender_role='staff' AND m.sender_id = st.id)
                LEFT JOIN customers cu ON (m.sender_role='customer' AND m.sender_id = cu.id)
                WHERE m.conversation_id = $conversation_id
                ORDER BY m.created_at ASC";
    $msg_res = mysqli_query($conn, $msg_sql);
    if ($msg_res) {
        while ($m = mysqli_fetch_assoc($msg_res)) {
            $messages[] = $m;
        }
    }
}

// Notes
$notes = [];
$notes_res = mysqli_query($conn, "SELECT n.*, 
        CASE 
            WHEN n.created_by_role = 'admin' THEN a.name 
            WHEN n.created_by_role = 'staff' THEN s.name 
            ELSE 'User'
        END AS creator_name
    FROM enquiry_notes n
    LEFT JOIN admin a ON (n.created_by_role='admin' AND n.created_by_id = a.id)
    LEFT JOIN staff s ON (n.created_by_role='staff' AND n.created_by_id = s.id)
    WHERE n.enquiry_id = $enquiry_id
    ORDER BY n.created_at DESC");
if ($notes_res) {
    while ($n = mysqli_fetch_assoc($notes_res)) {
        $notes[] = $n;
    }
}
?>

<style>
    .card-modern {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .msg-thread {
        max-height: 420px;
        overflow-y: auto;
        padding: 16px;
        background: #f8fafc;
    }
    .msg-bubble {
        padding: 10px 12px;
        border-radius: 10px;
        margin-bottom: 10px;
        max-width: 70%;
        background: #ffffff;
        border: 1px solid #e2e8f0;
    }
    .msg-admin { border-left: 4px solid #3b82f6; }
    .msg-staff { border-left: 4px solid #10b981; }
    .msg-customer { border-left: 4px solid #f59e0b; }
    .msg-meta { font-size: 0.75rem; color: #64748b; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Enquiry #<?= $enquiry_id ?></h2>
                    <div class="text-muted small">Status: <?= ucfirst($enquiry['status']) ?></div>
                </div>
                <div class="d-flex gap-2">
                    <a href="enquiry_email.php?id=<?= $enquiry_id ?>" class="btn btn-sm btn-dark">Send Email</a>
                    <?php if (!empty($wa_phone)): ?>
                        <a href="<?= $wa_link ?>" target="_blank" class="btn btn-sm btn-outline-success">WhatsApp</a>
                    <?php endif; ?>
                    <form method="POST" action="db/enquiry_status.php">
                        <input type="hidden" name="enquiry_id" value="<?= $enquiry_id ?>">
                        <input type="hidden" name="action" value="convert">
                        <button class="btn btn-sm btn-outline-success" type="submit" <?= $is_closed_or_converted ? 'disabled' : '' ?>>Mark Converted</button>
                    </form>
                    <form method="POST" action="db/enquiry_status.php">
                        <input type="hidden" name="enquiry_id" value="<?= $enquiry_id ?>">
                        <input type="hidden" name="action" value="close">
                        <button class="btn btn-sm btn-outline-secondary" type="submit" <?= $is_closed_or_converted ? 'disabled' : '' ?>>Close Enquiry</button>
                    </form>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card card-modern mb-3">
                        <div class="card-body">
                            <h5 class="fw-bold">Enquiry Details</h5>
                            <div class="small text-muted">Applicant</div>
                            <div class="fw-semibold"><?= htmlspecialchars($enquiry['full_name']) ?></div>
                            <div class="small"><?= htmlspecialchars($enquiry['email']) ?> • <?= htmlspecialchars($enquiry['phone']) ?></div>
                            <div class="mt-3 small text-muted">Loan Type</div>
                            <div class="fw-semibold"><?= htmlspecialchars($enquiry['loan_type_name']) ?></div>
                            <div class="mt-3 small text-muted">Message</div>
                            <div><?= nl2br(htmlspecialchars($enquiry['query_message'])) ?></div>
                            <?php if ($enquiry['status'] === 'closed'): ?>
                                <div class="mt-3 small text-muted">Closed By</div>
                                <div class="fw-semibold"><?= htmlspecialchars($enquiry['closed_by_role'] ?: '-') ?> #<?= htmlspecialchars($enquiry['closed_by_id'] ?: '-') ?></div>
                            <?php elseif ($enquiry['status'] === 'converted'): ?>
                                <div class="mt-3 small text-muted">Converted By</div>
                                <div class="fw-semibold"><?= htmlspecialchars($enquiry['converted_by_role'] ?: '-') ?> #<?= htmlspecialchars($enquiry['converted_by_id'] ?: '-') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card card-modern mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold">Assignment</h6>
                            <div class="small text-muted">Assigned Staff</div>
                            <div class="fw-semibold"><?= htmlspecialchars($enquiry['assigned_staff_name'] ?: 'Unassigned') ?></div>
                            <div class="small text-muted"><?= htmlspecialchars($enquiry['assigned_staff_email'] ?: '') ?></div>
                            <form method="POST" action="db/enquiry_assign.php" class="mt-3">
                                <input type="hidden" name="enquiry_id" value="<?= $enquiry_id ?>">
                                <input type="hidden" name="redirect" value="../enquiry_view.php?id=<?= $enquiry_id ?>">
                                <select name="staff_id" class="form-select form-select-sm">
                                    <option value="0">Unassigned</option>
                                    <?php foreach ($staff_list as $s): ?>
                                        <option value="<?= $s['id'] ?>" <?= ((int)$enquiry['assigned_staff_id'] === (int)$s['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($s['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-sm btn-dark w-100 mt-2" type="submit">Update Assignment</button>
                            </form>
                        </div>
                    </div>

                    <?php if (!empty($enquiry['customer_id'])): ?>
                    <div class="card card-modern">
                        <div class="card-body">
                            <h6 class="fw-bold">Customer</h6>
                            <div class="fw-semibold"><?= htmlspecialchars($enquiry['customer_name']) ?></div>
                            <div class="small"><?= htmlspecialchars($enquiry['customer_email']) ?> • <?= htmlspecialchars($enquiry['customer_phone']) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-8">
                    <div class="card card-modern mb-3" id="messages">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Conversation</h5>
                            <div class="msg-thread">
                                <?php if (empty($messages)): ?>
                                    <div class="text-muted small">No messages yet.</div>
                                <?php else: ?>
                                    <?php foreach ($messages as $m): 
                                        $role = $m['sender_role'];
                                        $name = $role === 'admin' ? $m['admin_name'] : ($role === 'staff' ? $m['staff_name'] : $m['customer_name']);
                                        $class = $role === 'admin' ? 'msg-admin' : ($role === 'staff' ? 'msg-staff' : 'msg-customer');
                                    ?>
                                        <div class="msg-bubble <?= $class ?>">
                                            <div class="msg-meta"><?= ucfirst($role) ?> • <?= htmlspecialchars($name ?: 'User') ?> • <?= date('M d, Y h:i A', strtotime($m['created_at'])) ?></div>
                                            <div><?= nl2br(htmlspecialchars($m['message'])) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php if ($is_closed_or_converted): ?>
                                <div class="text-muted small">This enquiry is closed/converted. Messaging is disabled.</div>
                            <?php else: ?>
                                <form method="POST" action="db/enquiry_message.php" class="mt-3">
                                    <input type="hidden" name="enquiry_id" value="<?= $enquiry_id ?>">
                                    <textarea name="message" class="form-control" rows="3" placeholder="Type a reply..." required></textarea>
                                    <button class="btn btn-sm btn-dark mt-2">Send Reply</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card card-modern" id="notes">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Internal Notes</h5>
                            <?php if (empty($notes)): ?>
                                <div class="text-muted small">No notes yet.</div>
                            <?php else: ?>
                                <?php foreach ($notes as $n): ?>
                                    <div class="mb-3">
                                        <div class="small text-muted"><?= htmlspecialchars($n['creator_name']) ?> • <?= date('M d, Y h:i A', strtotime($n['created_at'])) ?></div>
                                        <div><?= nl2br(htmlspecialchars($n['note'])) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <form method="POST" action="db/enquiry_note.php" class="mt-3">
                                <input type="hidden" name="enquiry_id" value="<?= $enquiry_id ?>">
                                <textarea name="note" class="form-control" rows="3" placeholder="Add internal note..." required></textarea>
                                <button class="btn btn-sm btn-outline-dark mt-2">Add Note</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
