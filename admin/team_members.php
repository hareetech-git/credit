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

<?php if(isset($_GET['msg'])): ?>
<div class="alert alert-success border-0 shadow-sm mb-4 py-3">
    <i class="fas fa-check-circle me-2"></i>
    <?php echo $_GET['msg']; ?>
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
<th width="120" class="text-end">Action</th>
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
