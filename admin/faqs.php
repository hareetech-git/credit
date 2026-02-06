<?php
include 'db/config.php';
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

$msg = $_GET['msg'] ?? '';
$err = $_GET['err'] ?? '';

$faq_res = mysqli_query($conn, "SELECT id, question, answer, created_by, created_role, status, created_at FROM faqs ORDER BY id DESC");
?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
    }

    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .btn-add-pro {
        background: var(--slate-900);
        color: white !important;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        transition: opacity 0.2s;
    }
    .btn-add-pro:hover { opacity: 0.9; color: white !important; }

    .badge-soft {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid var(--slate-200);
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">FAQ Management</h2>
                    <p class="text-muted small mb-0">Create and manage FAQs shown on the homepage.</p>
                </div>
            </div>

            <?php if (!empty($msg)): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4 py-3">
                    <i class="fas fa-check-circle me-1"></i> <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($err)): ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4 py-3">
                    <i class="fas fa-exclamation-circle me-1"></i> <?= htmlspecialchars($err) ?>
                </div>
            <?php endif; ?>

            <div class="card card-modern p-4 mb-4">
                <h5 class="fw-bold mb-3">Add New FAQ</h5>
                <form action="db/insert/faq_insert.php" method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Question</label>
                        <input type="text" name="question" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="1" selected>Active</option>
                            <option value="0">Hidden</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Answer</label>
                        <textarea name="answer" rows="4" class="form-control" required></textarea>
                    </div>
                    <div class="col-12">
                        <button class="btn-add-pro" type="submit">
                            <i class="fas fa-plus me-1"></i> Add FAQ
                        </button>
                    </div>
                </form>
            </div>

            <div class="card card-modern p-4">
                <h5 class="fw-bold mb-3">All FAQs</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Question</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($faq_res && mysqli_num_rows($faq_res) > 0): ?>
                                <?php while ($faq = mysqli_fetch_assoc($faq_res)): ?>
                                    <tr>
                                        <td><?= (int)$faq['id'] ?></td>
                                        <td><?= htmlspecialchars($faq['question']) ?></td>
                                        <td>
                                            <span class="badge-soft">
                                                <?= ((int)$faq['status'] === 1) ? 'Active' : 'Hidden' ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($faq['created_role']) ?> #<?= (int)$faq['created_by'] ?></td>
                                        <td><?= htmlspecialchars($faq['created_at']) ?></td>
                                        <td class="text-end">
                                            <a href="db/delete/faq_delete.php?id=<?= (int)$faq['id'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Delete this FAQ?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No FAQs found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
