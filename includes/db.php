<?php
require_once 'config.php';

function connect_db() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT => true, // Use persistent connections
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
        } catch (PDOException $e) {
            log_error("Database connection failed: " . $e->getMessage());
            die("Database connection failed. Please try again later.");
        }
    }
    
    return $pdo;
}

function get_domains($type, $limit = 15) {
    $pdo = connect_db();
    $limit = intval($limit); // Ensure it's an integer
    
    switch ($type) {
        case 'last_added':
            $sql = "SELECT * FROM domains WHERE status = 'visible' ORDER BY id DESC LIMIT " . $limit;
            break;
        case 'top':
            $sql = "SELECT d.*, COUNT(pv.id) as view_count 
                   FROM domains d 
                   LEFT JOIN page_views pv ON d.id = pv.domain_id 
                   WHERE d.status = 'visible' 
                   GROUP BY d.id 
                   ORDER BY view_count DESC, d.id DESC 
                   LIMIT " . $limit;
            break;
        case 'last_visited':
            $sql = "SELECT d.*, MAX(pv.view_timestamp) as last_visit 
                   FROM domains d 
                   LEFT JOIN page_views pv ON d.id = pv.domain_id 
                   WHERE d.status = 'visible' 
                   GROUP BY d.id 
                   ORDER BY last_visit DESC, d.id DESC 
                   LIMIT " . $limit;
            break;
        default:
            return [];
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_domain($domain_name) {
    $pdo = connect_db();
    $stmt = $pdo->prepare("SELECT * FROM domains WHERE domain_name = ? AND status = 'visible'");
    $stmt->execute([$domain_name]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_domains_by_letter($letter, $page = 1, $per_page = DOMAINS_PER_PAGE) {
    $pdo = connect_db();
    $page = intval($page);
    $per_page = intval($per_page);
    $offset = ($page - 1) * $per_page;
    
    $sql = "SELECT * FROM domains 
            WHERE domain_name LIKE ? AND status = 'visible' 
            ORDER BY domain_name 
            LIMIT " . $per_page . " OFFSET " . $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$letter . '%']);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_domains_count_by_letter($letter) {
    $pdo = connect_db();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM domains WHERE domain_name LIKE ? AND status = 'visible'");
    $stmt->execute([$letter . '%']);
    return $stmt->fetchColumn();
}

function get_categories() {
    $pdo = connect_db();
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_countries() {
    $pdo = connect_db();
    $stmt = $pdo->query("SELECT * FROM countries ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_domain_views($domain_id, $is_bot = null) {
    $pdo = connect_db();
    
    if ($is_bot !== null) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM page_views WHERE domain_id = ? AND is_bot = ?");
        $stmt->execute([$domain_id, $is_bot]);
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM page_views WHERE domain_id = ?");
        $stmt->execute([$domain_id]);
    }
    
    return $stmt->fetchColumn();
}

function log_view($domain_id, $user_agent) {
    $pdo = connect_db();
    $is_bot = detect_bot($user_agent);
    
    $stmt = $pdo->prepare("INSERT INTO page_views (domain_id, is_bot, user_agent) VALUES (?, ?, ?)");
    return $stmt->execute([$domain_id, $is_bot, $user_agent]);
}

function add_domain($data) {
    $pdo = connect_db();
    
    // Ensure proper data types
    $ssl_status = !empty($data['ssl_status']) ? 1 : 0;
    $http_status = !empty($data['http_status']) ? intval($data['http_status']) : null;
    $load_time = !empty($data['load_time']) ? floatval($data['load_time']) : null;
    
    $sql = "INSERT INTO domains (domain_name, creation_date, expiration_date, renewal_date, 
            registrar, meta_description, meta_title, meta_keywords, http_status, server_type, 
            content_type, load_time, redirects, ssl_status, outbound_link) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['domain_name'],
        $data['creation_date'] ?? null,
        $data['expiration_date'] ?? null,
        $data['renewal_date'] ?? null,
        $data['registrar'] ?? null,
        $data['meta_description'] ?? null,
        $data['meta_title'] ?? null,
        $data['meta_keywords'] ?? null,
        $http_status,
        $data['server_type'] ?? null,
        $data['content_type'] ?? null,
        $load_time,
        $data['redirects'] ?? null,
        $ssl_status,
        $data['outbound_link'] ?? true
    ]);
}

function update_domain($id, $data) {
    $pdo = connect_db();
    
    // Ensure proper data types
    $ssl_status = !empty($data['ssl_status']) ? 1 : 0;
    $http_status = !empty($data['http_status']) ? intval($data['http_status']) : null;
    $load_time = !empty($data['load_time']) ? floatval($data['load_time']) : null;
    
    $sql = "UPDATE domains SET 
            domain_name = ?, creation_date = ?, expiration_date = ?, renewal_date = ?,
            registrar = ?, meta_description = ?, meta_title = ?, meta_keywords = ?,
            http_status = ?, server_type = ?, content_type = ?, load_time = ?,
            redirects = ?, ssl_status = ?, outbound_link = ? WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['domain_name'],
        $data['creation_date'] ?? null,
        $data['expiration_date'] ?? null,
        $data['renewal_date'] ?? null,
        $data['registrar'] ?? null,
        $data['meta_description'] ?? null,
        $data['meta_title'] ?? null,
        $data['meta_keywords'] ?? null,
        $http_status,
        $data['server_type'] ?? null,
        $data['content_type'] ?? null,
        $load_time,
        $data['redirects'] ?? null,
        $ssl_status,
        $data['outbound_link'] ?? true,
        $id
    ]);
}

function delete_domain($id) {
    $pdo = connect_db();
    $stmt = $pdo->prepare("DELETE FROM domains WHERE id = ?");
    return $stmt->execute([$id]);
}

function hide_domain($id, $status) {
    $pdo = connect_db();
    $stmt = $pdo->prepare("UPDATE domains SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $id]);
}

function toggle_outbound_link($id) {
    $pdo = connect_db();
    $stmt = $pdo->prepare("UPDATE domains SET outbound_link = NOT outbound_link WHERE id = ?");
    return $stmt->execute([$id]);
}

function save_contact_submission($data) {
    $pdo = connect_db();
    $stmt = $pdo->prepare("INSERT INTO contact_submissions (name, email, message) VALUES (?, ?, ?)");
    return $stmt->execute([$data['name'], $data['email'], $data['message']]);
}

function get_contact_submissions($page = 1, $per_page = ADMIN_ITEMS_PER_PAGE) {
    $pdo = connect_db();
    $page = intval($page);
    $per_page = intval($per_page);
    $offset = ($page - 1) * $per_page;
    
    $stmt = $pdo->prepare("SELECT * FROM contact_submissions ORDER BY submission_timestamp DESC LIMIT " . $per_page . " OFFSET " . $offset);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_contact_submissions_count() {
    $pdo = connect_db();
    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_submissions");
    return $stmt->fetchColumn();
}

function get_total_domains() {
    $pdo = connect_db();
    $stmt = $pdo->query("SELECT COUNT(*) FROM domains WHERE status = 'visible'");
    return $stmt->fetchColumn();
}

function get_total_views() {
    $pdo = connect_db();
    $stmt = $pdo->query("SELECT COUNT(*) FROM page_views");
    return $stmt->fetchColumn();
}

function get_bot_views() {
    $pdo = connect_db();
    $stmt = $pdo->query("SELECT COUNT(*) FROM page_views WHERE is_bot = 1");
    return $stmt->fetchColumn();
}

function get_human_views() {
    $pdo = connect_db();
    $stmt = $pdo->query("SELECT COUNT(*) FROM page_views WHERE is_bot = 0");
    return $stmt->fetchColumn();
}

function get_homepage_domains($limit = 15) {
    $pdo = connect_db();
    $limit = intval($limit);
    
    // Single optimized query to get all three lists
    $sql = "SELECT 
                domain_name,
                id,
                last_updated,
                view_count,
                last_visit,
                'last_added' as type
            FROM (
                SELECT d.*, 0 as view_count, NULL as last_visit
                FROM domains d 
                WHERE d.status = 'visible'
                ORDER BY d.id DESC 
                LIMIT ?
            ) as last_added
            
            UNION ALL
            
            SELECT 
                domain_name,
                id,
                last_updated,
                view_count,
                last_visit,
                'top_sites' as type
            FROM (
                SELECT d.*, COUNT(pv.id) as view_count, NULL as last_visit
                FROM domains d 
                LEFT JOIN page_views pv ON d.id = pv.domain_id 
                WHERE d.status = 'visible' 
                GROUP BY d.id 
                ORDER BY view_count DESC, d.id DESC 
                LIMIT ?
            ) as top_sites
            
            UNION ALL
            
            SELECT 
                domain_name,
                id,
                last_updated,
                view_count,
                last_visit,
                'last_visited' as type
            FROM (
                SELECT d.*, 0 as view_count, MAX(pv.view_timestamp) as last_visit
                FROM domains d 
                LEFT JOIN page_views pv ON d.id = pv.domain_id 
                WHERE d.status = 'visible' 
                GROUP BY d.id 
                HAVING last_visit IS NOT NULL
                ORDER BY last_visit DESC, d.id DESC 
                LIMIT ?
            ) as last_visited";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$limit, $limit, $limit]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organize results by type
    $organized = [
        'last_added' => [],
        'top_sites' => [],
        'last_visited' => []
    ];
    
    foreach ($results as $row) {
        $organized[$row['type']][] = $row;
    }
    
    return $organized;
}

function get_homepage_domains_prepared($limit = 15) {
    $pdo = connect_db();
    $limit = intval($limit);
    
    // Prepare all statements once with LIMIT values concatenated
    $stmt_last_added = $pdo->prepare("SELECT * FROM domains WHERE status = 'visible' ORDER BY id DESC LIMIT " . $limit);
    $stmt_top_sites = $pdo->prepare("SELECT d.*, COUNT(pv.id) as view_count FROM domains d LEFT JOIN page_views pv ON d.id = pv.domain_id WHERE d.status = 'visible' GROUP BY d.id ORDER BY view_count DESC, d.id DESC LIMIT " . $limit);
    $stmt_last_visited = $pdo->prepare("SELECT d.*, MAX(pv.view_timestamp) as last_visit FROM domains d LEFT JOIN page_views pv ON d.id = pv.domain_id WHERE d.status = 'visible' GROUP BY d.id HAVING last_visit IS NOT NULL ORDER BY last_visit DESC, d.id DESC LIMIT " . $limit);
    
    // Execute all queries
    $stmt_last_added->execute();
    $stmt_top_sites->execute();
    $stmt_last_visited->execute();
    
    return [
        'last_added' => $stmt_last_added->fetchAll(PDO::FETCH_ASSOC),
        'top_sites' => $stmt_top_sites->fetchAll(PDO::FETCH_ASSOC),
        'last_visited' => $stmt_last_visited->fetchAll(PDO::FETCH_ASSOC)
    ];
}
?> 