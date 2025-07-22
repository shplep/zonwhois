<?php
// Database Configuration
define('DB_HOST', '63.142.241.185');
define('DB_PORT', '3306');
define('DB_NAME', 'coyotebytes_zonwho');
define('DB_USER', 'coyotebytes_zonwhoadm');
define('DB_PASS', 'k2!gN6KqraG53$6j');

// Site Configuration
define('SITE_URL', 'https://zonwhois.com');
define('SITE_NAME', 'ZoneWhois');
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'abcd1234');

// Security Settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('RATE_LIMIT_REQUESTS', 100); // requests per hour
define('RATE_LIMIT_PERIOD', 3600); // 1 hour

// cURL Settings
define('CURL_TIMEOUT', 30);
define('CURL_FOLLOW_REDIRECTS', true);
define('CURL_MAX_REDIRECTS', 5);

// Pagination
define('DOMAINS_PER_PAGE', 20);
define('ADMIN_ITEMS_PER_PAGE', 50);

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Set timezone
date_default_timezone_set('UTC');
?> 