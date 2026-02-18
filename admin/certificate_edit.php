<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// No session_start() here - it's in header.php
include 'db/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: certificates.php");
    exit;
}

// Fetch certificate details
$result = mysqli_query($conn, "SELECT * FROM certificates WHERE id = $id");
$certificate = mysqli_fetch_assoc($result);

if (!$certificate) {
    header("Location: certificates.php");
    exit;
}

// Image path resolver function for certificates
if (!function_exists('resolveAdminCertificateImage')) {
    function resolveAdminCertificateImage(string $storedPath): string
    {
        $path = trim($storedPath);
        if ($path === '') {
            return '';
        }
        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }
        $path = ltrim($path, '/');
        if (strpos($path, 'admin/') === 0) {
            return substr($path, 6);
        }
        if (strpos($path, 'assets/') === 0) {
            return $path;
        }
        if (strpos($path, 'uploads/') === 0) {
            return '../' . $path;
        }
        return 'assets/certificates/' . $path;
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
        width: 150px;
        height: 150px;
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
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
        object-fit: contain;
    }
    
    .current-image {
        width: 100px;
        height: 100px;
        object-fit: contain;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
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
                                <?php unset($_SESSION['success_message']); ?>">×</button>
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
                                <?php unset($_SESSION['errors']); ?>">×</button>
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
                                <?php unset($_SESSION['error']); ?>">×</button>
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
                                <?php unset($_SESSION['warning']); ?>">×</button>
                        </div>
                        <?php unset($_SESSION['warning']); ?>
                    <?php endif; ?>
                    
                    <div class="mb-4 text-center text-md-start">
                        <h2 class="fw-bold text-dark mb-1">Update Certificate</h2>
                        <p class="text-muted small">Modify certificate details for <span class="fw-bold text-dark">#<?= $certificate['id'] ?></span></p>
                    </div>

                    <div class="card card-modern">
                        <div class="card-body p-4 p-md-5">

                            <form method="POST" action="db/update/certificate_update.php" enctype="multipart/form-data" novalidate id="certificateForm">
                                <input type="hidden" name="certificate_id" value="<?= $certificate['id'] ?>">
                                <input type="hidden" name="existing_image" value="<?= htmlspecialchars($certificate['certificate_img'] ?? '') ?>">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Certificate Name <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="name"
                                               class="form-control"
                                               id="certName"
                                               value="<?= htmlspecialchars($certificate['name']) ?>"
                                               required>
                                        <div class="invalid-feedback">Certificate name is required.</div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Current Image</label>
                                        <div class="mb-2">
                                            <?php if (!empty($certificate['certificate_img'])): ?>
                                                <?php 
                                                $img_path = resolveAdminCertificateImage((string) $certificate['certificate_img']);
                                                ?>
                                                <img src="<?= htmlspecialchars($img_path) ?>" 
                                                     alt="Current Image"
                                                     class="current-image"
                                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/100x100?text=Certificate';">
                                            <?php else: ?>
                                                <img src="https://via.placeholder.com/100x100?text=No+Image" 
                                                     alt="No Image"
                                                     class="current-image">
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Change Image (optional)</label>
                                        <input type="file" 
                                               name="certificate_img" 
                                               class="form-control"
                                               id="certImage"
                                               accept="image/*"
                                               onchange="previewImage(this)">
                                        <div class="invalid-feedback" id="certImageError">Please upload a valid image (JPG, JPEG, PNG, GIF, WEBP) up to 5 MB.</div>
                                        <small class="text-muted d-block mt-1">Leave empty to keep current image</small>
                                        <div class="image-preview" id="imagePreview">
                                            <i class="fas fa-image text-muted opacity-50" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-2 pt-4 border-top d-flex align-items-center">
                                        <button type="submit" class="btn btn-submit-pro">
                                            <i class="fas fa-save me-2"></i> Update Certificate
                                        </button>
                                        <a href="certificates.php" class="btn-cancel ms-4">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </a>
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
        preview.innerHTML = '<i class="fas fa-image text-muted opacity-50" style="font-size: 2rem;"></i>';
    }
}

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

// Client-side validation for inline UI errors
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('certificateForm');
    const nameInput = document.getElementById('certName');
    const fileInput = document.getElementById('certImage');
    const fileError = document.getElementById('certImageError');
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
                fileError.textContent = 'File size too large. Maximum allowed size is 5 MB.';
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
    if (fileInput) {
        fileInput.addEventListener('change', validateFile);
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            const okName = validateName();
            const okFile = validateFile();
            if (!okName || !okFile) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }
});
</script>

<?php include 'footer.php'; ?>
