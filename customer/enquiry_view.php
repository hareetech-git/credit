<?php
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

$enquiry_id = (int)($_GET['id'] ?? 0);
$customer_id = (int)$_SESSION['customer_id'];

if ($enquiry_id <= 0) {
    header("Location: enquiries.php?err=Invalid enquiry");
    exit;
}

$enquiry_sql = "SELECT e.*, 
                       s.name AS assigned_staff_name, s.email AS assigned_staff_email
                FROM enquiries e
                LEFT JOIN staff s ON e.assigned_staff_id = s.id
                WHERE e.id = $enquiry_id AND e.customer_id = $customer_id
                LIMIT 1";
$enquiry_res = mysqli_query($conn, $enquiry_sql);
if (!$enquiry_res || mysqli_num_rows($enquiry_res) === 0) {
    header("Location: enquiries.php?err=Access denied");
    exit;
}
$enquiry = mysqli_fetch_assoc($enquiry_res);
$is_closed_or_converted = in_array($enquiry['status'], ['closed','converted'], true);

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
?>

<style>
    :root {
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-700: #334155;
        --slate-900: #0f172a;
        --blue-600: #2563eb;
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
        padding: 6px 16px; border-radius: 50px; font-weight: 700; font-size: 0.7rem;
        text-transform: uppercase; letter-spacing: 0.05em;
    }
    .status-pending, .status-new { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
    .status-closed { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .status-conversation { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }

    /* Messaging UI */
    .msg-thread {
        height: 480px; overflow-y: auto; padding: 20px;
        background: #fdfdfd; display: flex; flex-direction: column; gap: 15px;
    }
    .msg-bubble {
        padding: 12px 16px; border-radius: 14px; max-width: 80%;
        font-size: 0.9rem; line-height: 1.5; position: relative;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    /* Customer (You) on the Left */
    .msg-customer { align-self: flex-start; background: var(--slate-100); color: var(--slate-900); border-bottom-left-radius: 2px; }
    /* Support Team on the Right */
    .msg-admin, .msg-staff { align-self: flex-end; background: var(--slate-900); color: white; border-bottom-right-radius: 2px; }
    .msg-staff { background: var(--blue-600); }
    
    .msg-meta { font-size: 0.65rem; margin-bottom: 4px; opacity: 0.8; font-weight: 600; text-transform: uppercase; }
    
    .form-control:focus {
        border-color: var(--slate-900);
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="enquiries.php" class="text-muted">My Enquiries</a></li>
                            <li class="breadcrumb-item active">Case #<?= $enquiry_id ?></li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold text-dark mb-0">Conversation</h2>
                </div>
                <div class="d-flex gap-2">
                    <form method="POST" action="db/enquiry_status.php">
                        <input type="hidden" name="enquiry_id" value="<?= $enquiry_id ?>">
                        <input type="hidden" name="action" value="close">
                        <button class="btn btn-outline-secondary px-3 rounded-pill fw-bold btn-sm shadow-sm" type="submit" <?= $is_closed_or_converted ? 'disabled' : '' ?>>
                            Close Enquiry
                        </button>
                    </form>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card-modern p-4 text-center">
                        <span class="status-badge status-<?= $enquiry['status'] ?> mb-3 d-inline-block">
                            <?= ucfirst($enquiry['status']) ?>
                        </span>
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($enquiry['loan_type_name']) ?></h5>
                        <p class="text-muted small">Application ID: #ENQ-<?= $enquiry_id ?></p>
                    </div>

                    <div class="card-modern p-4">
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Your Original Request</h6>
                        <div class="p-3 bg-light rounded-3 small text-dark border">
                            <?= nl2br(htmlspecialchars($enquiry['query_message'])) ?>
                        </div>
                        <div class="mt-3 text-muted" style="font-size: 0.7rem;">
                            Submitted on <?= date('d M, Y', strtotime($enquiry['created_at'])) ?>
                        </div>
                    </div>

                    <div class="card-modern p-4">
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Assigned Expert</h6>
                        <?php if ($enquiry['assigned_staff_name']): ?>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:36px; height:36px; font-weight:bold;">
                                    <?= strtoupper(substr($enquiry['assigned_staff_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="fw-bold small text-dark"><?= htmlspecialchars($enquiry['assigned_staff_name']) ?></div>
                                    <div class="text-muted x-small">Relationship Manager</div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-muted small italic">Waiting for agent assignment...</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card-modern">
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                            <h6 class="fw-bold mb-0">Message History</h6>
                            <i class="fas fa-shield-alt text-muted" title="Secure Conversation"></i>
                        </div>
                        
                        <div class="msg-thread">
                            <?php if (empty($messages)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-comments fa-3x text-light mb-3"></i>
                                    <p class="text-muted small">No messages yet. Our team will respond shortly.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($messages as $m): 
                                    $role = $m['sender_role'];
                                    $is_me = ($role === 'customer');
                                    $name = $role === 'admin' ? 'Support Admin' : ($role === 'staff' ? $m['staff_name'] : 'You');
                                    $class = $role === 'admin' ? 'msg-admin' : ($role === 'staff' ? 'msg-staff' : 'msg-customer');
                                ?>
                                    <div class="msg-bubble <?= $class ?>">
                                        <div class="msg-meta"><?= $name ?> • <?= date('h:i A', strtotime($m['created_at'])) ?></div>
                                        <div><?= nl2br(htmlspecialchars($m['message'])) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="p-3 bg-light border-top">
                            <?php if ($is_closed_or_converted): ?>
                                <div class="text-center text-muted small py-2">
                                    <i class="fas fa-lock me-1"></i> This conversation is closed.
                                </div>
                            <?php else: ?>
                                <form method="POST" action="db/enquiry_message.php">
                                    <input type="hidden" name="enquiry_id" value="<?= $enquiry_id ?>">
                                    <div class="input-group">
                                        <textarea name="message" class="form-control" rows="2" placeholder="Type your message here..." required style="border-radius: 12px; resize: none;"></textarea>
                                        <button class="btn btn-dark ms-2 rounded-circle" style="width:48px; height:48px;">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>