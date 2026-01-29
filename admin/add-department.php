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
        --slate-50: #f8fafc;
    }
    
    .content-page { background-color: #fcfcfd; min-height: 100vh; }

    /* Clickable Breadcrumbs */
    .breadcrumb-item a {
        color: var(--slate-600);
        text-decoration: none;
        transition: color 0.2s ease;
    }
    .breadcrumb-item a:hover {
        color: var(--slate-900);
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        color: var(--slate-900);
        font-weight: 700;
    }
    
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
        box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
    }

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

    .helper-box {
        background-color: var(--slate-50);
        border-radius: 8px;
        padding: 12px;
        border-left: 4px solid var(--slate-200);
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            
            <div class="">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home me-1"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="departments.php">Departments</a></li>
                        <li class="breadcrumb-item active"><?= $isEdit ? 'Edit' : 'New' ?></li>
                    </ol>
                </nav>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-7">

                    <div class="mb-4">
                        <h2 class="fw-bold text-dark mb-1">
                            <?= $isEdit ? 'Update Department' : 'New Department' ?>
                        </h2>
                  
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
                                        
                                  
                                    </div>

                                    <div class="col-12 mt-2 pt-4 border-top d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <button type="button" id="submit-btn" class="btn btn-submit-pro">
                                                <i class="fas fa-check-circle me-2"></i>
                                                <?= $isEdit ? 'Update Changes' : 'Create Department' ?>
                                            </button>
                                            <a href="departments.php" class="btn-cancel ms-4">
                                                Cancel
                                            </a>
                                        </div>
                                        
                                        <?php if($isEdit): ?>
                                            <small class="text-muted italic">ID: #<?= $department['id'] ?></small>
                                        <?php endif; ?>
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