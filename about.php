<?php
require_once 'includes/config.php';

$page_title = 'About Us';
$page_description = 'Learn about ZoneWhois - your trusted source for domain information and WHOIS lookup services.';
$page_keywords = 'about, domain lookup, whois, zonewhois';

include 'includes/header.php';
?>

<div class="container">
    <div class="domain-details">
        <div class="domain-header">
            <h1>About ZoneWhois</h1>
            <p>Your trusted source for domain information</p>
        </div>
        
        <div class="domain-stats">
            <div class="stat-item">
                <h3>What We Do</h3>
                <p>ZoneWhois provides comprehensive domain information and WHOIS lookup services. We help you discover detailed information about any domain including creation dates, expiration dates, registrar information, HTTP status, server details, and more.</p>
            </div>
            
            <div class="stat-item">
                <h3>Our Features</h3>
                <ul>
                    <li>Real-time domain information lookup</li>
                    <li>WHOIS data retrieval</li>
                    <li>HTTP status and server information</li>
                    <li>SSL/TLS security status</li>
                    <li>Page load time analysis</li>
                    <li>Meta tag extraction</li>
                    <li>Redirect tracking</li>
                    <li>Comprehensive domain statistics</li>
                </ul>
            </div>
            
            <div class="stat-item">
                <h3>How It Works</h3>
                <p>Simply enter any domain name in our search bar, and we'll fetch comprehensive information about that domain. Our system performs real-time lookups to provide you with the most current and accurate data available.</p>
            </div>
            
            <div class="stat-item">
                <h3>Privacy & Security</h3>
                <p>We respect your privacy and implement strict security measures to protect your data. All lookups are performed securely, and we don't store personal information beyond what's necessary for service operation.</p>
            </div>
        </div>
        
        <div class="domain-actions">
            <a href="/" class="form-button">Start Searching</a>
            <a href="/contact.php" class="form-button" style="background: #6c757d;">Contact Us</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 