<?php
require_once 'payment-helpers.php';

$uuid = $_GET['id'] ?? 'N/A';
$short_id = strtoupper(substr($uuid, 0, 8));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Thank You — Logo Element Design</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-KW7SCQJP');</script>

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --brand: #BE5264;
  --brand-dark: #9e3e52;
  --success: #3B6D11;
}

body {
  font-family: 'Outfit', sans-serif;
  background: linear-gradient(135deg, #f4f2ef 0%, #e8e6e3 100%);
  color: #1a1a1a;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.thank-you-container {
  max-width: 600px;
  width: 100%;
  background: #fff;
  border-radius: 24px;
  padding: 3rem 2.5rem;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0,0,0,0.12);
  animation: slideUp 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes slideUp {
  from { transform: translateY(30px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.success-icon {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--brand) 0%, #e07a8c 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 2rem;
  box-shadow: 0 10px 30px rgba(190,82,100,0.3);
  animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.3s both;
}

@keyframes popIn {
  from { transform: scale(0); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

.success-icon i {
  color: #fff;
  font-size: 48px;
}

h1 {
  font-size: 36px;
  font-weight: 700;
  color: var(--brand);
  margin-bottom: 1rem;
  line-height: 1.2;
}

.message {
  font-size: 16px;
  color: #6b6b6b;
  line-height: 1.7;
  margin-bottom: 2rem;
}

.order-id {
  display: inline-block;
  background: #faf9f7;
  border: 1px solid #ebe9e4;
  border-radius: 12px;
  padding: 1rem 1.5rem;
  margin-bottom: 2rem;
}

.order-id-label {
  font-size: 12px;
  color: #888;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 6px;
}

.order-id-value {
  font-size: 24px;
  font-weight: 700;
  color: var(--brand);
}

.next-steps {
  background: #f0f9ff;
  border: 1px solid #bae6fd;
  border-radius: 16px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  text-align: left;
}

.next-steps h3 {
  font-size: 18px;
  font-weight: 600;
  color: #0369a1;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 8px;
}

.next-steps ul {
  list-style: none;
  padding: 0;
}

.next-steps li {
  font-size: 14px;
  color: #0c4a6e;
  padding: 8px 0;
  padding-left: 28px;
  position: relative;
}

.next-steps li:before {
  content: "✓";
  position: absolute;
  left: 0;
  color: var(--success);
  font-weight: 700;
  font-size: 16px;
}

.btn-primary {
  display: inline-block;
  padding: 14px 32px;
  background: linear-gradient(135deg, var(--brand) 0%, #d4667a 100%);
  color: #fff;
  font-family: 'Outfit', sans-serif;
  font-size: 15px;
  font-weight: 600;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  text-decoration: none;
  transition: transform 0.2s, box-shadow 0.2s;
  box-shadow: 0 4px 16px rgba(190,82,100,0.3);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(190,82,100,0.4);
}

.contact-info {
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #ebe9e4;
  font-size: 14px;
  color: #888;
}

.contact-info a {
  color: var(--brand);
  text-decoration: none;
  font-weight: 600;
}

.contact-info a:hover {
  text-decoration: underline;
}

@media (max-width: 600px) {
  .thank-you-container {
    padding: 2rem 1.5rem;
  }
  h1 {
    font-size: 28px;
  }
  .success-icon {
    width: 80px;
    height: 80px;
  }
  .success-icon i {
    font-size: 36px;
  }
}
</style>
</head>
<body>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KW7SCQJP" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

<div class="thank-you-container">
  
  <div class="success-icon">
    <i class="fa fa-check"></i>
  </div>

  <h1>Thank You!</h1>
  
  <p class="message">
    Your order has been successfully submitted. Our creative team is excited to bring your vision to life!
  </p>

  <div class="order-id">
    <div class="order-id-label">Order ID</div>
    <div class="order-id-value">#<?php echo $short_id; ?></div>
  </div>

  <div class="next-steps">
    <h3><i class="fa fa-info-circle"></i> What Happens Next?</h3>
    <ul>
      <li>You'll receive a confirmation email shortly</li>
      <li>Our design team will review your requirements</li>
      <li>We'll start working on your project within 24 hours</li>
      <li>You'll receive regular updates on progress</li>
    </ul>
  </div>

  <a href="brief-form.php?encrypted_lead_id=<?php echo htmlspecialchars($uuid, ENT_QUOTES, 'UTF-8'); ?>" class="btn-primary">
    Complete Your Brief Form
  </a>

  <div class="contact-info">
    Questions? Contact us at <a href="tel:+12792251157">(279) 225-1157</a><br>
    or email <a href="mailto:support@logoelementdesign.com">info@logoelementdesign.com</a>
  </div>

</div>

</body>
</html>
