<?php
include 'db/config.php';
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

$msg = $_GET['msg'] ?? '';
$err = $_GET['err'] ?? '';

$blogs = mysqli_query(
    $conn,
    "SELECT id, title, slug, short_description, featured_image, status, created_at
     FROM blogs
     ORDER BY id DESC"
);

if (!function_exists('blogExcerpt')) {
    function blogExcerpt($text, $limit = 90) {
        $clean = trim(strip_tags((string) $text));
        if (strlen($clean) <= $limit) {
            return $clean;
        }
        return substr($clean, 0, $limit) . '...';
    }
}
?>

<style>
    .card-modern {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .blog-thumb {
        width: 84px;
        height: 56px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .badge-status {
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .badge-published { background: #dcfce7; color: #166534; }
    .badge-draft { background: #fee2e2; color: #991b1b; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Blog Management</h2>
                    <p class="text-muted small mb-0">Manage published and draft blog posts.</p>
                </div>
                <a href="blog_add.php" class="btn btn-dark rounded-3 px-3">
                    <i class="fas fa-plus me-1"></i> Add Blog
                </a>
            </div>

            <?php if (!empty($msg)): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4 py-3">
                    <i class="fas fa-check-circle me-1"></i> <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($err)): ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4 py-3">
                    <i class="fas fa-exclamation-circle me-1"></i> <?= htmlspecialchars($err) ?>
                </div>
            <?php endif; ?>

            <div class="card card-modern">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Slug</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($blogs && mysqli_num_rows($blogs) > 0): ?>
                                    <?php while ($blog = mysqli_fetch_assoc($blogs)): ?>
                                        <tr>
                                            <td>#<?= (int) $blog['id'] ?></td>
                                            <td>
                                                <?php if (!empty($blog['featured_image'])): ?>
                                                    <img src="../<?= htmlspecialchars($blog['featured_image']) ?>" alt="Blog" class="blog-thumb">
                                                <?php else: ?>
                                                    <span class="text-muted small">No image</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="fw-semibold"><?= htmlspecialchars($blog['title']) ?></div>
                                                <small class="text-muted"><?= htmlspecialchars(blogExcerpt($blog['short_description'])) ?></small>
                                            </td>
                                            <td><code><?= htmlspecialchars($blog['slug']) ?></code></td>
                                            <td>
                                                <span class="badge-status <?= ((int) $blog['status'] === 1) ? 'badge-published' : 'badge-draft' ?>">
                                                    <?= ((int) $blog['status'] === 1) ? 'Published' : 'Draft' ?>
                                                </span>
                                            </td>
                                            <td><?= date('d M Y', strtotime((string) $blog['created_at'])) ?></td>
                                            <td class="text-end">
                                                <a href="../blog-details.php?slug=<?= urlencode((string) $blog['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="blog_edit.php?id=<?= (int) $blog['id'] ?>" class="btn btn-sm btn-outline-dark" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="db/delete/blog_delete.php?id=<?= (int) $blog['id'] ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this blog post?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No blogs found.</td>
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

<?php include 'footer.php'; ?>
