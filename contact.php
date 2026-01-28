<?php
require_once 'includes/header.php';

?>

<style>
    /* Simple Contact Page Styles */
    .contact-section {
        padding: 80px 0;
        background: #f8fafc;
    }
    
    .contact-header {
        text-align: center;
        margin-bottom: 60px;
    }
    
    .trusted-badge {
        display: inline-block;
        background: #e8f4ff;
        color: #1e40af;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    
    .contact-title {
        font-size: 48px;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 15px;
    }
    
    .contact-subtitle {
        font-size: 20px;
        color: #6b7280;
        max-width: 600px;
        margin: 0 auto 40px;
        line-height: 1.6;
    }
    
    .contact-container {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }
    
    .contact-info-side {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    
    .contact-form-side {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    
    .section-heading {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 10px;
    }
    
    .section-subheading {
        color: #6b7280;
        font-size: 16px;
        margin-bottom: 30px;
    }
    
    .contact-details {
        margin: 40px 0;
    }
    
    .contact-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .contact-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .contact-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #dc2626, #ef4444);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        flex-shrink: 0;
    }
    
    .contact-icon i {
        color: white;
        font-size: 20px;
    }
    
    .contact-content h4 {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 5px;
    }
    
    .contact-content p {
        color: #6b7280;
        margin-bottom: 8px;
        font-size: 16px;
    }
    
    .contact-link {
        color: #dc2626;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
    }
    
    .contact-link:hover {
        text-decoration: underline;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }
    
    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.2s;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }
    
    textarea.form-input {
        min-height: 120px;
        resize: vertical;
    }
    
    .checkbox-group {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
    }
    
    .checkbox-input {
        width: 18px;
        height: 18px;
        margin-right: 10px;
        accent-color: #dc2626;
    }
    
    .checkbox-label {
        font-size: 14px;
        color: #4b5563;
    }
    
    .submit-btn {
        background: linear-gradient(135deg, #dc2626, #ef4444);
        color: white;
        border: none;
        padding: 16px 32px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        width: 100%;
        transition: transform 0.2s;
    }
    
    .submit-btn:hover {
        transform: translateY(-2px);
    }
    
    .security-note {
        text-align: center;
        font-size: 12px;
        color: #9ca3af;
        margin-top: 15px;
    }
    
    .location-map {
        margin-top: 40px;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    
    .map-container {
        width: 100%;
        height: 400px;
    }
    
    .map-placeholder {
        width: 100%;
        height: 100%;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        font-size: 18px;
    }
    
    .location-details {
        padding: 30px;
        border-top: 1px solid #e5e7eb;
    }
    
    .location-details h3 {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 15px;
    }
    
    .location-details p {
        color: #6b7280;
        line-height: 1.6;
        margin-bottom: 10px;
    }
    
    /* Messages */
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
    }
    
    .alert-success {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    
    .alert-danger {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .contact-container {
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        .contact-title {
            font-size: 36px;
        }
        
        .contact-section {
            padding: 60px 20px;
        }
    }
    
    @media (max-width: 576px) {
        .contact-info-side,
        .contact-form-side {
            padding: 25px;
        }
        
        .contact-title {
            font-size: 32px;
        }
        
        .contact-item {
            flex-direction: column;
        }
        
        .contact-icon {
            margin-bottom: 15px;
        }
    }
</style>

<section class="contact-section">
    <div class="contact-header">
        <div class="trusted-badge">
            <i class="fas fa-star me-1"></i> Trusted By the Genius People with <strong>Udhaar Capital</strong>
        </div>
        
        <h1 class="contact-title">GET IN TOUCH</h1>
        
        <p class="contact-subtitle">
            Media leadership skills before cross-media innovation main technology develop standardized platforms without consalt.
        </p>
    </div>
    
    <div class="container">
        <div class="contact-container">
            <!-- Left Side: Contact Form -->
            <div class="contact-form-side">
                <h2 class="section-heading">CONTACT US</h2>
                <p class="section-subheading">Fill the form to get in touch with Us</p>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <div><i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="name">Your Name</label>
                        <input type="text" name="name" id="name" class="form-input" 
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone No</label>
                        <input type="tel" name="phone" id="phone" class="form-input" 
                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" 
                               pattern="[0-9]{10}" maxlength="10" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">E-Mail Address</label>
                        <input type="email" name="email" id="email" class="form-input" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="subject">Subject</label>
                        <input type="text" name="subject" id="subject" class="form-input" 
                               value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="message">Write Message</label>
                        <textarea name="message" id="message" class="form-input" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" name="request_call" id="requestCall" class="checkbox-input"
                               <?php echo (isset($_POST['request_call']) && $_POST['request_call']) ? 'checked' : ''; ?>>
                        <label class="checkbox-label" for="requestCall">
                            <i class="fas fa-phone me-1"></i> Request Call Back
                        </label>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane me-2"></i> Send Message
                    </button>
                    
                    <div class="security-note">
                        <i class="fas fa-lock me-1"></i> Your information is secure and encrypted
                    </div>
                </form>
            </div>
            
            <!-- Right Side: Contact Info -->
            <div class="contact-info-side">
                <div class="contact-details">
                    <!-- Phone -->
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-content">
                            <h4>Call us Anytime</h4>
                            <p>+91 8810380146</p>
                            <a href="tel:+918810380146" class="contact-link">
                                <i class="fas fa-phone-alt me-1"></i> Call Now
                            </a>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-content">
                            <h4>Email Us</h4>
                            <p>contact@taxesquire.in</p>
                            <a href="mailto:contact@taxesquire.in" class="contact-link">
                                <i class="fas fa-envelope me-1"></i> Send Email
                            </a>
                        </div>
                    </div>
                    
                    <!-- Location -->
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-content">
                            <h4>Our Locations</h4>
                            <p>
                                Kasana Tower, 712-A, Alpha-1 Commercial Belt,<br>
                                Block A, Alpha 1, Greater Noida,<br>
                                Uttar Pradesh 201310
                            </p>
                            <a href="https://maps.google.com/?q=Kasana+Tower+Greater+Noida" target="_blank" class="contact-link">
                                <i class="fas fa-map me-1"></i> View on Map
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Map Section -->
        <div class="location-map">
            <div class="map-container">
                <!-- Google Maps Embed -->
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3508.136847287864!2d77.497722!3d28.474722!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ceb8c00e7e3b3%3A0x7c8b6c7b5c8c8c8c!2sKasana%20Tower%2C%20Alpha-I%20Commercial%20Belt%2C%20Greater%20Noida%2C%20Uttar%20Pradesh%20201310!5e0!3m2!1sen!2sin!4v1641234567890!5m2!1sen!2sin" 
                    width="100%" 
                    height="400" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            
           
        </div>
    </div>
</section>

<script>
    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\D/g, '').substring(0, 10);
            });
        }
        
        // Form submission validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const phone = document.getElementById('phone').value;
                const email = document.getElementById('email').value;
                let isValid = true;
                
                // Phone validation
                if (phone.length !== 10 || !/^\d+$/.test(phone)) {
                    alert('Please enter a valid 10-digit phone number');
                    isValid = false;
                }
                
                // Email validation
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    alert('Please enter a valid email address');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        }
    });
</script>

<?php
require_once 'includes/footer.php';
?>