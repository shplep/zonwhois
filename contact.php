<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/security.php';
require_once 'includes/functions.php';

$page_title = 'Contact Us';
$page_description = 'Get in touch with ZoneWhois. We\'re here to help with any questions about our domain lookup services.';
$page_keywords = 'contact, support, help, zonewhois';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request. Please try again.';
        $message_type = 'error';
    } else {
        // Sanitize input
        $name = sanitize_input($_POST['name'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $message_text = sanitize_input($_POST['message'] ?? '');
        
        // Validate input
        if (empty($name) || empty($email) || empty($message_text)) {
            $message = 'Please fill in all fields.';
            $message_type = 'error';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Please enter a valid email address.';
            $message_type = 'error';
        } else {
            // Save contact submission
            $data = [
                'name' => $name,
                'email' => $email,
                'message' => $message_text
            ];
            
            if (save_contact_submission($data)) {
                $message = 'Thank you for your message. We\'ll get back to you soon!';
                $message_type = 'success';
                
                // Clear form data
                $name = $email = $message_text = '';
            } else {
                $message = 'Sorry, there was an error sending your message. Please try again.';
                $message_type = 'error';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="domain-details">
        <div class="domain-header">
            <h1>Contact Us</h1>
            <p>Get in touch with our team</p>
        </div>
        
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" class="contact-form">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="form-group">
                <label for="name" class="form-label">Name *</label>
                <input type="text" id="name" name="name" class="form-input" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email *</label>
                <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="message" class="form-label">Message *</label>
                <textarea id="message" name="message" class="form-input form-textarea" required><?php echo htmlspecialchars($message_text ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" class="form-button">Send Message</button>
            </div>
        </form>
        
        <div class="contact-info">
            <h3>Other Ways to Reach Us</h3>
            <div class="domain-stats">
                <div class="stat-item">
                    <div class="stat-label">Email</div>
                    <div class="stat-value">support@zonwhois.com</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-label">Response Time</div>
                    <div class="stat-value">Within 24 hours</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-label">Business Hours</div>
                    <div class="stat-value">Monday - Friday, 9 AM - 5 PM EST</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 