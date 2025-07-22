<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Set content type to XML
header('Content-Type: application/xml; charset=utf-8');

// Generate sitemap
echo generate_sitemap(); 