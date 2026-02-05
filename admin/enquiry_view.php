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
    :root {
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-700: #334155;
        --slate-900: #0f172a;
    }
    .content-page { background-color: #f8fafc; }
    
    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .status-badge {
        padding: 6px 16px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .status-pending { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
    .status-closed { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .status-converted { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }

    /* Messaging UI */
    .msg-thread {
        height: 450px;
        overflow-y: auto;
        padding: 20px;
        background: #fdfdfd;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .msg-bubble {
        padding: 12px 16px;
        border-radius: 14px;
        max-width: 85%;
        font-size: 0.9rem;
        line-height: 1.5;
        position: relative;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .msg-admin { align-self: flex-end; background: var(--slate-900); color: white; border-bottom-right-radius: 2px; }
    .msg-staff { align-self: flex-end; background: #3b82f6; color: white; border-bottom-right-radius: 2px; }
    .msg-customer { align-self: flex-start; background: var(--slate-100); color: var(--slate-900); border-bottom-left-radius: 2px; }
    
    .msg-meta { font-size: 0.65rem; margin-bottom: 4px; opacity: 0.8; font-weight: 600; text-transform: uppercase; }
    
    .internal-note-box {
        background: #fffbeb;
        border: 1px solid #fef3c7;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 10px;
    }

    .form-control:focus {
        border-color: var(--slate-900);
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="enquiries.php" class="text-muted">Enquiries</a></li>
                            <li class="breadcrumb-item active">#<?= $enquiry_id ?></li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold text-dark mb-0">Review Enquiry</h2>
                </div>
                <div class="d-flex gap-2">
                    <a href="enquiry_email.php?id=<?= $enquiry_id ?>" class="btn btn-dark px-3 rounded-pill fw-bold btn-sm shadow-sm">
                        <i class="fas fa-envelope me-2"></i>Email
                    </a>
                    <?php if (!empty($wa_phone)): ?>
                        <a href="<?= $wa_link ?>" target="_blank" class="btn btn-success px-3 rounded-pill fw-bold btn-sm shadow-sm">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card-modern p-4 text-center">
                        <span class="status-badge status-<?= $enquiry['status'] ?> mb-3 d-inline-block">
                            <?= ucfirst($enquiry['status']) ?>
                        </span>
                        <h4 class="fw-bold mb-1"><?= htmlspecialchars($enquiry['full_name']) ?></h4>
                        <p class="text-muted small mb-3"><?= htmlspecialchars($enquiry['email']) ?></p>
                        
                        <div class="d-flex gap-2 justify-content-center border-top pt-3">
                            <form method="POST" action="db/enquiry_status.php" class="flex-fill">
                                <input type="hidden" name="enquiry_id" value="<?= $enquiry_id ?>">
                                <input type="hidden" name="action" value="convert">
                                <button class="btn btn-sm btn-outline-success w-100 fw-bold rounded-pill" type="submit" <?= $is_closed_or_converted ? 'disabled' : '' ?>>Convert</button>
                            </form>
                            <form method="POST" action="db/enquiry_status.php" class="flex-fill">
                                <input type="hidden" name="enquiry_id" value="<?= $enquiry_id ?>">
                                <input type="hidden" name="action" value="close">
                                <button class="btn btn-sm btn-outline-secondary w-100 fw-bold rounded-pill" type="submit" <?= $is_closed_or_converted ? 'disabled' : '' ?>>Close</button>
                            </form>
                        </div>
                    </div>

                    <div class="card-modern p-4">
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Enquiry Information</h6>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Loan Interest</label>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($enquiry['loan_type_name']) ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Initial Message</label>
                            <span class="text-dark small"><?= nl2br(htmlspecialchars($enquiry['query_message'])) ?></span>
                        </div>
                        
                        <?php if ($enquiry['status'] !== 'pending'): ?>
                        <div class="mt-3 p-2 bg-light rounded border">
                            <label class="text-muted x-small d-block">Resolved By</label>
                            <span class="small fw-bold"><?= ucfirst($enquiry['status'] === 'closed' ? $enquiry['closed_by_role'] : $enquiry['converted_by_role']) ?> (ID: <?= $enquiry['status'] === 'closed' ? $enquiry['closed_by_id'] : $enquiry['converted_by_id'] ?>)</span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-modern p-4">
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Staff Assignment</h6>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px; height:32px; font-size:10px;">
                                <?= strtoupper(substr($enquiry['assigned_staff_name'] ?: 'U', 0, 1)) ?>
                            </div>
                            <div>
                                <div class="fw-bold small text-dark"><?= htmlspecialchars($enquiry['assigned_staff_name'] ?: 'Unassigned') ?></div>
                                <div class="text-muted x-small"><?= htmlspecialchars($enquiry['assigned_staff_email'] ?: 'No staff assigned') ?></div>
                            </div>
                        </div>
                        <form method="POST" action="db/enquiry_assign.php">
                            <input type="hidden" name="enquiry_id" value="<?= $enquiry_id ?>">
                            <input type="hidden" name="redirect" value="../enquiry_view.php?id=<?= $enquiry_id ?>">
                            <select name="staff_id" class="form-select form-select-sm mb-2 rounded-pill">
                                <option value="0">Assign Staff Member</option>
                                <?php foreach ($staff_list as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= ((int)$enquiry['assigned_staff_id'] === (int)$s['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-xs btn-dark w-100 rounded-pill" type="submit">Update Agent</button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card-modern">
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">Conversation History</h6>
                            <i class="fas fa-comments text-muted"></i>
                        </div>
                        <div class="msg-thread">
                            <?php if (empty($messages)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-comment-slash fa-2x text-light mb-2"></i>
                                    <p class="text-muted small">No message history yet.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($messages as $m): 
                                    $role = $m['sender_role'];
                                    $name = $role === 'admin' ? $m['admin_name'] : ($role === 'staff' ? $m['staff_name'] : $m['customer_name']);
                                    $class = $role === 'admin' ? 'msg-admin' : ($role === 'staff' ? 'msg-staff' : 'msg-customer');
                                ?>
                                    <div class="msg-bubble <?= $class ?>">
                                        <div class="msg-meta"><?= ucfirst($role) ?> • <?= htmlspecialchars($name ?: 'User') ?></div>
                                        <div><?= nl2br(htmlspecialchars($m['message'])) ?></div>
                                        <div class="text-end mt-1" style="font-size: 0.6rem; opacity: 0.7;">
                                            <?= date('h:i A', strtotime($m['created_at'])) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="p-3 bg-light">
                            <?php if ($is_closed_or_converted): ?>
                                <div class="text-center text-muted small py-2"><i class="fas fa-lock me-1"></i> Enquiry is locked (Closed/Converted)</div>
                            <?php else: ?>
                                <form method="POST" action="db/enquiry_message.php">
                                    <input type="hidden" name="enquiry_id" value="<?= $enquiry_id ?>">
                                    <div class="input-group">
                                        <textarea name="message" class="form-control" rows="2" placeholder="Write a reply..." required style="border-radius: 12px;"></textarea>
                                        <button class="btn btn-dark ms-2 rounded-circle" style="width:45px; height:45px;"><i class="fas fa-paper-plane"></i></button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-modern">
                        <div class="p-3 border-bottom">
                            <h6 class="fw-bold mb-0">Internal Team Notes</h6>
                        </div>
                        <div class="p-4">
                            <?php if (empty($notes)): ?>
                                <p class="text-muted small italic">No internal notes added for this enquiry.</p>
                            <?php else: ?>
                                <?php foreach ($notes as $n): ?>
                                    <div class="internal-note-box">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="fw-bold small text-dark"><?= htmlspecialchars($n['creator_name']) ?></span>
                                            <span class="text-muted x-small"><?= date('M d, h:i A', strtotime($n['created_at'])) ?></span>
                                        </div>
                                        <div class="small text-secondary"><?= nl2br(htmlspecialchars($n['note'])) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <form method="POST" action="db/enquiry_note.php" class="mt-4 pt-3 border-top">
                                <input type="hidden" name="enquiry_id" value="<?= $enquiry_id ?>">
                                <label class="form-label small fw-bold">Add Note</label>
                                <textarea name="note" class="form-control mb-2" rows="2" placeholder="Share updates with the team..." required></textarea>
                                <button class="btn btn-sm btn-outline-dark fw-bold px-3 rounded-pill">Add Note</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>