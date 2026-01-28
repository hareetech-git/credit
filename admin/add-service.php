<?php
include 'db/config.php';
session_start();

/* ======================
    CHECK EDIT MODE
====================== */
$isEdit = false;
$service = null;
$serviceImages = [];

if (isset($_GET['id'])) {
    $isEdit = true;
    $id = (int)$_GET['id'];

    $service = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM services WHERE id = $id")
    );

    if (!$service) {
        header("Location: services.php");
        exit;
    }

    $serviceImages = mysqli_query(
        $conn, "SELECT * FROM services_imgs WHERE service_id = $id"
    );
}

/* FETCH CATEGORIES */
$categories = mysqli_query(
    $conn, "SELECT id, name FROM categories ORDER BY name ASC"
);
?>

<?php include 'header.php'; ?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<style>
    .card-modern { border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
    .form-label { font-weight: 600; color: #1e293b; font-size: 0.875rem; }
    .form-control { border-radius: 8px; padding: 0.6rem 1rem; border: 1px solid #cbd5e1; }
    .form-control:focus { border-color: #2563eb; box-shadow: none; }
    .btn-submit { padding: 0.75rem 2rem; font-weight: 600; border-radius: 8px; }
    .img-preview-container { position: relative; width: 100px; height: 100px; }
    .img-preview-container img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-4">

            <div class="row">
                <div class="col-12">
                    <div class="card card-modern">
                        <div class="card-header bg-white border-bottom p-4">
                            <h4 class="mb-0 fw-bold"><?= $isEdit ? 'Edit Service Details' : 'Create New Service' ?></h4>
                        </div>

                        <div class="card-body p-4">
                            <div id="alert-container" style="display:none" class="alert"></div>

                            <form id="service-form" enctype="multipart/form-data">
                                <input type="hidden" id="service_id" value="<?= $isEdit ? $service['id'] : '' ?>">

                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label">Category</label>
                                        <select class="form-control" id="category_id" required>
                                            <option value="">Select Category</option>
                                            <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
                                                <option value="<?= $cat['id'] ?>"
                                                    <?= $isEdit && $cat['id'] == $service['category_id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat['name']) ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label">Service Name</label>
                                        <input type="text" class="form-control" id="service_name" 
                                               value="<?= $isEdit ? htmlspecialchars($service['name']) : '' ?>" required>
                                    </div>

                                    <div class="col-lg-12 mb-3">
                                        <label class="form-label">Service Description</label>
                                        <textarea class="form-control" id="description" rows="4" 
                                                  placeholder="Provide a detailed description of the service..."><?= $isEdit ? htmlspecialchars($service['description'] ?? '') : '' ?></textarea>
                                    </div>

                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label">Estimated Time</label>
                                        <input type="text" class="form-control" id="estimated_time" 
                                               placeholder="e.g. 2 Hours" value="<?= $isEdit ? htmlspecialchars($service['estimated_time'] ?? '') : '' ?>">
                                    </div>

                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label">Base Price</label>
                                        <input type="number" class="form-control" id="price" 
                                               value="<?= $isEdit ? $service['price'] : '' ?>" required>
                                    </div>

                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label">Discount Price</label>
                                        <input type="number" class="form-control" id="discount_price" 
                                               value="<?= $isEdit ? $service['discount_price'] : '' ?>">
                                    </div>

                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label">Initial Rating</label>
                                        <input type="number" step="0.1" max="5" class="form-control" id="rating" 
                                               value="<?= $isEdit ? $service['rating'] : '' ?>">
                                    </div>

                                    <div class="col-lg-12 mb-3">
                                        <label class="form-label">Upload Assets</label>
                                        <input type="file" class="form-control" id="service_images" multiple accept="image/*">
                                        <div id="image-preview" class="d-flex flex-wrap gap-2 mt-3"></div>
                                    </div>

                                    <?php if ($isEdit) { ?>
                                    <div class="col-lg-12 mb-4">
                                        <label class="form-label d-block">Current Gallery</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php while ($img = mysqli_fetch_assoc($serviceImages)) { ?>
                                                <div class="img-preview-container" id="img-box-<?= $img['id'] ?>">
                                                    <img src="<?= $img['img'] ?>">
                                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-image-btn" 
                                                            data-id="<?= $img['id'] ?>" style="padding:0px 5px; font-size:10px;">✕</button>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <div class="col-lg-12">
                                        <hr class="my-4">
                                        <button type="button" id="submit-btn" class="btn btn-dark btn-submit px-5">
                                            <?= $isEdit ? 'Update Changes' : 'Publish Service' ?>
                                        </button>
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
const submitBtn = document.getElementById('submit-btn');
const alertBox = document.getElementById('alert-container');
const imageInput = document.getElementById('service_images');
const previewBox = document.getElementById('image-preview');
const serviceId = document.getElementById('service_id').value;

let imageFiles = [];

/* IMAGE PREVIEW LOGIC */
imageInput.addEventListener('change', () => {
    previewBox.innerHTML = '';
    imageFiles = Array.from(imageInput.files);
    imageFiles.forEach(file => {
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'img-preview-container';
            div.innerHTML = `<img src="${e.target.result}">`;
            previewBox.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});

/* SUBMIT LOGIC */
submitBtn.addEventListener('click', async () => {
    submitBtn.disabled = true;
    submitBtn.innerText = 'Processing...';

    const formData = new FormData();
    formData.append('id', serviceId);
    formData.append('category_id', document.getElementById('category_id').value);
    formData.append('name', document.getElementById('service_name').value.trim());
    formData.append('description', document.getElementById('description').value.trim());
    formData.append('estimated_time', document.getElementById('estimated_time').value.trim());
    formData.append('price', document.getElementById('price').value);
    formData.append('discount_price', document.getElementById('discount_price').value);
    formData.append('rating', document.getElementById('rating').value);

    imageFiles.forEach(file => { formData.append('images[]', file); });

    try {
        const res = await fetch('db/service-store.php', { method: 'POST', body: formData });
        const result = await res.json();

        if (result.success) {
            alertBox.className = 'alert alert-success py-3';
            alertBox.textContent = result.message;
            alertBox.style.display = 'block';
            setTimeout(() => { window.location.href = 'services.php'; }, 1500);
        } else {
            throw new Error(result.message);
        }
    } catch (err) {
        alertBox.className = 'alert alert-danger py-3';
        alertBox.textContent = err.message || 'Operation failed';
        alertBox.style.display = 'block';
        submitBtn.disabled = false;
        submitBtn.innerText = 'Try Again';
    }
});

/* DELETE IMAGE LOGIC */
document.querySelectorAll('.delete-image-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('Permanently remove this image?')) return;
        const imageId = btn.dataset.id;
        try {
            const res = await fetch('db/service-image-delete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: imageId })
            });
            const data = await res.json();
            if (data.success) { document.getElementById('img-box-' + imageId).remove(); }
        } catch { alert('Network error'); }
    });
});
</script>