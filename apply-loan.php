<?php
include 'includes/connection.php';
require_once 'insert/service_detail.php'; 
session_start();

$is_logged_in = isset($_SESSION['customer_id']);
$cid = $is_logged_in ? $_SESSION['customer_id'] : null;

// --- REDIRECT LOGIC ---
$current_slug = isset($_GET['slug']) ? "slug=" . urlencode($_GET['slug']) : "";
$return_path = "apply-loan.php" . ($current_slug ? "?" . $current_slug : "");

// Detect if the user just wants to register
$is_register_only = isset($_GET['mode']) && $_GET['mode'] === 'register';

$pData = [];
if ($is_logged_in) {
    $res = mysqli_query($conn, "SELECT c.*, cp.* FROM customers c LEFT JOIN customer_profiles cp ON c.id = cp.customer_id WHERE c.id = $cid");
    $pData = mysqli_fetch_assoc($res);
}

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$pre_selected_id = null;
if (!empty($slug)) {
    $slugRes = mysqli_query($conn, "SELECT id FROM services WHERE slug = '" . mysqli_real_escape_string($conn, $slug) . "' LIMIT 1");
    if($sRow = mysqli_fetch_assoc($slugRes)) { $pre_selected_id = $sRow['id']; }
}

include 'includes/header.php';
?>

<style>
    :root { --udhhar-navy: #0b081b; --udhhar-teal: #00d4aa; --udhhar-border: #eef2f6; }
    body { background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); font-family: 'Plus Jakarta Sans', sans-serif; }
    
    .wireframe-stepper { display: flex; justify-content: space-between; margin-bottom: 50px; position: relative; max-width: 600px; margin: 0 auto; }
    .progress-track { position: absolute; top: 20px; left: 0; width: 100%; height: 4px; background: #d1d5db; z-index: 0; }
    .progress-fill { position: absolute; top: 20px; left: 0; width: 0%; height: 4px; background: var(--udhhar-teal); z-index: 1; transition: 0.5s ease; }
    .step-item { position: relative; z-index: 2; text-align: center; width: 60px; }
    .step-blob { width: 44px; height: 44px; background: white; border: 3px solid #d1d5db; border-radius: 14px; margin: 0 auto 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; transition: 0.4s; }
    
    .step-item.active .step-blob { border-color: var(--udhhar-teal); color: var(--udhhar-teal); transform: scale(1.1); box-shadow: 0 10px 20px rgba(0, 212, 170, 0.2); }
    .step-item.completed .step-blob { background: var(--udhhar-teal); border-color: var(--udhhar-teal); color: white; }

    .apply-glass-card { background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(15px); border-radius: 30px; border: 1px solid white; box-shadow: 0 30px 60px rgba(11, 8, 27, 0.1); }
    .form-label { font-weight: 700; font-size: 0.75rem; text-transform: uppercase; color: var(--udhhar-navy); margin-bottom: 8px; display: block; }
    .form-control, .form-select { border-radius: 12px; padding: 12px; border: 2px solid var(--udhhar-border); background: #fbfcfd; font-size: 0.9rem; }
    .form-control:focus { border-color: var(--udhhar-teal); box-shadow: none; background: white; }
    .section-title { font-weight: 800; color: var(--udhhar-navy); border-left: 4px solid var(--udhhar-teal); padding-left: 15px; margin-bottom: 25px; }
    .consent-wrapper { background: #f8fafc; border-radius: 20px; border: 1px solid var(--udhhar-border); }
    .login-banner { background: white; border-radius: 50px; padding: 10px 25px; display: inline-block; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 20px; border: 1px solid var(--udhhar-border); }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <?php if (!empty($_GET['msg'])): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($_GET['err'])): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                <?= htmlspecialchars($_GET['err']) ?>
            </div>
            <?php endif; ?>
            
            <?php if(!$is_logged_in): ?>
            <div class="text-center">
                <div class="login-banner">
                    <span class="text-muted small fw-bold">ALREADY HAVE AN ACCOUNT?</span> 
                    <a href="login.php?redirect=<?= urlencode($return_path) ?>" class="ms-2 fw-bold text-decoration-none" style="color: var(--udhhar-navy);">
                        LOGIN HERE <i class="fas fa-chevron-right ms-1 small"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <div class="wireframe-stepper">
                <div class="progress-track"></div>
                <div class="progress-fill" id="progressLine"></div>
                
                <div class="step-item <?= $is_logged_in ? 'completed' : 'active' ?>" id="s0">
                    <div class="step-blob"><?= $is_logged_in ? '✓' : '1' ?></div>
                    <small class="fw-bold">Auth</small>
                </div>
                
                <div class="step-item <?= $is_logged_in ? 'completed' : '' ?>" id="s1">
                    <div class="step-blob"><?= $is_logged_in ? '✓' : '2' ?></div>
                    <small class="fw-bold">Profile</small>
                </div>

                <?php if(!$is_register_only): ?>
                <div class="step-item <?= $is_logged_in ? 'active' : '' ?>" id="s2">
                    <div class="step-blob">3</div>
                    <small class="fw-bold">Apply</small>
                </div>
                <?php endif; ?>
            </div>

            <div class="apply-glass-card mt-4">
                <form id="loanForm" action="insert/process_universal_apply.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="mode" value="<?= $is_register_only ? 'register' : 'apply' ?>">

                    <div class="p-4 p-md-5">
                        
                        <?php if(!$is_logged_in): ?>
                        <div class="tab" id="tab0">
                            <h4 class="section-title">Step 1: Account Setup</h4>
                            <div class="row g-4">
                                <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" placeholder="Enter full name" required></div>
                                <div class="col-md-6"><label class="form-label">Mobile</label><input type="text" name="phone" class="form-control" placeholder="Enter mobile number" required pattern="[6-9]{1}[0-9]{9}"></div>
                                <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" placeholder="Enter email address" required></div>
                                <?php if($is_register_only): ?>
                                <div class="col-md-6"><label class="form-label">Password</label><input type="password" name="password" class="form-control" placeholder="Enter password" required minlength="8"></div>
                                <?php endif; ?>
                                <div class="col-md-12"><label class="form-label">Birth Date</label><input type="date" name="birth_date" id="birth_date" class="form-control" required></div>
                            </div>
                        </div>

                        <div class="tab d-none" id="tab1">
                            <h4 class="section-title">Step 2: Profile & KYC</h4>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label">PAN Number</label>
                                    <input type="text" name="pan_number" id="pan_number" class="form-control" style="text-transform:uppercase" required pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}">
                                    <div class="invalid-feedback">Enter a valid PAN (e.g., ABCDE1234F).</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Employment</label>
                                    <select name="employee_type" id="employee_type" class="form-select" onchange="toggleCompany(this.value)">
                                        <option value="salaried">Salaried</option>
                                        <option value="business">Business Owner</option>
                                    </select>
                                </div>
                                <div class="col-md-4"><label class="form-label">Monthly Income</label><input type="number" name="monthly_income" class="form-control" required></div>
                                
                                <div class="col-md-12" id="comp_box" style="display:none">
                                    <label class="form-label">Company / Business Name</label>
                                    <input type="text" name="company_name" id="comp_input" class="form-control">
                                </div>

                                <div class="col-md-4"><label class="form-label">State</label><input type="text" name="state" class="form-control" required></div>
                                <div class="col-md-4"><label class="form-label">City</label><input type="text" name="city" class="form-control" required></div>
                                <div class="col-md-4"><label class="form-label">Pin Code</label><input type="text" name="pin_code" class="form-control" required maxlength="6" pattern="[0-9]{6}"></div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Reference 1</label>
                                    <div class="input-group">
                                        <input type="text" name="reference1_name" class="form-control" placeholder="Enter reference 1 name" required pattern="[A-Za-z][A-Za-z\s]{1,}" title="Enter a valid name">
                                        <input type="text" name="reference1_phone" class="form-control" placeholder="Enter reference 1 phone" required maxlength="10" pattern="[6-9]{1}[0-9]{9}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Reference 2</label>
                                    <div class="input-group">
                                        <input type="text" name="reference2_name" class="form-control" placeholder="Enter reference 2 name" required pattern="[A-Za-z][A-Za-z\s]{1,}" title="Enter a valid name">
                                        <input type="text" name="reference2_phone" class="form-control" placeholder="Enter reference 2 phone" required maxlength="10" pattern="[6-9]{1}[0-9]{9}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if(!$is_register_only): ?>
                       <div class="tab <?= ($is_logged_in && !$is_register_only) ? '' : 'd-none' ?>" id="tab2">
                            <h4 class="section-title">Step 3: Loan Submission</h4>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Loan Product</label>
                                    <select name="service_id" id="service_select" class="form-select" onchange="fetchDocs(this.value)" required>
                                        <option value="">-- Choose --</option>
                                        <?php 
                                        $sQuery = mysqli_query($conn, "SELECT id, service_name FROM services");
                                        while($s = mysqli_fetch_assoc($sQuery)): 
                                            $sel = ($pre_selected_id == $s['id']) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $s['id'] ?>" <?= $sel ?>><?= $s['service_name'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-6"><label class="form-label">Amount Required (₹)</label><input type="number" name="requested_amount" class="form-control" required></div>
                                <div class="col-12"><div id="doc_container" class="row g-3"></div></div>
                                
                                <div class="col-12">
                                    <div class="consent-wrapper p-4">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="c2" onchange="toggleSubmit()">
                                            <label class="form-check-label small fw-bold">I agree to <a href="terms.php" target="_blank" rel="noopener">Terms & Conditions</a>.</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="c3" onchange="toggleSubmit()">
                                            <label class="form-check-label small fw-bold">I accept the <a href="privacy.php" target="_blank" rel="noopener">Privacy Policy</a>.</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                            <button type="button" class="btn btn-light" id="prevBtn" onclick="changeTab(-1)">Back</button>
                            <button type="button" class="btn btn-dark px-5 py-3 fw-bold" id="nextBtn" onclick="changeTab(1)" style="border-radius:15px; background:var(--udhhar-navy)">Next Step</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    // Configuration from PHP
    const isLoggedIn = <?= $is_logged_in ? 'true' : 'false' ?>;
    const isRegisterOnly = <?= $is_register_only ? 'true' : 'false' ?>;
    
    // Always start at 0. 
    // If logged in, tabs[0] is the Apply tab. 
    // If guest, tabs[0] is the Auth tab.
    let currentTab = 0; 
    const tabs = document.getElementsByClassName("tab");

    /**
     * Core function to display the correct step
     */
    function showTab(n) {
        if (!tabs[n]) return; // Safety guard: exit if tab index doesn't exist in DOM

        // 1. Hide all tabs to prevent overlaps
        for (let i = 0; i < tabs.length; i++) {
            tabs[i].classList.add("d-none");
        }
        
        // 2. Show the target tab
        tabs[n].classList.remove("d-none");

        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");

        // 3. UI Logic for Logged In Users (Step 3 only)
        if (isLoggedIn && !isRegisterOnly) {
            prevBtn.style.display = "none";
            nextBtn.innerText = "Submit Application";
            
            // Update Stepper to show steps 1 & 2 as completed
            document.getElementById('s0')?.classList.add('completed');
            document.getElementById('s1')?.classList.add('completed');
            document.getElementById('s2')?.classList.add('active');
            document.getElementById("progressLine").style.width = "100%";
            
            toggleSubmit(); // Verify checkboxes for Step 3
        } 
        // 4. UI Logic for Guests (Multi-step)
        else {
            prevBtn.style.display = (n === 0) ? "none" : "block";
            
            let totalSteps = isRegisterOnly ? 1 : 2; 
            document.getElementById("progressLine").style.width = (n / totalSteps) * 100 + "%";
            
            // Sync Stepper Classes
            for(let i=0; i <= 2; i++) {
                let el = document.getElementById('s'+i);
                if(el) {
                    el.classList.remove('completed', 'active');
                    if(i < n) el.classList.add('completed');
                    if(i === n) el.classList.add('active');
                }
            }

            // Determine if this is the final visible tab
            let isFinalTab = (isRegisterOnly && n === 1) || (!isRegisterOnly && n === 2);
            if (isFinalTab) {
                nextBtn.innerText = isRegisterOnly ? "Complete Registration" : "Submit Application";
                if(!isRegisterOnly) toggleSubmit();
            } else {
                nextBtn.innerText = "Next Step";
                nextBtn.disabled = false;
                nextBtn.style.opacity = "1";
            }
        }
    }

    /**
     * Handles Next/Back navigation
     */
    function changeTab(n) {
        if (n === 1 && !validateForm()) return;
        
        // If in "Register Only" mode and finishing Step 2
        if (isRegisterOnly && currentTab === 1 && n === 1) { 
            document.getElementById("loanForm").submit(); 
            return; 
        }

        currentTab += n;

        // If we reached the end of the tabs currently in the DOM
        if (currentTab >= tabs.length) { 
            document.getElementById("loanForm").submit(); 
            return; 
        }
        
        showTab(currentTab);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /**
     * Validates required fields on the current tab
     */
    function validateForm() {
        let inputs = tabs[currentTab].querySelectorAll("input[required], select[required]");
        let valid = true;
        inputs.forEach(i => {
            if (i.name === 'pan_number') {
                const pan = (i.value || '').toUpperCase();
                i.value = pan;
                const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]$/;
                i.setCustomValidity(panRegex.test(pan) ? '' : 'Invalid PAN');
            } else if (i.name === 'birth_date') {
                const dob = new Date(i.value);
                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                const m = today.getMonth() - dob.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
                i.setCustomValidity(age >= 18 ? '' : 'Age must be 18+');
            }
            if(!i.checkValidity()) { 
                i.classList.add("is-invalid"); 
                valid = false; 
            } else { 
                i.classList.remove("is-invalid"); 
            }
        });
        return valid;
    }

    /**
     * Manages the Submit button state based on checkboxes
     */
    function toggleSubmit() {
        // Find checkboxes (c2 is Terms, c3 is Privacy)
        const c2 = document.getElementById("c2");
        const c3 = document.getElementById("c3");
        const btn = document.getElementById("nextBtn");

        // If checkboxes exist (Step 3), validate them
        if (c2 && c3) {
            const isReady = c2.checked && c3.checked;
            btn.disabled = !isReady;
            btn.style.opacity = isReady ? "1" : "0.5";
        }
    }

    /**
     * Dynamic document fetch for Step 3
     */
    async function fetchDocs(sid) {
        if(!sid) return;
        try {
            const res = await fetch(`api/get_service_docs.php?service_id=${sid}`);
            const docs = await res.json();
            const container = document.getElementById('doc_container');
            container.innerHTML = '';
            docs.forEach(d => {
                container.innerHTML += `
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-white text-center">
                            <label class="form-label small">${d.doc_name}</label>
                            <input type="file" name="loan_docs[${d.doc_name.replace(/ /g, '_')}]" class="form-control form-control-sm" required>
                        </div>
                    </div>`;
            });
        } catch (e) { console.error("Error fetching docs", e); }
    }

    /**
     * Toggle company input for business employment type
     */
    function toggleCompany(val) {
        const box = document.getElementById('comp_box');
        const input = document.getElementById('comp_input');
        if(box && input) {
            if(val === 'business') { 
                box.style.display = "block"; 
                input.required = true; 
            } else { 
                box.style.display = "none"; 
                input.required = false; 
                input.value = ""; 
            }
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        const dobInput = document.getElementById('birth_date');
        if (dobInput) {
            const today = new Date();
            today.setFullYear(today.getFullYear() - 18);
            dobInput.max = today.toISOString().split('T')[0];
        }

        const panInput = document.getElementById('pan_number');
        if (panInput) {
            panInput.addEventListener('input', () => {
                panInput.value = panInput.value.toUpperCase();
                const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]$/;
                panInput.setCustomValidity(panRegex.test(panInput.value) ? '' : 'Invalid PAN');
                if (panInput.classList.contains('is-invalid')) {
                    panInput.classList.toggle('is-invalid', !panRegex.test(panInput.value));
                }
            });
        }

        // For Step 3, if a service is already selected (via slug), fetch docs immediately
        const serviceSelect = document.getElementById('service_select');
        if(serviceSelect && serviceSelect.value) {
            fetchDocs(serviceSelect.value);
        }
        
        showTab(currentTab);
    });
</script>

<?php 'footer.php' ?>
