<?php
session_start();
require_once 'includes/header.php';
include 'includes/connection.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$id = (int) ($_GET['id'] ?? 0);

$where = '';
if ($slug !== '') {
    $slugSafe = mysqli_real_escape_string($conn, $slug);
    $where = "slug = '$slugSafe'";
} elseif ($id > 0) {
    $where = "id = $id";
}

$blog = null;
if ($where !== '') {
    $res = mysqli_query(
        $conn,
        "SELECT id, title, slug, short_description, content, featured_image, created_at
         FROM blogs
         WHERE status = 1 AND $where
         LIMIT 1"
    );
    $blog = $res ? mysqli_fetch_assoc($res) : null;
}

$readingTime = 1;
$relatedBlogs = [];

if ($blog) {
    $wordCount = str_word_count(strip_tags((string) ($blog['content'] ?? '')));
    $readingTime = max(1, (int) ceil($wordCount / 200));

    $blogId = (int) ($blog['id'] ?? 0);
    $relatedRes = mysqli_query(
        $conn,
        "SELECT id, title, slug, featured_image, created_at
         FROM blogs
         WHERE status = 1 AND id != $blogId
         ORDER BY id DESC
         LIMIT 4"
    );

    if ($relatedRes && mysqli_num_rows($relatedRes) > 0) {
        while ($row = mysqli_fetch_assoc($relatedRes)) {
            $relatedBlogs[] = $row;
        }
    }
}
?>

<style>
    .blog-detail-hero {
        padding: 110px 0 46px;
        background:
            radial-gradient(380px 220px at 12% 18%, rgba(255, 255, 255, 0.08), transparent),
            radial-gradient(360px 220px at 84% 90%, rgba(255, 255, 255, 0.06), transparent),
            linear-gradient(132deg, var(--primary-color) 0%, var(--primary-dark) 55%, #1b1540 100%);
        color: #ffffff;
        position: relative;
        overflow: hidden;
    }

    .hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.08);
        border-radius: 999px;
        padding: 6px 14px;
        font-weight: 600;
        font-size: 0.78rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .hero-breadcrumb a {
        color: rgba(255, 255, 255, 0.82);
        text-decoration: none;
    }

    .hero-breadcrumb a:hover {
        color: #ffffff;
    }

    .hero-title {
        font-size: clamp(1.9rem, 3vw, 2.9rem);
        line-height: 1.2;
        letter-spacing: -0.02em;
        margin-top: 14px;
    }

    .hero-summary {
        color: rgba(255, 255, 255, 0.85);
        max-width: 850px;
    }

    .meta-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
    }

    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 13px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.16);
        font-size: 0.85rem;
    }

    .blog-detail-wrap {
        padding: 56px 0 86px;
        background: #f8fafc;
    }

    .blog-detail-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 18px;
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.09);
    }

    .blog-detail-image {
        width: 100%;
        max-height: 450px;
        object-fit: cover;
        border-radius: 14px;
    }

    .blog-content {
        color: #334155;
        line-height: 1.85;
        font-size: 1.04rem;
        padding: 6px 10px 14px;
    }

    .blog-content h1,
    .blog-content h2,
    .blog-content h3,
    .blog-content h4 {
        color: #0f172a;
        margin-top: 1.7rem;
        margin-bottom: 0.75rem;
        line-height: 1.3;
    }

    .blog-content h2 {
        font-size: 1.7rem;
        border-left: 4px solid var(--primary-color);
        padding-left: 10px;
    }

    .blog-content a {
        color: var(--primary-color);
        text-decoration: underline;
    }

    .blog-content ul,
    .blog-content ol {
        padding-left: 1.3rem;
    }

    .blog-content img {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
        margin: 1rem 0;
    }

    .blog-content blockquote {
        border-left: 4px solid var(--primary-color);
        background: rgba(11, 8, 27, 0.06);
        margin: 1.1rem 0;
        padding: 14px 16px;
        border-radius: 0 10px 10px 0;
        color: #0f172a;
    }

    .sidebar-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.06);
    }

    .sticky-col {
        position: sticky;
        top: 96px;
    }

    .toc-list {
        max-height: 240px;
        overflow: auto;
        margin: 0;
        padding-left: 1rem;
    }

    .toc-list li {
        margin-bottom: 8px;
    }

    .toc-list a {
        text-decoration: none;
        color: #334155;
        font-size: 0.92rem;
    }

    .toc-list a:hover {
        color: var(--primary-color);
    }

    .side-post {
        display: flex;
        gap: 10px;
        text-decoration: none;
        color: inherit;
        padding: 8px 0;
        border-bottom: 1px dashed #e2e8f0;
    }

    .side-post:last-child {
        border-bottom: none;
    }

    .side-post img {
        width: 72px;
        height: 56px;
        border-radius: 8px;
        object-fit: cover;
    }

    .side-post h6 {
        font-size: 0.88rem;
        margin: 0 0 4px;
        line-height: 1.35;
    }

    .share-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
        margin-top: 8px;
    }

    .share-btn {
        border: 1px solid #cbd5e1;
        color: #334155;
        text-decoration: none;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 0.83rem;
        transition: all 0.2s ease;
    }

    .share-btn:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .article-footer-cta {
        margin-top: 24px;
        padding: 18px;
        border-radius: 14px;
        background: linear-gradient(135deg, rgba(11, 8, 27, 0.1), rgba(16, 11, 44, 0.06));
        border: 1px solid rgba(11, 8, 27, 0.18);
    }

    .related-section {
        margin-top: 42px;
    }

    .related-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
        overflow: hidden;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .related-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
    }

    .related-card img {
        width: 100%;
        height: 140px;
        object-fit: cover;
    }

    .blog-detail-wrap .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        border: none;
        box-shadow: 0 8px 16px rgba(11, 8, 27, 0.25);
    }

    .blog-detail-wrap .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
    }

    .related-section a.small {
        color: var(--primary-color);
    }

    @media (max-width: 991px) {
        .sticky-col {
            position: static;
            top: auto;
        }
        .blog-content {
            padding: 10px 4px 8px;
        }
    }
</style>

<section class="blog-detail-hero">
    <div class="container">
        <?php if ($blog): ?>
            <div class="hero-kicker"><i class="fas fa-pen-nib"></i> Blog Article</div>
            <div class="hero-breadcrumb mt-3 small">
                <a href="index.php">Home</a> / <a href="blogs.php">Blogs</a> / <span class="text-white-50">Details</span>
            </div>
            <h1 class="hero-title fw-bold"><?= htmlspecialchars((string) $blog['title']) ?></h1>
            <?php if (!empty($blog['short_description'])): ?>
                <p class="hero-summary mb-0"><?= htmlspecialchars((string) $blog['short_description']) ?></p>
            <?php endif; ?>
            <div class="meta-pills">
                <span class="meta-pill"><i class="fas fa-calendar-alt"></i> <?= date('d M Y', strtotime((string) $blog['created_at'])) ?></span>
                <span class="meta-pill"><i class="fas fa-clock"></i> <?= $readingTime ?> min read</span>
            </div>
        <?php else: ?>
            <h1 class="fw-bold mb-2">Blog Not Found</h1>
            <p class="mb-0 text-white-50">The requested blog post does not exist.</p>
        <?php endif; ?>
    </div>
</section>

<section class="blog-detail-wrap">
    <div class="container">
        <?php if ($blog): ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="blog-detail-card">
                        <?php if (!empty($blog['featured_image'])): ?>
                            <img src="<?= htmlspecialchars((string) $blog['featured_image']) ?>" alt="<?= htmlspecialchars((string) $blog['title']) ?>" class="blog-detail-image">
                        <?php endif; ?>

                        <div class="blog-content mt-3" id="articleContent">
                            <?= (string) $blog['content'] ?>
                        </div>

                        <div class="article-footer-cta">
                            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-1 fw-bold">Need help choosing the right loan?</h6>
                                    <small class="text-muted">Speak with our experts for free guidance.</small>
                                </div>
                                <a href="apply-loan.php" class="btn btn-primary btn-sm rounded-pill px-3">
                                    Apply Now
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($relatedBlogs)): ?>
                        <div class="related-section">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h4 class="fw-bold mb-0">More Articles You May Like</h4>
                                <a href="blogs.php" class="small text-decoration-none">View all</a>
                            </div>
                            <div class="row g-3">
                                <?php foreach ($relatedBlogs as $rel): ?>
                                    <div class="col-md-6">
                                        <a href="blog-details.php?slug=<?= urlencode((string) $rel['slug']) ?>" class="text-decoration-none text-dark">
                                            <div class="related-card">
                                                <?php if (!empty($rel['featured_image'])): ?>
                                                    <img src="<?= htmlspecialchars((string) $rel['featured_image']) ?>" alt="<?= htmlspecialchars((string) $rel['title']) ?>">
                                                <?php endif; ?>
                                                <div class="p-3">
                                                    <small class="text-muted d-block mb-1"><?= date('d M Y', strtotime((string) $rel['created_at'])) ?></small>
                                                    <h6 class="fw-bold mb-0"><?= htmlspecialchars((string) $rel['title']) ?></h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <div class="sticky-col d-flex flex-column gap-3">
                        <div class="sidebar-card">
                            <h6 class="fw-bold mb-3">Quick Navigation</h6>
                            <ol class="toc-list" id="tocList">
                                <li class="text-muted small">No headings found in this article.</li>
                            </ol>
                        </div>

                     

                        <?php if (!empty($relatedBlogs)): ?>
                            <div class="sidebar-card">
                                <h6 class="fw-bold mb-3">Latest Blogs</h6>
                                <?php foreach ($relatedBlogs as $side): ?>
                                    <a class="side-post" href="blog-details.php?slug=<?= urlencode((string) $side['slug']) ?>">
                                        <?php if (!empty($side['featured_image'])): ?>
                                            <img src="<?= htmlspecialchars((string) $side['featured_image']) ?>" alt="<?= htmlspecialchars((string) $side['title']) ?>">
                                        <?php endif; ?>
                                        <div>
                                            <h6><?= htmlspecialchars((string) $side['title']) ?></h6>
                                            <small class="text-muted"><?= date('d M Y', strtotime((string) $side['created_at'])) ?></small>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">Blog not found or unpublished.</div>
        <?php endif; ?>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const content = document.getElementById('articleContent');
        const toc = document.getElementById('tocList');
        if (!content || !toc) {
            return;
        }

        const headings = content.querySelectorAll('h2, h3');
        if (!headings.length) {
            return;
        }

        toc.innerHTML = '';
        headings.forEach((heading, idx) => {
            const id = heading.id || ('article-heading-' + idx);
            heading.id = id;

            const li = document.createElement('li');
            const a = document.createElement('a');
            a.href = '#' + id;
            a.textContent = heading.textContent.trim();
            li.appendChild(a);
            toc.appendChild(li);
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>
