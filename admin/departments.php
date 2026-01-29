<?php
include 'db/config.php';

// Handle Search Filter logic
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = $search ? "WHERE name LIKE '%$search%'" : "";

$res = mysqli_query($conn,
    "SELECT id, name, created_at FROM departments $where_clause ORDER BY id DESC"
);
?>

<?php include 'header.php'; ?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<style>
    :root {
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-400: #94a3b8;
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

    /* Search & Filter Bar */
    .filter-section {
        background: #ffffff;
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        padding: 1.25rem;
    }

    .search-input-group .form-control {
        border-radius: 8px;
        border: 1px solid var(--slate-200);
        padding: 0.6rem 1rem;
    }

    /* Modern Table Card */
    .card-modern {
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    /* Reuse your original button style strictly */
    .btn-submit-pro {
        background: var(--slate-900);
        color: #ffffff !important;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
    }
    .btn-submit-pro:hover {
        background: #334155 !important;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home me-1"></i> Dashboard</a></li>
                       
                        <li class="breadcrumb-item active" aria-current="page">Departments</li>
                    </ol>
                </nav>
                
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-0">Departments</h2>
                        <p class="text-muted small">Manage your organization's primary service divisions.</p>
                    </div>
                    <a href="add-department.php" class="btn btn-submit-pro">
                        <i class="fas fa-plus-circle me-1"></i> New Department
                    </a>
                </div>
            </div>

            <div class="filter-section mb-4">
                <form method="GET" class="row g-3">
                    <div class="col-md-5 col-lg-4">
                        <div class="input-group search-input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0" 
                                   placeholder="Search by name..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark w-100 rounded-2 fw-bold">Filter</button>
                    </div>
                    <?php if($search): ?>
                    <div class="col-md-1">
                        <a href="departments.php" class="btn btn-link text-muted mt-1 px-0">Clear</a>
                    </div>
                    <?php endif; ?>
                </form>
            </div>

            <div class="card card-modern">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th width="80" class="ps-4">#</th>
                                    <th>Department Name</th>
                                    <th>Created On</th>
                                    <th width="180" class="text-center pe-4">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(mysqli_num_rows($res) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($res)) { ?>
                                    <tr>
                                        <td class="ps-4 text-muted small">#<?= $row['id'] ?></td>
                                        <td class="fw-semibold"><?= htmlspecialchars($row['name']) ?></td>
                                        <td>
                                            <span class="text-muted small">
                                                <i class="far fa-calendar-alt me-1"></i> <?= date('d M Y', strtotime($row['created_at'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <a href="add-department.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-action-edit me-1">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="db/department-delete.php" method="POST" style="display:inline" onsubmit="return confirm('Archive this record?')">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-action-delete border-0 bg-transparent">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        No departments found matching your criteria.
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
</div>

<?php include 'footer.php'; ?>