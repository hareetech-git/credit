<?php
include 'db/config.php';


// Fetch all certificates
$certificates = mysqli_query($conn, "SELECT * FROM certificates ORDER BY id DESC");

$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['success_message']);

// Image path resolver function for certificates
if (!function_exists('resolveAdminCertificateImage')) {
    function resolveAdminCertificateImage(string $storedPath): string
    {
        $path = trim($storedPath);
        if ($path === '') {
            return '';
        }
        
        // If it's already a full URL, return as is
        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        // Remove leading slash if present
        $path = ltrim($path, '/');

        // Handle different path patterns
        if (strpos($path, 'admin/') === 0) {
            // Path starts with admin/ - remove admin/ prefix
            return substr($path, 6);
        }
        if (strpos($path, 'assets/') === 0) {
            // Path starts with assets/ - use as is (already correct for admin)
            return $path;
        }
        if (strpos($path, 'uploads/') === 0) {
            // Path starts with uploads/ - need to go up one level
            return '../' . $path;
        }
        
        // Default: assume it's in assets/certificates/
        return 'assets/certificates/' . $path;
    }
}
?>

<?php include 'header.php'; ?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<style>
    .certificate-img {
        width: 80px;
        height: 80px;
        object-fit: contain;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 5px;
        background: #f8fafc;
    }
    
    .table th {
        font-weight: 600;
        color: #475569;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
    }
    
    .btn-edit {
        background: #e0f2fe;
        color: #0369a1;
    }
    
    .btn-delete {
        background: #fee2e2;
        color: #991b1b;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h2 class="fw-bold text-dark mb-1">Certificates & Awards</h2>
                            <p class="text-muted small">Manage certificates and awards</p>
                        </div>
                        <a href="certificate_add.php" class="btn btn-submit-pro">
                            <i class="fas fa-plus-circle me-2"></i> Add New Certificate
                        </a>
                    </div>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($success_message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card card-modern">
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Certificate Image</th>
                                            <th>Certificate Name</th>
                                            <th>Added On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($certificates) > 0): ?>
                                            <?php while ($certificate = mysqli_fetch_assoc($certificates)): ?>
                                                <tr>
                                                    <td>#<?= $certificate['id'] ?></td>
                                                    <td>
                                                        <?php if (!empty($certificate['certificate_img'])): ?>
                                                            <?php 
                                                            $img_path = resolveAdminCertificateImage((string) $certificate['certificate_img']);
                                                            ?>
                                                            <img src="<?= htmlspecialchars($img_path) ?>" 
                                                                 alt="<?= htmlspecialchars($certificate['name']) ?>"
                                                                 class="certificate-img"
                                                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/80x80?text=Certificate';">
                                                        <?php else: ?>
                                                            <img src="https://via.placeholder.com/80x80?text=No+Image" 
                                                                 alt="No Image"
                                                                 class="certificate-img">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="fw-bold"><?= htmlspecialchars($certificate['name']) ?></td>
                                                    <td><?= date('d M Y', strtotime($certificate['created_at'])) ?></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <a href="certificate_edit.php?id=<?= $certificate['id'] ?>" 
                                                               class="action-btn btn-edit"
                                                               title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="db/delete/certificate_delete.php?id=<?= $certificate['id'] ?>" 
                                                               class="action-btn btn-delete"
                                                               title="Delete"
                                                               onclick="return confirm('Are you sure you want to delete this certificate?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    <i class="fas fa-award mb-2" style="font-size: 2rem;"></i>
                                                    <p>No certificates added yet. <a href="certificate_add.php">Add your first certificate</a></p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>