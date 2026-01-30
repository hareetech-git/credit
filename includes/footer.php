</main>

    <!-- Footer -->
   <footer class="border-top" style="background-color:#EEEEEE; border-color:#e2e8f0 !important;">

        <!-- Main Footer Content -->
        <div class="container py-5">
            <div class="row text-center">
                <!-- Logo -->
                <div class="col-12 mb-4">
                    <h2 class="fw-bold mb-0" style="color: var(--primary-color); font-size: 2rem; letter-spacing: -0.5px;">
                        UDHAR CAPITAL
                    </h2>
                </div>
            </div>

            <!-- Horizontal Line -->
            <hr class="my-4" style="border-color: #e2e8f0;">

            <div class="row align-items-center">
                <!-- Left: Download App -->
                <div class="col-lg-4 text-lg-start text-center mb-3 mb-lg-0">
                    <span class="me-3 fw-semibold" style="color: #334155;">Download App</span>
                    <a href="#" class="d-inline-block">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" 
                             alt="Get it on Google Play" 
                             style="height: 40px;">
                    </a>
                </div>

                <!-- Center: Social Media Icons -->
                <div class="col-lg-4 text-center mb-3 mb-lg-0">
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="text-dark" style="font-size: 1.5rem; transition: color 0.3s ease;">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <span style="color: #cbd5e1;">|</span>
                        <a href="#" class="text-dark" style="font-size: 1.5rem; transition: color 0.3s ease;">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <span style="color: #cbd5e1;">|</span>
                        <a href="#" class="text-dark" style="font-size: 1.5rem; transition: color 0.3s ease;">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <span style="color: #cbd5e1;">|</span>
                        <a href="#" class="text-dark" style="font-size: 1.5rem; transition: color 0.3s ease;">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <span style="color: #cbd5e1;">|</span>
                        <a href="#" class="text-dark" style="font-size: 1.5rem; transition: color 0.3s ease;">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>

                <!-- Right: Links -->
                <div class="col-lg-4 text-lg-end text-center">
                    <a href="#" class="text-dark text-decoration-none fw-semibold me-3" style="transition: color 0.3s ease;">Media</a>
                    <span style="color: #cbd5e1;">|</span>
                    <a href="#" class="text-dark text-decoration-none fw-semibold ms-3" style="transition: color 0.3s ease;">FAQs</a>
                </div>
            </div>

            <!-- Horizontal Line -->
            <hr class="my-4" style="border-color: #e2e8f0;">

            <!-- Bottom Links -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-center gap-3 mb-3">
                        <a href="#" class="text-muted text-decoration-none small footer-link">Privacy Policy</a>
                        <a href="#" class="text-muted text-decoration-none small footer-link">Corporate Governance</a>
                        <a href="#" class="text-muted text-decoration-none small footer-link">RBI Disclaimer</a>
                        <a href="#" class="text-muted text-decoration-none small footer-link">Our Partners</a>
                        <a href="#" class="text-muted text-decoration-none small footer-link">Sitemap</a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-muted small mb-0">
                        Copyright © <?php echo date('Y'); ?> Udhar Capital India. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>
<!-- Sticky Loan Marquee -->
<div class="loan-marquee-fixed">
    <marquee behavior="scroll" direction="left" scrollamount="5">
        ⚠ Beware of fraud! UDHAR CAPITAL se loan lena hai easy & fast —
        ✔ Low interest
        ✔ Minimum documents
        ✔ Quick approval
        ✔ Secure & trusted platform —
        Sirf official website se hi apply karein
    </marquee>
</div>
<style>
    .loan-marquee-fixed {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: var(--primary-color);
        color: #fff;
        padding: 8px 0;
        font-size: 14px;
        font-weight: 500;
        z-index: 9999;
    }

    .loan-marquee-fixed marquee {
        white-space: nowrap;
    }

    /* content footer ke niche chhup na jaye */
    body {
        padding-bottom: 40px;
    }
</style>

    <!-- Footer Specific Styles using Root Variables -->
    <style>
        /* Footer hover effects using root variables */
        footer a.text-dark:hover {
            color: var(--primary-color) !important;
        }

        footer .footer-link:hover {
            color: var(--primary-color) !important;
        }
    </style>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Mobile Menu Toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const mobileMenu = document.getElementById('mobileMenu');
        
        if (mobileToggle) {
            // Toggle mobile menu
            mobileToggle.addEventListener('click', function() {
                this.classList.toggle('active');
                if (mobileMenu) mobileMenu.classList.toggle('active');
                if (mobileOverlay) mobileOverlay.classList.toggle('active');
            });
            
            // Close menu when clicking overlay
            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', function() {
                    mobileToggle.classList.remove('active');
                    if (mobileMenu) mobileMenu.classList.remove('active');
                    this.classList.remove('active');
                });
            }
        }
        
        // Mobile dropdown toggles
        const mobileLoanToggle = document.getElementById('mobileLoanToggle');
        const mobileDropdown = document.getElementById('mobileDropdown');
        
        if (mobileLoanToggle && mobileDropdown) {
            mobileLoanToggle.addEventListener('click', function(e) {
                e.preventDefault();
                const icon = this.querySelector('i');
                mobileDropdown.classList.toggle('active');
                if (icon) {
                    icon.style.transform = mobileDropdown.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0)';
                }
            });
        }

        // Scroll to form function
        function scrollToForm() {
            const form = document.getElementById('loanForm');
            if (form) {
                form.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            // Close mobile menu if open
            if (mobileToggle) mobileToggle.classList.remove('active');
            if (mobileMenu) mobileMenu.classList.remove('active');
            if (mobileOverlay) mobileOverlay.classList.remove('active');
        }
    </script>
</body>
</html>