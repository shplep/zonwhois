<?php
// ZoneWhois Configuration Template
// Copy this file to config.php and update with your settings

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'zonwhois');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Site Configuration
define('SITE_NAME', 'ZoneWhois');
define('SITE_URL', 'https://yourdomain.com');
define('ADMIN_ITEMS_PER_PAGE', 20);

// Admin Credentials
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'your_secure_password');

// Rate Limiting
define('RATE_LIMIT_REQUESTS', 100);
define('RATE_LIMIT_WINDOW', 3600); // 1 hour in seconds

// cURL Settings
define('CURL_TIMEOUT', 30);
define('CURL_USER_AGENT', 'ZoneWhois/1.0');

// Session Settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('SESSION_NAME', 'zonwhois_session');

// Error Reporting (set to false in production)
define('DEBUG_MODE', true);

// Cache Settings
define('CACHE_ENABLED', true);
define('CACHE_TTL', 300); // 5 minutes

// WHOIS Settings
define('WHOIS_TIMEOUT', 10);
define('WHOIS_SERVERS', [
    'com' => 'whois.verisign-grs.com',
    'net' => 'whois.verisign-grs.com',
    'org' => 'whois.pir.org',
    'info' => 'whois.afilias.net'
]);
?> 