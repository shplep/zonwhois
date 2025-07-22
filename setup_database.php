<?php
require_once 'includes/config.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    
    echo "Connected to database successfully!\n";
    
    // Create tables
    $tables = [
        "CREATE TABLE IF NOT EXISTS domains (
            id INT AUTO_INCREMENT PRIMARY KEY,
            domain_name VARCHAR(255) UNIQUE NOT NULL,
            creation_date DATE NULL,
            expiration_date DATE NULL,
            renewal_date DATE NULL,
            registrar VARCHAR(100) NULL,
            meta_description TEXT NULL,
            meta_title VARCHAR(255) NULL,
            meta_keywords TEXT NULL,
            http_status INT NULL,
            server_type VARCHAR(100) NULL,
            content_type VARCHAR(100) NULL,
            load_time FLOAT NULL,
            redirects TEXT NULL,
            ssl_status BOOLEAN DEFAULT FALSE,
            status ENUM('visible', 'hidden') DEFAULT 'visible',
            last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_domain_name (domain_name),
            INDEX idx_creation_date (creation_date),
            INDEX idx_status (status)
        )",
        
        "CREATE TABLE IF NOT EXISTS page_views (
            id INT AUTO_INCREMENT PRIMARY KEY,
            domain_id INT NOT NULL,
            view_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_bot BOOLEAN DEFAULT FALSE,
            user_agent TEXT NULL,
            INDEX idx_domain_id (domain_id),
            INDEX idx_view_timestamp (view_timestamp),
            INDEX idx_is_bot (is_bot),
            FOREIGN KEY (domain_id) REFERENCES domains(id) ON DELETE CASCADE
        )",
        
        "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) UNIQUE NOT NULL
        )",
        
        "CREATE TABLE IF NOT EXISTS countries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) UNIQUE NOT NULL
        )",
        
        "CREATE TABLE IF NOT EXISTS contact_submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            submission_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS domain_categories (
            domain_id INT NOT NULL,
            category_id INT NOT NULL,
            PRIMARY KEY (domain_id, category_id),
            FOREIGN KEY (domain_id) REFERENCES domains(id) ON DELETE CASCADE,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        )",
        
        "CREATE TABLE IF NOT EXISTS domain_countries (
            domain_id INT NOT NULL,
            country_id INT NOT NULL,
            PRIMARY KEY (domain_id, country_id),
            FOREIGN KEY (domain_id) REFERENCES domains(id) ON DELETE CASCADE,
            FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE CASCADE
        )"
    ];
    
    foreach ($tables as $sql) {
        $pdo->exec($sql);
        echo "Table created successfully.\n";
    }
    
    // Insert initial categories
    $categories = [
        'Technology', 'E-commerce', 'News', 'Entertainment', 'Education',
        'Finance', 'Health', 'Travel', 'Sports', 'Business'
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
    foreach ($categories as $category) {
        $stmt->execute([$category]);
    }
    echo "Categories inserted.\n";
    
    // Insert initial countries
    $countries = [
        'United States', 'United Kingdom', 'Canada', 'Australia', 'Germany',
        'France', 'Japan', 'Brazil', 'India', 'China'
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO countries (name) VALUES (?)");
    foreach ($countries as $country) {
        $stmt->execute([$country]);
    }
    echo "Countries inserted.\n";
    
    echo "\nDatabase setup completed successfully!\n";
    
} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage() . "\n");
}
?> 