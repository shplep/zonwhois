<?php
require_once 'config.php';
require_once 'db.php';
require_once 'security.php';

function generate_pagination($total, $page, $per_page, $base_url) {
    $total_pages = ceil($total / $per_page);
    
    if ($total_pages <= 1) {
        return '';
    }
    
    $html = '<div class="pagination">';
    
    // Previous button
    if ($page > 1) {
        $html .= '<a href="' . $base_url . '?page=' . ($page - 1) . '" class="page-link">&laquo; Previous</a>';
    }
    
    // Page numbers
    $start = max(1, $page - 2);
    $end = min($total_pages, $page + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        $active = ($i == $page) ? ' active' : '';
        $html .= '<a href="' . $base_url . '?page=' . $i . '" class="page-link' . $active . '">' . $i . '</a>';
    }
    
    // Next button
    if ($page < $total_pages) {
        $html .= '<a href="' . $base_url . '?page=' . ($page + 1) . '" class="page-link">Next &raquo;</a>';
    }
    
    $html .= '</div>';
    return $html;
}

function generate_sitemap() {
    $pdo = connect_db();
    
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // Static pages
    $static_pages = ['', 'about', 'categories', 'countries', 'contact'];
    foreach ($static_pages as $page) {
        $url = SITE_URL . '/' . $page;
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($url) . '</loc>' . "\n";
        $xml .= '    <changefreq>weekly</changefreq>' . "\n";
        $xml .= '    <priority>0.8</priority>' . "\n";
        $xml .= '  </url>' . "\n";
    }
    
    // Domain pages
    $stmt = $pdo->query("SELECT domain_name, last_updated FROM domains WHERE status = 'visible'");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $url = SITE_URL . '/domain/' . $row['domain_name'];
        $lastmod = date('Y-m-d', strtotime($row['last_updated']));
        
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($url) . '</loc>' . "\n";
        $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . "\n";
        $xml .= '    <changefreq>monthly</changefreq>' . "\n";
        $xml .= '    <priority>0.6</priority>' . "\n";
        $xml .= '  </url>' . "\n";
    }
    
    $xml .= '</urlset>';
    return $xml;
}

function format_date($date) {
    if (!$date) return 'N/A';
    return date('M j, Y', strtotime($date));
}

function format_time($seconds) {
    if (!$seconds) return 'N/A';
    return number_format($seconds, 2) . 'ms';
}

function get_status_class($status) {
    if ($status >= 200 && $status < 300) return 'status-success';
    if ($status >= 300 && $status < 400) return 'status-redirect';
    if ($status >= 400 && $status < 500) return 'status-client-error';
    if ($status >= 500) return 'status-server-error';
    return 'status-unknown';
}

function truncate_text($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

function get_alphabet_links() {
    $letters = array_merge(range('A', 'Z'), range('0', '9'));
    $html = '<div class="alphabet-nav">';
    
    foreach ($letters as $letter) {
        $count = get_domains_count_by_letter($letter);
        $class = $count > 0 ? 'has-domains' : 'no-domains';
        $html .= '<a href="list.php?letter=' . $letter . '" class="' . $class . '">' . $letter . '</a>';
    }
    
    $html .= '</div>';
    return $html;
}

function log_error($message, $context = []) {
    $log_entry = date('Y-m-d H:i:s') . " - ERROR: " . $message;
    if (!empty($context)) {
        $log_entry .= " - Context: " . json_encode($context);
    }
    $log_entry .= "\n";
    
    file_put_contents('logs/errors.log', $log_entry, FILE_APPEND | LOCK_EX);
}

function create_logs_directory() {
    if (!is_dir('logs')) {
        mkdir('logs', 0755, true);
    }
}

// Initialize logs directory
create_logs_directory();
?> 