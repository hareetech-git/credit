<?php
include 'db/config.php';

/* ======================
    CHECK EDIT MODE
====================== */
$isEdit = false;
$category = null;

if (isset($_GET['id'])) {
    $isEdit = true;
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $category = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$category) {
        header("Location: categories.php");
        exit;
    }
}
?>

<?php include 'header.php'; ?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --border-color: #e2e8f0;
    }
    .content-page { background-color: #fcfcfd; }
    .card-modern {
        border: 1px solid var(--border-color);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .form-label {
        font-weight: 600;
        color: var(--slate-900);
        font-size: 0.875rem;
        margin-bottom: 8px;
    }
    .form-control {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e1;
        transition: all 0.2s;
    }
    .form-control:focus {
        border-color: var(--slate-900);
        box-shadow: none;
    }
    .btn-submit {
        padding: 0.8rem 2.5rem;
        font-weight: 600;
        border-radius: 8px;
        background-color: var(--slate-900);
        border: none;
        color: white;
    }
    .preview-img-wrapper {
        width: 140px;
        height: 140px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .preview-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <div class="mb-4 text-center text-md-start">
                        <h2 class="fw-bold text-dark"><?= $isEdit ? 'Update Category' : 'New Category' ?></h2>
                        <p class="text-muted">Organize your services by defining a clear category.</p>
                    </div>

                    <div class="card card-modern">
                        <div class="card-body p-4 p-md-5">
                            
                            <div id="alert-container" style="display:none" class="alert py-3 mb-4"></div>

                            <form id="category-form" enctype="multipart/form-data">
                                <input type="hidden" id="category-id" value="<?= $isEdit ? $category['id'] : '' ?>">

                                <div class="row">
                                    
                                    <div class="col-md-7 mb-4">
                                        <label class="form-label">Category Name</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="category-name" 
                                               placeholder="e.g. Bridal Mehndi"
                                               value="<?= $isEdit ? htmlspecialchars($category['name']) : '' ?>" 
                                               required>
                                        <small class="text-muted mt-2 d-block">This name will be visible to your customers.</small>
                                    </div>

                                    <div class="col-md-5 mb-4">
                                        <label class="form-label">Display Image</label>
                                        <input type="file" 
                                               class="form-control mb-3" 
                                               id="category-image" 
                                               accept="image/*">
                                        
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if ($isEdit && $category['img']) { ?>
                                                <div id="old-image-box text-center">
                                                    <div class="preview-img-wrapper">
                                                        <img src="<?= $category['img'] ?>">
                                                    </div>
                                                    <span class="text-muted small d-block mt-1">Current Image</span>
                                                </div>
                                            <?php } ?>

                                            <div id="image-preview"></div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4 pt-3 border-top">
                                        <button type="button" id="submit-btn" class="btn btn-submit">
                                            <?= $isEdit ? 'Save Changes' : 'Create Category' ?>
                                        </button>
                                        <a href="categories.php" class="btn btn-link text-muted ms-2 text-decoration-none fw-medium">Cancel</a>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
const submitBtn   = document.getElementById('submit-btn');
const alertBox    = document.getElementById('alert-container');
const imageInput  = document.getElementById('category-image');
const imagePreview = document.getElementById('image-preview');
const categoryId = document.getElementById('category-id').value;
const isEdit = categoryId !== '';

let imageFile = null;

imageInput.addEventListener('change', function () {
    imagePreview.innerHTML = '';
    imageFile = null;

    // Hide old image if new one is selected
    const oldImageBox = document.getElementById('old-image-box');
    if (oldImageBox) oldImageBox.style.display = 'none';

    const file = this.files[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        showError('Only image files allowed');
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        showError('Image must be less than 2MB');
        return;
    }

    imageFile = file;
    const reader = new FileReader();
    reader.onload = e => {
        imagePreview.innerHTML = `
            <div class="preview-img-wrapper">
                <img src="${e.target.result}">
            </div>
            <span class="text-muted small d-block mt-1">New Selection</span>
        `;
    };
    reader.readAsDataURL(file);
});

submitBtn.addEventListener('click', async () => {
    const name = document.getElementById('category-name').value.trim();
    if (!name) { showError('Category name is required'); return; }

    submitBtn.disabled = true;
    submitBtn.innerText = 'Syncing...';

    const formData = new FormData();
    formData.append('name', name);
    if (imageFile) formData.append('img', imageFile);
    if (isEdit) formData.append('id', categoryId);

    const url = isEdit ? 'db/category-update.php' : 'db/category-store.php';

    try {
        const response = await fetch(url, { method: 'POST', body: formData });
        const result = await response.json();

        if (result.success) {
            showSuccess(result.message);
            setTimeout(() => { window.location.href = 'categories.php'; }, 1500);
        } else {
            showError(result.message);
            submitBtn.disabled = false;
            submitBtn.innerText = 'Try Again';
        }
    } catch {
        showError('Network error occurred');
        submitBtn.disabled = false;
    }
});

function showError(msg) {
    alertBox.className = 'alert alert-danger py-3';
    alertBox.textContent = msg;
    alertBox.style.display = 'block';
}

function showSuccess(msg) {
    alertBox.className = 'alert alert-success py-3';
    alertBox.textContent = msg;
    alertBox.style.display = 'block';
}
</script>