<?php
include 'db/config.php';

/* ---------------------------------
| 1. INITIALIZE CONTEXT
----------------------------------*/
$service_id = (int) ($_GET['service_id'] ?? 0);
$tab = $_GET['tab'] ?? 'info';
$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';

/* ---------------------------------
| 2. HIERARCHY LOGIC
----------------------------------*/
$selected_department = (int) ($_GET['department'] ?? 0);
$selected_category = (int) ($_GET['category'] ?? 0);
$selected_subcat = (int) ($_GET['sub_category'] ?? 0);

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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
        --primary-accent: #2563eb;
    }

    .content-page {
        background-color: #fcfcfd;
    }

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

    .list-group-item.disabled {
        background-color: #ffffff;
        opacity: 0.5;
        cursor: not-allowed;
    }

    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .form-label {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: var(--slate-600);
        margin-bottom: 8px;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 0.6rem 1rem;
        border: 1px solid #cbd5e1;
    }

    .btn-primary-custom,
    .btn-submit-dark,
    .btn-add-new {
        background: var(--slate-900);
        color: #ffffff !important;
        border: 1px solid var(--slate-900);
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-submit-dark:hover,
    .btn-primary-custom:hover,
    .btn-add-new:hover {
        background: #334155 !important;
        border-color: #334155 !important;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <?php if ($msg): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="tab-navigation sticky-top" style="top:100px">
                        <?php
                        $tabs = [
                            'info' => '1. Service Info',
                            'overview' => '2. Overview',
                            'features' => '3. Features',
                            'eligibility' => '4. Eligibility',
                            'documents' => '5. Documents',
                            'fees' => '6. Fees & Charges',
                            'repayment' => '7. Loan Repayment',
                            'why' => '8. Why Choose Us',
                            'banks' => '9. Banks'
                        ];

                        foreach ($tabs as $key => $label) {
                            $active = ($tab === $key) ? 'active' : '';
                            $disabled = ($key !== 'info' && $service_id === 0) ? 'disabled' : '';
                            $link = $disabled ? 'javascript:void(0)' : "service_add.php?service_id=$service_id&tab=$key";

                            if ($key === 'info' && $selected_subcat) {
                                $link .= "&department=$selected_department&category=$selected_category&sub_category=$selected_subcat";
                            }
                            ?>
                            <a href="<?= $link ?>"
                                class="list-group-item list-group-item-action <?= $active ?> <?= $disabled ?>">
                                <?= $label ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card card-modern shadow-sm">
                        <div class="card-body p-4 p-md-5">

                            <?php if ($tab === 'info') { ?>
                                <h3 class="fw-bold mb-4">Service Classification</h3>

                                <form method="GET" class="mb-4">
                                    <label class="form-label">Department</label>
                                    <select name="department" class="form-select" onchange="this.form.submit()">
                                        <option value="">Select Department</option>
                                        <?php while ($d = mysqli_fetch_assoc($departments)) { ?>
                                            <option value="<?= $d['id'] ?>" <?= $selected_department == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
                                        <?php } ?>
                                    </select>
                                </form>

                                <?php if ($selected_department) { ?>
                                    <form method="GET" class="mb-4">
                                        <input type="hidden" name="department" value="<?= $selected_department ?>">
                                        <label class="form-label">Category</label>
                                        <select name="category" class="form-select" onchange="this.form.submit()">
                                            <option value="">Select Category</option>
                                            <?php while ($c = mysqli_fetch_assoc($categories)) { ?>
                                                <option value="<?= $c['id'] ?>" <?= $selected_category == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['category_name']) ?></option>
                                            <?php } ?>
                                        </select>
                                    </form>
                                <?php } ?>

                                <?php if ($selected_category) { ?>
                                    <form method="GET" class="mb-5">
                                        <input type="hidden" name="department" value="<?= $selected_department ?>">
                                        <input type="hidden" name="category" value="<?= $selected_category ?>">
                                        <label class="form-label">Sub Category</label>
                                        <select name="sub_category" class="form-select" onchange="this.form.submit()">
                                            <option value="">Select Subcategory</option>
                                            <?php while ($s = mysqli_fetch_assoc($subcategories)) { ?>
                                                <option value="<?= $s['id'] ?>" <?= $selected_subcat == $s['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($s['sub_category_name']) ?></option>
                                            <?php } ?>
                                        </select>
                                    </form>
                                <?php } ?>

                                <?php if ($selected_subcat) { ?>
                                    <div class="pt-4 border-top">
                                      <form method="POST" action="db/insert/service_handler.php" enctype="multipart/form-data">

                                            <input type="hidden" name="type" value="create_service">
                                            <input type="hidden" name="category_id" value="<?= $selected_category ?>">
                                            <input type="hidden" name="sub_category_id" value="<?= $selected_subcat ?>">

                                            <div class="mb-4">
                                                <label class="form-label">Service Name </label>
                                                <input type="text" name="service_name" class="form-control"
                                                    placeholder="e.g. PL-HDFC-001" required>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <label class="form-label">Service Title (Display Name) <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" id="service_title" name="title" class="form-control"
                                                        placeholder="e.g. Personal Loan" required onkeyup="generateSlug()">
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <label class="form-label">URL Slug</label>
                                                    <input type="text" id="service_slug" name="slug" class="form-control"
                                                        placeholder="personal-loan" oninput="manualSlug()">
                                                    <small class="text-muted" style="font-size: 0.7rem;">Leave empty to
                                                        auto-generate from title.</small>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label">Short Summary</label>
                                                <textarea name="short_description" class="form-control" rows="2"
                                                    placeholder="Visible on cards..."></textarea>
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label">Full Description</label>
                                                <textarea name="long_description" class="form-control" rows="5"
                                                    placeholder="Detailed information..."></textarea>
                                            </div>
                                            <div class="mb-4">
    <label class="form-label">
        Service Hero Image
        <small class="text-muted">(Right side image on service page)</small>
    </label>
    <input type="file" name="hero_image" class="form-control" accept="image/*">
    <small class="text-muted" style="font-size:0.75rem;">
        JPG / PNG recommended (max 2MB)
    </small>
</div>

                                            <button class="btn btn-primary-custom px-5">Save & Next</button>
                                        </form>
                                    </div>
                                <?php } ?>
                            <?php } ?>

                            <?php
                            $genericTabs = [
                                'overview' => ['title' => 'Service Overview', 'has_title' => true, 'h1' => 'Overview Title', 'k' => 'keys[]', 'v' => 'values[]', 'kp' => 'Feature', 'vp' => 'Details', 'type' => 'save_overview'],
                                'features' => ['title' => 'Key Features', 'h1' => 'Feature Title', 'k' => 'title[]', 'v' => 'description[]', 'kp' => 'Title', 'vp' => 'Description', 'type' => 'add_feature'],
                                'eligibility' => ['title' => 'Eligibility Criteria', 'h1' => 'Criteria', 'k' => 'criteria_key[]', 'v' => 'criteria_value[]', 'kp' => 'Criteria', 'vp' => 'Value', 'type' => 'add_eligibility'],
                                'documents' => ['title' => 'Required Documents', 'h1' => 'Document Name', 'k' => 'doc_name[]', 'v' => 'disclaimer[]', 'kp' => 'Name', 'vp' => 'Notes', 'type' => 'add_document'],
                                'fees' => ['title' => 'Fees & Charges', 'h1' => 'Fee Name', 'k' => 'fee_key[]', 'v' => 'fee_value[]', 'kp' => 'Type', 'vp' => 'Amount/%', 'type' => 'add_fee', 'mode' => 'input'],
                                'repayment' => ['title' => 'Repayment Details', 'h1' => 'Option', 'k' => 'title[]', 'v' => 'description[]', 'kp' => 'Title', 'vp' => 'Description', 'type' => 'add_repayment'],
                                'why' => ['title' => 'Why Choose Us', 'has_image' => true, 'h1' => 'Reason', 'k' => 'title[]', 'v' => 'description[]', 'kp' => 'Benefit', 'vp' => 'Details', 'type' => 'add_why'],
                                'banks' => ['title' => 'Partner Banks', 'h1' => 'Bank Name', 'k' => 'bank_key[]', 'v' => 'bank_value[]', 'kp' => 'Bank', 'vp' => 'Offers', 'type' => 'add_bank', 'mode' => 'input','has_image' => true]
                            ];

                            if (array_key_exists($tab, $genericTabs) && $service_id) {
                                $config = $genericTabs[$tab];
                                // Check if we need image upload capabilities
                                $encType = (isset($config['has_image']) && $config['has_image']) ? 'enctype="multipart/form-data"' : '';
                                ?>
                                <h3 class="fw-bold mb-1"><?= $config['title'] ?></h3>
                                <p class="text-muted mb-5">Define the data points for this service section.</p>

                                <form method="POST" action="db/insert/service_handler.php" <?= $encType ?>>
                                    <input type="hidden" name="type" value="<?= $config['type'] ?>">
                                    <input type="hidden" name="service_id" value="<?= $service_id ?>">

                                    <?php if (isset($config['has_title']) && $config['has_title']): ?>
                                        <div class="mb-4">
                                            <label class="form-label">Overview Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control"
                                                placeholder="e.g. Service Highlights" required>
                                        </div>
                                    <?php endif; ?>

                                    <div id="dynamic_container">
                                        <div class="row mb-3 gx-2 input-row align-items-center">

                                            <div class="<?= isset($config['has_image']) ? 'col-md-3' : 'col-md-4' ?>">
                                                <input type="text" name="<?= $config['k'] ?>" class="form-control"
                                                    placeholder="<?= $config['kp'] ?>" required>
                                            </div>

                                            <div class="<?= isset($config['has_image']) ? 'col-md-4' : 'col-md-7' ?>">
                                                <?php if (isset($config['mode']) && $config['mode'] == 'input'): ?>
                                                    <input type="text" name="<?= $config['v'] ?>" class="form-control"
                                                        placeholder="<?= $config['vp'] ?>">
                                                <?php else: ?>
                                                    <textarea name="<?= $config['v'] ?>" class="form-control" rows="1"
                                                        placeholder="<?= $config['vp'] ?>"></textarea>
                                                <?php endif; ?>
                                            </div>

                                            <?php if (isset($config['has_image']) && $config['has_image']): ?>
                                                <div class="col-md-4">
                                                    <input type="file" name="image[]" class="form-control">
                                                </div>
                                            <?php endif; ?>

                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-outline-danger border-0"
                                                    onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button"
                                        class="btn btn-link text-primary text-decoration-none fw-bold p-0 mb-4"
                                        onclick="addGenericRow('dynamic_container', '<?= $config['k'] ?>', '<?= $config['kp'] ?>', '<?= $config['v'] ?>', '<?= $config['vp'] ?>', '<?= $config['mode'] ?? 'textarea' ?>', <?= isset($config['has_image']) ? 'true' : 'false' ?>)">
                                        + Add New Row
                                    </button>
                                    <br>
                                    <button class="btn btn-primary-custom px-5">Save <?= $config['title'] ?></button>
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
    // 1. Slug Generation Logic
    function generateSlug() {
        const title = document.getElementById('service_title').value;
        const slug = convertToSlug(title);
        document.getElementById('service_slug').value = slug;
    }

    // 2. Manual Slug Editing
    function manualSlug() {
        const input = document.getElementById('service_slug');
        input.value = convertToSlug(input.value);
    }

    // Helper: Convert to URL format
    function convertToSlug(text) {
        return text.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-') // Replace non-alphanumeric with hyphen
            .replace(/^-+|-+$/g, '');    // Remove leading/trailing hyphens
    }

    // 3. Dynamic Row Logic
    function addGenericRow(containerId, name1, ph1, name2, ph2, type2, hasImage) {
        const container = document.getElementById(containerId);
        const newRow = document.createElement('div');
        newRow.className = 'row mb-3 gx-2 input-row align-items-center';

        // HTML for the second field (textarea or input)
        let field2HTML = (type2 === 'textarea')
            ? `<textarea name="${name2}" class="form-control" rows="1" placeholder="${ph2}"></textarea>`
            : `<input type="text" name="${name2}" class="form-control" placeholder="${ph2}">`;

        // Layout Logic: If we have an image, columns are 3-4-4-1. If not, 4-7-1.
        if (hasImage) {
            newRow.innerHTML = `
            <div class="col-md-3"><input type="text" name="${name1}" class="form-control" placeholder="${ph1}" required></div>
            <div class="col-md-4">${field2HTML}</div>
            <div class="col-md-4"><input type="file" name="image[]" class="form-control"></div>
            <div class="col-md-1"><button type="button" class="btn btn-outline-danger border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></div>
        `;
        } else {
            newRow.innerHTML = `
            <div class="col-md-4"><input type="text" name="${name1}" class="form-control" placeholder="${ph1}" required></div>
            <div class="col-md-7">${field2HTML}</div>
            <div class="col-md-1"><button type="button" class="btn btn-outline-danger border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></div>
        `;
        }

        container.appendChild(newRow);
    }

    function removeRow(btn) {
        const row = btn.closest('.input-row');
        if (row) row.remove();
    }
</script>

<?php include 'footer.php'; ?>