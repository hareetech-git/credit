<?php
include 'db/config.php';
session_start(); // Add session start for messages

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

// Get messages from session
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];

// Clear messages after retrieving
unset($_SESSION['success_message']);
unset($_SESSION['errors']);

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
        background-color: #e8f5e9;
        color: #1e4620;
        border-left: 4px solid #2e7d32;
    }
    
    .alert-danger {
        background-color: #ffebee;
        color: #621b1b;
        border-left: 4px solid #c62828;
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
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle"></i>
                            <div class="alert-content">
                                <span class="alert-title">Success!</span>
                                <p class="mb-0"><?= htmlspecialchars($success_message) ?></p>
                            </div>
                            <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Error Messages Display -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle"></i>
                            <div class="alert-content">
                                <span class="alert-title">Error!</span>
                                
                                <?php if (is_array($errors) && count($errors) > 1): ?>
                                    <!-- Multiple errors as list -->
                                    <ul>
                                        <?php foreach ($errors as $error): ?>
                                            <li><?= htmlspecialchars($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <!-- Single error -->
                                    <p class="mb-0">
                                        <?= htmlspecialchars(is_array($errors) ? $errors[0] : $errors) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-4 text-center text-md-start">
                        <h2 class="fw-bold text-dark mb-1">Update Certificate</h2>
                        <p class="text-muted small">Modify certificate details for <span class="fw-bold text-dark">#<?= $certificate['id'] ?></span></p>
                    </div>

                    <div class="card card-modern">
                        <div class="card-body p-4 p-md-5">

                            <form method="POST" action="db/update/certificate_update.php" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?= $certificate['id'] ?>">
                                <input type="hidden" name="existing_image" value="<?= htmlspecialchars($certificate['certificate_img'] ?? '') ?>">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Certificate Name <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="name"
                                               class="form-control"
                                               value="<?= htmlspecialchars($certificate['name']) ?>"
                                               required>
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
                                               accept="image/*"
                                               onchange="previewImage(this)">
                                        <small class="text-muted">Leave empty to keep current image</small>
                                        <div class="image-preview" id="imagePreview">
                                            <i class="fas fa-image text-muted opacity-50" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-2 pt-4 border-top d-flex align-items-center">
                                        <button type="submit" class="btn btn-submit-pro">
                                            <i class="fas fa-save me-2"></i> Update Certificate
                                        </button>
                                        <a href="certificates.php" class="btn-cancel ms-4">
                                            Cancel
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

// Manual dismiss function for alerts
document.addEventListener('DOMContentLoaded', function() {
    const closeButtons = document.querySelectorAll('.alert .btn-close');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.alert').remove();
        });
    });
});
</script>

<?php include 'footer.php'; ?>