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

/**
 * Same pattern as getPayPalConfigByLeadUuid — Laravel:
 * Route::get('/leads/{uuid}/stripe-config', ...);
 */
function getStripeConfigByLeadUuid($leadUuid) {
    $url = BASE_URL . 'api/leads/' . $leadUuid . '/stripe-config';

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
        if (isset($data['success']) && $data['success'] && !empty($data['stripe'])) {
            $stripe = $data['stripe'];
            return [
                'success' => true,
                'publishable_key' => $stripe['publishable_key'] ?? $stripe['publishable'] ?? null,
                'secret_key' => $stripe['secret_key'] ?? $stripe['stripe_secret_key'] ?? null,
                'mode' => $stripe['mode'] ?? null,
                'environment' => $stripe['environment'] ?? null,
                'lead' => $data['lead'] ?? null,
                'brand' => $data['brand'] ?? [],
            ];
        }
    }

    return [
        'success' => false,
        'error' => 'Unable to fetch Stripe configuration',
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

function getSquareCheckoutUrl($linkData, $service, $amount, $uuid, $type) {
  
   
    $squareAccessToken = $linkData['brand']['square_access_token'] ?? null;
    $squareLocationId = $linkData['brand']['square_location_id'] ?? null;
    $squareEnvironment = $linkData['brand']['square_environment'] ?? 'sandbox';
    
    if (!$squareAccessToken || !$squareLocationId) {
        return ['error' => 'Square payment is not configured for this brand.'];
    }
    
    // Determine Square API URL based on environment
    $squareApiUrl = ($squareEnvironment === 'production') 
        ? 'https://connect.squareup.com/v2/online-checkout/payment-links'
        : 'https://connect.squareupsandbox.com/v2/online-checkout/payment-links';
    
    // Build redirect URLs
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . '://' . $host;
    
    if ($type === 'pkg') {
        $redirectUrl = $baseUrl . '/payment-step.php?status=success&id=' . $uuid . '&pkg=' . urlencode($service) . '&amt=' . $amount;
    } else {
        $redirectUrl = $baseUrl . '/pay.php?status=success&id=' . $uuid;
    }
    
    // Prepare order data
    $orderData = [
        'idempotency_key' => uniqid('square_', true),
        'quick_pay' => [
            'name' => is_array($service) ? implode(', ', $service) : $service,
            'price_money' => [
                'amount' => (int)($amount * 100), // Square uses cents
                'currency' => 'USD'
            ],
            'location_id' => $squareLocationId
        ],
        'checkout_options' => [
            'redirect_url' => $redirectUrl,
            'ask_for_shipping_address' => false
        ]
    ];
    
    // Make API request to Square
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $squareApiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Square-Version: 2024-12-18',
        'Authorization: Bearer ' . $squareAccessToken,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200) {
        $data = json_decode($response, true);
        if (isset($data['payment_link']['url'])) {
            return ['url' => $data['payment_link']['url']];
        }
    }
    
    $errorData = json_decode($response, true);
    $errorMsg = $errorData['errors'][0]['detail'] ?? 'Failed to create Square checkout';
    return ['error' => $errorMsg];
}

/**
 * Creates a Stripe PaymentIntent for embedded Payment Element (not Checkout redirect).
 */
function createStripePaymentIntent($linkData, $uuid) {
    $brand = $linkData['brand'] ?? [];
    $stripeSecretKey = $brand['stripe_secret_key']
        ?? $brand['stripe_secret']
        ?? $brand['stripe_sk']
        ?? $linkData['stripe_secret_key']
        ?? $linkData['stripe_secret']
        ?? null;

    if (!$stripeSecretKey) {
        return ['error' => 'Stripe payment is not configured for this brand.'];
    }

    $amount = (float) ($linkData['amount'] ?? 0);
    if ($amount <= 0) {
        return ['error' => 'Invalid payment amount.'];
    }

    $amountCents = (int) round($amount * 100);
    $description = 'Payment for Invoice #' . substr((string) $uuid, 0, 8);

    $postFields = [
        'amount' => (string) $amountCents,
        'currency' => 'usd',
        'automatic_payment_methods[enabled]' => 'true',
        'excluded_payment_method_types[0]' => 'crypto',
        'description' => $description,
        'metadata[payment_link_uuid]' => (string) $uuid,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $stripeSecretKey,
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        $data = json_decode($response, true);
        if (!empty($data['client_secret'])) {
            return ['clientSecret' => $data['client_secret']];
        }
    }

    $errorData = json_decode($response, true);
    $errorMsg = $errorData['error']['message'] ?? 'Failed to create Stripe payment';
    return ['error' => $errorMsg];
}

/**
 * Stripe PaymentIntent for package / lead checkout (payment-step.php).
 */
function createStripePaymentIntentForLead($brand, $leadUuid, $amount, $pkgName) {
    $stripeSecretKey = $brand['stripe_secret_key']
        ?? $brand['stripe_secret']
        ?? $brand['stripe_sk']
        ?? null;

    if (!$stripeSecretKey) {
        return ['error' => 'Stripe payment is not configured for this brand.'];
    }

    $amount = (float) $amount;
    if ($amount <= 0) {
        return ['error' => 'Invalid payment amount.'];
    }

    $amountCents = (int) round($amount * 100);
    $pkgLabel = is_string($pkgName) ? trim($pkgName) : '';
    $description = 'Signup Payment' . ($pkgLabel !== '' ? ' - ' . $pkgLabel : '');

    $postFields = [
        'amount' => (string) $amountCents,
        'currency' => 'usd',
        'automatic_payment_methods[enabled]' => 'true',
        'excluded_payment_method_types[0]' => 'crypto',
        'description' => $description,
        'metadata[lead_uuid]' => (string) $leadUuid,
        'metadata[context]' => 'package_lead',
        'metadata[package]' => $pkgLabel,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $stripeSecretKey,
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        $data = json_decode($response, true);
        if (!empty($data['client_secret'])) {
            return ['clientSecret' => $data['client_secret']];
        }
    }

    $errorData = json_decode($response, true);
    $errorMsg = $errorData['error']['message'] ?? 'Failed to create Stripe payment';
    return ['error' => $errorMsg];
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
