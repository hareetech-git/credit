<?php
// apply-loan.php
include 'includes/connection.php';
require_once 'insert/service_detail.php'; 
session_start();

// --- 1. AUTH & PROFILE GATE ---
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php?err=Please login to apply&type=toast");
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Check 100% completion (Aadhaar, PAN, References, etc.)
$checkQuery = "SELECT c.aadhaar_number, cp.* FROM customers c 
               LEFT JOIN customer_profiles cp ON c.id = cp.customer_id 
               WHERE c.id = $customer_id";
$checkRes = mysqli_query($conn, $checkQuery);
$pData = mysqli_fetch_assoc($checkRes);

$pFields = ['aadhaar_number','pan_number','birth_date','state','city','pin_code','employee_type','monthly_income','reference1_name','reference1_phone'];
$filled = 0;
foreach ($pFields as $f) { if (!empty($pData[$f])) $filled++; }
$percentage = round(($filled / count($pFields)) * 100);

if ($percentage < 100) {
    header("Location: customer/profile.php?err=Complete profile 100% to apply&type=toast");
    exit;
}

// --- 2. GET SERVICE DATA (Slug Logic) ---
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$selected_service = null;

if (!empty($slug)) {
    $data = getServiceData($slug);
    $selected_service = $data['service'];
}

// Always fetch all services for the dropdown fallback
$all_services = mysqli_query($conn, "SELECT id, service_name, slug FROM services ORDER BY service_name ASC");

include 'includes/header.php';
?>

<style>
    :root {
        --service-primary: #130c3b; 
        --service-accent: #00a08e; 
        --service-bg: #f9fafb;
        --service-text: #4b5563;
        --service-border: #e5e7eb;
    }

    .fade-in-up { animation: fadeInUp 0.8s ease-out forwards; opacity: 0; transform: translateY(30px); }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

    .service-hero-section { padding: 60px 0; background: linear-gradient(135deg, #f0fdfa 0%, #fff 100%); }
    .application-box { background: white; padding: 40px; border-radius: 20px; border: 1px solid var(--service-border); box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
    .form-label { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; color: var(--service-primary); margin-bottom: 8px; display: block; }
    .form-control, .form-select { border-radius: 10px; padding: 12px; border: 1px solid var(--service-border); background: #f8fafc; }
    
    .doc-upload-item { border: 2px dashed var(--service-border); padding: 20px; border-radius: 15px; text-align: center; margin-bottom: 20px; transition: 0.3s; }
    .doc-upload-item:hover { border-color: var(--service-accent); background: #f0fdfa; }

    .service-btn-custom { padding: 16px 45px; font-weight: 600; border-radius: 50px; background: var(--service-accent); color: white; border: none; transition: 0.3s; cursor: pointer; }
    .service-btn-custom:hover { background: var(--service-primary); transform: translateY(-3px); }
</style>

<main>
    <section class="service-hero-section fade-in-up">
        <div class="container text-center">
            <span class="badge bg-light text-primary mb-3 px-3 py-2 border">Secure Loan Application</span>
            <h1 class="fw-bold" style="color: var(--service-primary); font-size: 2.5rem;">
                <?= $selected_service ? 'Apply for ' . htmlspecialchars($selected_service['service_name']) : 'Start Your Application' ?>
            </h1>
            <p class="text-muted mx-auto" style="max-width: 600px;">Fast-track your funding by providing the details below. Our team reviews applications in real-time.</p>
        </div>
    </section>

    <section class="section-padding bg-light" style="padding: 60px 0;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 fade-in-up">
                    <div class="application-box">
                        <form action="db/submit_loan.php" method="POST" enctype="multipart/form-data">
                            
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label">Type of Loan Service</label>
                                    <?php if ($selected_service): ?>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($selected_service['service_name']) ?>" readonly>
                                        <input type="hidden" name="service_id" id="service_id" value="<?= $selected_service['id'] ?>">
                                    <?php else: ?>
                                        <select name="service_id" id="service_id" class="form-select" required onchange="loadRequiredDocs(this.value)">
                                            <option value="">-- Choose a Loan Product --</option>
                                            <?php while($s = mysqli_fetch_assoc($all_services)): ?>
                                                <option value="<?= $s['id'] ?>"><?= $s['service_name'] ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Required Loan Amount (₹)</label>
                                    <input type="number" name="requested_amount" class="form-control" placeholder="e.g. 500000" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tenure Preference</label>
                                    <select name="tenure_years" class="form-select" required>
                                        <?php for($i=1; $i<=15; $i++) echo "<option value='$i'>$i Year".($i>1?'s':'')."</option>"; ?>
                                    </select>
                                </div>

                                <div class="col-12 mt-5">
                                    <h4 class="fw-bold mb-4" style="color: var(--service-primary);">Upload Required Documents</h4>
                                    <div class="row" id="doc_container">
                                        <?php 
                                        if($selected_service):
                                            $sid = $selected_service['id'];
                                            $docRes = mysqli_query($conn, "SELECT * FROM service_documents WHERE service_id = $sid");
                                            while($doc = mysqli_fetch_assoc($docRes)):
                                        ?>
                                            <div class="col-md-6">
                                                <div class="doc-upload-item">
                                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                                    <label class="d-block fw-bold small mb-2"><?= $doc['doc_name'] ?></label>
                                                    <input type="file" name="loan_docs[<?= str_replace(' ', '_', $doc['doc_name']) ?>]" class="form-control form-control-sm" required>
                                                </div>
                                            </div>
                                        <?php 
                                            endwhile;
                                        else:
                                            echo '<div class="col-12 text-center py-4 text-muted border rounded">Please select a service above to see document requirements.</div>';
                                        endif; 
                                        ?>
                                    </div>
                                </div>

                                <div class="col-12 text-center mt-5 border-top pt-5">
                                    <button type="submit" class="service-btn-custom shadow-lg">
                                        Submit My Application <i class="fas fa-paper-plane ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
/**
 * Dynamic Document Loader for the Dropdown mode
 */
async function loadRequiredDocs(serviceId) {
    if(!serviceId) return;
    const container = document.getElementById('doc_container');
    container.innerHTML = '<div class="col-12 text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><p>Fetching requirements...</p></div>';

    try {
        const response = await fetch(`api/get_service_docs.php?service_id=${serviceId}`);
        const docs = await response.json();
        
        container.innerHTML = '';
        if(docs.length === 0) {
            container.innerHTML = '<div class="col-12 text-center text-muted py-4 border rounded">No specific documents required. We will use your profile KYC.</div>';
            return;
        }

        docs.forEach(doc => {
            container.innerHTML += `
                <div class="col-md-6">
                    <div class="doc-upload-item">
                        <i class="fas fa-file-image text-muted mb-2 fa-lg"></i>
                        <label class="d-block fw-bold small text-dark mb-2">${doc.doc_name}</label>
                        <input type="file" name="loan_docs[${doc.doc_name.replace(/ /g, '_')}]" class="form-control form-control-sm" required>
                    </div>
                </div>
            `;
        });
    } catch (e) {
        container.innerHTML = '<div class="alert alert-danger">Network error. Please refresh and try again.</div>';
    }
}
</script>

<?php include 'includes/footer.php'; ?>