<?php
include 'db/config.php';

$id = (int)($_GET['id'] ?? 0);

$service = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT s.*, c.category_name, sc.sub_category_name
    FROM services s
    LEFT JOIN service_categories c ON c.id=s.category_id
    LEFT JOIN services_subcategories sc ON sc.id=s.sub_category_id
    WHERE s.id=$id
"));

if(!$service){
    header("Location: services.php");
    exit;
}

$features = mysqli_query($conn,"SELECT * FROM service_features WHERE service_id=$id");
$eligibility = mysqli_query($conn,"SELECT * FROM service_eligibility_criteria WHERE service_id=$id");
$documents = mysqli_query($conn,"SELECT * FROM service_documents WHERE service_id=$id");
$fees = mysqli_query($conn,"SELECT * FROM service_fees_charges WHERE service_id=$id");
$repayment = mysqli_query($conn,"SELECT * FROM service_loan_repayment WHERE service_id=$id");
$why = mysqli_query($conn,"SELECT * FROM service_why_choose_us WHERE service_id=$id");
$banks = mysqli_query($conn,"SELECT * FROM service_banks WHERE service_id=$id");

include 'header.php';
include 'topbar.php';
include 'sidebar.php';
?>

<div class="content-page">
<div class="content">
<div class="container-fluid pt-5">

<h3 class="fw-bold mb-3"><?= htmlspecialchars($service['title']) ?></h3>
<p class="text-muted">
    <?= htmlspecialchars($service['category_name']) ?> →
    <?= htmlspecialchars($service['sub_category_name']) ?>
</p>

<div class="card card-modern mb-4">
<div class="card-body">
<h5>Description</h5>
<p><?= nl2br(htmlspecialchars($service['long_description'])) ?></p>
</div>
</div>

<?php
function render_list($title, $rs, $key='title', $val='description'){
    if(mysqli_num_rows($rs)==0) return;
    echo "<div class='card card-modern mb-4'><div class='card-body'>";
    echo "<h5 class='mb-3'>$title</h5><ul>";
    while($r=mysqli_fetch_assoc($rs)){
        echo "<li><strong>".htmlspecialchars($r[$key]).":</strong> ".htmlspecialchars($r[$val])."</li>";
    }
    echo "</ul></div></div>";
}

render_list('Features', $features);
render_list('Eligibility', $eligibility, 'criteria_key','criteria_value');
render_list('Documents', $documents, 'doc_name','disclaimer');
render_list('Fees & Charges', $fees, 'fee_key','fee_value');
render_list('Loan Repayment', $repayment);
render_list('Why Choose Us', $why);
render_list('Banks', $banks, 'bank_key','bank_value');
?>

<a href="services.php" class="btn btn-outline-dark">Back</a>

</div>
</div>
</div>

<?php include 'footer.php'; ?>
