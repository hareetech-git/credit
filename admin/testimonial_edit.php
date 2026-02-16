<?php
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

if (!function_exists('resolveAdminTestimonialImage')) {
    function resolveAdminTestimonialImage(string $storedPath): string
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
        return 'assets/testimonials/' . $path;
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
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <div class="mb-4 text-center text-md-start">
                        <h2 class="fw-bold text-dark mb-1">Edit Testimonial</h2>
                        <p class="text-muted small">Update testimonial information</p>
                    </div>

                    <div class="card card-modern">
                        <div class="card-body p-4 p-md-5">

                            <form method="POST" action="db/update/testimonial_update.php" enctype="multipart/form-data">
                                <input type="hidden" name="testimonial_id" value="<?= $testimonial['id'] ?>">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Partner Name <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="partner_name"
                                               class="form-control"
                                               value="<?= htmlspecialchars($testimonial['partner_name']) ?>"
                                               required>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Designation</label>
                                        <input type="text"
                                               name="designation"
                                               class="form-control"
                                               value="<?= htmlspecialchars($testimonial['designation']) ?>">
                                    </div>

                                    <div class="col-12 mb-4">
                                        <label class="form-label">Testimonial Text <span class="text-danger">*</span></label>
                                        <textarea name="testimonial_text" 
                                                  class="form-control" 
                                                  rows="5"
                                                  required><?= htmlspecialchars($testimonial['testimonial_text']) ?></textarea>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Current Image</label>
                                        <div class="mb-2">
                                            <?php if (!empty($testimonial['partner_img'])): ?>
                                                <?php 
                                                $img_path = resolveAdminTestimonialImage((string) $testimonial['partner_img']);
                                                ?>
                                                <img src="<?= htmlspecialchars($img_path) ?>" 
                                                     alt="Current Image"
                                                     class="current-image"
                                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/100x100?text=User';">
                                                <input type="hidden" name="existing_image" value="<?= htmlspecialchars($testimonial['partner_img']) ?>">
                                            <?php else: ?>
                                                <img src="https://via.placeholder.com/100x100?text=No+Image" 
                                                     alt="No Image"
                                                     class="current-image">
                                                <input type="hidden" name="existing_image" value="">
                                            <?php endif; ?>
                                        </div>
                                        <label class="form-label">Change Image (optional)</label>
                                        <input type="file" 
                                               name="partner_img" 
                                               class="form-control" 
                                               accept="image/*"
                                               onchange="previewImage(this)">
                                        <small class="text-muted">Leave empty to keep current image</small>
                                        <div class="image-preview" id="imagePreview">
                                            <i class="fas fa-user text-muted opacity-50" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Visibility Status</label>
                                        <select name="active" class="form-select">
                                            <option value="1" <?= $testimonial['active'] == 1 ? 'selected' : '' ?>>Active</option>
                                            <option value="0" <?= $testimonial['active'] == 0 ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </div>

                                    <div class="col-12 mt-2 pt-4 border-top d-flex align-items-center">
                                        <button type="submit" class="btn btn-submit-pro">
                                            <i class="fas fa-save me-2"></i> Update Testimonial
                                        </button>
                                        <a href="testimonials.php" class="btn-cancel ms-4">
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
        preview.innerHTML = '<i class="fas fa-user text-muted opacity-50" style="font-size: 2rem;"></i>';
    }
}
</script>

<?php include 'footer.php'; ?>
