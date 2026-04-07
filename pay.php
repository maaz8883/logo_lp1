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

// If coming back from Clover Success, update CRM
if (isset($_GET['status']) && $_GET['status'] == 'success' && $uuid && $linkData && $linkData['status'] == 'pending') {
   
//   echo 1;
 verifyPaymentWithCrm($uuid);
    // exit;
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
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap');

    :root {
           --primary-gradient: linear-gradient(135deg, #FFD700 0%, #B8860B 100%);
           --dark-bg: #0a0a0a;
       }

     .thank-you-container {
           max-width: 600px;
           width: 90%;
           padding: 30px 30px;
           background: rgba(255, 255, 255, 0.03);
           backdrop-filter: blur(20px);
           border: 1px solid rgba(255, 215, 0, 0.1);
           border-radius: 30px;
           text-align: center;
           position: relative;
           box-shadow: 0 2px 10px 0px rgba(0, 0, 0, 0.5);
       }

       .success-icon {
           width: 100px;
           height: 100px;
           background: linear-gradient(45deg, #BE5264 20%, #BE5264 50%, #F8BB16 100%);
           border-radius: 50%;
           display: flex;
           align-items: center;
           justify-content: center;
           margin: 0 auto 30px;
           font-size: 50px;
           color: #000;
           box-shadow: 0 0 30px rgba(184, 134, 11, 0.4);
           animation: scaleIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
       }

       @keyframes scaleIn {
           from {
               transform: scale(0);
               opacity: 0;
           }

           to {
               transform: scale(1);
               opacity: 1;
           }
       }

       h1 {
           font-size: 40px;
           font-weight: 700;
           background: var(--primary-gradient);
           margin-bottom: 15px;
           background-image: linear-gradient(45deg, #BE5264 20%, #BE5264 50%, #F8BB16 100%);
           -webkit-background-clip: text;
           -webkit-text-fill-color: transparent;
       }

       p.lead-text {
           color: #000;
           font-size: 16px;
           margin-bottom: 30px;
           font-weight: 400;
       } 

       .order-info {
           background: rgba(255, 255, 255, 0.05);
           border-radius: 20px;
           padding: 25px;
           margin-bottom: 35px;
           text-align: left;
           border: 1px solid rgba(255, 255, 255, 0.05);
       }

       .info-row {
           display: flex;
           justify-content: space-between;
           margin-bottom: 12px;
           border-bottom: 1px solid rgba(255, 255, 255, 0.05);
           padding-bottom: 12px;
       }

       .info-row:last-child {
           margin-bottom: 0;
           border-bottom: none;
           padding-bottom: 0;
       }

       .info-label {
           color: #000;
           font-size: 16px;
           font-weight: 600;
       }

       .info-value {
           color: #b75267;
           font-weight: 600;
           font-size: 15px;
       }

       .btn-home {
           display: inline-block;
           padding: 15px 40px;
           background: linear-gradient(45deg, #BE5264 20%, #BE5264 50%, #F8BB16 100%);
           color: #ffffff;
           text-decoration: none;
           border-radius: 12px;
           font-weight: 700;
           text-transform: uppercase;
           letter-spacing: 1px;
           transition: all 0.3s ease;
           box-shadow: 0 10px 20px rgba(184, 134, 11, 0.2);
       }

       .btn-home:hover {
           transform: translateY(-3px);
           box-shadow: 0 15px 30px rgba(184, 134, 11, 0.4);
           color: #000;
       }

       .confetti-bg {
           position: absolute;
           top: 0;
           left: 0;
           width: 100%;
           height: 100%;
           pointer-events: none;
           z-index: -1;
       }
       
       .btn-home:hover {
            background: #000;
            transition: 0.5s;
            color: #fff;
        }

        .success-icon i {
             font-size: 34px;
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
    <div class="main-content">
     <div class="simple-card"> 
        <?php if ($linkData['status'] != 'pending'): ?>
           

            <div class="thank-you-container">
                <div class="success-icon">
                    <i class="fa fa-check"></i>
                </div>
                <h1>Order Confirmed!</h1>
                <p class="lead-text">Thank you for choosing Logo Element Design. Our creative team has been notified and will
                    begin working on your project immediately.</p>
    
                <div class="order-info">
                    <div class="info-row">
                        <span class="info-label">Order ID:</span>
                        <span class="info-value">#
                        <?php echo substr($linkData['uuid'], 0, 8); ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Billed To:</span>
                        <span class="info-value">
                            <strong><?php echo htmlspecialchars($linkData['customer_name']); ?></strong>
                        </span>
                    </div>
    
                    <div class="info-row">
                        <span class="info-label">Amount Paid:</span>
                        <span class="info-value">
                        <strong class="text-success">$<?php echo number_format($linkData['amount'], 2); ?></strong>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value">Payment Successful</span>
                    </div>
                </div>
                
                <?php if($linkData['sale_type'] == "front"){ ?>

            <a href="brief-form.php?encrypted_lead_id=<?php echo $linkData['lead_uuid']; ?>"  class="btn-home">Fill Brief Form</a>


                <?php } ?>


    
            </div>
     
            <?php elseif ($error): ?>
            <div class="alert alert-danger text-center m-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php elseif ($linkData): ?>

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
        <?php endif; ?>
    </div>
    <script src="api.js"></script>
</body>

</html>