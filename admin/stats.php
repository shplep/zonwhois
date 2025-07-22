<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/security.php';
require_once '../includes/functions.php';

require_admin_login();
check_session_timeout();

$total_domains = get_total_domains();
$total_views = get_total_views();
$bot_views = get_bot_views();
$human_views = get_human_views();

// Get recent view data
$pdo = connect_db();
$stmt = $pdo->query("SELECT DATE(view_timestamp) as date, COUNT(*) as views, SUM(is_bot) as bot_views 
                     FROM page_views 
                     WHERE view_timestamp >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                     GROUP BY DATE(view_timestamp) 
                     ORDER BY date DESC");
$recent_views = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'admin_header.php';
?>

<div class="container">
    <div class="admin-dashboard">
        <h1>Statistics</h1>
        
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
                <small><?php echo $total_views > 0 ? round(($human_views / $total_views) * 100, 1) : 0; ?>%</small>
            </div>
            
            <div class="stat-card">
                <h3>Bot Views</h3>
                <div class="stat-number"><?php echo number_format($bot_views); ?></div>
                <small><?php echo $total_views > 0 ? round(($bot_views / $total_views) * 100, 1) : 0; ?>%</small>
            </div>
        </div>
        
        <div class="recent-activity" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 2rem;">
            <h2>Recent Activity (Last 30 Days)</h2>
            <?php if (empty($recent_views)): ?>
            <p>No recent activity.</p>
            <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding: 0.5rem; text-align: left; border-bottom: 1px solid #ddd;">Date</th>
                            <th style="padding: 0.5rem; text-align: left; border-bottom: 1px solid #ddd;">Total Views</th>
                            <th style="padding: 0.5rem; text-align: left; border-bottom: 1px solid #ddd;">Human Views</th>
                            <th style="padding: 0.5rem; text-align: left; border-bottom: 1px solid #ddd;">Bot Views</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_views as $view): ?>
                        <tr>
                            <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo format_date($view['date']); ?></td>
                            <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo number_format($view['views']); ?></td>
                            <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo number_format($view['views'] - $view['bot_views']); ?></td>
                            <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo number_format($view['bot_views']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="admin-actions">
            <a href="export.php" class="admin-button">Export Statistics</a>
        </div>
    </div>
</div>

<?php include 'admin_footer.php'; ?> 