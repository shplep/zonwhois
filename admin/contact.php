<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/security.php';
require_once '../includes/functions.php';

require_admin_login();
check_session_timeout();

$page = max(1, intval($_GET['page'] ?? 1));
$submissions = get_contact_submissions($page);
$total_submissions = get_contact_submissions_count();
$total_pages = ceil($total_submissions / ADMIN_ITEMS_PER_PAGE);

include 'admin_header.php';
?>

<div class="container">
    <div class="admin-dashboard">
        <h1>Contact Submissions</h1>
        <p>Total submissions: <?php echo $total_submissions; ?></p>
        
        <div class="submissions-list">
            <?php if (empty($submissions)): ?>
            <div class="alert alert-warning">
                No contact submissions found.
            </div>
            <?php else: ?>
            <?php foreach ($submissions as $submission): ?>
            <div class="submission-item" style="background: white; padding: 1.5rem; margin: 1rem 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <h3><?php echo htmlspecialchars($submission['name']); ?></h3>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($submission['email']); ?></p>
                        <p><strong>Date:</strong> <?php echo format_date($submission['submission_timestamp']); ?></p>
                        <div style="margin-top: 1rem;">
                            <strong>Message:</strong>
                            <p style="margin-top: 0.5rem; white-space: pre-wrap;"><?php echo htmlspecialchars($submission['message']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
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