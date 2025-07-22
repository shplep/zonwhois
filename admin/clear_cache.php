<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
require_once '../includes/functions.php';

// Require admin login
require_admin_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pattern = $_POST['pattern'] ?? null;
    clear_cache($pattern);
    $message = "Cache cleared successfully!";
}

include 'admin_header.php';
?>

<div class="admin-content">
    <h1>Clear Cache</h1>
    
    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <div class="admin-section">
        <h2>Cache Management</h2>
        <p>Clear cached data to refresh the website content. This is useful after making changes to domain data.</p>
        
        <form method="POST" class="admin-form">
            <div class="form-group">
                <label for="pattern">Cache Pattern (optional):</label>
                <input type="text" id="pattern" name="pattern" class="form-control" 
                       placeholder="e.g., homepage_domains_ to clear only homepage cache">
                <small class="form-text">Leave empty to clear all cache files</small>
            </div>
            
            <button type="submit" class="form-button">Clear Cache</button>
        </form>
        
        <div class="cache-info">
            <h3>Cache Information</h3>
            <ul>
                <li><strong>Homepage Cache:</strong> 5 minutes TTL</li>
                <li><strong>Cache Directory:</strong> /cache/</li>
                <li><strong>Cache Files:</strong> Automatically managed</li>
            </ul>
        </div>
    </div>
</div>

<?php include 'admin_footer.php'; ?> 