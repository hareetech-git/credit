<?php
session_start();
require_once 'includes/header.php';
include 'includes/connection.php';

$blogs = [];
$res = mysqli_query(
    $conn,
    "SELECT id, title, slug, short_description, content, featured_image, created_at
     FROM blogs
     WHERE status = 1
     ORDER BY id DESC"
);

if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        if (trim((string) $row['short_description']) === '') {
            $plain = trim(strip_tags((string) $row['content']));
            $row['short_description'] = strlen($plain) > 180 ? substr($plain, 0, 180) . '...' : $plain;
        }
        $blogs[] = $row;
    }
}
?>

<style>
    .blog-hero {
        padding: 110px 0 45px;
        background: linear-gradient(130deg, #0f172a, #1e293b);
        color: #ffffff;
    }

    .blog-list-section {
        padding: 70px 0;
        background: #f8fafc;
    }

    .blog-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        background: #ffffff;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        height: 100%;
    }

    .blog-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.12);
    }

    .blog-card-image {
        width: 100%;
        height: 220px;
        object-fit: cover;
    }
</style>

<section class="blog-hero">
    <div class="container">
        <h1 class="fw-bold mb-2">Our Blogs</h1>
        <p class="mb-0 text-white-50">Insights, loan guidance, and updates from Udhar Capital.</p>
    </div>
</section>

<section class="blog-list-section">
    <div class="container">
        <div class="row g-4">
            <?php if (!empty($blogs)): ?>
                <?php foreach ($blogs as $blog): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="blog-card">
                            <?php if (!empty($blog['featured_image'])): ?>
                                <img src="<?= htmlspecialchars((string) $blog['featured_image']) ?>" alt="<?= htmlspecialchars((string) $blog['title']) ?>" class="blog-card-image">
                            <?php endif; ?>
                            <div class="p-4">
                                <small class="text-muted d-block mb-2">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <?= date('d M Y', strtotime((string) $blog['created_at'])) ?>
                                </small>
                                <h5 class="fw-bold mb-2"><?= htmlspecialchars((string) $blog['title']) ?></h5>
                                <p class="text-muted mb-3"><?= htmlspecialchars((string) $blog['short_description']) ?></p>
                                <a href="blog-details.php?slug=<?= urlencode((string) $blog['slug']) ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                                    Read More <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted">No blogs available right now.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
