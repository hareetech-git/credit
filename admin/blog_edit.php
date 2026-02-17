<?php
include 'db/config.php';
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    echo '<div class="content-page"><div class="content"><div class="container-fluid pt-5"><div class="alert alert-danger">Invalid blog ID.</div></div></div></div>';
    include 'footer.php';
    exit;
}

$blogRes = mysqli_query($conn, "SELECT * FROM blogs WHERE id = $id LIMIT 1");
$blog = $blogRes ? mysqli_fetch_assoc($blogRes) : null;

if (!$blog) {
    echo '<div class="content-page"><div class="content"><div class="container-fluid pt-5"><div class="alert alert-danger">Blog not found.</div></div></div></div>';
    include 'footer.php';
    exit;
}

$err = $_GET['err'] ?? '';
?>

<link href="assets/vendor/quill/quill.snow.css" rel="stylesheet" type="text/css" />

<style>
    .card-modern {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .form-label {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #475569;
        margin-bottom: 8px;
    }

    #blog_editor {
        min-height: 280px;
        border-radius: 8px;
    }

    .ql-toolbar.ql-snow,
    .ql-container.ql-snow {
        border-color: #cbd5e1;
    }

    .current-image {
        max-width: 190px;
        max-height: 120px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        object-fit: cover;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="mb-4">
                        <h2 class="fw-bold text-dark mb-1">Edit Blog</h2>
                        <p class="text-muted small mb-0">Update blog details and content.</p>
                    </div>

                    <?php if (!empty($err)): ?>
                        <div class="alert alert-danger border-0 shadow-sm mb-4 py-3">
                            <i class="fas fa-exclamation-circle me-1"></i> <?= htmlspecialchars($err) ?>
                        </div>
                    <?php endif; ?>

                    <div class="card card-modern">
                        <div class="card-body p-4 p-md-5">
                            <form method="POST" action="db/update/blog_update.php" enctype="multipart/form-data" id="blogForm">
                                <input type="hidden" name="id" value="<?= (int) $blog['id'] ?>">
                                <input type="hidden" name="existing_image" value="<?= htmlspecialchars((string) $blog['featured_image']) ?>">

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Blog Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="title" class="form-control" required value="<?= htmlspecialchars((string) $blog['title']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                                        <input type="text" name="slug" id="slug" class="form-control" required value="<?= htmlspecialchars((string) $blog['slug']) ?>">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Short Description</label>
                                        <textarea name="short_description" class="form-control" rows="3"><?= htmlspecialchars((string) $blog['short_description']) ?></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Featured Image</label>
                                        <?php if (!empty($blog['featured_image'])): ?>
                                            <div class="mb-2">
                                                <img src="../<?= htmlspecialchars((string) $blog['featured_image']) ?>" alt="Current" class="current-image">
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" name="featured_image" class="form-control" accept="image/*">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="1" <?= ((int) $blog['status'] === 1) ? 'selected' : '' ?>>Published</option>
                                            <option value="0" <?= ((int) $blog['status'] === 0) ? 'selected' : '' ?>>Draft</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Blog Content <span class="text-danger">*</span></label>
                                        <div id="blog_editor"></div>
                                        <input type="hidden" name="content" id="content">
                                    </div>
                                    <div class="col-12 pt-2">
                                        <button type="submit" class="btn btn-dark px-4 mt-3">
                                            <i class="fas fa-save me-2"></i> Update Blog
                                        </button>
                                        <a href="blogs.php" class="btn btn-link text-muted text-decoration-none ms-2">Cancel</a>
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

<script src="assets/vendor/quill/quill.min.js"></script>
<script>
    function toSlug(text) {
        return text.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    }

    document.getElementById('slug').addEventListener('input', function () {
        this.value = toSlug(this.value);
    });

    const quill = new Quill('#blog_editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ color: [] }, { background: [] }],
                [{ align: [] }],
                ['blockquote', 'code-block', 'link'],
                ['clean']
            ]
        }
    });

    quill.root.innerHTML = <?= json_encode((string) ($blog['content'] ?? '')) ?>;

    document.getElementById('blogForm').addEventListener('submit', function (e) {
        const content = quill.root.innerHTML.trim();
        const plain = quill.getText().trim();
        if (!plain) {
            e.preventDefault();
            alert('Blog content is required.');
            return;
        }
        document.getElementById('content').value = content;
    });
</script>

<?php include 'footer.php'; ?>
