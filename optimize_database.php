<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

try {
    $pdo = connect_db();
    echo "Connected to database successfully!\n";

    // Add performance indexes
    $indexes = [
        "CREATE INDEX idx_domains_status_id ON domains(status, id)",
        "CREATE INDEX idx_domains_status_views ON domains(status, id, last_updated)",
        "CREATE INDEX idx_page_views_domain_timestamp ON page_views(domain_id, view_timestamp)",
        "CREATE INDEX idx_page_views_domain_bot ON page_views(domain_id, is_bot)",
        "CREATE INDEX idx_domains_domain_name ON domains(domain_name)"
    ];

    foreach ($indexes as $sql) {
        try {
            $pdo->exec($sql);
            echo "✅ Index created successfully.\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
                echo "ℹ️  Index already exists.\n";
            } else {
                echo "⚠️  Index creation failed: " . $e->getMessage() . "\n";
            }
        }
    }

    // Analyze table performance
    echo "\nAnalyzing table performance...\n";
    $tables = ['domains', 'page_views'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->prepare("ANALYZE TABLE $table");
            $stmt->execute();
            echo "✅ Analyzed table: $table\n";
        } catch (PDOException $e) {
            echo "⚠️  Failed to analyze $table: " . $e->getMessage() . "\n";
        }
    }

    echo "\nDatabase optimization completed successfully!\n";

} catch (PDOException $e) {
    die("Database optimization failed: " . $e->getMessage() . "\n");
}
?> 