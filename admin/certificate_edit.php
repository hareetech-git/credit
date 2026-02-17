<?php
include 'db/config.php';
session_start();

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
?>

<?php include 'header.php'; ?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<style>
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
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <div class="mb-4 text-center text-md-start">
                        <h2 class="fw-bold text-dark mb-1">Edit Certificate</h2>
                        <p class="text-muted small">Update certificate information</p>
                    </div>

                    <div class="card card-modern">
                        <div class="card-body p-4 p-md-5">

                            <form method="POST" action="db/update/certificate_update.php" enctype="multipart/form-data">
                                <input type="hidden" name="certificate_id" value="<?= $certificate['id'] ?>">
                                
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
                                                <img src="../<?= htmlspecialchars($certificate['certificate_img']) ?>" 
                                                     alt="Current Image"
                                                     class="current-image"
                                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/100x100?text=Certificate';">
                                                <input type="hidden" name="existing_image" value="<?= htmlspecialchars($certificate['certificate_img']) ?>">
                                            <?php else: ?>
                                                <img src="https://via.placeholder.com/100x100?text=No+Image" 
                                                     alt="No Image"
                                                     class="current-image">
                                                <input type="hidden" name="existing_image" value="">
                                            <?php endif; ?>
                                        </div>
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
</script>

<?php include 'footer.php'; ?>