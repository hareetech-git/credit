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
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">Customer Details</h5>
                    </div>
                    <div class="card-body p-4">
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
                            <div class="col-md-4">
                                <label class="form-label">PAN Number</label>
                                <input type="text" name="pan_number" id="pan_number" class="form-control" maxlength="10" style="text-transform: uppercase;" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" title="Enter valid PAN (e.g., ABCDE1234F)" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Birth Date</label>
                                <input type="date" name="birth_date" id="birth_date" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Employment Type</label>
                                <select name="employee_type" id="employee_type" class="form-select" required>
                                    <option value="salaried">Salaried</option>
                                    <option value="business">Business Owner</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Monthly Income</label>
                                <input type="number" name="monthly_income" class="form-control" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-8" id="company_wrap" style="display:none;">
                                <label class="form-label">Company / Business Name</label>
                                <input type="text" name="company_name" id="company_name" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-modern mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">Address and References</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pin Code</label>
                                <input type="text" name="pin_code" class="form-control" maxlength="6" pattern="[0-9]{6}" title="Enter valid 6-digit pin code" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Reference 1 Name</label>
                                <input type="text" name="reference1_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reference 1 Phone</label>
                                <input type="text" name="reference1_phone" class="form-control reference-phone" maxlength="10" pattern="[6-9]{1}[0-9]{9}" title="Enter valid 10-digit mobile number starting with 6-9" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Reference 2 Name</label>
                                <input type="text" name="reference2_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reference 2 Phone</label>
                                <input type="text" name="reference2_phone" class="form-control reference-phone" maxlength="10" pattern="[6-9]{1}[0-9]{9}" title="Enter valid 10-digit mobile number starting with 6-9" required>
                            </div>

                            <div class="col-12">
                                <small id="referenceError" class="text-danger d-none">Reference 1 and Reference 2 must be different (name and phone).</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-modern">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">Loan Details</h5>
                    </div>
                    <div class="card-body p-4">
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
                            <button type="submit" class="btn btn-dark px-4" id="submitBtn">Submit Lead</button>
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
const employeeType = document.getElementById('employee_type');
const companyWrap = document.getElementById('company_wrap');
const companyName = document.getElementById('company_name');
const phoneInput = document.querySelector('input[name="phone"]');
const panInput = document.getElementById('pan_number');
const birthDate = document.getElementById('birth_date');
const refError = document.getElementById('referenceError');
const submitBtn = document.getElementById('submitBtn');

function toggleCompanyField() {
    const isBusiness = employeeType && employeeType.value === 'business';
    if (!companyWrap || !companyName) return;
    companyWrap.style.display = isBusiness ? '' : 'none';
    companyName.required = isBusiness;
    if (!isBusiness) companyName.value = '';
}

function digitsOnly(el) {
    if (!el) return;
    el.value = String(el.value || '').replace(/\D+/g, '').slice(0, 10);
}

function normalizePan() {
    if (!panInput) return;
    panInput.value = String(panInput.value || '').toUpperCase();
}

function validateReferenceUniqueness() {
    const r1n = document.querySelector('input[name="reference1_name"]');
    const r2n = document.querySelector('input[name="reference2_name"]');
    const r1p = document.querySelector('input[name="reference1_phone"]');
    const r2p = document.querySelector('input[name="reference2_phone"]');
    if (!r1n || !r2n || !r1p || !r2p || !refError) return true;

    const n1 = String(r1n.value || '').trim().toLowerCase().replace(/\s+/g, ' ');
    const n2 = String(r2n.value || '').trim().toLowerCase().replace(/\s+/g, ' ');
    const p1 = String(r1p.value || '').replace(/\D+/g, '');
    const p2 = String(r2p.value || '').replace(/\D+/g, '');

    const duplicate = (n1 !== '' && n1 === n2) || (p1 !== '' && p1 === p2);
    refError.classList.toggle('d-none', !duplicate);
    return !duplicate;
}

function enforceAge18() {
    if (!birthDate || !birthDate.value) return true;
    const dob = new Date(birthDate.value);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
    return age >= 18;
}

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

employeeType?.addEventListener('change', toggleCompanyField);
phoneInput?.addEventListener('input', () => digitsOnly(phoneInput));
document.querySelectorAll('.reference-phone').forEach((el) => {
    el.addEventListener('input', () => digitsOnly(el));
});
panInput?.addEventListener('input', normalizePan);

['reference1_name','reference2_name','reference1_phone','reference2_phone'].forEach((name) => {
    const el = document.querySelector(`[name="${name}"]`);
    el?.addEventListener('input', validateReferenceUniqueness);
});

document.querySelector('form')?.addEventListener('submit', (e) => {
    normalizePan();
    const refsOk = validateReferenceUniqueness();
    const ageOk = enforceAge18();
    if (!ageOk) {
        e.preventDefault();
        alert('Applicant must be at least 18 years old.');
        return;
    }
    if (!refsOk) {
        e.preventDefault();
        return;
    }
    if (submitBtn) submitBtn.disabled = true;
});

document.addEventListener('DOMContentLoaded', () => {
    toggleCompanyField();
    if (birthDate) {
        const today = new Date();
        today.setFullYear(today.getFullYear() - 18);
        birthDate.max = today.toISOString().split('T')[0];
    }
});
</script>

<?php include 'footer.php'; ?>
