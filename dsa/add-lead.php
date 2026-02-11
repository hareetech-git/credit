<?php
include 'header.php';
dsaRequireAccess($conn, 'dsa_lead_create', 'dashboard.php');
include 'topbar.php';
include 'sidebar.php';

$services = mysqli_query($conn, "SELECT id, service_name FROM services ORDER BY service_name ASC");
?>
<style>
    .card-modern { border: 1px solid #e2e8f0; border-radius: 14px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.03); }
    .form-label { font-weight: 700; font-size: 0.75rem; text-transform: uppercase; color: #475569; letter-spacing: 0.04em; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Add New Lead</h2>
                    <p class="text-muted small mb-0">Create customer lead and submit loan request from DSA panel.</p>
                </div>
            </div>

            <?php if (!empty($_GET['msg'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars((string)$_GET['msg']) ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['err'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars((string)$_GET['err']) ?></div>
            <?php endif; ?>

            <form action="db/add_lead.php" method="POST" enctype="multipart/form-data">
                <div class="card card-modern mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Customer Details</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mobile Number</label>
                                <input type="text" name="phone" class="form-control" required maxlength="10" pattern="[6-9]{1}[0-9]{9}" title="Enter valid 10-digit mobile number starting with 6-9">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-modern">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Loan Details</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Loan Product</label>
                                <select name="service_id" id="serviceSelect" class="form-select" required>
                                    <option value="">Select Loan Product</option>
                                    <?php if ($services): while ($s = mysqli_fetch_assoc($services)): ?>
                                        <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars((string)$s['service_name']) ?></option>
                                    <?php endwhile; endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Requested Amount</label>
                                <input type="number" name="requested_amount" class="form-control" min="1" step="0.01" required>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6 class="fw-bold mb-2">Required Documents</h6>
                            <div id="docContainer" class="row g-3"></div>
                            <small class="text-muted d-block mt-2">Allowed: PDF/JPG/JPEG/PNG, max 5 MB per file.</small>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-dark px-4">Submit Lead</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const serviceSelect = document.getElementById('serviceSelect');
const docContainer = document.getElementById('docContainer');

function renderDocFields(docs) {
    docContainer.innerHTML = '';
    if (!Array.isArray(docs) || docs.length === 0) {
        docContainer.innerHTML = '<div class="col-12"><div class="alert alert-light border mb-0">No mandatory documents configured for this product.</div></div>';
        return;
    }

    docs.forEach((doc) => {
        const rawName = String(doc.doc_name || '').trim();
        if (!rawName) return;
        const fieldKey = rawName.replace(/\s+/g, '_').replace(/[^A-Za-z0-9_-]/g, '');
        const note = String(doc.disclaimer || '').trim();
        const html = `
            <div class="col-md-6">
                <div class="border rounded p-3 bg-light-subtle">
                    <label class="form-label">${rawName}</label>
                    <input type="file" name="loan_docs[${fieldKey}]" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" required>
                    ${note ? `<small class="text-muted d-block mt-1">${note}</small>` : ''}
                </div>
            </div>
        `;
        docContainer.insertAdjacentHTML('beforeend', html);
    });
}

async function fetchDocs(serviceId) {
    docContainer.innerHTML = '';
    if (!serviceId) return;

    try {
        const response = await fetch(`../api/get_service_docs.php?service_id=${encodeURIComponent(serviceId)}`);
        const docs = await response.json();
        renderDocFields(docs);
    } catch (err) {
        docContainer.innerHTML = '<div class="col-12"><div class="alert alert-danger mb-0">Unable to load required documents. Please try again.</div></div>';
    }
}

serviceSelect?.addEventListener('change', (e) => {
    fetchDocs(e.target.value);
});
</script>

<?php include 'footer.php'; ?>
