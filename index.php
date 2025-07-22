<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$page_title = 'Home';
$page_description = 'Domain information and WHOIS lookup service. Search for domain details, creation dates, registrars, and more.';

// Try to get cached results first
$cache_key = 'homepage_domains_' . date('Y-m-d-H-i'); // Cache by 5-minute intervals
$cached_results = get_cache($cache_key);

if ($cached_results) {
    $last_added = $cached_results['last_added'];
    $top_sites = $cached_results['top_sites'];
    $last_visited = $cached_results['last_visited'];
} else {
    // Use optimized function with single database connection and prepared statements
    $results = get_homepage_domains_prepared(15);
    
    $last_added = $results['last_added'];
    $top_sites = $results['top_sites'];
    $last_visited = $results['last_visited'];
    
    // Cache the results for 5 minutes
    set_cache($cache_key, $results, 300);
}

include 'includes/header.php';
?>

<div class="search-section">
    <div class="container">
        <div class="search-container">
            <h1 class="search-title">Domain Information Lookup</h1>
            <p class="search-subtitle">Get detailed information about any domain including WHOIS data, HTTP status, server information, and more.</p>
            
            <form class="search-form" method="GET" action="/domain/">
                <input type="text" name="domain" class="search-input" placeholder="Enter domain name (e.g., example.com)" required>
                <button type="submit" class="search-button">Search Domain</button>
            </form>
        </div>
    </div>
</div>

<div class="columns-section">
    <div class="container">
        <div class="columns-grid">
            <div class="column">
                <h2 class="column-title">Last Added Sites</h2>
                <ul class="domain-list">
                    <?php if (empty($last_added)): ?>
                        <li class="domain-item">No domains added yet.</li>
                    <?php else: ?>
                        <?php foreach ($last_added as $domain): ?>
                            <li class="domain-item">
                                <a href="/domain/<?php echo htmlspecialchars($domain['domain_name']); ?>" class="domain-link">
                                    <?php echo htmlspecialchars($domain['domain_name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="column">
                <h2 class="column-title">Top Sites</h2>
                <ul class="domain-list">
                    <?php if (empty($top_sites)): ?>
                        <li class="domain-item">No domains with views yet.</li>
                    <?php else: ?>
                        <?php foreach ($top_sites as $domain): ?>
                            <li class="domain-item">
                                <a href="/domain/<?php echo htmlspecialchars($domain['domain_name']); ?>" class="domain-link">
                                    <?php echo htmlspecialchars($domain['domain_name']); ?>
                                </a>
                                <?php if (isset($domain['view_count']) && $domain['view_count'] > 0): ?>
                                    <small>(<?php echo $domain['view_count']; ?> views)</small>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="column">
                <h2 class="column-title">Last Visited Sites</h2>
                <ul class="domain-list">
                    <?php if (empty($last_visited)): ?>
                        <li class="domain-item">No domains visited yet.</li>
                    <?php else: ?>
                        <?php foreach ($last_visited as $domain): ?>
                            <li class="domain-item">
                                <a href="/domain/<?php echo htmlspecialchars($domain['domain_name']); ?>" class="domain-link">
                                    <?php echo htmlspecialchars($domain['domain_name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 