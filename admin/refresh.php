<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/security.php';
require_once '../includes/curl.php';
require_once '../includes/functions.php';

require_admin_login();
check_session_timeout();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $domain_id = intval($_POST['domain_id'] ?? 0);
    
    if ($domain_id > 0) {
        if (refresh_domain($domain_id)) {
            $message = 'Domain data refreshed successfully.';
            $message_type = 'success';
        } else {
            $message = 'Failed to refresh domain data.';
            $message_type = 'error';
        }
    } else {
        $message = 'Invalid domain ID.';
        $message_type = 'error';
    }
}

// Get domains for listing
$pdo = connect_db();
$stmt = $pdo->query("SELECT * FROM domains ORDER BY last_updated ASC LIMIT 50");
$domains = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'admin_header.php';
?>

<div class="container">
    <div class="admin-dashboard">
        <h1>Refresh Domain Data</h1>
        <p>Select domains to refresh their WHOIS and HTTP data.</p>
        
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <div class="domain-list">
            <?php foreach ($domains as $domain): ?>
            <div class="domain-item" style="background: white; padding: 1rem; margin: 1rem 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3><?php echo htmlspecialchars($domain['domain_name']); ?></h3>
                        <p>Last Updated: <?php echo format_date($domain['last_updated']); ?></p>
                        <p>HTTP Status: <?php echo $domain['http_status'] ?? 'N/A'; ?></p>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="/domain/<?php echo htmlspecialchars($domain['domain_name']); ?>" target="_blank" class="form-button" style="background: #6f42c1; text-decoration: none; color: white; padding: 0.5rem 1rem; border-radius: 4px; border: none; cursor: pointer;">
                            View
                        </a>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="domain_id" value="<?php echo $domain['id']; ?>">
                            <button type="submit" class="form-button" style="background: #28a745;">Refresh Data</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($domains)): ?>
        <div class="alert alert-warning">
            No domains found to refresh.
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'admin_footer.php'; ?> 