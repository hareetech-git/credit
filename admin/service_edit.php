<?php
include 'db/config.php';

/* ---------------------------------
| 1. INITIALIZE & FETCH BASIC DATA
----------------------------------*/
$service_id = (int)($_GET['service_id'] ?? 0);
$tab        = $_GET['tab'] ?? 'info';
$msg        = $_GET['msg'] ?? '';
$error      = $_GET['error'] ?? '';

if ($service_id === 0) {
    die("Invalid Service ID");
}

/* ---------------------------------
| 2. FETCH SERVICE DETAILS (For Tab 1)
----------------------------------*/
$service_query = mysqli_query($conn, "SELECT * FROM services WHERE id = $service_id");
$service_data  = mysqli_fetch_assoc($service_query);

if (!$service_data) {
    die("Service not found");
}

// Set initial hierarchy based on saved data
$selected_department = (int)($_GET['department'] ?? 0); 
if ($selected_department === 0) {
    $selected_category = $service_data['category_id'];
    $selected_subcat   = $service_data['sub_category_id'];
    
    $cat_res = mysqli_query($conn, "SELECT department FROM service_categories WHERE id = $selected_category");
    $cat_row = mysqli_fetch_assoc($cat_res);
    $selected_department = $cat_row['department'] ?? 0;
} else {
    $selected_category = (int)($_GET['category'] ?? 0);
    $selected_subcat   = (int)($_GET['sub_category'] ?? 0);
}

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

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
        --primary-accent: #2563eb;
    }

    .content-page { background-color: #fcfcfd; }
    
    .tab-navigation {
        background: #ffffff;
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        overflow: hidden;
    }
    
    .list-group-item {
        border: none;
        padding: 14px 20px;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--slate-600);
        transition: all 0.2s;
    }
    
    .list-group-item.active {
        background-color: #f8fafc !important;
        color: var(--primary-accent) !important;
        font-weight: 700;
        border-left: 4px solid var(--primary-accent);
    }

    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .form-label {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: var(--slate-600);
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.6rem 1rem;
        border: 1px solid #cbd5e1;
    }

    /* Fixed Button Hover States */
    .btn-primary-pro {
        background-color: var(--slate-900) !important;
        color: #ffffff !important;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-primary-pro:hover {
        background-color: #334155 !important;
        color: #ffffff !important;
        opacity: 1;
    }

    .btn-outline-pro {
        background: transparent;
        color: var(--slate-600) !important;
        border: 1px solid var(--slate-200);
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .btn-outline-pro:hover {
        background-color: #f8fafc !important;
        color: var(--slate-900) !important;
        border-color: var(--slate-900);
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <?php if ($msg): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4 py-3">
                    <i class="ri-checkbox-circle-line me-1"></i> <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="tab-navigation sticky-top" style="top:100px">
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
                            $active = ($tab === $key) ? 'active' : '';
                            $link   = "service_edit.php?service_id=$service_id&tab=$key";
                            echo "<a href='$link' class='list-group-item list-group-item-action $active'>$label</a>";
                        }
                        ?>
                        <div class="p-3 border-top text-center">
                            <a href="services.php" class="btn btn-outline-pro btn-sm w-100">
                                <i class="ri-arrow-left-line me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card card-modern shadow-sm">
                        <div class="card-body p-4 p-md-5">

                            <?php if ($tab === 'info') { ?>
                                <h3 class="fw-bold mb-4">Core Information</h3>
                                
                                <div class="bg-light p-4 mb-4 rounded-3 border">
                                    <label class="form-label mb-3">Update Hierarchy (Optional)</label>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <form method="GET">
                                                <input type="hidden" name="service_id" value="<?= $service_id ?>">
                                                <select name="department" class="form-select shadow-none" onchange="this.form.submit()">
                                                    <option value="">Department</option>
                                                    <?php while ($d = mysqli_fetch_assoc($departments)) { ?>
                                                        <option value="<?= $d['id'] ?>" <?= $selected_department == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
                                                    <?php } ?>
                                                </select>
                                            </form>
                                        </div>
                                        <?php if ($selected_department) { ?>
                                        <div class="col-md-4">
                                            <form method="GET">
                                                <input type="hidden" name="service_id" value="<?= $service_id ?>">
                                                <input type="hidden" name="department" value="<?= $selected_department ?>">
                                                <select name="category" class="form-select shadow-none" onchange="this.form.submit()">
                                                    <option value="">Category</option>
                                                    <?php while ($c = mysqli_fetch_assoc($categories)) { ?>
                                                        <option value="<?= $c['id'] ?>" <?= $selected_category == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['category_name']) ?></option>
                                                    <?php } ?>
                                                </select>
                                            </form>
                                        </div>
                                        <?php } ?>
                                        <?php if ($selected_category) { ?>
                                        <div class="col-md-4">
                                            <form method="GET">
                                                <input type="hidden" name="service_id" value="<?= $service_id ?>">
                                                <input type="hidden" name="department" value="<?= $selected_department ?>">
                                                <input type="hidden" name="category" value="<?= $selected_category ?>">
                                                <select name="sub_category" class="form-select shadow-none" onchange="this.form.submit()">
                                                    <option value="">Subcategory</option>
                                                    <?php while ($s = mysqli_fetch_assoc($subcategories)) { ?>
                                                        <option value="<?= $s['id'] ?>" <?= $selected_subcat == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['sub_category_name']) ?></option>
                                                    <?php } ?>
                                                </select>
                                            </form>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <form method="POST" action="db/update/service_update_handler.php">
                                    <input type="hidden" name="type" value="update_service">
                                    <input type="hidden" name="service_id" value="<?= $service_id ?>">
                                    <input type="hidden" name="category_id" value="<?= $selected_category ?: $service_data['category_id'] ?>">
                                    <input type="hidden" name="sub_category_id" value="<?= $selected_subcat ?: $service_data['sub_category_id'] ?>">

                                    <div class="mb-4">
                                        <label class="form-label">Service Title</label>
                                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($service_data['title']) ?>" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Short Summary</label>
                                        <textarea name="short_description" class="form-control" rows="2"><?= htmlspecialchars($service_data['short_description']) ?></textarea>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Full Description</label>
                                        <textarea name="long_description" class="form-control" rows="5"><?= htmlspecialchars($service_data['long_description']) ?></textarea>
                                    </div>
                                    <button class="btn btn-primary-pro px-5">Update Core Info</button>
                                </form>
                            <?php } ?>

                            <?php 
                            // DYNAMIC TABS HANDLING (Tabs 2 to 9)
                            $genericConfigs = [
                                'overview'    => ['title' => 'Service Overview', 't1' => 'Overview Title', 'table' => 'service_overview', 'handler' => 'update_overview'],
                                'features'    => ['title' => 'Service Features', 'table' => 'service_features', 'handler' => 'update_feature', 'k' => 'title', 'v' => 'description'],
                                'eligibility' => ['title' => 'Eligibility Criteria', 'table' => 'service_eligibility_criteria', 'handler' => 'update_eligibility', 'k' => 'criteria_key', 'v' => 'criteria_value'],
                                'documents'   => ['title' => 'Required Documents', 'table' => 'service_documents', 'handler' => 'update_document', 'k' => 'doc_name', 'v' => 'disclaimer'],
                                'fees'        => ['title' => 'Fees & Charges', 'table' => 'service_fees_charges', 'handler' => 'update_fee', 'k' => 'fee_key', 'v' => 'fee_value', 'v_type' => 'input'],
                                'repayment'   => ['title' => 'Loan Repayment', 'table' => 'service_loan_repayment', 'handler' => 'update_repayment', 'k' => 'title', 'v' => 'description'],
                                'why'         => ['title' => 'Why Choose Us', 'table' => 'service_why_choose_us', 'handler' => 'update_why', 'k' => 'title', 'v' => 'description'],
                                'banks'       => ['title' => 'Associated Banks', 'table' => 'service_banks', 'handler' => 'update_bank', 'k' => 'bank_key', 'v' => 'bank_value', 'v_type' => 'input']
                            ];

                            if (array_key_exists($tab, $genericConfigs)) {
                                $config = $genericConfigs[$tab];
                                ?>
                                <h3 class="fw-bold mb-1"><?= $config['title'] ?></h3>
                                <p class="text-muted mb-5">Update the specific data points for this section.</p>

                                <form method="POST" action="db/update/service_update_handler.php">
                                    <input type="hidden" name="type" value="<?= $config['handler'] ?>">
                                    <input type="hidden" name="service_id" value="<?= $service_id ?>">

                                    <?php if($tab === 'overview'): 
                                        $ov_q = mysqli_query($conn, "SELECT * FROM service_overview WHERE service_id = $service_id");
                                        $ov = mysqli_fetch_assoc($ov_q);
                                        $keys = $ov ? json_decode($ov['keys'], true) : [];
                                        $vals = $ov ? json_decode($ov['values'], true) : [];
                                    ?>
                                        <div class="mb-4">
                                            <label class="form-label">Section Heading</label>
                                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($ov['title'] ?? '') ?>" required>
                                        </div>
                                        <div id="overview_container">
                                            <?php for($i=0; $i<max(1, count($keys)); $i++) { ?>
                                                <div class="row mb-3 gx-2 overview-row align-items-center">
                                                    <div class="col-md-5"><input type="text" name="keys[]" class="form-control" placeholder="Key" value="<?= htmlspecialchars($keys[$i] ?? '') ?>"></div>
                                                    <div class="col-md-6"><input type="text" name="values[]" class="form-control" placeholder="Value" value="<?= htmlspecialchars($vals[$i] ?? '') ?>"></div>
                                                    <div class="col-md-1"><button type="button" class="btn btn-outline-danger border-0" onclick="removeRow(this)"><i class="ri-delete-bin-line"></i></button></div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <button type="button" class="btn btn-link text-primary text-decoration-none fw-bold p-0 mb-4" onclick="addRow()">+ Add Key/Value Pair</button>
                                    <?php else: 
                                        $res = mysqli_query($conn, "SELECT * FROM {$config['table']} WHERE service_id = $service_id");
                                    ?>
                                        <div id="dynamic_container">
                                            <?php while($row = mysqli_fetch_assoc($res)) { ?>
                                                <div class="row mb-3 gx-2 input-row align-items-center">
                                                    <div class="col-md-4"><input type="text" name="<?= $config['k'] ?>[]" class="form-control" value="<?= htmlspecialchars($row[$config['k']]) ?>"></div>
                                                    <div class="col-md-7">
                                                        <?php if(isset($config['v_type']) && $config['v_type'] == 'input'): ?>
                                                            <input type="text" name="<?= $config['v'] ?>[]" class="form-control" value="<?= htmlspecialchars($row[$config['v']]) ?>">
                                                        <?php else: ?>
                                                            <textarea name="<?= $config['v'] ?>[]" class="form-control" rows="1"><?= htmlspecialchars($row[$config['v']]) ?></textarea>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-md-1 text-center"><button type="button" class="btn btn-outline-danger border-0" onclick="removeRow(this)"><i class="ri-delete-bin-line"></i></button></div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <button type="button" class="btn btn-link text-primary text-decoration-none fw-bold p-0 mb-4" 
                                                onclick="addGenericRow('dynamic_container', '<?= $config['k'] ?>[]', 'Field', '<?= $config['v'] ?>[]', 'Details', '<?= $config['v_type'] ?? 'textarea' ?>')">
                                            + Add New Row
                                        </button>
                                    <?php endif; ?>
                                    <br><button class="btn btn-primary-pro px-5">Save Changes</button>
                                </form>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function addRow() {
    const container = document.getElementById('overview_container');
    const div = document.createElement('div');
    div.className = 'row mb-3 gx-2 overview-row align-items-center';
    div.innerHTML = `
        <div class="col-md-5"><input type="text" name="keys[]" class="form-control" placeholder="Key" required></div>
        <div class="col-md-6"><input type="text" name="values[]" class="form-control" placeholder="Value" required></div>
        <div class="col-md-1"><button type="button" class="btn btn-outline-danger border-0" onclick="removeRow(this)"><i class="ri-delete-bin-line"></i></button></div>
    `;
    container.appendChild(div);
}

function addGenericRow(containerId, name1, ph1, name2, ph2, type2) {
    const container = document.getElementById(containerId);
    const div = document.createElement('div');
    div.className = 'row mb-3 gx-2 input-row align-items-center';
    let field2 = (type2 === 'textarea') ? `<textarea name="${name2}" class="form-control" rows="1" placeholder="${ph2}"></textarea>` : `<input type="text" name="${name2}" class="form-control" placeholder="${ph2}">`;
    div.innerHTML = `
        <div class="col-md-4"><input type="text" name="${name1}" class="form-control" placeholder="${ph1}" required></div>
        <div class="col-md-7">${field2}</div>
        <div class="col-md-1"><button type="button" class="btn btn-outline-danger border-0" onclick="removeRow(this)"><i class="ri-delete-bin-line"></i></button></div>
    `;
    container.appendChild(div);
}

function removeRow(btn) {
    const row = btn.closest('.overview-row') || btn.closest('.input-row');
    if (row) row.remove();
}
</script>

<?php include 'footer.php'; ?>