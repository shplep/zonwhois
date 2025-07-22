<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/security.php';

require_admin_login();
check_session_timeout();

$type = $_GET['type'] ?? 'domains';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $type . '_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

switch ($type) {
    case 'domains':
        // Export domains
        fputcsv($output, ['ID', 'Domain Name', 'Creation Date', 'Expiration Date', 'Registrar', 'HTTP Status', 'Server Type', 'Load Time', 'SSL Status', 'Status', 'Last Updated']);
        
        $pdo = connect_db();
        $stmt = $pdo->query("SELECT * FROM domains ORDER BY id DESC");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, [
                $row['id'],
                $row['domain_name'],
                $row['creation_date'],
                $row['expiration_date'],
                $row['registrar'],
                $row['http_status'],
                $row['server_type'],
                $row['load_time'],
                $row['ssl_status'] ? 'Yes' : 'No',
                $row['status'],
                $row['last_updated']
            ]);
        }
        break;
        
    case 'views':
        // Export page views
        fputcsv($output, ['ID', 'Domain ID', 'Domain Name', 'View Timestamp', 'Is Bot', 'User Agent']);
        
        $pdo = connect_db();
        $stmt = $pdo->query("SELECT pv.*, d.domain_name FROM page_views pv 
                             LEFT JOIN domains d ON pv.domain_id = d.id 
                             ORDER BY pv.view_timestamp DESC");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, [
                $row['id'],
                $row['domain_id'],
                $row['domain_name'],
                $row['view_timestamp'],
                $row['is_bot'] ? 'Yes' : 'No',
                $row['user_agent']
            ]);
        }
        break;
        
    case 'contacts':
        // Export contact submissions
        fputcsv($output, ['ID', 'Name', 'Email', 'Message', 'Submission Timestamp']);
        
        $pdo = connect_db();
        $stmt = $pdo->query("SELECT * FROM contact_submissions ORDER BY submission_timestamp DESC");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, [
                $row['id'],
                $row['name'],
                $row['email'],
                $row['message'],
                $row['submission_timestamp']
            ]);
        }
        break;
        
    default:
        fputcsv($output, ['Error: Invalid export type']);
}

fclose($output);
?> 