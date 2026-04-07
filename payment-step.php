<?php


require_once 'packages.php';
require_once 'payment-helpers.php';

// Stripe PaymentIntent API — same URL as this page (avoids live redirects that turn POST→GET on extensionless URLs)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Allow: POST, OPTIONS');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Max-Age: 86400');
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stripe_intent_lead'])) {
    header('Content-Type: application/json');
    $leadIdPost = $_POST['lead_id'] ?? null;
    $amountPost = $_POST['amount'] ?? null;
    $pkgPost = $_POST['pkg'] ?? '';
    if (!$leadIdPost || $amountPost === null || $amountPost === '') {
        echo json_encode(['error' => 'Missing parameters']);
        exit;
    }
    $config = getStripeConfigByLeadUuid($leadIdPost);
    if (empty($config['success'])) {
        echo json_encode(['error' => 'Unable to load lead payment configuration']);
        exit;
    }
    $brand = $config['brand'] ?? [];
    if (!empty($config['secret_key'])) {
        $brand['stripe_secret_key'] = $config['secret_key'];
    }
    echo json_encode(createStripePaymentIntentForLead($brand, $leadIdPost, (float) $amountPost, $pkgPost));
    exit;
}

// 1. Initialize Inputs
$leadId = $_GET['id'] ?? '';
$urlPkg = $_GET['pkg'] ?? null;
$urlAmt = $_GET['amt'] ?? 35;
$error = null;
$linkData = null;
$paypalClientId = null;
$stripePublishableKey = null;
$selectedMerchant = '';

// 2. Data Fetching — PayPal: /leads/{uuid}/paypal-config | Stripe: /leads/{uuid}/stripe-config
if (!empty($leadId)) {
    $paypalConfig = getPayPalConfigByLeadUuid($leadId);
    $stripeConfig = getStripeConfigByLeadUuid($leadId);

    if (!empty($paypalConfig['success'])) {
        $paypalClientId = $paypalConfig['client_id'] ?? null;
        $selectedMerchant = strtolower((string) ($paypalConfig['lead']['merchant'] ?? $paypalConfig['brand']['merchant'] ?? ''));
    }

    if (!empty($stripeConfig['success'])) {
        $stripePublishableKey = $stripeConfig['publishable_key'] ?? null;
        if ($selectedMerchant === '') {
            $selectedMerchant = strtolower((string) ($stripeConfig['lead']['merchant'] ?? $stripeConfig['brand']['merchant'] ?? ''));
        }
    }

    if ($stripePublishableKey === null && !empty($paypalConfig['success'])) {
        $brand = $paypalConfig['brand'] ?? [];
        $stripePublishableKey = $brand['stripe_publishable_key']
            ?? $brand['stripe_publishable']
            ?? $brand['stripe_pk']
            ?? null;
    }

    if ($selectedMerchant === '') {
        if (!empty($paypalClientId)) {
            $selectedMerchant = 'paypal';
        } elseif (!empty($stripePublishableKey)) {
            $selectedMerchant = 'stripe';
        }
    }
}

// 3. Handle Clover Payment Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_method']) && $_POST['pay_method'] === 'clover' && $linkData) {
    
    
    $checkout = getCloverCheckoutUrl($linkData, $urlPkg, $urlAmt , $leadId , "pkg");


    // print_r($checkout);
    // exit;

    if (isset($checkout['url'])) {
        header("Location: " . $checkout['url']);
        exit;
    } else {
        $error = $checkout['error'];
    }
}

// 3b. Handle Square Payment Submission
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_method']) && $_POST['pay_method'] === 'square' && $linkData) {
    
//     $checkout = getSquareCheckoutUrl($linkData, $urlPkg, $urlAmt , $leadId , "pkg");

//     if (isset($checkout['url'])) {
//         header("Location: " . $checkout['url']);
//         exit;
//     } else {
//         $error = $checkout['error'];
//     }
// }

// 4. Handle Post-Payment Redirection/Verification
if (isset($_GET['status']) && $_GET['status'] == 'success' && $leadId && $linkData && $linkData['status'] == 'pending') {
    verifyPaymentWithCrm($leadId);
}

// 5. Build View Data
$packageData = getPackageFeatures($urlAmt);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Logo Element Design</title>
    <link rel="stylesheet" href="style.css">
    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KW7SCQJP');</script>
<!-- End Google Tag Manager -->
    
    <style>
        .split-card {
            display: flex;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-top: 30px;
        }

        .payment-side {
            flex: 1.5;
            padding: 40px;
        }

        .package-side {
            flex: 1;
            background: #fdfdfd;
            padding: 40px;
            border-left: 1px solid #f0f0f0;
        }

        .pkg-includes-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #333;
        }

        .pkg-feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pkg-feature-list li {
            padding-left: 25px;
            position: relative;
            margin-bottom: 12px;
            font-size: 14px;
            color: #555;
        }

        .pkg-feature-list li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: #000;
            font-weight: bold;
        }

        .btn-pay {
            background: #ff5e3a;
            color: #fff;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 6px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            text-decoration: none;
            display: inline-block;
        }

        @media (max-width: 768px) {
            .split-card {
                flex-direction: column;
            }

            .package-side {
                border-left: none;
                border-top: 1px solid #f0f0f0;
            }
        }

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

    <?php if (!empty($leadId) && $selectedMerchant === 'paypal' && !empty($paypalClientId)): ?>
        <script src="https://www.paypal.com/sdk/js?client-id=<?= htmlspecialchars($paypalClientId) ?>&currency=USD"></script>
    <?php endif; ?>
    <?php if (!empty($leadId) && $selectedMerchant === 'stripe' && !empty($stripePublishableKey)): ?>
        <script src="https://js.stripe.com/v3/"></script>
    <?php endif; ?>

</head>

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

    <div class="progress-container">
        <div class="progress-line"></div>
        <div class="progress-line-fill" style="width: 87.5%;"></div>
        <div class="progress-steps">
            <div class="step completed"></div>
            <div class="step completed"></div>
            <div class="step completed"></div>
            <div class="step"></div>
        </div>
    </div>

    <div class="container">
        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="split-card">
                <div class="payment-side text-center py-5">
                    <div class="success-icon" style="font-size: 60px; color: #28a745; margin-bottom: 20px;">✓</div>
                    <h2 class="thank-you-title" style="font-weight: 700; margin-bottom: 10px;">Payment Successful!</h2>
                    <p class="thank-you-message" style="color: #666; margin-bottom: 30px;">Thank you for your payment. Your
                        order is being processed.</p>
                    <a href="index.php" class="btn-pay" style="width: auto; padding: 12px 30px;">Back to Home</a>
                </div>
                <div class="package-side">
                    <h3 class="pkg-includes-title">Order Summary:</h3>
                    <h5 class="text-primary mb-3"><?= htmlspecialchars($packageData['name']) ?></h5>
                    <ul class="pkg-feature-list">
                        <li>Amount Paid: <strong>$<?= number_format($urlAmt, 2) ?></strong></li>
                        <li>Lead ID: <strong>#<?= htmlspecialchars($leadId) ?></strong></li>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <h1 class="page-title">Complete Your Payment</h1>

            <div class="split-card">
                <div class="payment-side">
                    <div class="amount-box mb-4 p-3 border rounded bg-light text-center">
                        <div class="amount-label text-muted small uppercase">Total Amount Due</div>
                        <div class="amount-value h3 font-weight-bold" style="color: var(--primary-color);">
                            $<?= number_format($urlAmt, 2) ?></div>
                    </div>

                    <!--<div class="mb-4">-->
                    <!--    <p class="text-muted small">Choose your preferred payment method to finalize your order. All-->
                    <!--        transactions are secure and encrypted.</p>-->
                    <!--</div>-->

                    <?php if ($error): ?>
                        <div class="alert alert-danger mb-3 small"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <!-- <div class="payment-tabs mb-4">
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="payment_choice" id="pay_paypal" checked>
                            <label class="btn btn-outline-primary" for="pay_paypal">PayPal</label>

                            <input type="radio" class="btn-check" name="payment_choice" id="pay_square">
                            <label class="btn btn-outline-primary" for="pay_square">Square</label>
                        </div>
                    </div>
  -->
                    <?php if ($selectedMerchant === 'paypal' && !empty($paypalClientId)): ?>
                    <div id="paypal-section">
                        <div id="paypal-button-container"></div>
                    </div>
                    <?php endif; ?>

                    <?php if ($selectedMerchant === 'stripe' && !empty($stripePublishableKey)): ?>
                    <div id="stripe-section" style="margin-top: 8px;">
                        <form id="stripe-payment-form">
                            <div id="stripe-payment-element"></div>
                            <button type="submit" class="btn-pay" style="background: #635bff; margin-top: 12px;">
                                <span style="margin-right: 8px;">💳</span> Pay with card
                            </button>
                            <div id="stripe-payment-message" class="text-danger small mt-2" role="alert"></div>
                        </form>
                    </div>
                    <?php endif; ?>

                    <div id="square-section" style="display: none;">
                        <form method="POST">
                            <input type="hidden" name="pay_method" value="square">
                            <button type="submit" class="btn-pay" style="background: #006aff;">
                                <span style="margin-right: 8px;">💳</span> Pay with Square
                            </button>
                        </form>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const pRadio = document.getElementById('pay_paypal'), 
                                  sqRadio = document.getElementById('pay_square');
                            const pSec = document.getElementById('paypal-section'), 
                                  sqSec = document.getElementById('square-section');
                            
                            if (pRadio) pRadio.addEventListener('change', () => { 
                                pSec.style.display = 'block'; 
                                sqSec.style.display = 'none';
                            });
                            
                            if (sqRadio) sqRadio.addEventListener('change', () => { 
                                pSec.style.display = 'none'; 
                                sqSec.style.display = 'block';
                            });
                        });
                    </script>
                </div>

                <div class="package-side">
                    <h3 class="pkg-includes-title">Your Package includes:</h3>
                    <ul class="pkg-feature-list">
                        <?php foreach ($packageData['features'] as $feature): ?>
                            <li><?= htmlspecialchars($feature) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="api.js"></script>

    <?php if ($selectedMerchant === 'paypal' && !empty($paypalClientId)): ?>
    <script>
        paypal.Buttons({
            style: { layout: 'vertical', color: 'gold', shape: 'rect', label: 'paypal' },
            createOrder: (data, actions) => actions.order.create({
                purchase_units: [{
                    amount: { value: '<?= htmlspecialchars((string) $urlAmt) ?>' },
                    description: 'Signup Payment for <?= htmlspecialchars($packageData['name']) ?>'
                }]
            }),
            onApprove: (data, actions) => actions.order.capture().then(async (details) => {
                document.getElementById('payment-loader').style.display = 'flex';
                try { await submitStep5(<?= json_encode($leadId) ?>, <?= json_encode($urlPkg) ?>); } catch (e) { }
                window.location.href = "thank-you.php?status=success&id=<?= urlencode($leadId) ?>&pkg=<?= urlencode((string) $urlPkg) ?>";
            })
        }).render('#paypal-button-container');
    </script>
    <?php endif; ?>

    <?php if ($selectedMerchant === 'stripe' && !empty($stripePublishableKey) && !empty($leadId)): ?>
    <script>
    (function () {
        var stripe = Stripe(<?= json_encode($stripePublishableKey) ?>);
        var leadId = <?= json_encode($leadId) ?>;
        var pkg = <?= json_encode($urlPkg) ?>;
        var amt = <?= json_encode((string) $urlAmt) ?>;

        function showLoader(show) {
            var el = document.getElementById('payment-loader');
            if (el) el.style.display = show ? 'flex' : 'none';
        }
        function msgEl() {
            return document.getElementById('stripe-payment-message');
        }
        function goThankYou() {
            window.location.href =
                'thank-you.php?status=success&id=' +
                encodeURIComponent(leadId) +
                '&pkg=' +
                encodeURIComponent(pkg || '');
        }

        var params = new URLSearchParams(window.location.search);
        if (params.get('stripe_payment_return') === '1' && params.get('payment_intent')) {
            showLoader(true);
            (async function () {
                try {
                    await submitStep5(leadId, pkg);
                } catch (e) { }
                goThankYou();
            })();
            return;
        }

        var form = document.getElementById('stripe-payment-form');
        if (!form) return;

        var fd = new FormData();
        fd.append('stripe_intent_lead', '1');
        fd.append('lead_id', leadId);
        fd.append('amount', amt);
        fd.append('pkg', pkg || '');

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
                    encodeURIComponent(leadId) +
                    '&pkg=' +
                    encodeURIComponent(pkg || '') +
                    '&amt=' +
                    encodeURIComponent(amt) +
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
                                (async function () {
                                    try {
                                        await submitStep5(leadId, pkg);
                                    } catch (e) { }
                                    goThankYou();
                                })();
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
</body>

</html>