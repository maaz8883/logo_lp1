<?php

$host = $_SERVER['HTTP_HOST'];

if ($host === 'localhost' || $host === '127.0.0.1') {
    $base = 'http://127.0.0.1:8000/';
} else {
    $base = 'https://elementdesignagency.com/crm/';
}

define('BASE_URL', $base);
define('CRM_API_URL', $base . 'api/lead-links/');
define('CRM_API_URL_PAYMENT', $base . 'api/payment-links/');


function getPayPalConfigByLeadUuid($leadUuid) {
    $url = BASE_URL . 'api/leads/' . $leadUuid . '/paypal-config';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error_msg = curl_error($ch);
    curl_close($ch);
    
    if ($http_code === 200) {
        $data = json_decode($response, true);
        if (isset($data['success']) && $data['success']) {
            return [
                'success' => true,
                'client_id' => $data['paypal']['client_id'],
                'mode' => $data['paypal']['mode'],
                'environment' => $data['paypal']['environment'],
                'lead' => $data['lead'],
                'brand' => $data['brand']
            ];
        }
    }
    
    return [
        'success' => false,
        'error' => 'Unable to fetch PayPal configuration',
        'http_code' => $http_code,
        'error_msg' => $error_msg
    ];
}

function PaymentDetails_uuid($uuid) {
    $url = CRM_API_URL_PAYMENT . $uuid;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error_msg = curl_error($ch);
    curl_close($ch);
    
    if ($http_code === 200) {
        return json_decode($response, true);
    }
    return null;
}

function getPaymentDetails($id) {
    $url = CRM_API_URL . $id;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200) {
        return json_decode($response, true);
    }
    return null;
}

function verifyPaymentWithCrm($id) {
    $verifyUrl = BASE_URL . 'api/payment-links/' . $id . '/verify';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $verifyUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $response;
}

function getBriefFormUrl($leadId, $baseUrl = null) {
    if (!$leadId) {
        return ['error' => 'Lead ID is required'];
    }
    
    if (!$baseUrl) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $baseUrl = $protocol . '://' . $host;
    }
    
    $apiUrl = str_replace('lead-links/', 'api/leads/' . $leadId . '/encrypted-id', CRM_API_URL);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200) {
        $data = json_decode($response, true);
        $encryptedId = $data['encrypted_lead_id'] ?? $data['encrypted_id'] ?? null;
        
        if ($encryptedId) {
            $scriptPath = dirname($_SERVER['PHP_SELF']);
            $briefFormPath = rtrim($scriptPath, '/\\') . '/brief-form.php';
            return $baseUrl . $briefFormPath . '?encrypted_lead_id=' . urlencode($encryptedId);
        }
    }
    
    return ['error' => 'Unable to generate encrypted lead ID'];
}
