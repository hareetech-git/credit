<?php include 'db/config.php'; ?>
<?php include 'header.php'; ?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --border-color: #e2e8f0;
    }
    .content-page { background-color: #fcfcfd; }
    
    /* Premium Table Card */
    .card-modern {
        border: 1px solid var(--border-color);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    
    .card-header-modern {
        background: #fff;
        padding: 24px;
        border-bottom: 1px solid var(--border-color);
    }
    
    /* Table Styling */
    .table-modern thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: var(--slate-600);
        padding: 16px 24px;
        border-top: none;
    }
    
    .table-modern tbody td {
        padding: 16px 24px;
        font-size: 0.9rem;
        color: var(--slate-900);
        border-bottom: 1px solid var(--border-color);
    }

    /* Category Image Circle */
    .category-img {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid var(--border-color);
    }

    .no-image-placeholder {
        width: 48px;
        height: 48px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        color: #94a3b8;
        font-size: 0.7rem;
        text-transform: uppercase;
    }

    /* Action Buttons */
    .btn-action-edit {
        background: transparent;
        color: var(--slate-900);
        border: 1px solid var(--border-color);
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .btn-action-edit:hover { background: #f8fafc; color: #000; }

    .btn-action-delete {
        color: #ef4444;
        background: transparent;
        border: none;
        font-weight: 600;
        margin-left: 10px;
    }
    .btn-action-delete:hover { text-decoration: underline; }

    .btn-add-new {
        background: var(--slate-900);
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        transition: opacity 0.2s;
    }
    .btn-add-new:hover { opacity: 0.9; color: #fff; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <div class="row">
                <div class="col-12">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="fw-bold text-dark mb-1">Categories</h2>
                            <p class="text-muted small mb-0">Manage your mehndi service classifications</p>
                        </div>
                        <a href="add-category.php" class="btn-add-new">
                            Create New Category
                        </a>
                    </div>

                    <div class="card card-modern">
                        <div class="card-body p-0">
                            <div id="alert-container" style="display:none" class="m-3 alert py-3"></div>

                            <div class="table-responsive">
                                <table class="table table-modern align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th width="100">ID</th>
                                            <th>Category Details</th>
                                            <th>Preview</th>
                                            <th>Timestamp</th>
                                            <th width="200">Manage</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    $sql = "SELECT * FROM categories ORDER BY id DESC";
                                    $result = mysqli_query($conn, $sql);

                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            ?>
                                            <tr id="row-<?= $row['id'] ?>">
                                                <td class="fw-bold text-muted">#<?= $row['id'] ?></td>
                                                <td>
                                                    <span class="fw-bold text-dark d-block"><?= htmlspecialchars($row['name']) ?></span>
                                                </td>
                                                <td>
                                                    <?php if ($row['img']) { ?>
                                                        <img src="<?= $row['img'] ?>" class="category-img">
                                                    <?php } else { ?>
                                                        <div class="no-image-placeholder">N/A</div>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-muted small">
                                                    <?= date('M d, Y', strtotime($row['created_at'])) ?>
                                                </td>
                                                <td>
                                                    <a href="add-category.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-action-edit text-decoration-none">
                                                        Edit
                                                    </a>
                                                    <button class="btn-action-delete delete-btn" data-id="<?= $row['id'] ?>">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center py-5 text-muted">No records found. Click "Create New Category" to start.</td></tr>';
                                    }
                                    ?>

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

<script>
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('This action cannot be undone. Delete this category?')) return;

        const id = btn.dataset.id;
        const alertBox = document.getElementById('alert-container');

        try {
            const res = await fetch('db/category-delete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });

            const data = await res.json();

            if (data.success) {
                document.getElementById('row-' + id).style.opacity = '0.5';
                setTimeout(() => {
                    document.getElementById('row-' + id).remove();
                }, 300);
            } else {
                alert(data.message || 'Delete failed');
            }
        } catch (err) {
            alert('Something went wrong');
        }
    });
});
</script>