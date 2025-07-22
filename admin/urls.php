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

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $domain_id = intval($_POST['domain_id'] ?? 0);
    
    switch ($action) {
        case 'delete':
            if (delete_domain($domain_id)) {
                $message = 'Domain deleted successfully.';
                $message_type = 'success';
            } else {
                $message = 'Failed to delete domain.';
                $message_type = 'error';
            }
            break;
            
        case 'hide':
            $status = $_POST['status'] === 'visible' ? 'hidden' : 'visible';
            if (hide_domain($domain_id, $status)) {
                $message = 'Domain status updated successfully.';
                $message_type = 'success';
            } else {
                $message = 'Failed to update domain status.';
                $message_type = 'error';
            }
            break;
            
        case 'refresh':
            if (refresh_domain($domain_id)) {
                $message = 'Domain data refreshed successfully.';
                $message_type = 'success';
            } else {
                $message = 'Failed to refresh domain data.';
                $message_type = 'error';
            }
            break;
            
        case 'toggle_outbound':
            if (toggle_outbound_link($domain_id)) {
                $message = 'Outbound link status updated successfully.';
                $message_type = 'success';
            } else {
                $message = 'Failed to update outbound link status.';
                $message_type = 'error';
            }
            break;
    }
}

// Get domains for listing
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * ADMIN_ITEMS_PER_PAGE;

$pdo = connect_db();
$per_page = intval(ADMIN_ITEMS_PER_PAGE);
$offset = intval($offset);
$stmt = $pdo->prepare("SELECT * FROM domains ORDER BY id DESC LIMIT " . $per_page . " OFFSET " . $offset);
$stmt->execute();
$domains = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_domains = get_total_domains();
$total_pages = ceil($total_domains / ADMIN_ITEMS_PER_PAGE);

include 'admin_header.php';
?>

<div class="container">
    <div class="admin-dashboard">
        <h1>Manage Domains</h1>
        
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
                        <p>Status: <?php echo $domain['status']; ?> | Views: <?php echo get_domain_views($domain['id']); ?></p>
                        <p>Last Updated: <?php echo format_date($domain['last_updated']); ?></p>
                        <p>Outbound Link: <strong><?php echo $domain['outbound_link'] ? 'Enabled' : 'Disabled'; ?></strong></p>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="/domain/<?php echo htmlspecialchars($domain['domain_name']); ?>" target="_blank" class="form-button" style="background: #6f42c1; text-decoration: none; color: white; padding: 0.5rem 1rem; border-radius: 4px; border: none; cursor: pointer;">
                            View
                        </a>
                        
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="domain_id" value="<?php echo $domain['id']; ?>">
                            <input type="hidden" name="action" value="refresh">
                            <button type="submit" class="form-button" style="background: #28a745;">Refresh</button>
                        </form>
                        
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="domain_id" value="<?php echo $domain['id']; ?>">
                            <input type="hidden" name="action" value="toggle_outbound">
                            <button type="submit" class="form-button" style="background: #17a2b8;">
                                <?php echo $domain['outbound_link'] ? 'Disable Link' : 'Enable Link'; ?>
                            </button>
                        </form>
                        
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="domain_id" value="<?php echo $domain['id']; ?>">
                            <input type="hidden" name="action" value="hide">
                            <input type="hidden" name="status" value="<?php echo $domain['status']; ?>">
                            <button type="submit" class="form-button" style="background: #ffc107;">
                                <?php echo $domain['status'] === 'visible' ? 'Hide' : 'Show'; ?>
                            </button>
                        </form>
                        
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this domain?');">
                            <input type="hidden" name="domain_id" value="<?php echo $domain['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="form-button" style="background: #dc3545;">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="page-link<?php echo $i === $page ? ' active' : ''; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'admin_footer.php'; ?> 