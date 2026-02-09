<div class="navbar-custom">
    <div class="topbar container-fluid">
        <div class="d-flex align-items-center gap-1">
            <button class="button-toggle-menu"><i class="mdi mdi-menu"></i></button>
            <h4 class="page-title d-none d-sm-block">DSA Portal</h4>
        </div>
        <ul class="topbar-menu d-flex align-items-center gap-3">
            <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none nav-user" data-bs-toggle="dropdown" href="#">
                    <span class="d-lg-block d-none">
                        <h5 class="my-0 fw-normal"><?php echo $_SESSION['dsa_name'] ?? 'DSA'; ?><i class="ri-arrow-down-s-line fs-22 d-none d-sm-inline-block align-middle"></i></h5>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                    <a href="db/auth-logout.php" class="dropdown-item">
                        <i class="ri-logout-circle-r-line align-middle me-1"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</div>
