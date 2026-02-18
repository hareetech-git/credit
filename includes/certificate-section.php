<?php
// Certificate Section - Can be included in any page
// Make sure $conn is available from the parent page

if (!isset($conn)) {
    // If $conn is not available, try to include connection
    include_once 'connection.php';
}

// Fetch all certificates
$certificate_query = "SELECT name, certificate_img FROM certificates ORDER BY id DESC";
$certificate_result = mysqli_query($conn, $certificate_query);
$certificates = [];
if ($certificate_result && mysqli_num_rows($certificate_result) > 0) {
    while ($row = mysqli_fetch_assoc($certificate_result)) {
        $certificates[] = $row;
    }
}
?>

<!-- Certificates & Awards Section with Auto Slider -->
<section class="py-5 certificate-section" style="background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                <i class="fas fa-award me-2"></i> Our Achievements
            </span>
            <h2 class="fw-bold mb-3">Our Certificates & Awards</h2>
            <p class="text-muted">Udhaar Capital's achievements so far.</p>
        </div>
        
        <?php if (!empty($certificates)): ?>
            <div class="certificate-slider-container position-relative">
                <div class="certificate-slider-wrapper">
                    <div class="certificate-track" id="certificateTrack">
                        <?php foreach ($certificates as $certificate): ?>
                            <div class="certificate-slide">
                                <div class="certificate-card">
                                    <div class="certificate-image-wrapper">
                                        <!-- Background Image Layer -->
                                        <div class="certificate-bg-layer" style="background-image: url('includes/assets/bg_certi.jpg');"></div>
                                        
                                        <!-- Foreground Certificate Image -->
                                        <?php if (!empty($certificate['certificate_img'])): ?>
                                            <img src="admin/<?= htmlspecialchars($certificate['certificate_img']) ?>" 
                                                 alt="<?= htmlspecialchars($certificate['name']) ?>"
                                                 class="certificate-image"
                                                 onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="certificate-placeholder" style="display: none;">
                                                <i class="fas fa-award fa-4x text-primary opacity-25"></i>
                                            </div>
                                        <?php else: ?>
                                            <div class="certificate-placeholder">
                                                <i class="fas fa-award fa-4x text-primary opacity-25"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="certificate-name">
                                        <p class="mb-0 fw-bold"><?= htmlspecialchars($certificate['name']) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Navigation Arrows -->
                <button class="certificate-arrow certificate-arrow-prev" onclick="moveCertificateSlide(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="certificate-arrow certificate-arrow-next" onclick="moveCertificateSlide(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <!-- Dots Navigation -->
            <div class="certificate-dots text-center mt-4" id="certificateDots"></div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-award mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                <p>No certificates available yet.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.certificate-section .certificate-slider-container {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 40px;
}

.certificate-section .certificate-slider-wrapper {
    overflow: hidden;
    margin: 0 -10px;
}

.certificate-section .certificate-track {
    display: flex;
    transition: transform 0.5s ease;
    gap: 20px;
}

.certificate-section .certificate-slide {
    flex: 0 0 calc(20% - 16px); /* 5 items per row */
    min-width: 200px;
    padding: 10px;
}

.certificate-section .certificate-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    height: 100%;
    border: 1px solid #f1f5f9;
}

.certificate-section .certificate-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    border-color: var(--primary-color);
}

.certificate-section .certificate-image-wrapper {
    position: relative;
    height: 180px; /* Slightly increased to accommodate background */
    overflow: hidden;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 15px;
}

/* Background Image Layer */
.certificate-section .certificate-bg-layer {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    opacity: 0.15; /* Adjust opacity to make it subtle */
    z-index: 1;
    pointer-events: none; /* So it doesn't interfere with clicks */
}

/* Overlay to ensure text/foreground remains readable */
.certificate-section .certificate-image-wrapper::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.85); /* White overlay to lighten the background */
    z-index: 2;
    pointer-events: none;
}

/* Certificate Image - Now appears above background */
.certificate-section .certificate-image {
    position: relative;
    max-width: 85%; /* Slightly smaller to show background */
    max-height: 120px;
    object-fit: contain;
    transition: transform 0.3s ease;
    z-index: 3;
    filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
}

.certificate-section .certificate-card:hover .certificate-image {
    transform: scale(1.05);
}

.certificate-section .certificate-placeholder {
    position: relative;
    width: 100%;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 3;
}

.certificate-section .certificate-name {
    padding: 15px;
    text-align: center;
    border-top: 1px solid #f1f5f9;
    background: white;
    position: relative;
    z-index: 3;
}

.certificate-section .certificate-name p {
    font-size: 0.9rem;
    color: #2d3748;
    line-height: 1.4;
}

/* Navigation Arrows */
.certificate-section .certificate-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    border: 1px solid #e2e8f0;
    color: var(--primary-color);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 10;
}

.certificate-section .certificate-arrow:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.certificate-section .certificate-arrow-prev {
    left: 0;
}

.certificate-section .certificate-arrow-next {
    right: 0;
}

/* Dots */
.certificate-section .certificate-dots {
    display: flex;
    justify-content: center;
    gap: 8px;
}

.certificate-section .certificate-dots .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #cbd5e1;
    cursor: pointer;
    transition: all 0.3s ease;
}

.certificate-section .certificate-dots .dot.active {
    background: var(--primary-color);
    width: 24px;
    border-radius: 20px;
}

/* Responsive */
@media (max-width: 992px) {
    .certificate-section .certificate-slide {
        flex: 0 0 calc(25% - 15px); /* 4 items per row */
    }
}

@media (max-width: 768px) {
    .certificate-section .certificate-slide {
        flex: 0 0 calc(33.333% - 14px); /* 3 items per row */
    }
    
    .certificate-section .certificate-arrow {
        display: none;
    }
    
    .certificate-section .certificate-image-wrapper {
        height: 160px;
    }
}

@media (max-width: 576px) {
    .certificate-section .certificate-slide {
        flex: 0 0 calc(50% - 10px); /* 2 items per row */
    }
    
    .certificate-section .certificate-image-wrapper {
        height: 150px;
    }
}
</style>

<script>
// Certificate Slider Functionality - Namespaced to avoid conflicts
(function() {
    let currentCertificateIndex = 0;
    const certificateTrack = document.getElementById('certificateTrack');
    const certificateSlides = document.querySelectorAll('.certificate-slide');
    const dotsContainer = document.getElementById('certificateDots');
    let slidesPerView = 5;
    let slideInterval;

    // Only initialize if elements exist
    if (!certificateTrack || certificateSlides.length === 0) return;

    // Calculate slides per view based on screen width
    function updateSlidesPerView() {
        if (window.innerWidth <= 576) {
            slidesPerView = 2;
        } else if (window.innerWidth <= 768) {
            slidesPerView = 3;
        } else if (window.innerWidth <= 992) {
            slidesPerView = 4;
        } else {
            slidesPerView = 5;
        }
        return slidesPerView;
    }

    // Create dots
    function createDots() {
        if (!dotsContainer) return;
        const totalSlides = Math.ceil(certificateSlides.length / slidesPerView);
        dotsContainer.innerHTML = '';
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('span');
            dot.className = 'dot' + (i === 0 ? ' active' : '');
            dot.onclick = () => goToCertificateSlide(i);
            dotsContainer.appendChild(dot);
        }
    }

    // Update dots
    function updateDots() {
        const dots = document.querySelectorAll('.certificate-dots .dot');
        const totalSlides = Math.ceil(certificateSlides.length / slidesPerView);
        const activeIndex = Math.floor(currentCertificateIndex / slidesPerView);
        
        dots.forEach((dot, index) => {
            if (index === activeIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    // Move slide
    window.moveCertificateSlide = function(direction) {
        const maxIndex = Math.max(0, certificateSlides.length - slidesPerView);
        currentCertificateIndex += direction * slidesPerView;
        
        if (currentCertificateIndex < 0) {
            currentCertificateIndex = 0;
        } else if (currentCertificateIndex > maxIndex) {
            currentCertificateIndex = maxIndex;
        }
        
        const slideWidth = certificateSlides[0]?.offsetWidth || 200;
        const gap = 20; // gap between slides
        certificateTrack.style.transform = `translateX(-${currentCertificateIndex * (slideWidth + gap)}px)`;
        updateDots();
    }

    // Go to specific slide group
    function goToCertificateSlide(index) {
        const slideWidth = certificateSlides[0]?.offsetWidth || 200;
        const gap = 20;
        currentCertificateIndex = index * slidesPerView;
        certificateTrack.style.transform = `translateX(-${currentCertificateIndex * (slideWidth + gap)}px)`;
        updateDots();
    }

    // Auto slide
    function startCertificateAutoSlide() {
        if (certificateSlides.length <= slidesPerView) return;
        stopCertificateAutoSlide();
        slideInterval = setInterval(() => {
            const maxIndex = Math.max(0, certificateSlides.length - slidesPerView);
            currentCertificateIndex += slidesPerView;
            if (currentCertificateIndex > maxIndex) {
                currentCertificateIndex = 0;
            }
            const slideWidth = certificateSlides[0]?.offsetWidth || 200;
            const gap = 20;
            certificateTrack.style.transform = `translateX(-${currentCertificateIndex * (slideWidth + gap)}px)`;
            updateDots();
        }, 4000);
    }

    function stopCertificateAutoSlide() {
        if (slideInterval) {
            clearInterval(slideInterval);
        }
    }

    // Initialize
    function init() {
        updateSlidesPerView();
        createDots();
        startCertificateAutoSlide();
        
        // Pause on hover
        const container = document.querySelector('.certificate-slider-container');
        if (container) {
            container.addEventListener('mouseenter', stopCertificateAutoSlide);
            container.addEventListener('mouseleave', startCertificateAutoSlide);
        }
        
        // Update on resize
        window.addEventListener('resize', function() {
            updateSlidesPerView();
            createDots();
            currentCertificateIndex = 0;
            certificateTrack.style.transform = 'translateX(0)';
        });
    }

    // Run initialization when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>