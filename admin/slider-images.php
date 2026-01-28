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
    
    .card-modern {
        border: 1px solid var(--border-color);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .form-label { font-weight: 600; color: var(--slate-900); font-size: 0.85rem; }
    .form-control { border-radius: 8px; padding: 0.6rem 1rem; border: 1px solid #cbd5e1; }

    /* Table Styling */
    .table-modern thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: var(--slate-600);
        padding: 15px 20px;
        border: none;
    }
    
    .table-modern tbody td {
        padding: 15px 20px;
        vertical-align: middle;
        font-size: 0.9rem;
        border-bottom: 1px solid var(--border-color);
    }

    .slider-preview-img {
        width: 140px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }

    .btn-submit-dark {
        background: var(--slate-900);
        color: #fff;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        transition: opacity 0.2s;
    }

    .btn-delete-link {
        color: #ef4444;
        background: transparent;
        border: none;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .btn-delete-link:hover { text-decoration: underline; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <div class="mb-4">
                <h2 class="fw-bold text-dark mb-1">Slider Management</h2>
                <p class="text-muted small">Update your homepage hero banners and promotional titles.</p>
            </div>

            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card card-modern">
                        <div class="card-body p-4">
                            <div id="alert-container" style="display:none" class="alert py-3 mb-4"></div>

                            <form id="slider-form" enctype="multipart/form-data">
                                <div class="row align-items-end">
                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label">Primary Title</label>
                                        <input type="text" id="title" class="form-control" placeholder="Main Heading" required>
                                    </div>

                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label">Subtitle</label>
                                        <input type="text" id="sub_title" class="form-control" placeholder="Secondary Text">
                                    </div>

                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label">Slider Image (Landscape)</label>
                                        <input type="file" id="image" class="form-control" accept="image/*" required>
                                    </div>

                                    <div class="col-lg-2 mb-3">
                                        <button type="button" id="submit-btn" class="btn btn-submit-dark w-100">
                                            Upload
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card card-modern">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-modern mb-0">
                                    <thead>
                                        <tr>
                                            <th width="80">ID</th>
                                            <th width="180">Visual</th>
                                            <th>Content Details</th>
                                            <th width="120">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    $result = mysqli_query($conn, "SELECT * FROM slider_images ORDER BY id DESC");

                                    if ($result && mysqli_num_rows($result)) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            ?>
                                            <tr id="row-<?= $row['id'] ?>">
                                                <td class="text-muted fw-bold">#<?= $row['id'] ?></td>
                                                <td>
                                                    <img src="<?= $row['img'] ?>" class="slider-preview-img">
                                                </td>
                                                <td>
                                                    <span class="fw-bold d-block text-dark"><?= htmlspecialchars($row['title']) ?></span>
                                                    <span class="text-muted small"><?= htmlspecialchars($row['sub_title']) ?></span>
                                                </td>
                                                <td>
                                                    <button class="btn-delete-link delete-btn" data-id="<?= $row['id'] ?>">
                                                        Remove
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="4" class="text-center py-5 text-muted">No active sliders found.</td></tr>';
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
const submitBtn = document.getElementById('submit-btn');
const alertBox = document.getElementById('alert-container');

/* ADD SLIDER */
submitBtn.addEventListener('click', async () => {
    const fileInput = document.getElementById('image');
    if(!fileInput.files[0]) {
        showError('Please select an image file');
        return;
    }

    submitBtn.disabled = true;
    submitBtn.innerText = 'Uploading...';

    const formData = new FormData();
    formData.append('title', document.getElementById('title').value.trim());
    formData.append('sub_title', document.getElementById('sub_title').value.trim());
    formData.append('img', fileInput.files[0]);

    try {
        const res = await fetch('db/slider-store.php', {
            method: 'POST',
            body: formData
        });

        const result = await res.json();

        if (result.success) {
            alertBox.className = 'alert alert-success py-3';
            alertBox.textContent = 'Banner updated successfully';
            alertBox.style.display = 'block';
            setTimeout(() => location.reload(), 1000);
        } else {
            showError(result.message);
        }
    } catch {
        showError('Server connection failed');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Upload';
    }
});

/* DELETE SLIDER */
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.onclick = async () => {
        if (!confirm('Permanently remove this banner?')) return;

        const id = btn.dataset.id;
        const res = await fetch('db/slider-delete.php', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ id })
        });

        const data = await res.json();
        if (data.success) {
            const row = document.getElementById('row-' + id);
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 300);
        }
    };
});

function showError(msg) {
    alertBox.className = 'alert alert-danger py-3';
    alertBox.textContent = msg;
    alertBox.style.display = 'block';
}
</script>