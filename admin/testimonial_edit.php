<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// No session_start() here - it's in header.php
include 'db/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: testimonials.php");
    exit;
}

// Fetch testimonial details
$result = mysqli_query($conn, "SELECT * FROM testimonials WHERE id = $id");
$testimonial = mysqli_fetch_assoc($result);

if (!$testimonial) {
    header("Location: testimonials.php");
    exit;
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
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e1;
        transition: all 0.2s;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--slate-900);
        box-shadow: none;
    }

    .btn-submit-pro {
        background: var(--slate-900);
        color: #ffffff !important;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
    }

    .btn-submit-pro:hover {
        background: #334155 !important;
        color: #ffffff !important;
    }

    .btn-cancel {
        font-weight: 600;
        color: var(--slate-600);
        text-decoration: none;
        transition: color 0.2s;
    }

    .btn-cancel:hover {
        color: var(--slate-900);
    }
    
    .image-preview {
        width: 120px;
        height: 120px;
        border: 2px dashed #cbd5e1;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 10px;
        overflow: hidden;
        background: #f8fafc;
    }
    
    .image-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    
    .current-image {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #e2e8f0;
        padding: 5px;
        background: #f8fafc;
    }

    /* Alert Styles */
    .alert {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        border: none;
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .alert-success {
        background-color: #d1fae5;
        color: #065f46;
        border-left: 4px solid #059669;
    }

    .alert-danger {
        background-color: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #dc2626;
    }

    .alert-warning {
        background-color: #ffedd5;
        color: #9a3412;
        border-left: 4px solid #ea580c;
    }

    .alert i {
        font-size: 1.3rem;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .alert-content {
        flex: 1;
    }

    .alert-title {
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 6px;
        display: block;
    }

    .alert ul {
        margin: 8px 0 0 0;
        padding-left: 20px;
    }

    .alert li {
        margin-bottom: 4px;
        font-size: 0.95rem;
    }

    .alert .btn-close {
        position: absolute;
        top: 16px;
        right: 16px;
        cursor: pointer;
        background: none;
        border: none;
        font-size: 1.2rem;
        color: inherit;
        opacity: 0.6;
        transition: opacity 0.2s;
        padding: 4px;
        line-height: 1;
    }

    .alert .btn-close:hover {
        opacity: 1;
    }
</style>


<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <!-- Success Message Display -->
                    <?php if (isset($_SESSION['success_message']) && !empty($_SESSION['success_message'])): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle"></i>
                            <div class="alert-content">
                                <span class="alert-title">Success!</span>
                                <p class="mb-0"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
                            </div>
                            <button type="button" class="btn-close" onclick="this.parentElement.remove(); 
                                <?php unset($_SESSION['success_message']); ?>">Ã—</button>
                        </div>
                        <?php unset($_SESSION['success_message']); ?>
                    <?php endif; ?>

                    <!-- Error Messages Display -->
                    <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle"></i>
                            <div class="alert-content">
                                <span class="alert-title">Error!</span>

                                <?php if (is_array($_SESSION['errors']) && count($_SESSION['errors']) > 1): ?>
                                    <ul>
                                        <?php foreach ($_SESSION['errors'] as $error): ?>
                                            <li><?= htmlspecialchars($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="mb-0">
                                        <?= htmlspecialchars(is_array($_SESSION['errors']) ? $_SESSION['errors'][0] : $_SESSION['errors']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn-close" onclick="this.parentElement.remove(); 
                                <?php unset($_SESSION['errors']); ?>">Ã—</button>
                        </div>
                        <?php unset($_SESSION['errors']); ?>
                    <?php endif; ?>

                    <!-- Error Message Display (single) -->
                    <?php if (isset($_SESSION['error']) && !empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle"></i>
                            <div class="alert-content">
                                <span class="alert-title">Error!</span>
                                <p class="mb-0"><?= htmlspecialchars($_SESSION['error']) ?></p>
                            </div>
                            <button type="button" class="btn-close" onclick="this.parentElement.remove(); 
                                <?php unset($_SESSION['error']); ?>">Ã—</button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <!-- Warning Message Display -->
                    <?php if (isset($_SESSION['warning']) && !empty($_SESSION['warning'])): ?>
                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div class="alert-content">
                                <span class="alert-title">Warning!</span>
                                <p class="mb-0"><?= htmlspecialchars($_SESSION['warning']) ?></p>
                            </div>
                            <button type="button" class="btn-close" onclick="this.parentElement.remove(); 
                                <?php unset($_SESSION['warning']); ?>">Ã—</button>
                        </div>
                        <?php unset($_SESSION['warning']); ?>
                    <?php endif; ?>

                    <div class="mb-4">
                        <h2 class="fw-bold">Edit Testimonial</h2>
                        <p class="text-muted">Update testimonial information</p>
                    </div>

                    <div class="card card-modern">
                        <div class="card-body p-4 p-md-5">

                            <!-- ✅ FORM STARTS HERE -->
                            <form method="POST" action="db/update/testimonial_update.php" enctype="multipart/form-data" novalidate id="testimonialForm">
                                <input type="hidden" name="testimonial_id" value="<?= $testimonial['id'] ?>">

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Partner Name *</label>
                                        <input type="text" name="partner_name"
                                               class="form-control"
                                               id="partnerName"
                                               value="<?= htmlspecialchars($testimonial['partner_name']) ?>" required>
                                        <div class="invalid-feedback">Partner name is required.</div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Designation</label>
                                        <input type="text" name="designation"
                                               class="form-control"
                                               value="<?= htmlspecialchars($testimonial['designation']) ?>">
                                    </div>

                                    <div class="col-12 mb-4">
                                        <label class="form-label">Testimonial Text *</label>
                                        <textarea name="testimonial_text"
                                                  class="form-control"
                                                  id="testimonialText"
                                                  rows="5"
                                                  required><?= htmlspecialchars($testimonial['testimonial_text']) ?></textarea>
                                        <div class="invalid-feedback">Testimonial text is required.</div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Current Image</label><br>
                                        <?php if (!empty($testimonial['partner_img'])): ?>
                                            <img src="../<?= htmlspecialchars($testimonial['partner_img']) ?>"
                                                 class="current-image">
                                            <input type="hidden" name="existing_image"
                                                   value="<?= htmlspecialchars($testimonial['partner_img']) ?>">
                                        <?php else: ?>
                                            <img src="https://via.placeholder.com/100x100" class="current-image">
                                            <input type="hidden" name="existing_image" value="">
                                        <?php endif; ?>

                                        <input type="file" name="partner_img" class="form-control mt-2" id="partnerImg" accept="image/*">
                                        <div class="invalid-feedback" id="partnerImgError">Please upload a valid image (JPG, JPEG, PNG, GIF, WEBP) up to 5 MB.</div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Status</label>
                                        <select name="active" class="form-select">
                                            <option value="1" <?= $testimonial['active'] ? 'selected' : '' ?>>Active</option>
                                            <option value="0" <?= !$testimonial['active'] ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </div>

                                    <div class="col-12 pt-4 border-top">
                                        <button type="submit" class="btn btn-submit-pro">
                                            Update Testimonial
                                        </button>
                                        <a href="testimonials.php" class="btn-cancel ms-3">Cancel</a>
                                    </div>
                                </div>
                            </form>
                            <!-- ✅ FORM END -->


                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '<i class="fas fa-user text-muted opacity-50" style="font-size: 2rem;"></i>';
    }
}
</script>

<script>
// Auto dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});
</script>

<script>
// Client-side validation for inline UI errors
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('testimonialForm');
    const nameInput = document.getElementById('partnerName');
    const textInput = document.getElementById('testimonialText');
    const fileInput = document.getElementById('partnerImg');
    const fileError = document.getElementById('partnerImgError');
    const allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    const maxBytes = 5 * 1024 * 1024;

    function validateName() {
        if (!nameInput) return true;
        const val = (nameInput.value || '').trim();
        if (val === '') {
            nameInput.setCustomValidity('required');
            nameInput.classList.add('is-invalid');
            return false;
        }
        nameInput.setCustomValidity('');
        nameInput.classList.remove('is-invalid');
        return true;
    }

    function validateText() {
        if (!textInput) return true;
        const val = (textInput.value || '').trim();
        if (val === '') {
            textInput.setCustomValidity('required');
            textInput.classList.add('is-invalid');
            return false;
        }
        textInput.setCustomValidity('');
        textInput.classList.remove('is-invalid');
        return true;
    }

    function validateFile() {
        if (!fileInput) return true;
        fileInput.setCustomValidity('');
        fileInput.classList.remove('is-invalid');
        if (!fileInput.files || fileInput.files.length === 0) {
            return true;
        }
        const file = fileInput.files[0];
        const ext = ((file.name || '').split('.').pop() || '').toLowerCase();
        if (!allowed.includes(ext)) {
            fileInput.setCustomValidity('type');
            fileInput.classList.add('is-invalid');
            if (fileError) {
                fileError.textContent = 'Only JPG, JPEG, PNG, GIF & WEBP files are allowed.';
            }
            return false;
        }
        if (file.size > maxBytes) {
            fileInput.setCustomValidity('size');
            fileInput.classList.add('is-invalid');
            if (fileError) {
                fileError.textContent = 'Image size must be less than 5 MB.';
            }
            return false;
        }
        if (fileError) {
            fileError.textContent = 'Please upload a valid image (JPG, JPEG, PNG, GIF, WEBP) up to 5 MB.';
        }
        return true;
    }

    if (nameInput) {
        nameInput.addEventListener('input', validateName);
        nameInput.addEventListener('blur', validateName);
    }
    if (textInput) {
        textInput.addEventListener('input', validateText);
        textInput.addEventListener('blur', validateText);
    }
    if (fileInput) {
        fileInput.addEventListener('change', validateFile);
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            const okName = validateName();
            const okText = validateText();
            const okFile = validateFile();
            if (!okName || !okText || !okFile) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }
});
</script>

<?php include 'footer.php'; ?>
