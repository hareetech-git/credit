<?php
include 'db/config.php';
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

$id = (int)$_GET['id'];
if ($id === 0) die("Invalid ID");

// Fetch Main Service Info
$main_sql = "SELECT s.*, c.category_name, sub.sub_category_name 
             FROM services s
             LEFT JOIN service_categories c ON s.category_id = c.id
             LEFT JOIN services_subcategories sub ON s.sub_category_id = sub.id
             WHERE s.id = $id";
$main_res = mysqli_query($conn, $main_sql);
$service  = mysqli_fetch_assoc($main_res);

if (!$service) die("Service not found");

// Fetch Child Data Helper
function get_data($conn, $table, $id) {
    return mysqli_query($conn, "SELECT * FROM $table WHERE service_id = $id");
}

$overview_q = mysqli_query($conn, "SELECT * FROM service_overview WHERE service_id = $id");
$overview   = mysqli_fetch_assoc($overview_q);
?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
        --primary-accent: #2563eb;
    }

    .content-page { background-color: #fcfcfd; }

    /* Premium Navigation Pills */
    .nav-pills .nav-link {
        color: var(--slate-600);
        font-weight: 500;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 4px;
        transition: all 0.2s;
        border: 1px solid transparent;
    }

    .nav-pills .nav-link.active {
        background-color: #f8fafc !important;
        color: var(--primary-accent) !important;
        border-color: var(--slate-200);
        font-weight: 700;
    }

    .nav-pills .nav-link:hover:not(.active) {
        background-color: #f1f5f9;
        color: var(--slate-900);
    }

    /* Card Styling */
    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--slate-900);
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 12px;
        margin-bottom: 20px;
    }

    .info-label {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.05em;
        color: #94a3b8;
    }

    .data-box {
        background-color: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 8px;
        padding: 15px;
        color: var(--slate-900);
        line-height: 1.6;
    }

    .table-view th {
        background-color: #f8fafc;
        color: var(--slate-600);
        font-weight: 600;
        font-size: 0.85rem;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="fw-bold text-dark mb-1"><?= htmlspecialchars($service['title']) ?></h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item text-muted"><?= htmlspecialchars($service['category_name']) ?></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page"><?= htmlspecialchars($service['sub_category_name']) ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="service_edit.php?service_id=<?= $id ?>" class="btn btn-dark rounded-pill px-4">Edit Service</a>
                    <a href="services.php" class="btn btn-outline-secondary rounded-pill px-4">Back to List</a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="card card-modern p-2 sticky-top" style="top: 100px;">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                            <button class="nav-link active text-start" data-bs-toggle="pill" data-bs-target="#v-info">Basic Info</button>
                            <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#v-overview">Overview</button>
                            <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#v-features">Features</button>
                            <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#v-eligible">Eligibility</button>
                            <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#v-docs">Documents</button>
                            <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#v-fees">Fees & Charges</button>
                            <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#v-pay">Repayment</button>
                            <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#v-why">Why Us</button>
                            <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#v-bank">Banks</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card card-modern">
                        <div class="card-body tab-content p-4 p-md-5">

                            <div class="tab-pane fade show active" id="v-info">
                                <h5 class="section-title">General Information</h5>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="info-label">Internal Name</label>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($service['service_name']) ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="info-label">URL Slug</label>
                                        <div class="fw-bold text-primary">/<?= htmlspecialchars($service['slug']) ?></div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="info-label">Short Description</label>
                                    <div class="data-box mt-1"><?= nl2br(htmlspecialchars($service['short_description'])) ?></div>
                                </div>
                                <div class="mb-0">
                                    <label class="info-label">Full Details</label>
                                    <div class="data-box mt-1"><?= nl2br(htmlspecialchars($service['long_description'])) ?></div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="v-overview">
                                <h5 class="section-title">Overview: <?= htmlspecialchars($overview['title'] ?? 'N/A') ?></h5>
                                <div class="table-responsive">
                                    <table class="table table-view border">
                                        <?php 
                                        if ($overview) {
                                            $keys = json_decode($overview['keys'], true);
                                            $vals = json_decode($overview['values'], true);
                                            for ($i=0; $i<count($keys); $i++) { ?>
                                                <tr>
                                                    <th width="35%" class="ps-3"><?= htmlspecialchars($keys[$i]) ?></th>
                                                    <td class="ps-3 text-dark"><?= htmlspecialchars($vals[$i]) ?></td>
                                                </tr>
                                            <?php }
                                        } else { echo "<tr><td class='text-center py-4 text-muted'>No overview data available.</td></tr>"; }
                                        ?>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="v-features">
                                <h5 class="section-title">Key Features</h5>
                                <div class="row">
                                    <?php 
                                    $res = get_data($conn, 'service_features', $id);
                                    while($row = mysqli_fetch_assoc($res)) { ?>
                                        <div class="col-12 mb-3">
                                            <div class="p-3 border rounded shadow-sm bg-white">
                                                <div class="fw-bold text-primary mb-1"><?= htmlspecialchars($row['title']) ?></div>
                                                <div class="text-muted small"><?= htmlspecialchars($row['description']) ?></div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="v-eligible">
                                <h5 class="section-title">Eligibility Criteria</h5>
                                <table class="table table-view border">
                                    <?php 
                                    $res = get_data($conn, 'service_eligibility_criteria', $id);
                                    while($row = mysqli_fetch_assoc($res)) { ?>
                                        <tr>
                                            <th width="35%" class="ps-3"><?= htmlspecialchars($row['criteria_key']) ?></th>
                                            <td class="ps-3"><?= htmlspecialchars($row['criteria_value']) ?></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>

                            <div class="tab-pane fade" id="v-docs">
                                <h5 class="section-title">Required Documents</h5>
                                <div class="list-group list-group-flush border rounded overflow-hidden">
                                    <?php 
                                    $res = get_data($conn, 'service_documents', $id);
                                    while($row = mysqli_fetch_assoc($res)) { ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                            <span class="fw-medium text-dark"><?= htmlspecialchars($row['doc_name']) ?></span>
                                            <span class="badge rounded-pill bg-light text-muted border px-3"><?= htmlspecialchars($row['disclaimer']) ?></span>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="v-fees">
                                <h5 class="section-title">Fees & Charges</h5>
                                <table class="table table-view border">
                                    <?php 
                                    $res = get_data($conn, 'service_fees_charges', $id);
                                    while($row = mysqli_fetch_assoc($res)) { ?>
                                        <tr>
                                            <th width="35%" class="ps-3"><?= htmlspecialchars($row['fee_key']) ?></th>
                                            <td class="ps-3 fw-bold text-success"><?= htmlspecialchars($row['fee_value']) ?></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>

                            <div class="tab-pane fade" id="v-pay">
                                <h5 class="section-title">Repayment Structure</h5>
                                <?php 
                                $res = get_data($conn, 'service_loan_repayment', $id);
                                while($row = mysqli_fetch_assoc($res)) { ?>
                                    <div class="data-box mb-3">
                                        <div class="fw-bold text-dark mb-1"><?= htmlspecialchars($row['title']) ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($row['description']) ?></div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="tab-pane fade" id="v-why">
                                <h5 class="section-title">Value Proposition</h5>
                                <?php 
                                $res = get_data($conn, 'service_why_choose_us', $id);
                                while($row = mysqli_fetch_assoc($res)) { ?>
                                    <div class="alert alert-light border-0 shadow-sm d-flex align-items-start mb-3">
                                        <?php if (!empty($row['image'])): ?>
                                            <img src="<?= '../'.$row['image'] ?>" alt="icon" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; margin-right: 15px;">
                                        <?php else: ?>
                                            <i class="ri-checkbox-circle-fill text-primary me-3 fs-20 mt-1"></i>
                                        <?php endif; ?>
                                        
                                        <div>
                                            <strong class="text-dark d-block mb-1"><?= htmlspecialchars($row['title']) ?></strong>
                                            <div class="text-muted small"><?= htmlspecialchars($row['description']) ?></div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="tab-pane fade" id="v-bank">
                                <h5 class="section-title">Associated Lending Partners</h5>
                                <div class="row">
                                    <?php 
                                    $res = get_data($conn, 'service_banks', $id);
                                    while($row = mysqli_fetch_assoc($res)) { ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="p-3 border rounded shadow-sm h-100 bg-white d-flex flex-column justify-content-center">
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($row['bank_key']) ?></div>
                                                <div class="text-primary small fw-medium"><?= htmlspecialchars($row['bank_value']) ?></div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>