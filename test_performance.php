<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

echo "Performance Test Results\n";
echo "=======================\n\n";

// Test 1: Original method (3 separate connections)
echo "Test 1: Original method (3 separate connections)\n";
$start = microtime(true);

$pdo1 = connect_db();
$stmt1 = $pdo1->prepare("SELECT * FROM domains WHERE status = 'visible' ORDER BY id DESC LIMIT 15");
$stmt1->execute();
$last_added = $stmt1->fetchAll(PDO::FETCH_ASSOC);

$pdo2 = connect_db();
$stmt2 = $pdo2->prepare("SELECT d.*, COUNT(pv.id) as view_count FROM domains d LEFT JOIN page_views pv ON d.id = pv.domain_id WHERE d.status = 'visible' GROUP BY d.id ORDER BY view_count DESC, d.id DESC LIMIT 15");
$stmt2->execute();
$top_sites = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$pdo3 = connect_db();
$stmt3 = $pdo3->prepare("SELECT d.*, MAX(pv.view_timestamp) as last_visit FROM domains d LEFT JOIN page_views pv ON d.id = pv.domain_id WHERE d.status = 'visible' GROUP BY d.id HAVING last_visit IS NOT NULL ORDER BY last_visit DESC, d.id DESC LIMIT 15");
$stmt3->execute();
$last_visited = $stmt3->fetchAll(PDO::FETCH_ASSOC);

$original_time = microtime(true) - $start;
echo "Original method time: " . number_format($original_time, 4) . " seconds\n\n";

// Test 2: Optimized method (single connection, prepared statements)
echo "Test 2: Optimized method (single connection, prepared statements)\n";
$start = microtime(true);

$results = get_homepage_domains_prepared(15);

$optimized_time = microtime(true) - $start;
echo "Optimized method time: " . number_format($optimized_time, 4) . " seconds\n\n";

// Test 3: Cached method
echo "Test 3: Cached method\n";
$start = microtime(true);

$cache_key = 'performance_test_' . date('Y-m-d-H-i');
$cached_results = get_cache($cache_key);

if (!$cached_results) {
    $results = get_homepage_domains_prepared(15);
    set_cache($cache_key, $results, 300);
    $cached_results = $results;
}

$cached_time = microtime(true) - $start;
echo "Cached method time: " . number_format($cached_time, 4) . " seconds\n\n";

// Calculate improvements
$improvement_optimized = (($original_time - $optimized_time) / $original_time) * 100;
$improvement_cached = (($original_time - $cached_time) / $original_time) * 100;

echo "Performance Improvements:\n";
echo "=======================\n";
echo "Optimized vs Original: " . number_format($improvement_optimized, 1) . "% faster\n";
echo "Cached vs Original: " . number_format($improvement_cached, 1) . "% faster\n";
echo "Cached vs Optimized: " . number_format((($optimized_time - $cached_time) / $optimized_time) * 100, 1) . "% faster\n\n";

echo "Database Connection Info:\n";
echo "=======================\n";
$pdo = connect_db();
$stmt = $pdo->query("SELECT VERSION() as version");
$version = $stmt->fetchColumn();
echo "MySQL Version: " . $version . "\n";
echo "Database Host: " . DB_HOST . ":" . DB_PORT . "\n";
echo "Database Name: " . DB_NAME . "\n\n";

echo "Index Information:\n";
echo "==================\n";
$indexes = [
    'idx_domains_status_id',
    'idx_domains_status_views', 
    'idx_page_views_domain_timestamp',
    'idx_page_views_domain_bot',
    'idx_domains_domain_name'
];

foreach ($indexes as $index) {
    try {
        if (strpos($index, 'page_views') !== false) {
            $stmt = $pdo->query("SHOW INDEX FROM page_views WHERE Key_name = '$index'");
        } else {
            $stmt = $pdo->query("SHOW INDEX FROM domains WHERE Key_name = '$index'");
        }
        $result = $stmt->fetch();
        if ($result) {
            echo "✅ $index exists\n";
        } else {
            echo "❌ $index missing\n";
        }
    } catch (Exception $e) {
        echo "⚠️  Error checking $index: " . $e->getMessage() . "\n";
    }
}
?> 