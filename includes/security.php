<?php
require_once 'config.php';

function sanitize_input($input) {
    if (is_array($input)) {
        return array_map('sanitize_input', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validate_domain($domain) {
    // Remove protocol if present
    $domain = preg_replace('#^https?://#', '', $domain);
    
    // Remove path and query parameters
    $domain = parse_url($domain, PHP_URL_HOST) ?: $domain;
    
    // Basic domain validation
    if (empty($domain) || strlen($domain) > 255) {
        return false;
    }
    
    // Check for valid domain format
    if (!preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?)*$/', $domain)) {
        return false;
    }
    
    return $domain;
}

function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function require_admin_login() {
    if (!is_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function login_admin($username, $password) {
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_login_time'] = time();
        return true;
    }
    return false;
}

function logout_admin() {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_login_time']);
    session_destroy();
}

function check_session_timeout() {
    if (isset($_SESSION['admin_login_time']) && 
        (time() - $_SESSION['admin_login_time']) > SESSION_TIMEOUT) {
        logout_admin();
        return false;
    }
    return true;
}

function rate_limit($key, $limit, $period) {
    $pdo = connect_db();
    
    // Clean old entries
    $stmt = $pdo->prepare("DELETE FROM rate_limits WHERE created_at < DATE_SUB(NOW(), INTERVAL ? SECOND)");
    $stmt->execute([$period]);
    
    // Check current count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM rate_limits WHERE ip_key = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)");
    $stmt->execute([$key, $period]);
    $count = $stmt->fetchColumn();
    
    if ($count >= $limit) {
        return false; // Rate limit exceeded
    }
    
    // Add new entry
    $stmt = $pdo->prepare("INSERT INTO rate_limits (ip_key, created_at) VALUES (?, NOW())");
    $stmt->execute([$key]);
    
    return true; // Rate limit OK
}

function detect_bot($user_agent) {
    if (empty($user_agent)) {
        return true; // Treat empty user agent as bot
    }
    
    $bot_patterns = [
        'bot', 'crawler', 'spider', 'scraper', 'scout', 'slurp', 'baidu',
        'googlebot', 'bingbot', 'yandex', 'duckduckbot', 'facebookexternalhit',
        'twitterbot', 'linkedinbot', 'whatsapp', 'telegrambot', 'discord',
        'slackbot', 'redditbot', 'pinterest', 'instagram', 'tiktok',
        'semrush', 'ahrefs', 'moz', 'screaming frog', 'sitebulb',
        'curl', 'wget', 'python', 'java', 'perl', 'ruby', 'php'
    ];
    
    $user_agent_lower = strtolower($user_agent);
    
    foreach ($bot_patterns as $pattern) {
        if (strpos($user_agent_lower, $pattern) !== false) {
            return true;
        }
    }
    
    return false;
}

function get_client_ip() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function log_security_event($event, $details = '') {
    $log_entry = date('Y-m-d H:i:s') . " - " . $event . " - " . get_client_ip() . " - " . $details . "\n";
    file_put_contents('logs/security.log', $log_entry, FILE_APPEND | LOCK_EX);
}

// Create rate_limits table if it doesn't exist
function create_rate_limits_table() {
    $pdo = connect_db();
    $sql = "CREATE TABLE IF NOT EXISTS rate_limits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_key VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_ip_key (ip_key),
        INDEX idx_created_at (created_at)
    )";
    $pdo->exec($sql);
}

// Initialize rate limits table
create_rate_limits_table();
?> 