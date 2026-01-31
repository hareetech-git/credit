</main>

    <!-- Footer -->
    <footer class="border-top" style="background-color:#0f172a; border-color:#1e293b !important;">

        <!-- Wave Divider -->
        <div class="wave-divider">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
            </svg>
        </div>

        <!-- Main Footer Content -->
        <div class="container py-5" style="position: relative; padding-top: 5rem !important;">
            <div class="row text-center">
                <!-- Logo -->
                <div class="col-12 mb-4">
                    <h2 class="fw-bold mb-0 footer-logo" style=" font-size: 2rem; letter-spacing: -0.5px;">
                        UDHAR CAPITAL
                    </h2>
                    <p class="footer-tagline">Your Trusted Partner in Financial Growth</p>
                </div>
            </div>

            <!-- Horizontal Line -->
            <hr class="my-4" style="border-color: rgba(255, 255, 255, 0.1);">

            <div class="row align-items-center">
                <!-- Left: Download App -->
                <div class="col-lg-4 text-lg-start text-center mb-3 mb-lg-0">
                    <span class="me-3 fw-semibold" style="color: #cbd5e1;">Download App</span>
                    <a href="#" class="d-inline-block app-badge-link">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" 
                             alt="Get it on Google Play" 
                             class="app-badge"
                             style="height: 40px;">
                    </a>
                </div>

                <!-- Center: Social Media Icons -->
                <div class="col-lg-4 text-center mb-3 mb-lg-0">
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="social-icon facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <span style="color: #334155;">|</span>
                        <a href="#" class="social-icon youtube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <span style="color: #334155;">|</span>
                        <a href="#" class="social-icon twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <span style="color: #334155;">|</span>
                        <a href="#" class="social-icon linkedin">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <span style="color: #334155;">|</span>
                        <a href="#" class="social-icon instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>

                <!-- Right: Links -->
                <div class="col-lg-4 text-lg-end text-center">
                    <a href="#" class="footer-top-link text-decoration-none fw-semibold me-3" style="transition: color 0.3s ease;">Media</a>
                    <span style="color: #334155;">|</span>
                    <a href="#" class="footer-top-link text-decoration-none fw-semibold ms-3" style="transition: color 0.3s ease;">FAQs</a>
                </div>
            </div>

            <!-- Horizontal Line -->
            <hr class="my-4" style="border-color: rgba(255, 255, 255, 0.1);">

            <!-- Bottom Links -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-center gap-3 mb-3">
                        <a href="#" class="footer-link text-decoration-none small">Privacy Policy</a>
                        <a href="#" class="footer-link text-decoration-none small">Corporate Governance</a>
                        <a href="#" class="footer-link text-decoration-none small">RBI Disclaimer</a>
                        <a href="#" class="footer-link text-decoration-none small">Our Partners</a>
                        <a href="#" class="footer-link text-decoration-none small">Sitemap</a>
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
        </div>
    
    </footer>

   

    <style>
        /* Footer Background Pattern */
        footer {
            position: relative;
            overflow: hidden;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(37, 99, 235, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(124, 58, 237, 0.08) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Wave Divider */
        .wave-divider {
            position: absolute;
            top: -1px;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
        }

        .wave-divider svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 60px;
        }

        .wave-divider .shape-fill {
            fill: #EEEEEE;
        }

        /* Footer Logo Enhancement */
        .footer-logo {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800 !important;
            letter-spacing: -1px !important;
            color: white !important ;
        }

        .footer-tagline {
            color: #94a3b8;
            font-size: 0.95rem;
            margin-top: 0.5rem;
        }

        /* App Badge Hover */
        .app-badge-link {
            transition: transform 0.3s ease;
            display: inline-block;
        }

        .app-badge-link:hover {
            transform: scale(1.05);
        }

        .app-badge {
            filter: brightness(0.95);
            transition: filter 0.3s ease;
        }

        .app-badge-link:hover .app-badge {
            filter: brightness(1.1);
        }

        /* Social Icons Enhancement */
        .social-icon {
            color: #fff;
            font-size: 1.5rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            position: relative;
        }

        .social-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), #8b5cf6);
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: -1;
        }

        .social-icon:hover {
            transform: translateY(-8px) scale(1.15);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);
        }

        .social-icon:hover::before {
            opacity: 1;
        }

        .social-icon.facebook:hover::before {
            background: linear-gradient(135deg, #1877f2, #4267B2);
        }

        .social-icon.youtube:hover::before {
            background: linear-gradient(135deg, #ff0000, #c4302b);
        }

        .social-icon.twitter:hover::before {
            background: linear-gradient(135deg, #1da1f2, #0d8bd9);
        }

        .social-icon.linkedin:hover::before {
            background: linear-gradient(135deg, #0077b5, #00669c);
        }

        .social-icon.instagram:hover::before {
            background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045);
        }

        /* Footer Top Links */
        .footer-top-link {
            color: #cbd5e1;
            position: relative;
        }

        .footer-top-link::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .footer-top-link:hover {
            color: #fff !important;
        }

        .footer-top-link:hover::after {
            width: 100%;
        }

        /* Footer Bottom Links */
        .footer-link {
            color: #94a3b8;
            position: relative;
            transition: all 0.3s ease;
        }

        .footer-link::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .footer-link:hover {
            color: #fff !important;
        }

        .footer-link:hover::after {
            width: 100%;
        }

        /* Copyright Text */
        .text-muted {
            color: #64748b !important;
        }

        /* Loan Marquee Styling */
        .loan-marquee-fixed {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #0b081b;;
            color: #fcf9f9;
            padding: 12px 0;
            font-size: 14px;
            font-weight: 500;
            z-index: 9999;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.3);
        }

        .loan-marquee-fixed marquee {
            white-space: nowrap;
        }

        /* Content footer ke niche chhup na jaye */
        body {
            padding-bottom: 50px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .wave-divider svg {
                height: 40px;
            }

            .footer-logo {
                font-size: 1.75rem !important;
                color:white !important;
            }

            .social-icon {
                width: 40px;
                height: 40px;
                font-size: 1.3rem;
            }
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

</body>
</html>