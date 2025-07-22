<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$page_title = 'Domain List';
$page_description = 'Browse domains alphabetically on ZoneWhois.';
$page_keywords = 'domain list, browse domains, alphabetical';

$letter = $_GET['letter'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));

if (!preg_match('/^[A-Z0-9]$/', $letter)) {
    header('Location: /');
    exit;
}

$domains = get_domains_by_letter($letter, $page);
$total_domains = get_domains_count_by_letter($letter);
$total_pages = ceil($total_domains / DOMAINS_PER_PAGE);

$page_title = "Domains Starting with '$letter'";
$page_description = "Browse domains starting with the letter '$letter' on ZoneWhois.";

include 'includes/header.php';
?>

<div class="container">
    <div class="domain-details">
        <div class="domain-header">
            <h1>Domains Starting with "<?php echo htmlspecialchars($letter); ?>"</h1>
            <p><?php echo $total_domains; ?> domains found</p>
        </div>
        
        <?php if (empty($domains)): ?>
        <div class="alert alert-warning">
            No domains found starting with "<?php echo htmlspecialchars($letter); ?>".
        </div>
        <?php else: ?>
        <div class="domain-list">
            <?php foreach ($domains as $domain): ?>
            <div class="domain-item">
                <a href="/domain/<?php echo htmlspecialchars($domain['domain_name']); ?>" class="domain-link">
                    <?php echo htmlspecialchars($domain['domain_name']); ?>
                </a>
                <small>Added: <?php echo format_date($domain['last_updated']); ?></small>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($total_pages > 1): ?>
        <?php echo generate_pagination($total_domains, $page, DOMAINS_PER_PAGE, "/list.php?letter=$letter"); ?>
        <?php endif; ?>
        <?php endif; ?>
        
        <div class="domain-actions">
            <a href="/" class="form-button">Back to Home</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 