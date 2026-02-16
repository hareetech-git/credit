<?php
include 'db/config.php';


// Fetch all testimonials
$testimonials = mysqli_query($conn, "SELECT * FROM testimonials ORDER BY id DESC");

$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['success_message']);

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
    .testimonial-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
        border-radius: 50%;
        padding: 3px;
        background: #f8fafc;
    }
    
    .table th {
        font-weight: 600;
        color: #475569;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }
    
    .badge-status {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .badge-active {
        background: #dcfce7;
        color: #166534;
    }
    
    .badge-inactive {
        background: #fee2e2;
        color: #991b1b;
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
    
    .testimonial-text-preview {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h2 class="fw-bold text-dark mb-1">Testimonials</h2>
                            <p class="text-muted small">Manage partner testimonials</p>
                        </div>
                        <a href="testimonial_add.php" class="btn btn-submit-pro">
                            <i class="fas fa-plus-circle me-2"></i> Add New Testimonial
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
                                            <th>Image</th>
                                            <th>Partner Name</th>
                                            <th>Designation</th>
                                            <th>Testimonial</th>
                                            <th>Status</th>
                                            <th>Added On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($testimonials) > 0): ?>
                                            <?php while ($testimonial = mysqli_fetch_assoc($testimonials)): ?>
                                                <tr>
                                                   <td>#<?= $testimonial['id'] ?></td>
                                                   <td>
    <?php if (!empty($testimonial['partner_img'])): ?>
        <?php 
        $img_path = resolveAdminTestimonialImage((string) $testimonial['partner_img']);
        ?>
        <img src="<?= htmlspecialchars($img_path) ?>" 
             alt="<?= htmlspecialchars($testimonial['partner_name']) ?>"
             class="testimonial-img"
             onerror="this.onerror=null; this.src='https://via.placeholder.com/60x60?text=User';">
    <?php else: ?>
        <img src="https://via.placeholder.com/60x60?text=User" 
             alt="No Image"
             class="testimonial-img">
    <?php endif; ?>
</td>
                                                    <td class="fw-bold"><?= htmlspecialchars($testimonial['partner_name']) ?></td>
                                                    <td><?= htmlspecialchars($testimonial['designation'] ?: '-') ?></td>
                                                    <td>
                                                        <div class="testimonial-text-preview">
                                                            <?= htmlspecialchars(substr($testimonial['testimonial_text'], 0, 80)) ?>...
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge-status <?= $testimonial['active'] ? 'badge-active' : 'badge-inactive' ?>">
                                                            <?= $testimonial['active'] ? 'Active' : 'Inactive' ?>
                                                        </span>
                                                    </td>
                                                    <td><?= date('d M Y', strtotime($testimonial['created_at'])) ?></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <a href="testimonial_edit.php?id=<?= $testimonial['id'] ?>" 
                                                               class="action-btn btn-edit"
                                                               title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="db/delete/testimonial_delete.php?id=<?= $testimonial['id'] ?>" 
                                                               class="action-btn btn-delete"
                                                               title="Delete"
                                                               onclick="return confirm('Are you sure you want to delete this testimonial?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">
                                                    <i class="fas fa-star mb-2" style="font-size: 2rem;"></i>
                                                    <p>No testimonials added yet. <a href="testimonial_add.php">Add your first testimonial</a></p>
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
