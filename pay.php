<?php

require_once 'payment-helpers.php';

// Stripe PaymentIntent — same URL as this page (avoids live redirects POST→GET on extensionless /stripe-payment-intent)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Allow: POST, OPTIONS');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Max-Age: 86400');
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stripe_intent_payment_link'])) {
    header('Content-Type: application/json');
    $uuidPost = $_POST['uuid'] ?? null;
    if (!$uuidPost) {
        echo json_encode(['error' => 'Missing payment id']);
        exit;
    }
    $linkDataPost = PaymentDetails_uuid($uuidPost);
    if (!$linkDataPost || ($linkDataPost['status'] ?? '') !== 'pending') {
        echo json_encode(['error' => 'Invalid or expired payment link']);
        exit;
    }
    echo json_encode(createStripePaymentIntent($linkDataPost, $uuidPost));
    exit;
}

// $baseCrmUrl = 'https://elementdesignagency.com/crm/';
$hostname = $_SERVER['HTTP_HOST'];

if ($hostname === 'localhost' || $hostname === '127.0.0.1') {
    $baseCrmUrl = 'http://127.0.0.1:8000/';
} else {
    $baseCrmUrl = 'https://elementdesignagency.com/crm/';
}

// echo $baseCrmUrl;
// exit;

$uuid = $_GET['id'] ?? null;
$error = null;
$linkData = null;
$selectedMerchant = '';

if ($uuid) {
    $linkData = PaymentDetails_uuid($uuid);

    if (!$linkData) {
        $error = "Payment link not found or expired.";
    } else {
        // Get PayPal client ID from API response
        $paypalClientId = $linkData['brand']['paypal_client_id'] ?? null;
        $paypalMode = $linkData['brand']['paypal_mode'] ?? 'sandbox';
        $paypalEnvironment = $linkData['brand']['paypal_environment'] ?? 'sandbox';
        $stripePublishableKey = $linkData['brand']['stripe_publishable_key']
            ?? $linkData['brand']['stripe_publishable']
            ?? $linkData['brand']['stripe_pk']
            ?? $linkData['stripe_publishable_key']
            ?? $linkData['stripe_publishable']
            ?? null;
        $selectedMerchant = strtolower((string) ($linkData['merchant'] ?? ''));
    }
} else {
    $error = "No Payment ID provided.";
}

// Handle Payment Submission (Clover)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_method']) && $_POST['pay_method'] === 'clover' && $linkData) {
    $cloverKey = $linkData['brand']['clover_api_key'] ?? null;
    $merchantId = $linkData['brand']['clover_merchant_id'] ?? null;

    if (!$cloverKey || !$merchantId) {
        $error = "Clover payment is not configured for this brand.";
    } else {
        $checkout = getCloverCheckoutUrl($linkData, $linkData['custom_service'], $linkData['amount'],$uuid,'link');
       
        if(isset($checkout['url'])) {
            header("Location: " . $checkout['url']);
            exit;
        } else {
            $error = $checkout['error'];
        }
    }
}

// print_r($linkData);

// Handle Payment Submission (Square)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_method']) && $_POST['pay_method'] === 'square' && $linkData) {
    $squareAccessToken = $linkData['brand']['square_access_token'] ?? null;
    $squareLocationId = $linkData['brand']['square_location_id'] ?? null;

    if (!$squareAccessToken || !$squareLocationId) {
        $error = "Square payment is not configured for this brand.";
    } else {
        $checkout = getSquareCheckoutUrl($linkData, $linkData['custom_service'], $linkData['amount'], $uuid, 'link');
       
        if(isset($checkout['url'])) {
            header("Location: " . $checkout['url']);
            exit;
        } else {
            $error = $checkout['error'];
        }
    }
}

// If coming back from Clover Success, update CRM and redirect
if (isset($_GET['status']) && $_GET['status'] == 'success' && $uuid && $linkData) {
   
//   echo 1;
 verifyPaymentWithCrm($uuid);
 // Reload data after verification to get updated status
 $linkData = PaymentDetails_uuid($uuid);
 
 // Redirect to pay-thank-you.php
 header("Location: pay-thank-you.php?id=" . urlencode($uuid));
 exit;
}

// print_r($linkData);
// exit;

?>
<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KW7SCQJP');</script>
<!-- End Google Tag Manager -->
    
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .invoice-card {
            max-width: 500px;
            width: 100%;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            margin: 0 auto;
        }

        .brand-logo {
            max-height: 80px;
            object-fit: contain;
            margin-bottom: 20px;
        }

        .main-content {
            /*padding-top: 60px;*/
            padding-bottom: 60px;
            display: flex;
            justify-content: center;
        }

        /* Loading Overlay */
        #payment-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            z-index: 9999;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-family: 'Outfit', sans-serif;
        }

        .spinner {
            width: 80px;
            height: 80px;
            border: 4px solid rgba(255, 215, 0, 0.1);
            border-left-color: #FFD700;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
            box-shadow: 0 0 20px rgba(184, 134, 11, 0.2);
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loader-text {
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #FFD700 0%, #B8860B 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>




    <?php if ($linkData && $selectedMerchant === 'paypal' && $paypalClientId): ?>
        <!-- Dynamically load PayPal SDK with correct client ID -->
        <script src="https://www.paypal.com/sdk/js?client-id=<?= htmlspecialchars($paypalClientId) ?>&currency=USD"></script>
    <?php endif; ?>

    <?php if ($selectedMerchant === 'stripe' && !empty($stripePublishableKey)): ?>
        <script src="https://js.stripe.com/v3/"></script>
    <?php endif; ?>

</head>

<style>
/* ─────────────────────────────────────────
   RESET & BASE
───────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'Outfit', sans-serif;
  background: #f4f2ef;
  color: #1a1a1a;
  min-height: 100vh;
}

:root {
  --brand:       #BE5264;
  --brand-dark:  #9e3e52;
  --brand-light: #fdf0f2;
  --gold:        #F8BB16;
  --success:     #3B6D11;
  --success-bg:  #eaf3de;
  --warn-bg:     #faeeda;
  --warn-text:   #854F0B;
  --radius-sm:   8px;
  --radius-md:   12px;
  --radius-lg:   18px;
  --radius-xl:   24px;
}

/* ─────────────────────────────────────────
   MODAL OVERLAY - HIGHEST PRIORITY
───────────────────────────────────────── */
.overlay {
  position: fixed !important; 
  top: 0 !important;
  left: 0 !important;
  right: 0 !important;
  bottom: 0 !important;
  z-index: 9999 !important;
  background: rgba(10, 6, 6, 0.65) !important;
  backdrop-filter: blur(4px) !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  padding: 1rem !important;
  animation: fadeIn 0.3s ease !important;
  opacity: 1 !important;
  visibility: visible !important;
}
.overlay.hidden {
  display: none !important;
  opacity: 0 !important;
  visibility: hidden !important;
}
@keyframes fadeIn { 
  from { opacity: 0; } 
  to { opacity: 1; } 
}

.modal {
  background: #fff;
  border-radius: var(--radius-xl);
  border: 1px solid #ebe9e4;
  padding: 2.25rem 2rem 1.75rem;
  max-width: 430px;
  width: 100%;
  text-align: center;
  animation: slideUp 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  box-shadow: 0 24px 60px rgba(0,0,0,0.18);
}
@keyframes slideUp {
  from { transform: translateY(28px); opacity: 0; }
  to   { transform: translateY(0);    opacity: 1; }
}

.check-circle {
  width: 72px; height: 72px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--brand) 0%, #e07a8c 100%);
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 1.25rem;
  box-shadow: 0 8px 24px rgba(190,82,100,0.35);
  animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.15s both;
}
@keyframes popIn {
  from { transform: scale(0); opacity: 0; }
  to   { transform: scale(1); opacity: 1; }
}
.check-circle i { color: #fff; font-size: 28px; }

.modal h1 {
  font-size: 24px;
  font-weight: 700;
  color: var(--brand);
  margin-bottom: 8px;
}
.modal .sub {
  font-size: 13.5px;
  color: #6b6b6b;
  line-height: 1.65;
  margin-bottom: 1.5rem;
}

.order-meta {
  background: #faf9f7;
  border: 1px solid #ebe9e4;
  border-radius: var(--radius-md);
  padding: 1rem 1.1rem;
  margin-bottom: 1.5rem;
  text-align: left;
}
.meta-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 7px 0;
  border-bottom: 1px solid #ebe9e4;
  font-size: 13.5px;
}
.meta-row:last-child { border-bottom: none; padding-bottom: 0; }
.meta-label { color: #888; font-weight: 500; }
.meta-val   { font-weight: 600; color: var(--brand); }
.status-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--success); margin-right: 5px; }

.modal-btns { display: flex; flex-direction: column; gap: 10px; }

.btn-primary {
  display: block; width: 100%;
  padding: 13px;
  background: linear-gradient(135deg, var(--brand) 0%, #d4667a 100%);
  color: #fff;
  font-family: 'Outfit', sans-serif;
  font-size: 14px; font-weight: 600;
  border: none; border-radius: var(--radius-md);
  cursor: pointer;
  transition: transform 0.15s, box-shadow 0.15s;
  text-decoration: none;
}
.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(190,82,100,0.35);
}

.btn-ghost {
  display: block; width: 100%;
  padding: 11px;
  background: transparent;
  font-family: 'Outfit', sans-serif;
  font-size: 13px; font-weight: 500;
  color: #888;
  border: 1px solid #ddd;
  border-radius: var(--radius-md);
  cursor: pointer;
  text-decoration: none;
  transition: background 0.15s, color 0.15s;
}
.btn-ghost:hover { background: #f4f2ef; color: #444; }

/* ─────────────────────────────────────────
   ADDONS PAGE
───────────────────────────────────────── */
.addons-page {
  max-width: 920px;
  margin: 0 auto;
  padding: 2.5rem 1.25rem 2.5rem;
}

.page-header { text-align: center; margin-bottom: 2rem; }
.promo-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: var(--warn-bg);
  color: var(--warn-text);
  font-size: 12px; font-weight: 600;
  padding: 5px 14px;
  border-radius: 30px;
  margin-bottom: 12px;
  letter-spacing: 0.3px;
}
.page-header h2 {
  font-size: 30px; font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 6px;
}
.page-header p { font-size: 14px; color: #777; }

/* GRID */
.addons-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
  gap: 12px;
}

.addon-card {
  background: #fff;
  border: 1.5px solid #ebe9e4;
  border-radius: var(--radius-lg);
  padding: 1rem 1rem 1rem;
  cursor: pointer;
  display: flex;
  gap: 12px;
  align-items: flex-start;
  transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
  user-select: none;
}
.addon-card:hover {
  border-color: #d4a0aa;
  box-shadow: 0 4px 16px rgba(190,82,100,0.1);
}
.addon-card.sel {
  border-color: var(--brand);
  background: var(--brand-light);
  box-shadow: 0 4px 20px rgba(190,82,100,0.15);
}

.addon-icon {
  width: 44px; height: 44px; flex-shrink: 0;
  border-radius: var(--radius-sm);
  background: #f4f2ef;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px;
  transition: background 0.18s;
}
.addon-card.sel .addon-icon { background: #f7d8de; }

.addon-body { flex: 1; min-width: 0; }
.addon-body h3 {
  font-size: 14px; font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 4px;
  line-height: 1.3;
}
.addon-body p {
  font-size: 12px;
  color: #888;
  line-height: 1.55;
  margin-bottom: 9px;
}
.pricing { display: flex; gap: 7px; align-items: baseline; flex-wrap: wrap; }
.price-now { font-size: 16px; font-weight: 700; color: var(--brand); }
.price-was { font-size: 12px; color: #bbb; text-decoration: line-through; }
.save-pill {
  font-size: 11px; font-weight: 600;
  background: var(--success-bg);
  color: var(--success);
  padding: 2px 7px;
  border-radius: 20px;
}

/* Checkbox ring */
.ring {
  width: 22px; height: 22px; flex-shrink: 0;
  border-radius: 50%;
  border: 2px solid #ccc;
  display: flex; align-items: center; justify-content: center;
  margin-top: 1px;
  transition: background 0.18s, border-color 0.18s;
}
.addon-card.sel .ring {
  background: var(--brand);
  border-color: var(--brand);
}
.ring i {
  display: none;
  font-size: 11px;
  color: #fff;
}
.addon-card.sel .ring i { display: block; }

/* ─────────────────────────────────────────
   CHECKOUT BAR (sticky bottom)
───────────────────────────────────────── */
.checkout-bar {
  position: fixed;
  bottom: 0; left: 0; right: 0;
  z-index: 90;
  background: #fff;
  border-top: 2px solid #ebe9e4;
  box-shadow: 0 -4px 24px rgba(0,0,0,0.09);
  padding: 14px 40px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
  width:60%;
  margin: 0 auto;
}
body { padding-bottom: 82px; }
.bar-left small {
  display: block;
  font-size: 12px;
  color: #999;
  margin-bottom: 4px;
  font-weight: 500;
  letter-spacing: 0.3px;
}
.bar-left strong {
  font-size: 28px; font-weight: 700;
  color: #1a1a1a;
  line-height: 1;
}
.bar-left strong .currency { font-size: 18px; vertical-align: super; margin-right: 1px; }
.bar-left strong .amount   { color: var(--brand); }
.bar-right { display: flex; gap: 10px; align-items: center; }

.s-skip {
  font-family: 'Outfit', sans-serif;
  font-size: 13px; font-weight: 500;
  color: #999;
  background: transparent;
  border: 1px solid #ddd;
  border-radius: var(--radius-sm);
  padding: 11px 22px;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
  text-decoration: none;
  display: inline-block;
}
.s-skip:hover { background: #f4f2ef; color: #555; }

.s-checkout {
  font-family: 'Outfit', sans-serif;
  font-size: 14px; font-weight: 600;
  background: linear-gradient(135deg, var(--brand) 0%, #d4667a 100%);
  color: #fff;
  border: none;
  border-radius: var(--radius-sm);
  padding: 13px 28px;
  cursor: pointer;
  display: inline-flex; align-items: center; gap: 8px;
  transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
}
.s-checkout:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(190,82,100,0.35);
}
.s-checkout:disabled {
  opacity: 0.38;
  cursor: default;
  box-shadow: none;
  transform: none;
}

/* ─────────────────────────────────────────
   FOOTER
───────────────────────────────────────── */
.site-footer {
  background: #111;
  padding: 0;
  margin-bottom: 0;
}
.footer-inner {
  max-width: 920px;
  margin: 0 auto;
  padding: 26px 40px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 14px;
  border-bottom: 1px solid rgba(255,255,255,0.06);
}
.footer-logo img {
  height: 32px;
  filter: brightness(0) invert(1);
  opacity: 0.8;
}
.footer-links {
  display: flex;
  gap: 22px;
}
.footer-links a {
  font-size: 12px;
  color: rgba(255,255,255,0.38);
  text-decoration: none;
  transition: color 0.15s;
}
.footer-links a:hover { color: rgba(255,255,255,0.75); }
.footer-copy-bar {
  background: var(--brand);
  padding: 12px 40px;
  text-align: center;
}
.footer-copy-bar p {
  font-size: 12.5px;
  color: rgba(255,255,255,0.88);
  letter-spacing: 0.2px;
}
.footer-copy-bar p a {
  color: #fff;
  font-weight: 600;
  text-decoration: none;
}

/* ─────────────────────────────────────────
   RESPONSIVE
───────────────────────────────────────── */
@media (max-width: 600px) {
  header { padding: 14px 18px; }
  .addons-page { padding: 1.5rem 0.85rem 2rem; }
  .page-header h2 { font-size: 22px; }
  .checkout-bar { padding: 12px 16px; width:100%;}
  .bar-left strong { font-size: 20px; }
  body { padding-bottom: 74px; }
  .s-checkout { padding: 11px 18px; font-size: 13px; }
  .s-skip { padding: 10px 14px; }
  .modal { padding: 1.75rem 1.25rem 1.5rem; }
  .footer-inner { flex-direction: column; align-items: flex-start; padding: 22px 18px; }
  .footer-copy-bar { padding: 12px 18px; }
}
</style>

<body>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KW7SCQJP"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<script>
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".open-livechat").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      if (window.LiveChatWidget) {
        LiveChatWidget.call("maximize");
      }
    });
  });
});
</script>





    <div id="payment-loader">
        <div class="spinner"></div>
        <div class="loader-text">Verifying Payment...</div>
    </div>

    <header>
        <div class="logo">
            <img src="./assets/images/header-footer/black-logo.png" alt="">
        </div>
        <div class="header-right">
            <a href="tel:+12792251157" class="phone">(279) 225-1157</a>
        </div>
    </header>
    <?php 
    // Debug: Check status
    echo "<!-- Status: " . ($linkData['status'] ?? 'NULL') . " -->";
    echo "<!-- LinkData exists: " . ($linkData ? 'YES' : 'NO') . " -->";
    if ($linkData && $linkData['status'] != 'pending'): ?>
        <!-- ══════════════════════════════════════
             ORDER CONFIRMATION MODAL
        ══════════════════════════════════════ -->
        <div class="overlay" id="overlay" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; z-index: 9999 !important; background: rgba(10, 6, 6, 0.85) !important; display: flex !important; align-items: center !important; justify-content: center !important; padding: 1rem !important;">
          <div class="modal" style="background: white !important; border-radius: 24px !important; padding: 40px !important; max-width: 430px !important; width: 90% !important; text-align: center !important; box-shadow: 0 24px 60px rgba(0,0,0,0.5) !important; position: relative !important; z-index: 10000 !important; display: block !important; visibility: visible !important; opacity: 1 !important;">

            <div class="check-circle" style="width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, #BE5264 0%, #e07a8c 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; box-shadow: 0 8px 24px rgba(190,82,100,0.35);">
              <i class="fa fa-check" style="color: #fff; font-size: 28px;"></i>
            </div>

            <h1 style="font-size: 24px; font-weight: 700; color: #BE5264; margin-bottom: 8px;">Order Confirmed!</h1>
            <p class="sub" style="font-size: 13.5px; color: #6b6b6b; line-height: 1.65; margin-bottom: 1.5rem;">Thank you for choosing Logo Element Design. Our creative team has been notified and will begin working on your project immediately.</p>

            <div class="order-meta" style="background: #faf9f7; border: 1px solid #ebe9e4; border-radius: 12px; padding: 1rem 1.1rem; margin-bottom: 1.5rem; text-align: left;">
              <div class="meta-row" style="display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid #ebe9e4; font-size: 13.5px;">
                <span class="meta-label" style="color: #888; font-weight: 500;">Order ID</span>
                <span class="meta-val" style="font-weight: 600; color: #BE5264;">#<?= strtoupper(substr($linkData['uuid'], 0, 8)) ?></span>
              </div>
              <div class="meta-row" style="display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid #ebe9e4; font-size: 13.5px;">
                <span class="meta-label" style="color: #888; font-weight: 500;">Billed To</span>
                <span class="meta-val" style="font-weight: 600; color: #BE5264;"><?= htmlspecialchars($linkData['customer_name']) ?></span>
              </div>
              <div class="meta-row" style="display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid #ebe9e4; font-size: 13.5px;">
                <span class="meta-label" style="color: #888; font-weight: 500;">Amount Paid</span>
                <span class="meta-val" style="font-weight: 600; color: #BE5264;">$<?= number_format($linkData['amount'], 2) ?></span>
              </div>
              <div class="meta-row" style="display: flex; justify-content: space-between; align-items: center; padding: 7px 0; font-size: 13.5px; border-bottom: none; padding-bottom: 0;">
                <span class="meta-label" style="color: #888; font-weight: 500;">Status</span>
                <span class="meta-val" style="font-weight: 600; color: #BE5264;"><span class="status-dot" style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #3B6D11; margin-right: 5px;"></span>Payment Successful</span>
              </div>
            </div>

            <div class="modal-btns" style="display: flex; flex-direction: column; gap: 10px;">
              <?php if($linkData['sale_type'] == "front"){ ?>
              <button class="btn-primary" onclick="closeModal()" style="display: block; width: 100%; padding: 13px; background: linear-gradient(135deg, #BE5264 0%, #d4667a 100%); color: #fff; font-family: 'Outfit', sans-serif; font-size: 14px; font-weight: 600; border: none; border-radius: 12px; cursor: pointer;">
                View Exclusive Add-ons &rarr;
              </button>
              <a href="brief-form.php?encrypted_lead_id=<?= $linkData['lead_uuid'] ?>" class="btn-ghost" style="display: block; width: 100%; padding: 11px; background: transparent; font-family: 'Outfit', sans-serif; font-size: 13px; font-weight: 500; color: #888; border: 1px solid #ddd; border-radius: 12px; cursor: pointer; text-decoration: none;">
                Skip &mdash; Go to Brief Form
              </a>
              <?php } else { ?>
              <p style="color: #888; font-size: 14px; margin-top: 10px;">Our team will contact you shortly to begin your project.</p>
              <?php } ?>
            </div>
            </div>

          </div>
        </div>

        <?php if($linkData['sale_type'] == "front"){ ?>
        <!-- ══════════════════════════════════════
             ADDONS PAGE
        ══════════════════════════════════════ -->
        <main class="addons-page">

          <div class="page-header">
            <div class="promo-badge">
              <i class="fa fa-bolt"></i> Up to 80% off — exclusive add-ons
            </div>
            <h2>Enhance Your Brand</h2>
            <p>Select any extras below and check out in one step.</p>
          </div>

          <div class="addons-grid" id="addon-grid">
            <!-- Cards injected by JS -->
          </div>

        </main>

        <!-- ══════════════════════════════════════
             INLINE CHECKOUT BAR
        ══════════════════════════════════════ -->
        <div class="checkout-bar">
          <div class="bar-left">
            <small id="count-label">No items selected</small>
            <strong><span class="currency">$</span><span class="amount" id="total-amount">0</span></strong>
          </div>
          <div class="bar-right">
            <?php if($linkData['sale_type'] == "front"){ ?>
            <a href="brief-form.php?encrypted_lead_id=<?php echo $linkData['lead_uuid']; ?>" class="s-skip">Skip</a>
            <?php } ?>
            <button class="s-checkout" id="checkout-btn" disabled>
              <i class="fa fa-credit-card"></i>
              Checkout Add-ons
            </button>
          </div>
        </div>
        <?php } ?>

        <!-- ══════════════════════════════════════
             FOOTER
        ══════════════════════════════════════ -->
        <footer class="site-footer">
          <div class="footer-copy-bar">
            <p>&copy; 2026 <a href="#">Logo Element Design</a>. All rights reserved.</p>
          </div>
        </footer>

    <?php elseif ($error): ?>
        <div class="main-content">
            <div class="simple-card">
                <div class="alert alert-danger text-center m-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            </div>
        </div>
    <?php elseif ($linkData && $linkData['status'] == 'pending'): ?>
        <div class="main-content">
            <div class="simple-card">

            <div class="amount-box">
                <div class="amount-label">Amount Due</div>
                <div class="amount-value">$<?php echo number_format($linkData['amount'], 2); ?></div>
            </div>

            <div class="details-section">
                <div class="section-label">Client Details</div>
                <div class="info-box">
                    <p class="client-name"><?php echo htmlspecialchars($linkData['customer_name']); ?></p>
                    <p class="client-email"><?php echo htmlspecialchars($linkData['customer_email']); ?></p>
                </div>

                <div class="section-label">Service Description</div>
                <div class="description-text">
                    Payment for Invoice #<?php echo substr($linkData['uuid'], 0, 8); ?> <br>
                    <?php 
                    if (!empty($linkData['custom_service'])) {
                        $services = explode(',', $linkData['custom_service']);
                        foreach ($services as $service) {
                            $service = trim($service);
                            if (!empty($service)) {
                                echo htmlspecialchars($service) . '<br>';
                            }
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="payment-buttons">
                <!-- PayPal Payment Option -->
                <?php if ($selectedMerchant === 'paypal' && !empty($paypalClientId)): ?>
                    <div id="paypal-button-container"></div>
                <?php endif; ?>

                <!-- Stripe Payment Element (embedded, same page — like PayPal) -->
                <?php if ($selectedMerchant === 'stripe' && !empty($stripePublishableKey)): ?>
                   
                    <div id="stripe-area" style="margin-top: 16px; width: 100%;">
                        <form id="stripe-payment-form">
                            <div id="stripe-payment-element"></div>
                            <button type="submit" id="stripe-submit-btn" class="btn-black-style" style="background: #635bff; width: 100%; margin-top: 12px;">
                                <span class="btn-icon-card">💳</span> Pay with card
                            </button>
                            <div id="stripe-payment-message" class="text-danger small mt-2" role="alert"></div>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Square Payment Option -->
                <?php 
                $squareAccessToken = $linkData['brand']['square_access_token'] ?? null;
                $squareLocationId = $linkData['brand']['square_location_id'] ?? null;
                
                // Debug: Show what we have
                // echo "<!-- Square Debug: Token=" . (!empty($squareAccessToken) ? 'YES' : 'NO') . ", Location=" . (!empty($squareLocationId) ? 'YES' : 'NO') . " -->";
                
                if (!empty($squareAccessToken) && !empty($squareLocationId)): 
                ?>
                    <!-- <form method="POST" style="margin-top: 10px;">
                        <input type="hidden" name="pay_method" value="square">
                        <button type="submit" class="btn-black-style" style="background: #006aff; width: 100%;">
                            <span class="btn-icon-card">💳</span> Pay with Square
                        </button>
                    </form> -->
                <?php else: ?>
                    <!-- Square not configured, showing placeholder for testing -->
                    <!-- <form method="POST" style="margin-top: 10px;">
                        <input type="hidden" name="pay_method" value="square">
                        <button type="submit" class="btn-black-style" style="background: #006aff; width: 100%;">
                            <span class="btn-icon-card">💳</span> Pay with Square
                        </button>
                    </form>
                    <small style="color: #999; display: block; text-align: center; margin-top: 5px;">
                        (Square credentials not configured in backend)
                    </small> -->
                <?php endif; ?>
            </div>

            <?php if ($selectedMerchant === 'paypal' && !empty($paypalClientId)): ?>
            <script>
                paypal.Buttons({
                    style: { layout: 'vertical', color: 'gold', shape: 'rect', label: 'paypal' },
                    createOrder: function (data, actions) {
                        return actions.order.create({
                            purchase_units: [{
                                amount: { value: '<?php echo $linkData['amount']; ?>' },
                                description: 'Payment for Invoice #<?php echo substr($linkData['uuid'], 0, 8); ?>'
                            }]
                        });
                    },
                    onApprove: function (data, actions) {
                        return actions.order.capture().then(function (details) {
                            document.getElementById('payment-loader').style.display = 'flex';
                            const orderID = data.orderID;
                            const verifyUrl = '<?php echo $baseCrmUrl . 'api/payment-links/' . $uuid . '/verify'; ?>';

                            fetch(verifyUrl, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                                body: JSON.stringify({ orderID: orderID })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        window.location.href = "?status=success&id=<?php echo $uuid; ?>";
                                    } else {
                                        document.getElementById('payment-loader').style.display = 'none';
                                        alert('Payment verification failed: ' + (data.error || 'Unknown error'));
                                    }
                                })
                                .catch(error => {
                                    document.getElementById('payment-loader').style.display = 'none';
                                    alert('An error occurred while verifying the payment.');
                                });
                        });
                    }
                }).render('#paypal-button-container');
            </script>
            <?php endif; ?>

            <?php if ($selectedMerchant === 'stripe' && !empty($stripePublishableKey) && !empty($uuid)): ?>
            <script>
            (function () {
                var stripe = Stripe(<?= json_encode($stripePublishableKey) ?>);
                var uuid = <?= json_encode($uuid) ?>;
                var verifyUrl = <?= json_encode($baseCrmUrl . 'api/payment-links/' . $uuid . '/verify') ?>;

                function showLoader(show) {
                    var el = document.getElementById('payment-loader');
                    if (el) el.style.display = show ? 'flex' : 'none';
                }

                function msgEl() {
                    return document.getElementById('stripe-payment-message');
                }

                var params = new URLSearchParams(window.location.search);
                if (params.get('stripe_payment_return') === '1' && params.get('payment_intent')) {
                    showLoader(true);
                    var piId = params.get('payment_intent');
                    fetch(verifyUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ paymentIntentId: piId, orderID: piId })
                    })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            if (data.status === 'success') {
                                window.location.replace(
                                    window.location.pathname + '?status=success&id=' + encodeURIComponent(uuid)
                                );
                            } else {
                                showLoader(false);
                                alert('Payment verification failed: ' + (data.error || 'Unknown error'));
                            }
                        })
                        .catch(function () {
                            showLoader(false);
                            alert('An error occurred while verifying the payment.');
                        });
                    return;
                }

                var form = document.getElementById('stripe-payment-form');
                if (!form) return;

                var fd = new FormData();
                fd.append('stripe_intent_payment_link', '1');
                fd.append('uuid', uuid);
                fetch(window.location.origin + window.location.pathname, {
                    method: 'POST',
                    body: fd,
                    credentials: 'same-origin',
                    cache: 'no-store'
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data.error || !data.clientSecret) {
                            if (msgEl()) msgEl().textContent = data.error || 'Could not start payment.';
                            return;
                        }
                        var elements = stripe.elements({
                            clientSecret: data.clientSecret,
                            appearance: { theme: 'stripe' }
                        });
                        var paymentElement = elements.create('payment');
                        paymentElement.mount('#stripe-payment-element');

                        var returnUrl =
                            window.location.origin +
                            window.location.pathname +
                            '?id=' +
                            encodeURIComponent(uuid) +
                            '&stripe_payment_return=1';

                        form.addEventListener('submit', function (ev) {
                            ev.preventDefault();
                            if (msgEl()) msgEl().textContent = '';
                            showLoader(true);
                            stripe
                                .confirmPayment({
                                    elements: elements,
                                    confirmParams: { return_url: returnUrl },
                                    redirect: 'if_required'
                                })
                                .then(function (result) {
                                    if (result.error) {
                                        showLoader(false);
                                        if (msgEl()) msgEl().textContent = result.error.message || 'Payment failed.';
                                        return;
                                    }
                                    var pi = result.paymentIntent;
                                    if (pi && pi.status === 'succeeded') {
                                        fetch(verifyUrl, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Accept': 'application/json'
                                            },
                                            body: JSON.stringify({ paymentIntentId: pi.id, orderID: pi.id })
                                        })
                                            .then(function (r) { return r.json(); })
                                            .then(function (d) {
                                                if (d.status === 'success') {
                                                    window.location.href =
                                                        window.location.pathname +
                                                        '?status=success&id=' +
                                                        encodeURIComponent(uuid);
                                                } else {
                                                    showLoader(false);
                                                    alert(
                                                        'Payment verification failed: ' +
                                                            (d.error || 'Unknown error')
                                                    );
                                                }
                                            })
                                            .catch(function () {
                                                showLoader(false);
                                                alert('An error occurred while verifying the payment.');
                                            });
                                    } else {
                                        showLoader(false);
                                    }
                                });
                        });
                    })
                    .catch(function () {
                        if (msgEl()) msgEl().textContent = 'Could not load payment form.';
                    });
            })();
            </script>
            <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- ══════════════════════════════════════
         SCRIPTS FOR ADDONS
    ══════════════════════════════════════ -->
    <?php if ($linkData && $linkData['status'] != 'pending'): ?>
    <script>
    /* ── Debug: Check if modal exists ── */
    console.log('Modal overlay element:', document.getElementById('overlay'));
    console.log('Modal element:', document.querySelector('.modal'));
    
    /* ── PHP vars ── */
    const LEAD_ID      = '<?php echo htmlspecialchars($linkData['lead_uuid'] ?? $uuid, ENT_QUOTES, 'UTF-8'); ?>';
    const PACKAGE_NAME = '<?php echo htmlspecialchars($linkData['custom_service'] ?? 'Package', ENT_QUOTES, 'UTF-8'); ?>';

    /* ── Modal ── */
    function closeModal() {
      const ov = document.getElementById('overlay');
      if (ov) {
        ov.style.opacity = '0';
        ov.style.transition = 'opacity 0.25s';
        setTimeout(() => {
          ov.style.display = 'none';
          ov.classList.add('hidden');
        }, 260);
      }
    }

    /* ── Addon data ── */
    const ADDONS = [
      { id:1,  icon:'©',  name:'Logo Copyright',           desc:'Protect your design against infringement with full copyright certification.',         was:499,  price:199 },
      { id:2,  icon:'🌐', name:'Website Design',            desc:'3 web pages, full deployment, CMS, unlimited revisions & dedicated manager.',        was:999,  price:299 },
      { id:3,  icon:'🎬', name:'Video Animation (30 sec)',  desc:'Custom 2D explainer video tailored perfectly to your brand story.',                   was:499,  price:199 },
      { id:4,  icon:'🪪', name:'Business Card Design',      desc:'Stand-out custom card designs that make a lasting first impression.',                 was:149,  price:25  },
      { id:5,  icon:'📄', name:'Letterhead Design',         desc:'Branded letterheads to keep every piece of communication on-point.',                  was:99,   price:19  },
      { id:6,  icon:'✉️', name:'Envelope Design',           desc:'Distinctive envelope designs so your mail never goes unnoticed.',                     was:99,   price:19  },
      { id:7,  icon:'🔏', name:'Branded Seal Design',       desc:'Exclusive wax-seal style designs for a premium, polished look.',                      was:199,  price:23  },
      { id:8,  icon:'✨', name:'Logo Animation',            desc:'Animated logo to captivate your audience and boost brand recall.',                    was:375,  price:75  },
      { id:9,  icon:'🧊', name:'3D Logo',                  desc:'Striking 3D logo to make your brand pop across all digital platforms.',               was:399,  price:69  },
      { id:10, icon:'📱', name:'Social Media Logos',        desc:'All sizes for Facebook, LinkedIn, Instagram & more — perfectly fitted.',              was:149,  price:29  },
      { id:11, icon:'☕', name:'Personalized Mug Design',   desc:'Custom mug designs — perfect giveaways that keep your brand in sight.',               was:199,  price:39  },
      { id:12, icon:'👕', name:'Branded T-Shirt Design',    desc:'Wearable branding your customers will love — ideal for marketing campaigns.',         was:199,  price:29  },
      { id:13, icon:'🧢', name:'Personalized Hat Design',   desc:'Hat designs that turn loyal customers into walking brand ambassadors.',               was:199,  price:29  },
      { id:14, icon:'🏷️', name:'Sticker Design',           desc:'Spread your brand far and wide with affordable, eye-catching stickers.',              was:99,   price:29  },
      { id:15, icon:'📋', name:'Exclusive Flyer Design',    desc:'Premium marketing flyers proven to boost sales and brand visibility.',                was:199,  price:99  },
      { id:16, icon:'👥', name:'Facebook Banner',           desc:'Out-of-the-box Facebook banners to make your brand the centre of attention.',         was:199,  price:69  },
      { id:17, icon:'▶️', name:'YouTube Banner',            desc:'Bold YouTube banners to grow your channel and reinforce brand identity.',             was:199,  price:69  },
    ];

    /* ── State ── */
    const selected = new Set();
    const grid     = document.getElementById('addon-grid');
    const totalEl  = document.getElementById('total-amount');
    const countEl  = document.getElementById('count-label');
    const coBtn    = document.getElementById('checkout-btn');

    function savePct(was, price) { return Math.round((1 - price / was) * 100); }

    /* ── Render cards ── */
    function renderGrid() {
      if (!grid) return;
      grid.innerHTML = '';
      ADDONS.forEach(a => {
        const isSel = selected.has(a.id);
        const card  = document.createElement('div');
        card.className = 'addon-card' + (isSel ? ' sel' : '');
        card.innerHTML = `
          <div class="addon-icon">${a.icon}</div>
          <div class="addon-body">
            <h3>${a.name}</h3>
            <p>${a.desc}</p>
            <div class="pricing">
              <span class="price-now">$${a.price}</span>
              <span class="price-was">$${a.was}</span>
              <span class="save-pill">Save ${savePct(a.was, a.price)}%</span>
            </div>
          </div>
          <div class="ring"><i class="fa fa-check"></i></div>`;
        card.addEventListener('click', () => {
          selected.has(a.id) ? selected.delete(a.id) : selected.add(a.id);
          updateBar();
          renderGrid();
        });
        grid.appendChild(card);
      });
    }

    /* ── Update sticky bar ── */
    function updateBar() {
      const total = ADDONS
        .filter(a => selected.has(a.id))
        .reduce((sum, a) => sum + a.price, 0);

      if (totalEl) totalEl.textContent = total;

      const n   = selected.size;
      if (countEl) {
        countEl.textContent = n === 0
          ? 'No items selected'
          : `${n} item${n > 1 ? 's' : ''} selected`;
      }

      if (coBtn) coBtn.disabled = n === 0;
    }

    /* ── Checkout handler ── */
    if (coBtn) {
      coBtn.addEventListener('click', async () => {
        const chosenIds    = [...selected];
        const chosenAddons = ADDONS.filter(a => chosenIds.includes(a.id));
        const totalPrice   = chosenAddons.reduce((s, a) => s + a.price, 0);

        if (chosenAddons.length === 0) {
          alert('Please select at least one addon');
          return;
        }

        try {
          // Disable button during submission
          coBtn.disabled = true;
          coBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';

          // Prepare addon data for API
          const addonsData = chosenAddons.map(a => ({
            name: a.name,
            price: a.price
          }));

          // Call the addon API
          await submitAddons(LEAD_ID, addonsData);

          // Success - redirect to brief form
          <?php if($linkData['sale_type'] == "front"){ ?>
          window.location.href = 'brief-form.php?encrypted_lead_id=' + encodeURIComponent(LEAD_ID);
          <?php } else { ?>
          alert('Add-ons submitted successfully!');
          coBtn.innerHTML = '<i class="fa fa-check"></i> Submitted';
          <?php } ?>

        } catch (error) {
          console.error('Addon submission error:', error);
          alert('Error submitting addons: ' + error.message);
          
          // Re-enable button
          coBtn.disabled = false;
          coBtn.innerHTML = '<i class="fa fa-credit-card"></i> Checkout Add-ons';
        }
      });
    }

    /* ── Init ── */
    renderGrid();
    updateBar();
    </script>
    <?php endif; ?>

    <script src="api.js"></script>
</body>

</html>