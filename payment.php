<?php

// echo 1;
// exit;
// brand_portal/index.php

// Configuration    
// URL of your CRM (Change this to your actual CRM URL)
define('CRM_API_URL', 'http://127.0.0.1:8000/api/payment-links/');

// Helper function to call CRM API
function getPaymentDetails($uuid)
{
    $url = CRM_API_URL . $uuid;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Enable if having SSL issues locally
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        return json_decode($response, true);
    }
    return null;
}

$uuid = $_GET['id'] ?? null;
$error = null;
$linkData = null;

if ($uuid) {
    $linkData = getPaymentDetails($uuid);
    if (!$linkData) {
        $error = "Payment link not found or expired.";
    }
} else {
    $error = "No Payment ID provided.";
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Override bootstrap body if needed, or just let style.css handle it. 
           We remove the flex centering to allow header to be at top. */
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            /* display: flex; REMOVED */
            /* align-items: center; REMOVED */
            /* justify-content: center; REMOVED */
        }

        .invoice-card {
            max-width: 500px;
            width: 100%;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            /* fixed 'shadow' property name if it was typo, original was 'shadow: ...' which is invalid css, it's box-shadow usually, or boostrap class shadow-lg handles it. Original code had 'shadow: ...' which is likely invalid. I will fix it to box-shadow just in case, though bootstrap class handles it. */
            margin: 0 auto;
            /* Center the card horizontally */
        }

        .brand-logo {
            max-height: 80px;
            object-fit: contain;
            margin-bottom: 20px;
        }

        /* Add spacing for the main content */
        .main-content {
            padding-top: 60px;
            padding-bottom: 60px;
            display: flex;
            justify-content: center;
        }
    </style>

    <?php if ($linkData && $linkData['merchant'] === 'paypal'): ?>
        <!-- PayPal SDK -->
        <script
            src="https://www.paypal.com/sdk/js?client-id=<?php echo $linkData['brand']['paypal_client_id']; ?>&currency=USD"></script>
    <?php endif; ?>
</head>

<body>

    <header>
        <div class="logo">
            <img src="./assets/images/header-footer/black-logo.png" alt="">
        </div>
        <div class="header-right">
            <a href="tel:+12792251157" class="phone">(279) 225-1157</a>
            <!-- <a href="#" class="btn-chat">Chat With Us</a> -->
        </div>
    </header>

    <!-- Simple Card Design -->
    <div class="simple-card">
        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="text-center py-4">
                <div class="success-icon">✓</div>
                <h3 class="thank-you-title">Payment Successful!</h3>
                <p class="thank-you-message">Thank you for your payment</p>

                <?php if ($linkData): ?>
                    <div class="receipt-details">
                        <div class="receipt-row">
                            <span>Amount Paid</span>
                            <strong class="text-success">$<?php echo number_format($linkData['amount'], 2); ?></strong>
                        </div>
                        <div class="receipt-row">
                            <span>Transaction ID</span>
                            <strong><?php echo substr($linkData['uuid'], 0, 8); ?>...</strong>
                        </div>
                        <div class="receipt-row">
                            <span>Date</span>
                            <strong><?php echo date('M d, Y'); ?></strong>
                        </div>
                        <div class="receipt-row">
                            <span>Billed To</span>
                            <strong><?php echo htmlspecialchars($linkData['customer_name']); ?></strong>
                        </div>
                    </div>
                <?php endif; ?>

                <button onclick="window.print()" class="btn-print">
                    🖨️ Print Receipt
                </button>
            </div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger text-center">
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
                    Payment for Invoice #<?php echo substr($linkData['uuid'], 0, 8); ?>
                </div>
            </div>

            <div class="payment-buttons">
                <?php if ($linkData['merchant'] === 'clover'): ?>
                    <!-- Clover Payment -->
                    <form method="POST">
                        <button type="submit" class="btn-black-style">
                            <span class="btn-icon-card">💳</span> Debit or Credit Card
                        </button>
                    </form>

                <?php elseif ($linkData['merchant'] === 'paypal'): ?>
                    <!-- PayPal Buttons Container -->
                    <div id="paypal-button-container"></div>

                    <!-- We use the standard PayPal script below, styles are handled by SDK usually but we wrapper matches -->
                    <script>
                        paypal.Buttons({
                            style: {
                                layout: 'vertical',
                                color: 'gold',
                                shape: 'rect',
                                label: 'paypal'
                            },
                            createOrder: function (data, actions) {
                                return actions.order.create({
                                    purchase_units: [{
                                        amount: {
                                            value: '<?php echo $linkData['amount']; ?>'
                                        },
                                        description: 'Payment for Invoice #<?php echo substr($linkData['uuid'], 0, 8); ?>'
                                    }]
                                });
                            },
                            onApprove: function (data, actions) {
                                return actions.order.capture().then(function (details) {
                                    // Call server to verify and update status
                                    const orderID = data.orderID;
                                    const verifyUrl = '<?php echo 'http://127.0.0.1:8000/api/payment-links/' . $uuid . '/verify'; ?>';

                                    fetch(verifyUrl, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({ orderID: orderID })
                                    })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.status === 'success') {
                                                window.location.href = "?status=success&id=<?php echo $uuid; ?>";
                                            } else {
                                                alert('Payment verification failed: ' + (data.error || 'Unknown error'));
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            alert('An error occurred while verifying the payment.');
                                        });
                                });
                            },
                            onError: function (err) {
                                alert('PayPal Error: ' + err);
                            }
                        }).render('#paypal-button-container');
                    </script>
                <?php else: ?>
                    <div class="alert alert-warning">Unknown Merchant Type</div>
                <?php endif; ?>
            </div>

            <!-- 
                <div class="powered-by">
                    Powered by Brand
                </div>
                -->

        <?php endif; ?>
    </div>
    </div>

</body>

</html>