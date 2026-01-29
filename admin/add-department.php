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

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-200: #e2e8f0;
    }
    .content-page { background-color: #fcfcfd; }
    
    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .form-label {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: var(--slate-600);
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e1;
        transition: all 0.2s;
    }

    .form-control:focus {
        border-color: var(--slate-900);
        box-shadow: none;
    }

    /* Fixed Button Hover States */
    .btn-submit-pro {
        background: var(--slate-900);
        color: #ffffff !important;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
    }

    .btn-submit-pro:hover {
        background: #334155 !important;
        color: #ffffff !important;
    }

    .btn-cancel {
        font-weight: 600;
        color: var(--slate-600);
        text-decoration: none;
        transition: color 0.2s;
    }

    .btn-cancel:hover {
        color: var(--slate-900);
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid pt-5">

            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="mb-4 text-center text-md-start">
                        <h2 class="fw-bold text-dark mb-1">
                            <?= $isEdit ? 'Update Department' : 'New Department' ?>
                        </h2>
                        <p class="text-muted small">
                            Define a high-level division to organize your service categories.
                        </p>
                    </div>

                    <div class="card card-modern">
                        <div class="card-body p-4 p-md-5">

                            <div id="alert-container" style="display:none" class="alert py-3 mb-4 border-0 shadow-sm"></div>

                            <form id="department-form">
                                <input type="hidden" id="department-id" value="<?= $isEdit ? $department['id'] : '' ?>">

                                <div class="row">
                                    <div class="col-12 mb-4">
                                        <label class="form-label">Department Name</label>
                                        <input type="text"
                                               class="form-control"
                                               id="department-name"
                                               placeholder="e.g. Personal Banking"
                                               value="<?= $isEdit ? htmlspecialchars($department['name']) : '' ?>"
                                               required>
                                        <small class="text-muted mt-2 d-block">
                                            This name is used for internal service organization and hierarchy.
                                        </small>
                                    </div>

                                    <div class="col-12 mt-2 pt-4 border-top d-flex align-items-center">
                                        <button type="button" id="submit-btn" class="btn btn-submit-pro">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <?= $isEdit ? 'Update Department' : 'Create Department' ?>
                                        </button>
                                        <a href="departments.php" class="btn-cancel ms-4">
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
    const submitBtn = this;
    const alertBox = document.getElementById('alert-container');
    const id = document.getElementById('department-id').value;
    const name = document.getElementById('department-name').value.trim();

    if (!name) {
        alertBox.className = "alert alert-danger py-3 mb-4 border-0 shadow-sm";
        alertBox.textContent = "Please enter a department name.";
        alertBox.style.display = "block";
        return;
    }

    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';

    fetch('db/department-action.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id, name })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alertBox.className = "alert alert-success py-3 mb-4 border-0 shadow-sm";
            alertBox.textContent = "Department saved successfully. Redirecting...";
            alertBox.style.display = "block";
            setTimeout(() => {
                window.location.href = 'departments.php';
            }, 1000);
        } else {
            alertBox.className = "alert alert-danger py-3 mb-4 border-0 shadow-sm";
            alertBox.textContent = data.message || "An error occurred.";
            alertBox.style.display = "block";
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i> Try Again';
        }
    })
    .catch(err => {
        alertBox.className = "alert alert-danger py-3 mb-4 border-0 shadow-sm";
        alertBox.textContent = "Network error. Please try again.";
        alertBox.style.display = "block";
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i> Try Again';
    });
});
</script>