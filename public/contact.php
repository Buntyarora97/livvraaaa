<?php
require_once '../includes/config.php';
require_once '../includes/models/ContactInquiry.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            ContactInquiry::create([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'subject' => $subject,
                'message' => $message
            ]);
            $success = true;
        } catch (Exception $e) {
            $error = 'Failed to submit your inquiry. Please try again.';
        }
    }
}

$pageTitle = 'Contact Us';
require_once '../includes/header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="page-banner-content">
        <h1>Contact Us</h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span>/</span>
            <span>Contact</span>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="contact-form-wrapper reveal-left">
                <h3>Send Us a Message</h3>
                <p style="color: var(--text-light); margin-bottom: 25px;">Have a question or feedback? We'd love to hear from you. Fill out the form below and we'll get back to you as soon as possible.</p>
                
                <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>Thank you for your message! We'll get back to you soon.</span>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
                <?php endif; ?>
                
                <form action="contact.php" method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" placeholder="Enter your name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" placeholder="Enter your phone">
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <select id="subject" name="subject">
                                <option value="">Select a subject</option>
                                <option value="General Inquiry">General Inquiry</option>
                                <option value="Order Related">Order Related</option>
                                <option value="Product Information">Product Information</option>
                                <option value="Feedback">Feedback</option>
                                <option value="Complaint">Complaint</option>
                                <option value="Business Partnership">Business Partnership</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="message">Your Message *</label>
                        <textarea id="message" name="message" placeholder="Write your message here..." required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
            
            <!-- Contact Info -->
            <div class="contact-info-wrapper reveal-right">
                <h3>Get In Touch</h3>
                <p style="color: var(--text-light); margin-bottom: 25px;">We're here to help and answer any question you might have. We look forward to hearing from you!</p>
                
                <div class="contact-info-cards">
                    <div class="contact-info-card">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Our Address</h4>
                            <p><?php echo SITE_ADDRESS; ?></p>
                        </div>
                    </div>
                    
                    <div class="contact-info-card">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Email Us</h4>
                            <a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a>
                            <p>We reply within 24 hours</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-card">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Call Us</h4>
                            <a href="tel:<?php echo SITE_PHONE; ?>"><?php echo SITE_PHONE; ?></a>
                            <p>Mon - Sat: 9:00 AM - 7:00 PM</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-card">
                        <div class="contact-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="contact-text">
                            <h4>WhatsApp</h4>
                            <a href="https://wa.me/919876543210" target="_blank">+91 9876543210</a>
                            <p>Quick responses on WhatsApp</p>
                        </div>
                    </div>
                </div>
                
                <div class="contact-map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3444.5!2d74.9455!3d30.2110!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzDCsDEyJzM5LjYiTiA3NMKwNTYnNDMuOCJF!5e0!3m2!1sen!2sin!4v1234567890" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="section section-light">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge"><i class="fas fa-question-circle"></i> FAQ</span>
            <h2 class="section-title">Frequently Asked <span>Questions</span></h2>
        </div>
        <div style="max-width: 800px; margin: 0 auto;">
            <div class="why-choose-card reveal" style="text-align: left; margin-bottom: 20px;">
                <h4 style="margin-bottom: 10px; color: var(--dark-bg);">What is your return policy?</h4>
                <p style="color: var(--text-light);">We offer a 30-day hassle-free return policy on all products. If you're not satisfied, contact us for a full refund.</p>
            </div>
            <div class="why-choose-card reveal delay-100" style="text-align: left; margin-bottom: 20px;">
                <h4 style="margin-bottom: 10px; color: var(--dark-bg);">How long does shipping take?</h4>
                <p style="color: var(--text-light);">Standard delivery takes 3-5 business days. Express delivery is available for metro cities with 1-2 day delivery.</p>
            </div>
            <div class="why-choose-card reveal delay-200" style="text-align: left; margin-bottom: 20px;">
                <h4 style="margin-bottom: 10px; color: var(--dark-bg);">Are your products safe?</h4>
                <p style="color: var(--text-light);">Yes, all our products are 100% natural, lab-tested, and manufactured in GMP-certified facilities. They are completely safe for long-term use.</p>
            </div>
            <div class="why-choose-card reveal delay-300" style="text-align: left;">
                <h4 style="margin-bottom: 10px; color: var(--dark-bg);">Do you offer COD?</h4>
                <p style="color: var(--text-light);">Yes, we offer Cash on Delivery across India.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
