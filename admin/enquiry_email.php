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

$enquiry_sql = "SELECT e.*, c.full_name AS customer_name 
                FROM enquiries e
                LEFT JOIN customers c ON e.customer_id = c.id
                WHERE e.id = $enquiry_id
                LIMIT 1";
$enquiry_res = mysqli_query($conn, $enquiry_sql);
if (!$enquiry_res || mysqli_num_rows($enquiry_res) === 0) {
    header("Location: enquiries.php?err=Enquiry not found");
    exit;
}
$enquiry = mysqli_fetch_assoc($enquiry_res);

$subject_default = "Regarding your enquiry #{$enquiry_id}";
$message_default = "Dear " . htmlspecialchars($enquiry['full_name']) . ",\n\nThanks for reaching out to Udhar Capital. We’re currently reviewing your request and our team will get back to you shortly.\n\nBest Regards,\nTeam Udhar Capital";
?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-100: #f1f5f9;
        --accent-emerald: #10b981;
    }
    .content-page { background-color: #f8fafc; }
    
    .email-card {
        border: none;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.02);
        overflow: hidden;
    }

    .email-header-info {
        background: #f8fafc;
        padding: 20px 25px;
        border-bottom: 1px solid #e2e8f0;
    }

    .form-label {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 10px;
        padding: 12px 15px;
        border: 1px solid #e2e8f0;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        border-color: var(--slate-900);
        box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
    }

    .btn-send {
        background: var(--slate-900);
        color: white;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        border: none;
        transition: 0.3s;
    }

    .btn-send:hover {
        background: #1e293b;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        color: white;
    }

    .input-group-text {
        background: white;
        border-right: none;
        color: #94a3b8;
    }
    
    .has-icon .form-control {
        border-left: none;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h3 class="fw-bold text-dark mb-0">Compose Response</h3>
                            <p class="text-muted small">Replying to Enquiry <strong>#<?= $enquiry_id ?></strong></p>
                        </div>
                        <a href="enquiry_view.php?id=<?= $enquiry_id ?>" class="btn btn-white border rounded-pill px-4 btn-sm shadow-sm">
                            <i class="fas fa-arrow-left me-2"></i> Back to Detail
                        </a>
                    </div>

                    <div class="email-card">
                        <div class="email-header-info">
                            <div class="d-flex align-items-center">
                                <div class="avatar-slate bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; font-weight: 700;">
                                    <?= strtoupper(substr($enquiry['full_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($enquiry['full_name']) ?></h6>
                                    <span class="text-muted small"><?= htmlspecialchars($enquiry['email']) ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            <form method="POST" action="db/enquiry_send_email.php">
                                <input type="hidden" name="enquiry_id" value="<?= $enquiry_id ?>">
                                
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Recipient Email</label>
                                        <div class="input-group has-icon">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            <input type="email" name="to_email" class="form-control" value="<?= htmlspecialchars($enquiry['email']) ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Recipient Name</label>
                                        <div class="input-group has-icon">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" name="to_name" class="form-control" value="<?= htmlspecialchars($enquiry['full_name']) ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Email Subject</label>
                                        <input type="text" name="subject" class="form-control" placeholder="Enter subject..." value="<?= htmlspecialchars($subject_default) ?>" required>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Message Body</label>
                                        <textarea name="message" class="form-control" rows="8" placeholder="Type your message here..." required style="resize: none;"><?= htmlspecialchars($message_default) ?></textarea>
                                        <div class="form-text text-end small">Standard professional footer will be appended automatically.</div>
                                    </div>
                                </div>

                                <div class="mt-5 d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        <i class="fas fa-shield-alt me-1"></i> Secure SMTP Transmission
                                    </div>
                                    <button type="submit" class="btn btn-send shadow">
                                        Send Message <i class="fas fa-paper-plane ms-2"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>