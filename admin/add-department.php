<?php

include 'db/config.php';

$isEdit = false;
$department = null;

if (isset($_GET['id'])) {
    $isEdit = true;
    $id = (int)$_GET['id'];

    $res = mysqli_query($conn, "SELECT * FROM departments WHERE id = $id");
    $department = mysqli_fetch_assoc($res);
}
?>

<?php include 'header.php'; ?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <div class="mb-4 text-center text-md-start">
                        <h2 class="fw-bold text-dark">
                            <?= $isEdit ? 'Update Department' : 'New Department' ?>
                        </h2>
                        <p class="text-muted">
                            Organize your services by defining departments.
                        </p>
                    </div>

                    <div class="card card-modern">
                        <div class="card-body p-4 p-md-5">

                            <div id="alert-container" style="display:none" class="alert py-3 mb-4"></div>

                            <form id="department-form">
                                <input type="hidden" id="department-id" value="<?= $isEdit ? $department['id'] : '' ?>">

                                <div class="row">

                                    <div class="col-md-7 mb-4">
                                        <label class="form-label">Department Name</label>
                                        <input type="text"
                                               class="form-control"
                                               id="department-name"
                                               placeholder="e.g. Legal Services"
                                               value="<?= $isEdit ? htmlspecialchars($department['name']) : '' ?>"
                                               required>
                                        <small class="text-muted mt-2 d-block">
                                            This name is for internal service organization.
                                        </small>
                                    </div>

                                    <div class="col-12 mt-4 pt-3 border-top">
                                        <button type="button" id="submit-btn" class="btn btn-submit">
                                            <?= $isEdit ? 'Save Changes' : 'Create Department' ?>
                                        </button>
                                        <a href="departments.php"
                                           class="btn btn-link text-muted ms-2 text-decoration-none fw-medium">
                                            Cancel
                                        </a>
                                    </div>

                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
document.getElementById('submit-btn').addEventListener('click', function () {

    const id   = document.getElementById('department-id').value;
    const name = document.getElementById('department-name').value.trim();

    if (!name) return;

    fetch('db/department-action.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id, name })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.href = 'departments.php';
        }
    });
});
</script>
