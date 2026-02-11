<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php include 'db/config.php'; ?>

<div class="content-page">
<div class="container-fluid">

<h4 class="mb-4">Manage Team Members</h4>

<?php if(isset($_GET['msg'])): ?>
<div class="alert alert-success"><?php echo $_GET['msg']; ?></div>
<?php endif; ?>

<div class="card p-4 mb-4">
<form method="POST" action="db/insert/team_handler.php" enctype="multipart/form-data">

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="col-md-6 mb-3">
        <label>Designation</label>
        <input type="text" name="designation" class="form-control" required>
    </div>
</div>

<div class="mb-3">
    <label>Short Description</label>
    <textarea name="short_description" class="form-control" rows="2"></textarea>
</div>

<div class="mb-3">
    <label>Upload Image</label>
    <input type="file" name="image" class="form-control" required>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label>LinkedIn Link</label>
        <input type="text" name="linkedin_link" class="form-control">
    </div>

    <div class="col-md-4 mb-3">
        <label>Twitter Link</label>
        <input type="text" name="twitter_link" class="form-control">
    </div>

    <div class="col-md-4 mb-3">
        <label>Email Link</label>
        <input type="text" name="email_link" class="form-control">
    </div>
</div>

<button type="submit" class="btn btn-primary">Add Member</button>

</form>
</div>

<?php
$result = mysqli_query($conn, "SELECT * FROM team_members ORDER BY id DESC");
?>

<div class="card p-4">
<table class="table table-bordered">
<tr>
<th>Image</th>
<th>Name</th>
<th>Designation</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
<td><img src="../<?php echo $row['image']; ?>" width="60"></td>
<td><?php echo htmlspecialchars($row['name']); ?></td>
<td><?php echo htmlspecialchars($row['designation']); ?></td>
<td>
    <a href="db/delete/delete_team.php?id=<?php echo $row['id']; ?>" 
       onclick="return confirm('Delete this member?')" 
       class="btn btn-danger btn-sm">Delete</a>
</td>
</tr>
<?php endwhile; ?>

</table>
</div>

</div>
</div>

<?php include 'footer.php'; ?>
