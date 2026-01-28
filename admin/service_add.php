<?php
include 'db/config.php';

/* ---------------------------------
| 1. INITIALIZE CONTEXT
----------------------------------*/
$service_id = (int)($_GET['service_id'] ?? 0);
$tab        = $_GET['tab'] ?? 'info';
$msg        = $_GET['msg'] ?? '';
$error      = $_GET['error'] ?? '';

/* ---------------------------------
| 2. HIERARCHY LOGIC (Tab 1)
----------------------------------*/
$selected_department = (int)($_GET['department'] ?? 0);
$selected_category   = (int)($_GET['category'] ?? 0);
$selected_subcat     = (int)($_GET['sub_category'] ?? 0);

// Assuming this file exists based on your snippet
include 'pair_indie/get_departments.php'; 

$categories = [];
$subcategories = [];

if ($selected_department) {
    $categories = mysqli_query($conn, "SELECT id, category_name FROM service_categories WHERE department=$selected_department AND active=1");
}

if ($selected_category) {
    $subcategories = mysqli_query($conn, "SELECT id, sub_category_name FROM services_subcategories WHERE category_id=$selected_category AND status='active'");
}
?>

<?php include 'header.php'; ?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <?php if ($msg): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle me-2"></i> Action completed successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-circle me-2"></i> Error: <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">

                <div class="col-md-3">
                    <div class="list-group sticky-top" style="top:90px">
                        <?php 
                        $tabs = [
                            'info'        => '1. Service Info',
                            'overview'    => '2. Overview',
                            'features'    => '3. Features',
                            'eligibility' => '4. Eligibility',
                            'documents'   => '5. Documents',
                            'fees'        => '6. Fees & Charges',
                            'repayment'   => '7. Loan Repayment',
                            'why'         => '8. Why Choose Us',
                            'banks'       => '9. Banks'
                        ];

                        foreach($tabs as $key => $label) {
                            $active   = ($tab === $key) ? 'active' : '';
                            $disabled = ($key !== 'info' && $service_id === 0) ? 'disabled' : '';
                            $link     = "service_add.php?service_id=$service_id&tab=$key";
                            
                            // Keep selection state for Tab 1
                            if($key === 'info' && $selected_subcat) {
                                $link .= "&department=$selected_department&category=$selected_category&sub_category=$selected_subcat";
                            }
                            ?>
                            <a href="<?= $link ?>" class="list-group-item list-group-item-action <?= $active ?> <?= $disabled ?>">
                                <?= $label ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card card-modern shadow-sm">
                        <div class="card-body p-4 p-md-5">

                            <?php if ($tab === 'info') { ?>

                                <h4 class="fw-bold mb-4 text-primary">Service Information</h4>

                                <form method="GET" class="mb-3">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Department</label>
                                    <select name="department" class="form-select" onchange="this.form.submit()">
                                        <option value="">Select Department</option>
                                        <?php while ($d = mysqli_fetch_assoc($departments)) { ?>
                                            <option value="<?= $d['id'] ?>" <?= $selected_department == $d['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($d['name']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </form>

                                <?php if ($selected_department) { ?>
                                    <form method="GET" class="mb-3">
                                        <input type="hidden" name="department" value="<?= $selected_department ?>">
                                        <label class="form-label text-muted small text-uppercase fw-bold">Category</label>
                                        <select name="category" class="form-select" onchange="this.form.submit()">
                                            <option value="">Select Category</option>
                                            <?php while ($c = mysqli_fetch_assoc($categories)) { ?>
                                                <option value="<?= $c['id'] ?>" <?= $selected_category == $c['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($c['category_name']) ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </form>
                                <?php } ?>

                                <?php if ($selected_category) { ?>
                                    <form method="GET" class="mb-4">
                                        <input type="hidden" name="department" value="<?= $selected_department ?>">
                                        <input type="hidden" name="category" value="<?= $selected_category ?>">
                                        <label class="form-label text-muted small text-uppercase fw-bold">Sub Category</label>
                                        <select name="sub_category" class="form-select" onchange="this.form.submit()">
                                            <option value="">Select Subcategory</option>
                                            <?php while ($s = mysqli_fetch_assoc($subcategories)) { ?>
                                                <option value="<?= $s['id'] ?>" <?= $selected_subcat == $s['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($s['sub_category_name']) ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </form>
                                <?php } ?>

                                <?php if ($selected_subcat) { ?>
                                    <hr class="my-4">
                                    <form method="POST" action="db/insert/service_handler.php">
                                        <input type="hidden" name="type" value="create_service">
                                        <input type="hidden" name="category_id" value="<?= $selected_category ?>">
                                        <input type="hidden" name="sub_category_id" value="<?= $selected_subcat ?>">

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Service Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" placeholder="e.g. Home Loan" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Short Description</label>
                                            <textarea name="short_description" class="form-control" rows="2" placeholder="Brief summary for cards"></textarea>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Long Description</label>
                                            <textarea name="long_description" class="form-control" rows="4" placeholder="Detailed description"></textarea>
                                        </div>

                                        <button class="btn btn-primary w-100 py-2">Save & Continue <i class="fa fa-arrow-right ms-2"></i></button>
                                    </form>
                                <?php } ?>

                            <?php } ?>

                            <?php if ($tab === 'overview' && $service_id) { ?>

                                <h4 class="fw-bold mb-3 text-primary">Service Overview</h4>
                                <p class="text-muted small mb-4">Add key highlights like Interest Rates, Tenure, etc.</p>

                                <form method="POST" action="db/insert/service_handler.php">
                                    <input type="hidden" name="type" value="save_overview">
                                    <input type="hidden" name="service_id" value="<?= $service_id ?>">

                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Overview Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control" placeholder="e.g. At a Glance" required>
                                    </div>

                                    <label class="form-label fw-bold mb-2">Highlights</label>
                                    
                                    <div id="overview_container">
                                        <div class="row mb-2 overview-row">
                                            <div class="col-md-5">
                                                <input type="text" name="keys[]" class="form-control" placeholder="Key (e.g. Interest Rate)" required>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" name="values[]" class="form-control" placeholder="Value (e.g. 8.5% p.a.)" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4 mt-2">
                                        <button type="button" class="btn btn-light btn-sm border" onclick="addRow()">
                                            <i class="fa fa-plus text-primary"></i> Add Row
                                        </button>
                                    </div>

                                    <button class="btn btn-primary">Save Overview</button>
                                </form>

                            <?php } ?>

                         <?php if ($tab === 'features' && $service_id) { ?>
    <h4 class="fw-bold mb-3 text-primary">Service Features</h4>
    <form method="POST" action="db/insert/service_handler.php">
        <input type="hidden" name="type" value="add_feature">
        <input type="hidden" name="service_id" value="<?= $service_id ?>">

        <div id="container_features">
            <div class="row mb-3 gx-2 input-row">
                <div class="col-md-4">
                    <input type="text" name="title[]" class="form-control" placeholder="Feature Title" required>
                </div>
                <div class="col-md-7">
                    <textarea name="description[]" class="form-control" rows="1" placeholder="Description"></textarea>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger w-100" onclick="removeRow(this)"><i class="fa fa-times"></i></button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-secondary btn-sm mb-3" 
                onclick="addGenericRow('container_features', 'title[]', 'Feature Title', 'description[]', 'Description')">
            <i class="fa fa-plus"></i> Add Another
        </button>
        <br>
        <button class="btn btn-primary">Save Features</button>
    </form>
<?php } ?>

<?php if ($tab === 'eligibility' && $service_id) { ?>
    <h4 class="fw-bold mb-3 text-primary">Eligibility Criteria</h4>
    <form method="POST" action="db/insert/service_handler.php">
        <input type="hidden" name="type" value="add_eligibility">
        <input type="hidden" name="service_id" value="<?= $service_id ?>">

        <div id="container_eligibility">
            <div class="row mb-3 gx-2 input-row">
                <div class="col-md-4">
                    <input type="text" name="criteria_key[]" class="form-control" placeholder="Criteria (e.g. Age)" required>
                </div>
                <div class="col-md-7">
                    <textarea name="criteria_value[]" class="form-control" rows="1" placeholder="Value (e.g. 21-60)"></textarea>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger w-100" onclick="removeRow(this)"><i class="fa fa-times"></i></button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-secondary btn-sm mb-3" 
                onclick="addGenericRow('container_eligibility', 'criteria_key[]', 'Criteria', 'criteria_value[]', 'Value')">
            <i class="fa fa-plus"></i> Add Another
        </button>
        <br>
        <button class="btn btn-primary">Save Criteria</button>
    </form>
<?php } ?>

<?php if ($tab === 'documents' && $service_id) { ?>
    <h4 class="fw-bold mb-3 text-primary">Required Documents</h4>
    <form method="POST" action="db/insert/service_handler.php">
        <input type="hidden" name="type" value="add_document">
        <input type="hidden" name="service_id" value="<?= $service_id ?>">

        <div id="container_documents">
            <div class="row mb-3 gx-2 input-row">
                <div class="col-md-4">
                    <input type="text" name="doc_name[]" class="form-control" placeholder="Document Name" required>
                </div>
                <div class="col-md-7">
                    <textarea name="disclaimer[]" class="form-control" rows="1" placeholder="Note/Disclaimer"></textarea>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger w-100" onclick="removeRow(this)"><i class="fa fa-times"></i></button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-secondary btn-sm mb-3" 
                onclick="addGenericRow('container_documents', 'doc_name[]', 'Doc Name', 'disclaimer[]', 'Note')">
            <i class="fa fa-plus"></i> Add Another
        </button>
        <br>
        <button class="btn btn-primary">Save Documents</button>
    </form>
<?php } ?>

<?php if ($tab === 'fees' && $service_id) { ?>
    <h4 class="fw-bold mb-3 text-primary">Fees & Charges</h4>
    <form method="POST" action="db/insert/service_handler.php">
        <input type="hidden" name="type" value="add_fee">
        <input type="hidden" name="service_id" value="<?= $service_id ?>">

        <div id="container_fees">
            <div class="row mb-3 gx-2 input-row">
                <div class="col-md-4">
                    <input type="text" name="fee_key[]" class="form-control" placeholder="Fee Name" required>
                </div>
                <div class="col-md-7">
                    <input type="text" name="fee_value[]" class="form-control" placeholder="Amount / %">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger w-100" onclick="removeRow(this)"><i class="fa fa-times"></i></button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-secondary btn-sm mb-3" 
                onclick="addGenericRow('container_fees', 'fee_key[]', 'Fee Name', 'fee_value[]', 'Amount', 'input')">
            <i class="fa fa-plus"></i> Add Another
        </button>
        <br>
        <button class="btn btn-primary">Save Fees</button>
    </form>
<?php } ?>

<?php if ($tab === 'repayment' && $service_id) { ?>
    <h4 class="fw-bold mb-3 text-primary">Loan Repayment</h4>
    <form method="POST" action="db/insert/service_handler.php">
        <input type="hidden" name="type" value="add_repayment">
        <input type="hidden" name="service_id" value="<?= $service_id ?>">

        <div id="container_repayment">
            <div class="row mb-3 gx-2 input-row">
                <div class="col-md-4">
                    <input type="text" name="title[]" class="form-control" placeholder="Repayment Title" required>
                </div>
                <div class="col-md-7">
                    <textarea name="description[]" class="form-control" rows="1" placeholder="Details"></textarea>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger w-100" onclick="removeRow(this)"><i class="fa fa-times"></i></button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-secondary btn-sm mb-3" 
                onclick="addGenericRow('container_repayment', 'title[]', 'Title', 'description[]', 'Details')">
            <i class="fa fa-plus"></i> Add Another
        </button>
        <br>
        <button class="btn btn-primary">Save Repayment</button>
    </form>
<?php } ?>

<?php if ($tab === 'why' && $service_id) { ?>
    <h4 class="fw-bold mb-3 text-primary">Why Choose Us</h4>
    <form method="POST" action="db/insert/service_handler.php">
        <input type="hidden" name="type" value="add_why">
        <input type="hidden" name="service_id" value="<?= $service_id ?>">

        <div id="container_why">
            <div class="row mb-3 gx-2 input-row">
                <div class="col-md-4">
                    <input type="text" name="title[]" class="form-control" placeholder="Reason Title" required>
                </div>
                <div class="col-md-7">
                    <textarea name="description[]" class="form-control" rows="1" placeholder="Reason Description"></textarea>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger w-100" onclick="removeRow(this)"><i class="fa fa-times"></i></button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-secondary btn-sm mb-3" 
                onclick="addGenericRow('container_why', 'title[]', 'Title', 'description[]', 'Description')">
            <i class="fa fa-plus"></i> Add Another
        </button>
        <br>
        <button class="btn btn-primary">Save Reasons</button>
    </form>
<?php } ?>

<?php if ($tab === 'banks' && $service_id) { ?>
    <h4 class="fw-bold mb-3 text-primary">Associated Banks</h4>
    <form method="POST" action="db/insert/service_handler.php">
        <input type="hidden" name="type" value="add_bank">
        <input type="hidden" name="service_id" value="<?= $service_id ?>">

        <div id="container_banks">
            <div class="row mb-3 gx-2 input-row">
                <div class="col-md-4">
                    <input type="text" name="bank_key[]" class="form-control" placeholder="Bank Name" required>
                </div>
                <div class="col-md-7">
                    <input type="text" name="bank_value[]" class="form-control" placeholder="Details/Offers">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger w-100" onclick="removeRow(this)"><i class="fa fa-times"></i></button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-secondary btn-sm mb-3" 
                onclick="addGenericRow('container_banks', 'bank_key[]', 'Bank Name', 'bank_value[]', 'Details', 'input')">
            <i class="fa fa-plus"></i> Add Another
        </button>
        <br>
        <button class="btn btn-primary">Save Banks</button>
    </form>
<?php } ?>


<script>
// Logic for Tab 2 (Overview)
function addRow() {
    const container = document.getElementById('overview_container');
    const newRow = document.createElement('div');
    newRow.className = 'row mb-2 overview-row';
    newRow.innerHTML = `
        <div class="col-md-5"><input type="text" name="keys[]" class="form-control" placeholder="Key" required></div>
        <div class="col-md-5"><input type="text" name="values[]" class="form-control" placeholder="Value" required></div>
        <div class="col-md-2"><button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)"><i class="fa fa-trash"></i></button></div>
    `;
    container.appendChild(newRow);
}

// Logic for Tabs 3-9 (Generic)
function addGenericRow(containerId, name1, ph1, name2, ph2, type2 = 'textarea') {
    const container = document.getElementById(containerId);
    const newRow = document.createElement('div');
    newRow.className = 'row mb-3 gx-2 input-row';
    
    // Create the second input based on type (input vs textarea)
    let field2HTML = '';
    if (type2 === 'textarea') {
        field2HTML = `<textarea name="${name2}" class="form-control" rows="1" placeholder="${ph2}"></textarea>`;
    } else {
        field2HTML = `<input type="text" name="${name2}" class="form-control" placeholder="${ph2}">`;
    }

    newRow.innerHTML = `
        <div class="col-md-4">
            <input type="text" name="${name1}" class="form-control" placeholder="${ph1}" required>
        </div>
        <div class="col-md-7">
            ${field2HTML}
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger w-100" onclick="removeRow(this)"><i class="fa fa-times"></i></button>
        </div>
    `;
    container.appendChild(newRow);
}

// Universal Remove Function
function removeRow(btn) {
    // Find the closest parent that is a row (either overview-row or input-row)
    const row = btn.closest('.row'); 
    row.remove();
}
</script>

                        </div> </div> </div> </div> </div> </div> </div> <script>
function addRow() {
    const container = document.getElementById('overview_container');
    const newRow = document.createElement('div');
    newRow.className = 'row mb-2 overview-row';
    newRow.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="keys[]" class="form-control" placeholder="Key" required>
        </div>
        <div class="col-md-5">
            <input type="text" name="values[]" class="form-control" placeholder="Value" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newRow);
}

function removeRow(btn) {
    const row = btn.closest('.overview-row');
    const totalRows = document.querySelectorAll('.overview-row').length;
    
    // Optional: Keep at least one row, remove this check if you want to allow 0 rows
    if (totalRows > 1) {
        row.remove();
    } else {
        // Clear values instead of removing the last row
        row.querySelector('input[name="keys[]"]').value = '';
        row.querySelector('input[name="values[]"]').value = '';
    }
}
</script>

<?php include 'footer.php'; ?>