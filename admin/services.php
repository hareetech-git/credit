<?php include 'db/config.php'; ?>
<?php include 'header.php'; ?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
        --success-green: #059669;
    }
    .content-page { background-color: #fcfcfd; }
    
    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
    }

    /* Table Typography */
    .table-modern thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: var(--slate-600);
        padding: 16px 20px;
        border: none;
    }
    
    .table-modern tbody td {
        padding: 16px 20px;
        vertical-align: middle;
        font-size: 0.875rem;
        color: var(--slate-900);
        border-bottom: 1px solid var(--slate-200);
    }

    /* Service Visuals */
    .img-stack {
        display: flex;
        align-items: center;
    }
    .img-stack img {
        width: 38px;
        height: 38px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-right: -12px;
        transition: transform 0.2s;
    }
    .img-stack img:hover {
        transform: translateY(-4px);
        z-index: 10;
    }

    /* Badges */
    .badge-time {
        background: #f1f5f9;
        color: #475569;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
    }
    .text-price {
        font-weight: 700;
        color: var(--slate-900);
    }
    .discount-price {
        font-size: 0.75rem;
        color: #94a3b8;
        text-decoration: line-through;
        display: block;
    }

    /* Actions */
    .btn-edit {
        color: var(--slate-900);
        font-weight: 600;
        text-decoration: none;
        margin-right: 15px;
    }
    .btn-delete {
        color: #ef4444;
        background: transparent;
        border: none;
        font-weight: 600;
    }
    .btn-add-primary {
        background: var(--slate-900);
        color: #fff;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Mehndi Services</h2>
                    <p class="text-muted small mb-0">Manage pricing, estimated duration, and gallery for each service.</p>
                </div>
                <a href="add-service.php" class="btn-add-primary">Create Service</a>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-modern">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-modern mb-0">
                                    <thead>
                                        <tr>
                                            <th width="80">ID</th>
                                            <th>Service Details</th>
                                            <th>Category</th>
                                            <th>Timing</th>
                                            <th>Pricing</th>
                                            <th>Gallery</th>
                                            <th width="180">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    // Updated SQL to include description and estimated_time
                                    $sql = "
                                        SELECT s.*, c.name AS category_name
                                        FROM services s
                                        JOIN categories c ON c.id = s.category_id
                                        ORDER BY s.id DESC
                                    ";
                                    $result = mysqli_query($conn, $sql);

                                    if ($result && mysqli_num_rows($result)) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            // Fetch images stack
                                            $imgs = mysqli_query(
                                                $conn,
                                                "SELECT img FROM services_imgs WHERE service_id={$row['id']} LIMIT 4"
                                            );
                                            ?>
                                            <tr id="row-<?= $row['id'] ?>">
                                                <td class="text-muted fw-medium">#<?= $row['id'] ?></td>
                                                <td>
                                                    <span class="fw-bold d-block text-dark"><?= htmlspecialchars($row['name']) ?></span>
                                                    <small class="text-muted d-block text-truncate" style="max-width: 200px;">
                                                        <?= htmlspecialchars($row['description'] ?? 'No description provided') ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="text-muted"><?= htmlspecialchars($row['category_name']) ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge-time">
                                                        <?= !empty($row['estimated_time']) ? htmlspecialchars($row['estimated_time']) : 'N/A' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-price">₹<?= number_format($row['price']) ?></span>
                                                    <?php if($row['discount_price']): ?>
                                                        <span class="discount-price">₹<?= number_format($row['discount_price']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="img-stack">
                                                        <?php while ($img = mysqli_fetch_assoc($imgs)) { ?>
                                                            <img src="<?= $img['img'] ?>" alt="Gallery">
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="add-service.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                                                    <button class="btn-delete delete-btn" data-id="<?= $row['id'] ?>">Delete</button>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="7" class="text-center py-5 text-muted">No services found in database.</td></tr>';
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
    btn.onclick = async () => {
        if (!confirm('This will permanently delete the service and all associated images. Proceed?')) return;

        const id = btn.dataset.id;
        try {
            const res = await fetch('db/service-delete.php', {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify({id})
            });
            const data = await res.json();
            if (data.success) {
                const row = document.getElementById('row-'+id);
                row.style.transition = '0.3s';
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);
            } else {
                alert(data.message || 'Error deleting service');
            }
        } catch (err) {
            alert('Communication error with server');
        }
    };
});
</script>