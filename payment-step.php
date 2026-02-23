<?php
/**
 * Payment Step Page
 * Modularized Structure for better maintenance.
 */

require_once 'packages.php';
require_once 'payment-helpers.php';

// 1. Initialize Inputs
$leadId = $_GET['id'] ?? '';
$urlPkg = $_GET['pkg'] ?? null;
$urlAmt = $_GET['amt'] ?? 19;
$error = null;
$linkData = null;

// 2. Data Fetching
if (!empty($leadId)) {
    $linkData = getPaymentDetails($leadId);
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
    
         <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KW7SCQJP');</script>
<!-- End Google Tag Manager -->

         <!-- Start of LiveChat (www.livechat.com) code -->
<script>
    window._lc = window._lc || {};
    window.__lc.license = 19454392;
    window.__lc.integration_name = "manual_onboarding";
    window.__lc.product_name = "livechat";
    ;(function(n,t,c){function i(n){return e.h?e._h.apply(null,n):e._q.push(n)}var e={_q:[],_h:null,_v:"2.0",on:function(){i(["on",c.call(arguments)])},once:function(){i(["once",c.call(arguments)])},off:function(){i(["off",c.call(arguments)])},get:function(){if(!e._h)throw new Error("[LiveChatWidget] You can't use getters before load.");return i(["get",c.call(arguments)])},call:function(){i(["call",c.call(arguments)])},init:function(){var n=t.createElement("script");n.async=!0,n.type="text/javascript",n.src="https://cdn.livechatinc.com/tracking.js",t.head.appendChild(n)}};!n._lc.asyncInit&&e.init(),n.LiveChatWidget=n.LiveChatWidget||e}(window,document,[].slice))
</script>
<!-- End of LiveChat code -->

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

</head>

<body>
    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KW7SCQJP"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

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

                    <!--<div class="payment-tabs mb-4">-->
                    <!--    <div class="btn-group w-100" role="group">-->
                    <!--        <input type="radio" class="btn-check" name="payment_choice" id="pay_paypal" checked>-->
                    <!--        <label class="btn btn-outline-primary" for="pay_paypal">PayPal</label>-->

                    <!--        <input type="radio" class="btn-check" name="payment_choice" id="pay_clover">-->
                    <!--        <label class="btn btn-outline-primary" for="pay_clover">Credit / Debit Card</label>-->
                    <!--    </div>-->
                    <!--</div>-->
 
                    <div id="paypal-section">
                        <div id="paypal-button-container"></div>
                    </div>

                    <div id="clover-section" style="display: none;">
                        <form method="POST">
                            <input type="hidden" name="pay_method" value="clover">
                            <button type="submit" class="btn-pay" style="background: #28a745;">
                                <span style="margin-right: 8px;">💳</span> Pay with Credit Card
                            </button>
                        </form>
                        <div class="text-center mt-3" style="margin-top:5px">
                            <img src="https://cdn.jsdelivr.net/gh/aaronfagan/svg-credit-card-payment-icons@master/flat/visa.svg"
                                height="24" class="mx-1">
                            <img src="https://cdn.jsdelivr.net/gh/aaronfagan/svg-credit-card-payment-icons@master/flat/mastercard.svg"
                                height="24" class="mx-1">
                            <img src="https://cdn.jsdelivr.net/gh/aaronfagan/svg-credit-card-payment-icons@master/flat/amex.svg"
                                height="24" class="mx-1">
                            <img src="https://cdn.jsdelivr.net/gh/aaronfagan/svg-credit-card-payment-icons@master/flat/discover.svg"
                                height="24" class="mx-1">
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const pRadio = document.getElementById('pay_paypal'), cRadio = document.getElementById('pay_clover');
                            const pSec = document.getElementById('paypal-section'), cSec = document.getElementById('clover-section');
                            pRadio.addEventListener('change', () => { pSec.style.display = 'block'; cSec.style.display = 'none'; });
                            cRadio.addEventListener('change', () => { pSec.style.display = 'none'; cSec.style.display = 'block'; });
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
     <!--// test mode -->
    <!-- <script src="https://www.paypal.com/sdk/js?client-id=AWRCRUFnNtXfdNCut8-YeeXQc7CDe-2FQmVt4jwPg3Cbl1TJ6pECsjdg8ITRSL-PPbIcVEOcmnptBAZe&currency=USD"></script>  -->
    <!--// live mode-->
    <!-- <script src="https://www.paypal.com/sdk/js?client-id=AWf9KL0KBi4GhT2rzRvazWLiDVxV8e1MwwSG6CrrM9Bh8gvdyfpG2vgcBxCrJQgXY5l3hiH3m774Q_e_&currency=USD"></script> -->
    <script src="https://www.paypal.com/sdk/js?client-id=<?= $paypalClientId ?>&currency=USD"></script>
    <!-- <script src="https://www.paypal.com/sdk/js?client-id=<?= $paypalClientId ?>&currency=USD"></script> -->
    <script>
        paypal.Buttons({
            style: { layout: 'vertical', color: 'gold', shape: 'rect', label: 'paypal' },
            createOrder: (data, actions) => actions.order.create({
                purchase_units: [{
                    amount: { value: '<?= $urlAmt ?>' },
                    description: 'Signup Payment for <?= htmlspecialchars($packageData['name']) ?>'
                }]
            }),
            onApprove: (data, actions) => actions.order.capture().then(async (details) => {
                document.getElementById('payment-loader').style.display = 'flex';
                try { await submitStep5('<?= $leadId ?>', '<?= $urlPkg ?>'); } catch (e) { }
                window.location.href = "thank-you.php?status=success&id=<?= $leadId ?>&pkg=<?= urlencode($urlPkg) ?>";
            })
        }).render('#paypal-button-container');
    </script>
</body>

</html>