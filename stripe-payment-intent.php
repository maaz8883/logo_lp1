<?php

require_once 'payment-helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Allow: POST, OPTIONS');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Max-Age: 86400');
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST, OPTIONS');
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$uuid = $_POST['uuid'] ?? null;
if (!$uuid) {
    echo json_encode(['error' => 'Missing payment id']);
    exit;
}

$linkData = PaymentDetails_uuid($uuid);
if (!$linkData || ($linkData['status'] ?? '') !== 'pending') {
    echo json_encode(['error' => 'Invalid or expired payment link']);
    exit;
}

echo json_encode(createStripePaymentIntent($linkData, $uuid));
