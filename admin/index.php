<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/security.php';

require_admin_login();
check_session_timeout();

$total_domains = get_total_domains();
$total_views = get_total_views();
$bot_views = get_bot_views();
$human_views = get_human_views();

include 'admin_header.php';
?>

<div class="container">
    <div class="admin-dashboard">
        <h1>Admin Dashboard</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Domains</h3>
                <div class="stat-number"><?php echo number_format($total_domains); ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Total Views</h3>
                <div class="stat-number"><?php echo number_format($total_views); ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Human Views</h3>
                <div class="stat-number"><?php echo number_format($human_views); ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Bot Views</h3>
                <div class="stat-number"><?php echo number_format($bot_views); ?></div>
            </div>
        </div>
        
        <div class="admin-actions">
            <a href="urls.php" class="admin-button">Manage Domains</a>
            <a href="stats.php" class="admin-button">View Statistics</a>
            <a href="contact.php" class="admin-button">Contact Submissions</a>
            <a href="refresh.php" class="admin-button">Refresh Domain Data</a>
        </div>
        
        <div class="recent-activity">
            <h2>Recent Activity</h2>
            <div class="activity-list">
                <p>No recent activity to display.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'admin_footer.php'; ?> 