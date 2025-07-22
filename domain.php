<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/curl.php';
require_once 'includes/security.php';

// Rate limiting
$client_ip = get_client_ip();
if (!rate_limit($client_ip, RATE_LIMIT_REQUESTS, RATE_LIMIT_PERIOD)) {
    http_response_code(429);
    die('Too many requests. Please try again later.');
}

// Get domain from URL
$domain_name = $_GET['domain'] ?? '';
$domain_name = validate_domain($domain_name);

if (!$domain_name) {
    header('Location: /');
    exit;
}

// Check if domain exists in database
$domain = get_domain($domain_name);

if (!$domain) {
    // Domain doesn't exist, fetch data and add it
    $http_data = fetch_domain_data($domain_name);
    $meta_data = fetch_meta_tags($domain_name);
    $whois_data = fetch_whois_data($domain_name);
    
    $domain_data = array_merge($http_data, $meta_data, $whois_data);
    $domain_data['domain_name'] = $domain_name;
    
    if (add_domain($domain_data)) {
        $domain = get_domain($domain_name);
    } else {
        // Failed to add domain
        header('Location: /');
        exit;
    }
}

// Log the view
log_view($domain['id'], $_SERVER['HTTP_USER_AGENT'] ?? '');

$page_title = $domain_name;
$page_description = $domain['meta_description'] ?? "Domain information for $domain_name";
$page_keywords = $domain['meta_keywords'] ?? "domain, whois, $domain_name";

include 'includes/header.php';
?>

<div class="container">
    <div class="domain-details">
        <div class="domain-header">
            <h1 class="domain-name"><?php echo htmlspecialchars($domain_name); ?></h1>
            <p>Domain Information and Statistics</p>
        </div>
        
        <div class="domain-stats">
            <div class="stat-item">
                <div class="stat-label">Domain Name</div>
                <div class="stat-value"><?php echo htmlspecialchars($domain_name); ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-label">Creation Date</div>
                <div class="stat-value"><?php echo format_date($domain['creation_date']); ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-label">Expiration Date</div>
                <div class="stat-value"><?php echo format_date($domain['expiration_date']); ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-label">Last Renewal</div>
                <div class="stat-value"><?php echo format_date($domain['renewal_date']); ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-label">Registrar</div>
                <div class="stat-value"><?php echo htmlspecialchars($domain['registrar'] ?? 'N/A'); ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-label">HTTP Status</div>
                <div class="stat-value <?php echo get_status_class($domain['http_status']); ?>">
                    <?php echo $domain['http_status'] ?? 'N/A'; ?>
                </div>
            </div>
            
            <div class="stat-item">
                <div class="stat-label">Server Type</div>
                <div class="stat-value"><?php echo htmlspecialchars($domain['server_type'] ?? 'N/A'); ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-label">Content Type</div>
                <div class="stat-value"><?php echo htmlspecialchars($domain['content_type'] ?? 'N/A'); ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-label">Load Time</div>
                <div class="stat-value"><?php echo format_time($domain['load_time']); ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-label">SSL/TLS Status</div>
                <div class="stat-value">
                    <?php echo $domain['ssl_status'] ? 'Secure (HTTPS)' : 'Not Secure (HTTP)'; ?>
                </div>
            </div>
            
            <?php if ($domain['redirects']): ?>
            <div class="stat-item">
                <div class="stat-label">Redirects To</div>
                <div class="stat-value"><?php echo htmlspecialchars($domain['redirects']); ?></div>
            </div>
            <?php endif; ?>
            
            <div class="stat-item">
                <div class="stat-label">Last Updated</div>
                <div class="stat-value"><?php echo format_date($domain['last_updated']); ?></div>
            </div>
        </div>
        
        <?php if ($domain['meta_title'] || $domain['meta_description'] || $domain['meta_keywords']): ?>
        <div class="domain-meta">
            <h3>Meta Information</h3>
            <div class="domain-stats">
                <?php if ($domain['meta_title']): ?>
                <div class="stat-item">
                    <div class="stat-label">Meta Title</div>
                    <div class="stat-value"><?php echo htmlspecialchars(truncate_text($domain['meta_title'], 150)); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($domain['meta_description']): ?>
                <div class="stat-item">
                    <div class="stat-label">Meta Description</div>
                    <div class="stat-value"><?php echo htmlspecialchars(truncate_text($domain['meta_description'], 200)); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($domain['meta_keywords']): ?>
                <div class="stat-item">
                    <div class="stat-label">Meta Keywords</div>
                    <div class="stat-value"><?php echo htmlspecialchars(truncate_text($domain['meta_keywords'], 150)); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="domain-actions">
            <?php if ($domain['outbound_link']): ?>
            <a href="http://<?php echo htmlspecialchars($domain_name); ?>" target="_blank" class="form-button">
                Visit Website
            </a>
            <?php endif; ?>
            <a href="/" class="form-button" style="background: #6c757d;">
                Search Another Domain
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 