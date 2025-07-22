<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

try {
    $pdo = connect_db();
    echo "Connected to database successfully!\n";

    // Add missing indexes for page_views table
    $indexes = [
        "CREATE INDEX idx_page_views_domain_timestamp ON page_views(domain_id, view_timestamp)",
        "CREATE INDEX idx_page_views_domain_bot ON page_views(domain_id, is_bot)"
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

    echo "\nMissing indexes added successfully!\n";

} catch (PDOException $e) {
    die("Failed to add indexes: " . $e->getMessage() . "\n");
}
?> 