<?php

require_once 'payment-helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$leadId = $_POST['lead_id'] ?? null;
$amount = $_POST['amount'] ?? null;
$pkg = $_POST['pkg'] ?? '';

if (!$leadId || $amount === null || $amount === '') {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$config = getStripeConfigByLeadUuid($leadId);
if (empty($config['success'])) {
    echo json_encode(['error' => 'Unable to load lead payment configuration']);
    exit;
}

$brand = $config['brand'] ?? [];
if (!empty($config['secret_key'])) {
    $brand['stripe_secret_key'] = $config['secret_key'];
}
echo json_encode(createStripePaymentIntentForLead($brand, $leadId, (float) $amount, $pkg));
