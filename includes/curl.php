<?php
require_once 'config.php';

function fetch_domain_data($domain) {
    $data = [
        'http_status' => null,
        'server_type' => null,
        'content_type' => null,
        'load_time' => null,
        'redirects' => null,
        'ssl_status' => false
    ];
    
    // Test HTTP
    $http_data = fetch_http_data($domain);
    $data = array_merge($data, $http_data);
    
    // Test HTTPS
    $https_data = fetch_http_data($domain, 'https');
    if ($https_data['http_status'] && $https_data['http_status'] < 400) {
        $data['ssl_status'] = true;
        $data = array_merge($data, $https_data);
    }
    
    // Ensure ssl_status is always a boolean
    $data['ssl_status'] = (bool)$data['ssl_status'];
    
    return $data;
}

function fetch_http_data($domain, $protocol = 'http') {
    $url = $protocol . '://' . $domain;
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => CURL_FOLLOW_REDIRECTS,
        CURLOPT_MAXREDIRS => CURL_MAX_REDIRECTS,
        CURLOPT_TIMEOUT => CURL_TIMEOUT,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; ZoneWhois/1.0)',
        CURLOPT_HEADER => true,
        CURLOPT_NOBODY => true
    ]);
    
    $start_time = microtime(true);
    $response = curl_exec($ch);
    $end_time = microtime(true);
    
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $server = curl_getinfo($ch, CURLINFO_HTTP_VERSION);
    $redirect_count = curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
    $final_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    
    curl_close($ch);
    
    $load_time = round(($end_time - $start_time) * 1000, 2); // milliseconds
    
    return [
        'http_status' => $http_code,
        'server_type' => get_server_type($response),
        'content_type' => $content_type,
        'load_time' => $load_time,
        'redirects' => $redirect_count > 0 ? $final_url : null
    ];
}

function get_server_type($response) {
    $headers = explode("\n", $response);
    foreach ($headers as $header) {
        if (stripos($header, 'Server:') === 0) {
            return trim(substr($header, 7));
        }
    }
    return null;
}

function fetch_meta_tags($domain) {
    $url = 'http://' . $domain;
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; ZoneWhois/1.0)'
    ]);
    
    $html = curl_exec($ch);
    curl_close($ch);
    
    if (!$html) {
        return ['meta_description' => null, 'meta_title' => null, 'meta_keywords' => null];
    }
    
    $meta = [
        'meta_description' => null,
        'meta_title' => null,
        'meta_keywords' => null
    ];
    
    // Extract meta tags
    if (preg_match('/<meta\s+name=["\']description["\']\s+content=["\']([^"\']+)["\']/i', $html, $matches)) {
        $meta['meta_description'] = $matches[1];
    }
    
    if (preg_match('/<meta\s+name=["\']keywords["\']\s+content=["\']([^"\']+)["\']/i', $html, $matches)) {
        $meta['meta_keywords'] = $matches[1];
    }
    
    if (preg_match('/<title>([^<]+)<\/title>/i', $html, $matches)) {
        $meta['meta_title'] = $matches[1];
    }
    
    return $meta;
}

function fetch_whois_data($domain) {
    $whois_data = [
        'creation_date' => null,
        'expiration_date' => null,
        'renewal_date' => null,
        'registrar' => null
    ];
    
    // Use whois command if available
    $whois_output = shell_exec("whois " . escapeshellarg($domain) . " 2>/dev/null");
    
    if ($whois_output) {
        // Parse creation date
        if (preg_match('/Creation Date:\s*(.+)$/im', $whois_output, $matches)) {
            $whois_data['creation_date'] = parse_whois_date($matches[1]);
        }
        
        // Parse expiration date
        if (preg_match('/Registry Expiry Date:\s*(.+)$/im', $whois_output, $matches)) {
            $whois_data['expiration_date'] = parse_whois_date($matches[1]);
        }
        
        // Parse registrar
        if (preg_match('/Registrar:\s*(.+)$/im', $whois_output, $matches)) {
            $whois_data['registrar'] = trim($matches[1]);
        }
    }
    
    return $whois_data;
}

function parse_whois_date($date_string) {
    $date_string = trim($date_string);
    
    // Try common WHOIS date formats
    $formats = [
        'Y-m-d\TH:i:s\Z',
        'Y-m-d H:i:s',
        'Y-m-d',
        'd-M-Y',
        'M d, Y'
    ];
    
    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $date_string);
        if ($date) {
            return $date->format('Y-m-d');
        }
    }
    
    return null;
}

function refresh_domain($domain_id) {
    require_once 'db.php';
    
    $pdo = connect_db();
    $stmt = $pdo->prepare("SELECT domain_name FROM domains WHERE id = ?");
    $stmt->execute([$domain_id]);
    $domain = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$domain) {
        return false;
    }
    
    $domain_name = $domain['domain_name'];
    
    // Fetch all data
    $http_data = fetch_domain_data($domain_name);
    $meta_data = fetch_meta_tags($domain_name);
    $whois_data = fetch_whois_data($domain_name);
    
    // Combine all data
    $update_data = array_merge($http_data, $meta_data, $whois_data);
    $update_data['domain_name'] = $domain_name;
    
    return update_domain($domain_id, $update_data);
}
?> 