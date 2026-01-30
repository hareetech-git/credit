<?php
include 'db/config.php';
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

$enquiries = mysqli_query($conn, "
    SELECT * 
    FROM enquiries
    ORDER BY id DESC
");
?>

<div class="content-page">
<div class="content">
<div class="container-fluid pt-5">

<h4 class="fw-bold mb-4">User Enquiries</h4>

<div class="card">
<div class="card-body">

<table class="table table-bordered table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Loan Type</th>
            <th>Message</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php if(mysqli_num_rows($enquiries) > 0): ?>
            <?php $i=1; while($row = mysqli_fetch_assoc($enquiries)): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['loan_type_name']) ?></td>
                    <td style="max-width:300px">
                        <?= nl2br(htmlspecialchars($row['query_message'])) ?>
                    </td>
                    <td><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center text-muted">
                    No enquiries received yet
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</div>
</div>

</div>
</div>
</div>


