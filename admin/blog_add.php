<?php
include 'db/config.php';
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

$err = $_GET['err'] ?? '';
?>

<link href="assets/vendor/quill/quill.snow.css" rel="stylesheet" type="text/css" />

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
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .form-label {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: var(--slate-600);
        margin-bottom: 8px;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e1;
    }

    #blog_editor {
        min-height: 280px;
        border-radius: 8px;
    }

    .ql-toolbar.ql-snow,
    .ql-container.ql-snow {
        border-color: #cbd5e1;
    }

    .btn-submit-pro {
        background: var(--slate-900);
        color: #ffffff !important;
        border: none;
        padding: 11px 26px;
        border-radius: 8px;
        font-weight: 600;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="mb-4">
                        <h2 class="fw-bold text-dark mb-1">Add New Blog</h2>
                        <p class="text-muted small mb-0">Create and publish blog content for website visitors.</p>
                    </div>

                    <?php if (!empty($err)): ?>
                        <div class="alert alert-danger border-0 shadow-sm mb-4 py-3">
                            <i class="fas fa-exclamation-circle me-1"></i> <?= htmlspecialchars($err) ?>
                        </div>
                    <?php endif; ?>

                    <div class="card card-modern">
                        <div class="card-body p-4 p-md-5">
                            <form method="POST" action="db/insert/blog_insert.php" enctype="multipart/form-data" id="blogForm">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Blog Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="title" class="form-control" required onkeyup="autoSlug()">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Slug (Optional)</label>
                                        <input type="text" name="slug" id="slug" class="form-control" placeholder="auto-generated-from-title">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Short Description</label>
                                        <textarea name="short_description" class="form-control" rows="3" placeholder="Used on cards and previews"></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Featured Image</label>
                                        <input type="file" name="featured_image" class="form-control" accept="image/*">
                                        <small class="text-muted">JPG, PNG, WEBP, GIF, AVIF</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="1" selected>Published</option>
                                            <option value="0">Draft</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Blog Content <span class="text-danger">*</span></label>
                                        <div id="blog_editor"></div>
                                        <input type="hidden" name="content" id="content">
                                    </div>
                                    <div class="col-12 pt-2">
                                        <button type="submit" class="btn-submit-pro">
                                            <i class="fas fa-save me-2"></i> Save Blog
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
    let slugTouched = false;
    const slugInput = document.getElementById('slug');
    slugInput.addEventListener('input', function () {
        slugTouched = this.value.trim() !== '';
    });

    function toSlug(text) {
        return text.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    }

    function autoSlug() {
        if (!slugTouched) {
            slugInput.value = toSlug(document.getElementById('title').value || '');
        }
    }

    const quill = new Quill('#blog_editor', {
        theme: 'snow',
        placeholder: 'Write blog content here...',
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
