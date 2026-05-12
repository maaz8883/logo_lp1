<?php
require_once 'payment-helpers.php';

$uuid = $_GET['id'] ?? 'N/A';
$pkg  = $_GET['pkg'] ?? 'Standard Package';
$status = $_GET['status'] ?? '';

if ($status === 'success' && $uuid !== 'N/A') {
    verifyPaymentWithCrm($uuid);
}

$short_id   = strtoupper(substr($uuid, 0, 8));
$safe_pkg   = htmlspecialchars($pkg, ENT_QUOTES, 'UTF-8');
$safe_uuid  = htmlspecialchars($uuid, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Confirmed — Logo Element Design</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-KW7SCQJP');</script>

<style>
/* ─────────────────────────────────────────
   RESET & BASE
───────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

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

body {
  font-family: 'Outfit', sans-serif;
  background: #f4f2ef;
  color: #1a1a1a;
  min-height: 100vh;
}

/* ─────────────────────────────────────────
   HEADER
───────────────────────────────────────── */
header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 40px;
  background: #fff;
  border-bottom: 1px solid #ebe9e4;
  position: sticky;
  top: 0;
  z-index: 40;
}
header .logo img { height: 42px; }
header .phone {
  font-size: 14px;
  font-weight: 600;
  color: var(--brand);
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 6px;
}
header .phone:hover { color: var(--brand-dark); }

/* ─────────────────────────────────────────
   MODAL OVERLAY
───────────────────────────────────────── */
.overlay {
  position: fixed; inset: 0; z-index: 200;
  background: rgba(10, 6, 6, 0.65);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  animation: fadeIn 0.3s ease;
}
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

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
/* stop footer being hidden under the sticky bar */
.site-footer { margin-bottom: 0; }
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
</head>
<body>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KW7SCQJP" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

<!-- ══════════════════════════════════════
     HEADER
══════════════════════════════════════ -->
<header>
  <div class="logo">
    <img src="./assets/images/header-footer/black-logo.png" alt="Logo Element Design">
  </div>
  <a href="tel:+12792251157" class="phone">
    <i class="fa fa-phone"></i> (279) 225-1157
  </a>
</header>

<!-- ══════════════════════════════════════
     ORDER CONFIRMATION MODAL
══════════════════════════════════════ -->
<div class="overlay" id="overlay">
  <div class="modal">

    <div class="check-circle">
      <i class="fa fa-check"></i>
    </div>

    <h1>Order Confirmed!</h1>
    <p class="sub">Thank you for choosing Logo Element Design. Our creative team has been notified and will begin working on your project immediately.</p>

    <div class="order-meta">
      <div class="meta-row">
        <span class="meta-label">Order ID</span>
        <span class="meta-val">#<?php echo $short_id; ?></span>
      </div>
      <div class="meta-row">
        <span class="meta-label">Package</span>
        <span class="meta-val"><?php echo $safe_pkg; ?></span>
      </div>
      <div class="meta-row">
        <span class="meta-label">Status</span>
        <span class="meta-val"><span class="status-dot"></span>Payment Successful</span>
      </div>
    </div>

    <div class="modal-btns">
      <button class="btn-primary" onclick="closeModal()">
        View Exclusive Add-ons &rarr;
      </button>
      <a href="brief-form.php?encrypted_lead_id=<?php echo $safe_uuid; ?>" class="btn-ghost">
        Skip &mdash; Go to Brief Form
      </a>
    </div>

  </div>
</div>

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
    <a href="brief-form.php?encrypted_lead_id=<?php echo $safe_uuid; ?>" class="s-skip">Skip</a>
    <button class="s-checkout" id="checkout-btn" disabled>
      <i class="fa fa-credit-card"></i>
      Process to Next Step
    </button>
  </div>
</div>

<!-- ══════════════════════════════════════
     FOOTER
══════════════════════════════════════ -->
<footer class="site-footer">

  <div class="footer-copy-bar">
    <p>&copy; 2026 <a href="#">Logo Element Design</a>. All rights reserved.</p>
  </div>
</footer>

<!-- ══════════════════════════════════════
     SCRIPTS
══════════════════════════════════════ -->
<script src="assets/js/jquery-3.3.1.min.js"></script>
<script src="assets/js/custom.js"></script>
<script src="api.js?v=<?php echo time(); ?>"></script>

<script>
/* ── PHP vars ── */
const LEAD_ID      = '<?php echo $safe_uuid; ?>';
const PACKAGE_NAME = '<?php echo $safe_pkg; ?>';
const PAY_STATUS   = '<?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>';

/* ── Sync CRM on success ── */
if (PAY_STATUS === 'success' && LEAD_ID && LEAD_ID !== 'N/A') {
  submitStep5(LEAD_ID, PACKAGE_NAME).catch(err => console.error('CRM sync error:', err));
}

/* ── Modal ── */
function closeModal() {
  const ov = document.getElementById('overlay');
  ov.style.opacity = '0';
  ov.style.transition = 'opacity 0.25s';
  setTimeout(() => ov.style.display = 'none', 260);
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
          <span class="price-now">${a.price}</span>
          <span class="price-was">${a.was}</span>
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

  totalEl.textContent = total;

  const n   = selected.size;
  countEl.textContent = n === 0
    ? 'No items selected'
    : `${n} item${n > 1 ? 's' : ''} selected`;

  coBtn.disabled = n === 0;
}

/* ── Checkout handler ── */
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
    window.location.href = 'brief-form.php?encrypted_lead_id=' + encodeURIComponent(LEAD_ID);

  } catch (error) {
    console.error('Addon submission error:', error);
    alert('Error submitting addons: ' + error.message);
    
    // Re-enable button
    coBtn.disabled = false;
    coBtn.innerHTML = '<i class="fa fa-credit-card"></i> Process to Next Step';
  }
});

/* ── Init ── */
renderGrid();
updateBar();
</script>

</body>
</html>
