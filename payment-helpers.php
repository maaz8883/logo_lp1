<?php

// CRM Configuration
if (!defined('CRM_API_URL')) {
    define('CRM_API_URL', 'https://elementdesignagency.com/crm/api/lead-links/');
}
define('CRM_API_URL_PAYMENT', 'https://elementdesignagency.com/crm/api/payment-links/');
define('BASE_URL', 'https://elementdesignagency.com/crm/');

// live mode
// $paypalClientId = 'AWf9KL0KBi4GhT2rzRvazWLiDVxV8e1MwwSG6CrrM9Bh8gvdyfpG2vgcBxCrJQgXY5l3hiH3m774Q_e_'; 

// test mode 
$paypalClientId = 'AWRCRUFnNtXfdNCut8-YeeXQc7CDe-2FQmVt4jwPg3Cbl1TJ6pECsjdg8ITRSL-PPbIcVEOcmnptBAZe'; 




function PaymentDetails_uuid($uuid)
{
    $url = CRM_API_URL_PAYMENT . $uuid;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    // Enable SSL verification bypass if needed for local/self-signed certs
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


function getPaymentDetails($id)
{
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


function verifyPaymentWithCrm($id)
{
    $verifyUrl = BASE_URL . 'api/payment-links/' . $id . '/verify';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $verifyUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    // return $http_code === 200;
    return $response;
}


// function getCloverCheckoutUrl($linkData, $packageName, $amount , $leadId = "",$type)
// {
//     $cloverKey = $linkData['brand']['clover_api_key'] ?? null;
//     $merchantId = $linkData['brand']['clover_merchant_id'] ?? null;

//     if (!$cloverKey || !$merchantId) {
//         return ['error' => "Clover payment is not configured for this brand."];
//     }
    
//     // return $linkData;

//     $env = 'live';
//     $baseUrl = ($env === 'live' || $env === 'production') ? 'https://www.clover.com' : 'https://sandbox.dev.clover.com';
//     $amountCents = round($amount * 100);
//     $checkoutUrl = $baseUrl . '/invoicingcheckoutservice/v1/checkouts';
    
//     if($type=="pkg"){
//        $success = "https://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/thank-you.php?status=success&id=" . $leadId . "&pkg=" . urlencode($packageName);
//     }else{
//         $success = "https://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/pay?status=success&id=" . $leadId;

//     }

//     $payload = [
//         "currency" => "USD",
//         "totalAmount" => $amountCents,
//         "shoppingCart" => [
//             "lineItems" => [
//                 [
//                     "name" => "Signup - " . ($packageName ?? "Custom Package"),
//                     "price" => $amountCents,
//                     "unitQty" => 1
//                 ]
//             ]
//         ],  
//         "customer" => [ 
//             "email" => $linkData['customer_email'] ?? "",
//             "name" => $linkData['customer_name'] ?? "",   
//         ],
//         "redirectUrls" => [
//             "success" => $success,
//             "cancel" => "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?id=" . $leadId . "&pkg=" . urlencode($packageName) . "&amt=" . $amount,
//         ]
//     ]; 
    
//     // return $checkoutUrl;

//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $checkoutUrl);
//     curl_setopt($ch, CURLOPT_POST, 1);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, [
//         'Authorization: Bearer ' . trim($cloverKey),
//         'Content-Type: application/json',
//         'Accept: application/json',
//         'X-Clover-Merchant-Id: ' . trim($merchantId)
//     ]);

//     $response = curl_exec($ch);
//     $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//     $respData = json_decode($response, true);
//     curl_close($ch);

//     if (($http_code === 200 || $http_code === 201) && isset($respData['href'])) {
//         return ['url' => $respData['href']];
//     }

//     return ['error' => "Clover Error ($http_code): " . ($respData['message'] ?? $response)];
// }


function getBriefFormUrl($leadId, $baseUrl = null) {
    if (!$leadId) {
        return ['error' => 'Lead ID is required'];
    }

    // If base URL not provided, detect from current request
    if (!$baseUrl) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $baseUrl = $protocol . '://' . $host;
    }

    // Get encrypted lead ID from Laravel API
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
            // Get the directory path for brief-form.php
            $scriptPath = dirname($_SERVER['PHP_SELF']);
            $briefFormPath = rtrim($scriptPath, '/\\') . '/brief-form.php';
            
            return $baseUrl . $briefFormPath . '?encrypted_lead_id=' . urlencode($encryptedId);
        }
    }

    return ['error' => 'Unable to generate encrypted lead ID'];
}

