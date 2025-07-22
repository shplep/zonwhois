<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$page_title = 'Countries';
$page_description = 'Browse domains by country on ZoneWhois.';
$page_keywords = 'countries, domain countries, browse domains';

$countries = get_countries();

include 'includes/header.php';
?>

<div class="container">
    <div class="domain-details">
        <div class="domain-header">
            <h1>Domain Countries</h1>
            <p>Browse domains by country</p>
        </div>
        
        <div class="domain-stats">
            <?php foreach ($countries as $country): ?>
            <div class="stat-item">
                <h3><?php echo htmlspecialchars($country['name']); ?></h3>
                <p>Browse domains from <?php echo htmlspecialchars($country['name']); ?>.</p>
                <a href="/list.php?country=<?php echo urlencode($country['name']); ?>" class="domain-link">View Domains</a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="domain-actions">
            <a href="/" class="form-button">Back to Home</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 