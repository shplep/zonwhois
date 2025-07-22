<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

try {
    $pdo = connect_db();
    
    // Check if outbound_link column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM domains LIKE 'outbound_link'");
    $column_exists = $stmt->fetch();
    
    if (!$column_exists) {
        echo "Adding outbound_link column to domains table...\n";
        
        // Add the outbound_link column
        $sql = "ALTER TABLE domains ADD COLUMN outbound_link BOOLEAN DEFAULT TRUE AFTER ssl_status";
        $pdo->exec($sql);
        
        echo "✅ outbound_link column added successfully\n";
    } else {
        echo "✅ outbound_link column already exists\n";
    }
    
    echo "Migration completed successfully!\n";
    
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
?> 