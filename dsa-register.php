<?php
require_once 'includes/header.php';

$customer_id = (int)($_SESSION['customer_id'] ?? 0);
$prefill = [
    'full_name' => '',
    'email' => '',
    'phone' => '',
    'pan_number' => '',
    'city' => '',
    'state' => '',
    'pin_code' => '',
];

if ($customer_id > 0 && isset($conn)) {
    $res = mysqli_query($conn, "SELECT c.full_name, c.email, c.phone, cp.pan_number, cp.city, cp.state, cp.pin_code
                                FROM customers c
                                LEFT JOIN customer_profiles cp ON cp.customer_id = c.id
                                WHERE c.id = $customer_id
                                LIMIT 1");
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        foreach ($prefill as $k => $_) {
            $prefill[$k] = (string)($row[$k] ?? '');
        }
    }
}
?>

<style>
    :root {
        --dsa-ink: #0f172a;
        --dsa-muted: #64748b;
        --dsa-bg: #f8fafc;
        --dsa-border: rgba(15, 23, 42, 0.08);
    }

    .dsa-hero {
        padding: 70px 0 40px;
        background: linear-gradient(140deg, rgba(15, 23, 42, 0.05), rgba(59, 130, 246, 0.08));
        border-bottom: 1px solid var(--dsa-border);
    }

    .dsa-hero h1 {
        color: var(--dsa-ink);
        font-size: 2.4rem;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .dsa-hero p {
        color: var(--dsa-muted);
        max-width: 760px;
        margin: 0 auto;
    }

    .dsa-content {
        background: var(--dsa-bg);
        padding: 50px 0 80px;
    }

    .dsa-card {
        background: #fff;
        border: 1px solid var(--dsa-border);
        border-radius: 18px;
        padding: 28px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
    }

    .form-label { font-weight: 700; font-size: 0.8rem; color: var(--dsa-ink); text-transform: uppercase; letter-spacing: 0.03em; }
    .form-control { border-radius: 10px; border: 1px solid #e2e8f0; padding: 12px; }
    .form-control:focus { box-shadow: none; border-color: #0f172a; }
</style>

<section class="dsa-hero text-center">
    <div class="container">
        <h1>Become DSA Partner</h1>
        <p>Join Udhar Capital as a DSA partner, submit leads, and track status from your dedicated DSA dashboard.</p>
    </div>
</section>

<section class="dsa-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="dsa-card">
                    <h4 class="fw-bold mb-3" style="color:#0f172a;">DSA Partner Registration Form</h4>
                    <p class="text-muted mb-4">Submit this form. Admin will verify details. On approval, you will receive login credentials by email.</p>

                    <?php if (!empty($_GET['msg'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($_GET['err'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($_GET['err']) ?></div>
                    <?php endif; ?>

                    <form action="insert/dsa_partner_register.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control" required value="<?= htmlspecialchars($prefill['full_name']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($prefill['email']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" maxlength="15" required value="<?= htmlspecialchars($prefill['phone']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Firm Name</label>
                                <input type="text" name="firm_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">PAN Number</label>
                                <input type="text" name="pan_number" class="form-control" maxlength="20" required value="<?= htmlspecialchars($prefill['pan_number']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" required value="<?= htmlspecialchars($prefill['city']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control" required value="<?= htmlspecialchars($prefill['state']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pin Code</label>
                                <input type="text" name="pin_code" class="form-control" maxlength="10" required value="<?= htmlspecialchars($prefill['pin_code']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Account Number</label>
                                <input type="text" name="account_number" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">IFSC Code</label>
                                <input type="text" name="ifsc_code" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message (Optional)</label>
                                <textarea name="message" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="mt-4 d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-dark">Submit DSA Registration</button>
                            <a href="dsa/index.php" class="btn btn-outline-dark">Already Approved? DSA Login</a>
                        </div>
                    </form>

                    <div class="mt-3">
                        <small class="text-muted">After approval, credentials are sent on registered email.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
