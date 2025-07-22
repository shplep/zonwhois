<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$page_title = 'Categories';
$page_description = 'Browse domains by category on ZoneWhois.';
$page_keywords = 'categories, domain categories, browse domains';

$categories = get_categories();

include 'includes/header.php';
?>

<div class="container">
    <div class="domain-details">
        <div class="domain-header">
            <h1>Domain Categories</h1>
            <p>Browse domains by category</p>
        </div>
        
        <div class="domain-stats">
            <?php foreach ($categories as $category): ?>
            <div class="stat-item">
                <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                <p>Browse domains in the <?php echo htmlspecialchars($category['name']); ?> category.</p>
                <a href="/list.php?category=<?php echo urlencode($category['name']); ?>" class="domain-link">View Domains</a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="domain-actions">
            <a href="/" class="form-button">Back to Home</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 