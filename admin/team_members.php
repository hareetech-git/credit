<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php include 'topbar.php';?>
<?php include 'db/config.php'; ?>
<style>
:root {
    --slate-900: #0f172a;
    --slate-600: #475569;
    --slate-200: #e2e8f0;
    --blue-500: #3b82f6;
}

.content-page { background-color: #fcfcfd; }

.card-modern {
    border: 1px solid var(--slate-200);
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.form-label-modern {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--slate-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.form-control-modern {
    border-radius: 10px;
    border: 1px solid var(--slate-200);
    padding: 10px 14px;
    font-size: 0.9rem;
}

.form-control-modern:focus {
    border-color: var(--blue-500);
    box-shadow: 0 0 0 2px rgba(59,130,246,0.15);
}

.btn-save {
    background: var(--slate-900);
    color: white;
    border-radius: 10px;
    padding: 10px 24px;
    font-weight: 600;
}

.btn-save:hover {
    background: #020617;
    color:white;
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
    border: none;
}

.table-modern tbody td {
    padding: 16px 24px;
    font-size: 0.9rem;
    color: var(--slate-900);
    border-bottom: 1px solid var(--slate-200);
    vertical-align: middle;
}

.btn-delete-modern {
    border: 1px solid var(--slate-200);
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 0.8rem;
    color: #dc2626;
    background: white;
    text-decoration: none;
}

.btn-delete-modern:hover {
    background: #ef4444;
    color: white;
    border-color: #ef4444;
}

.btn-edit-modern {
    border: 1px solid var(--slate-200);
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 0.8rem;
    color: #1d4ed8;
    background: #ffffff;
    text-decoration: none;
}

.btn-edit-modern:hover {
    background: #3b82f6;
    color: #ffffff;
    border-color: #3b82f6;
}

.modal-card-header {
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}

.current-preview {
    width: 72px;
    height: 72px;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    object-fit: cover;
}
</style>

<div class="content-page">
<div class="content">
<div class="container-fluid pt-5">

<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Team Management</h2>
    <p class="text-muted small mb-0">
        Add and manage team members displayed on your website.
    </p>
</div>

<?php
$flashMsg = trim((string) ($_GET['msg'] ?? ''));
if ($flashMsg !== ''):
?>
<div class="alert alert-success border-0 shadow-sm mb-4 py-3">
    <i class="fas fa-check-circle me-2"></i>
    <?php echo htmlspecialchars($flashMsg); ?>
</div>
<?php endif; ?>

<!-- Add Member Form -->
<div class="card card-modern p-4 mb-4">
<form method="POST" action="db/insert/team_handler.php" enctype="multipart/form-data">

<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label-modern">Full Name</label>
        <input type="text" name="name" class="form-control form-control-modern" required>
    </div>

    <div class="col-md-6">
        <label class="form-label-modern">Designation</label>
        <input type="text" name="designation" class="form-control form-control-modern" required>
    </div>

    <div class="col-md-12">
        <label class="form-label-modern">Short Description</label>
        <textarea name="short_description" class="form-control form-control-modern" rows="2"></textarea>
    </div>

    <div class="col-md-12">
        <label class="form-label-modern">Profile Image</label>
        <input type="file" name="image" class="form-control form-control-modern" required>
    </div>

    <div class="col-md-4">
        <label class="form-label-modern">LinkedIn URL</label>
        <input type="text" name="linkedin_link" class="form-control form-control-modern">
    </div>

    <div class="col-md-4">
        <label class="form-label-modern">Twitter URL</label>
        <input type="text" name="twitter_link" class="form-control form-control-modern">
    </div>

    <div class="col-md-4">
        <label class="form-label-modern">Email URL</label>
        <input type="text" name="email_link" class="form-control form-control-modern">
    </div>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fas fa-user-plus me-2"></i> Add Member
    </button>
</div>

</form>
</div>

<?php
$result = mysqli_query($conn, "SELECT * FROM team_members ORDER BY id DESC");
?>

<!-- Team List -->
<div class="card card-modern">
<div class="card-body p-0">
<div class="table-responsive">
<table class="table table-modern mb-0">
<thead>
<tr>
<th width="100">Photo</th>
<th>Name</th>
<th>Designation</th>
<th width="180" class="text-end">Action</th>
</tr>
</thead>

<tbody>
<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
<td>
    <img src="../<?php echo $row['image']; ?>" 
         style="width:50px; height:50px; object-fit:cover; border-radius:8px;">
</td>
<td class="fw-bold"><?php echo htmlspecialchars($row['name']); ?></td>
<td><?php echo htmlspecialchars($row['designation']); ?></td>
<td class="text-end">
    <button type="button"
            class="btn-edit-modern me-2"
            data-bs-toggle="modal"
            data-bs-target="#editTeamModal"
            data-id="<?php echo (int) $row['id']; ?>"
            data-name="<?php echo htmlspecialchars((string) ($row['name'] ?? ''), ENT_QUOTES); ?>"
            data-designation="<?php echo htmlspecialchars((string) ($row['designation'] ?? ''), ENT_QUOTES); ?>"
            data-short-description="<?php echo htmlspecialchars((string) ($row['short_description'] ?? ''), ENT_QUOTES); ?>"
            data-linkedin-link="<?php echo htmlspecialchars((string) ($row['linkedin_link'] ?? ''), ENT_QUOTES); ?>"
            data-twitter-link="<?php echo htmlspecialchars((string) ($row['twitter_link'] ?? ''), ENT_QUOTES); ?>"
            data-email-link="<?php echo htmlspecialchars((string) ($row['email_link'] ?? ''), ENT_QUOTES); ?>"
            data-image="<?php echo htmlspecialchars((string) ($row['image'] ?? ''), ENT_QUOTES); ?>"
            data-image-url="<?php echo htmlspecialchars(!empty($row['image']) ? '../' . ltrim((string) $row['image'], '/') : '', ENT_QUOTES); ?>">
        <i class="fas fa-pen me-1"></i>
    </button>
    <a href="db/delete/delete_team.php?id=<?php echo $row['id']; ?>" 
       onclick="return confirm('Delete this member?')" 
       class="btn-delete-modern">
       <i class="fas fa-trash me-1"></i> 
    </a>
</td>
</tr>
<?php endwhile; ?>
</tbody>

</table>
</div>
</div>
</div>

</div>
</div>
</div>


<?php include 'footer.php'; ?>

<!-- Edit Modal -->
<div class="modal fade" id="editTeamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header modal-card-header">
                <h5 class="modal-title fw-bold mb-0">Edit Team Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="db/update/team_update.php" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="edit_id">
                    <input type="hidden" name="existing_image" id="edit_existing_image">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-modern">Full Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control form-control-modern" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-modern">Designation</label>
                            <input type="text" name="designation" id="edit_designation" class="form-control form-control-modern" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label-modern">Short Description</label>
                            <textarea name="short_description" id="edit_short_description" class="form-control form-control-modern" rows="3"></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label-modern">Current Image</label>
                            <div class="d-flex align-items-center gap-3">
                                <img id="edit_current_image_preview" src="" alt="Current image" class="current-preview d-none">
                                <span id="edit_current_image_empty" class="text-muted small">No image</span>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label-modern">Replace Profile Image (Optional)</label>
                            <input type="file" name="image" class="form-control form-control-modern" accept=".jpg,.jpeg,.png,.webp">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label-modern">LinkedIn URL</label>
                            <input type="text" name="linkedin_link" id="edit_linkedin_link" class="form-control form-control-modern">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label-modern">Twitter URL</label>
                            <input type="text" name="twitter_link" id="edit_twitter_link" class="form-control form-control-modern">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label-modern">Email URL</label>
                            <input type="text" name="email_link" id="edit_email_link" class="form-control form-control-modern">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save me-1"></i> Update Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('editTeamModal');
    if (!modalEl) {
        return;
    }

    modalEl.addEventListener('show.bs.modal', function(event) {
        const trigger = event.relatedTarget;
        if (!trigger) {
            return;
        }

        const byId = function(id) {
            return document.getElementById(id);
        };

        byId('edit_id').value = trigger.getAttribute('data-id') || '';
        byId('edit_name').value = trigger.getAttribute('data-name') || '';
        byId('edit_designation').value = trigger.getAttribute('data-designation') || '';
        byId('edit_short_description').value = trigger.getAttribute('data-short-description') || '';
        byId('edit_linkedin_link').value = trigger.getAttribute('data-linkedin-link') || '';
        byId('edit_twitter_link').value = trigger.getAttribute('data-twitter-link') || '';
        byId('edit_email_link').value = trigger.getAttribute('data-email-link') || '';
        byId('edit_existing_image').value = trigger.getAttribute('data-image') || '';

        const imageUrl = trigger.getAttribute('data-image-url') || '';
        const img = byId('edit_current_image_preview');
        const emptyText = byId('edit_current_image_empty');

        if (imageUrl !== '') {
            img.src = imageUrl;
            img.classList.remove('d-none');
            emptyText.classList.add('d-none');
        } else {
            img.src = '';
            img.classList.add('d-none');
            emptyText.classList.remove('d-none');
        }
    });
});
</script>
