<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'Domain information and WHOIS lookup service'; ?>">
    <meta name="keywords" content="<?php echo isset($page_keywords) ? $page_keywords : 'domain, whois, lookup, information'; ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="/">
                        <img src="/assets/img/logo.png" alt="<?php echo SITE_NAME; ?>" class="logo-img">
                    </a>
                </div>
                <nav class="nav">
                    <ul class="nav-list">
                        <li><a href="/" class="nav-link">Home</a></li>
                        <li><a href="/about.php" class="nav-link">About Us</a></li>
                        <li><a href="/categories.php" class="nav-link">Categories</a></li>
                        <li><a href="/countries.php" class="nav-link">Countries</a></li>
                        <li><a href="/contact.php" class="nav-link">Contact</a></li>
                    </ul>
                </nav>
                <div class="mobile-menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>
    <main class="main"> 