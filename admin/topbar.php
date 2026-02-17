
		<!-- ========== Topbar Start ========== -->
<div class="navbar-custom">
    <div class="topbar container-fluid">
        <div class="d-flex align-items-center gap-1">

            <!-- Topbar Brand Logo -->
            <div class="logo-topbar">
                <!-- Logo light -->
                <a href="index.php" class="logo-light">
                    <span class="logo-lg">
                        <img src="assets/udhaar_logo.png" alt="logo">
                    </span>
                    <span class="logo-sm">
                        <img src="assets/udhaar_logo.png" alt="small logo">
                    </span>
                </a>

                <!-- Logo Dark -->
                <a href="index.php" class="logo-dark">
                    <span class="logo-lg">
                        <img src="assets/udhaar_logo.png" alt="dark logo">
                    </span>
                    <span class="logo-sm">
                        <img src="assets/udhaar_logo.png" alt="small logo">
                    </span>
                </a>
            </div>

            <!-- Sidebar Menu Toggle Button -->
            <button class="button-toggle-menu">
                <i class="mdi mdi-menu"></i>
            </button>

            <!-- Page Title -->
            <h4 class="page-title d-none d-sm-block">Dashboards</h4>
        </div>

        <ul class="topbar-menu d-flex align-items-center gap-3">
            

            <li class="d-none d-sm-inline-block">
                <a class="nav-link" data-bs-toggle="offcanvas" href="#theme-settings-offcanvas">
                    <span class="ri-settings-3-line fs-22"></span>
                </a>
            </li>

            <li class="d-none d-sm-inline-block">
                <div class="nav-link" id="light-dark-mode">
                    <i class="ri-moon-line fs-22"></i>
                </div>
            </li>

            <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none nav-user" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    
                    <span class="d-lg-block d-none">
                        <h5 class="my-0 fw-normal">Admin<i class="ri-arrow-down-s-line fs-22 d-none d-sm-inline-block align-middle"></i></h5>
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
<!-- ========== Topbar End ========== -->
