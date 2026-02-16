<?php
include 'db/config.php';


// Fetch all brands
$brands = mysqli_query($conn, "SELECT * FROM brands ORDER BY id DESC");

$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['success_message']);
?>

<?php include 'header.php'; ?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<style>
    .brand-img {
        width: 80px;
        height: 80px;
        object-fit: contain;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px;
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
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h2 class="fw-bold text-dark mb-1">Brands</h2>
                            <p class="text-muted small">Manage your partner brands</p>
                        </div>
                        <a href="brand_add.php" class="btn btn-submit-pro">
                            <i class="fas fa-plus-circle me-2"></i> Add New Brand
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
                                            <th>Brand Logo</th>
                                            <th>Brand Name</th>
                                            <th>Status</th>
                                            <th>Added On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($brands) > 0): ?>
                                            <?php while ($brand = mysqli_fetch_assoc($brands)): ?>
                                                <tr>
                                                    <td>#<?= $brand['id'] ?></td>
                                                    <td>
                                                        <?php if (!empty($brand['brand_img'])): ?>
                                                            <?php 
                                                            // Check what's stored in database
                                                            $stored_path = $brand['brand_img'];
                                                            
                                                            // If path already has 'admin/' in it
                                                            if (strpos($stored_path, 'admin/') === 0) {
                                                                // Path starts with admin/ - go up one level
                                                                $img_path = '../' . $stored_path;
                                                            } 
                                                            // If path starts with 'assets/'
                                                            elseif (strpos($stored_path, 'assets/') === 0) {
                                                                // Add admin/ prefix
                                                                $img_path = '../admin/' . $stored_path;
                                                            }
                                                            // If it's just a filename
                                                            elseif (strpos($stored_path, '/') === false) {
                                                                $img_path = '../admin/assets/brands/' . $stored_path;
                                                            }
                                                            else {
                                                                // Default case
                                                                $img_path = '../' . $stored_path;
                                                            }
                                                            ?>
                                                            <img src="<?= htmlspecialchars($img_path) ?>" 
                                                                 alt="<?= htmlspecialchars($brand['brand_name']) ?>"
                                                                 class="brand-img"
                                                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/80x80?text=Error';">
                                                        <?php else: ?>
                                                            <img src="https://via.placeholder.com/80x80?text=No+Image" 
                                                                 alt="No Image"
                                                                 class="brand-img">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="fw-bold"><?= htmlspecialchars($brand['brand_name']) ?></td>
                                                    <td>
                                                        <span class="badge-status <?= $brand['active'] ? 'badge-active' : 'badge-inactive' ?>">
                                                            <?= $brand['active'] ? 'Active' : 'Inactive' ?>
                                                        </span>
                                                    </td>
                                                    <td><?= date('d M Y', strtotime($brand['created_at'])) ?></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <a href="brand_edit.php?id=<?= $brand['id'] ?>" 
                                                               class="action-btn btn-edit"
                                                               title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="db/delete/brand_delete.php?id=<?= $brand['id'] ?>" 
                                                               class="action-btn btn-delete"
                                                               title="Delete"
                                                               onclick="return confirm('Are you sure you want to delete this brand?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    <i class="fas fa-building mb-2" style="font-size: 2rem;"></i>
                                                    <p>No brands added yet. <a href="brand_add.php">Add your first brand</a></p>
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

<!-- Add this debug section temporarily to see what paths are stored -->
<!-- Remove this after fixing -->
<div style="display: none;">
    <?php
    $debug_query = "SELECT id, brand_name, brand_img FROM brands";
    $debug_result = mysqli_query($conn, $debug_query);
    echo "<!-- DEBUG - Stored paths:\n";
    while ($debug = mysqli_fetch_assoc($debug_result)) {
        echo "ID: " . $debug['id'] . " - Brand: " . $debug['brand_name'] . " - Path: " . $debug['brand_img'] . "\n";
    }
    echo "-->";
    ?>
</div>

<?php include 'footer.php'; ?>