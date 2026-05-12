<?php
require_once 'payment-helpers.php';

$uuid = $_GET['id'] ?? null;
$error = null;
$linkData = null;

if ($uuid) {
    $linkData = PaymentDetails_uuid($uuid);
    
    if (!$linkData) {
        $error = "Payment link not found or expired.";
    }
} else {
    $error = "No Payment ID provided.";
}

$short_id = $linkData ? strtoupper(substr($linkData['uuid'], 0, 8)) : 'N/A';
$safe_uuid = htmlspecialchars($uuid, ENT_QUOTES, 'UTF-8');
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
   SCROLL DOWN ANIMATION
───────────────────────────────────────── */
.scroll-arrow {
  animation: bounce 2s ease-in-out infinite;
  transition: transform 0.3s ease;
}

.scroll-arrow:hover {
  animation-play-state: paused;
  transform: scale(1.1);
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% {
    transform: translateY(0);
  }
  40% {
    transform: translateY(-12px);
  }
  60% {
    transform: translateY(-6px);
  }
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
   ADDONS PAGE
───────────────────────────────────────── */
.addons-page {
  max-width: 920px;
  margin: 0 auto;
  padding: 0 1.25rem 2.5rem;
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
}
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
  .addons-page { padding: 0 0.85rem 2rem; }
  .page-header h2 { font-size: 22px; }
  .checkout-bar { padding: 12px 16px; width:100%;}
  .bar-left strong { font-size: 20px; }
  body { padding-bottom: 74px; }
  .s-checkout { padding: 11px 18px; font-size: 13px; }
  .s-skip { padding: 10px 14px; }
  .order-confirm-card {
    padding: 1rem 0.85rem !important;
    margin: 15px auto 10px !important;
  }
  .order-confirm-header {
    flex-direction: column !important;
    text-align: center !important;
    gap: 0.75rem !important;
    margin-bottom: 1rem !important;
  }
  .order-confirm-header > div:first-child {
    width: 50px !important;
    height: 50px !important;
  }
  .order-confirm-header > div:first-child i {
    font-size: 20px !important;
  }
  .order-confirm-header h1 {
    font-size: 20px !important;
    margin-bottom: 4px !important;
  }
  .order-confirm-header p {
    font-size: 12px !important;
    line-height: 1.4 !important;
  }
  .order-details-grid {
    grid-template-columns: 1fr !important;
    gap: 0 !important;
    padding: 0.75rem !important;
  }
  .order-details-grid > div {
    border: none !important;
    padding: 0.5rem 0 !important;
    border-bottom: 1px solid #e0e0e0 !important;
    text-align: left !important;
  }
  .order-details-grid > div:last-child {
    border-bottom: none !important;
    padding-bottom: 0 !important;
  }
  .order-details-grid > div > div:first-child {
    font-size: 10px !important;
    margin-bottom: 3px !important;
  }
  .order-details-grid > div > div:last-child {
    font-size: 14px !important;
  }
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

<?php if ($error): ?>
  <div style="max-width: 600px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 12px; text-align: center;">
    <p style="color: #dc3545; font-size: 16px;"><?= htmlspecialchars($error) ?></p>
  </div>
<?php elseif ($linkData): ?>

<!-- ══════════════════════════════════════
     ORDER CONFIRMATION SECTION (COMPACT)
══════════════════════════════════════ -->
<div style="max-width: 700px; margin: 20px auto 15px; padding: 0 20px;">
  <div class="order-confirm-card" style="background: #fff; border-radius: 20px; padding: 1.75rem 2rem; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #ebe9e4;">
    
    <div class="order-confirm-header" style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1.5rem;">
      <div style="width: 60px; height: 60px; flex-shrink: 0; border-radius: 50%; background: linear-gradient(135deg, #BE5264 0%, #e07a8c 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 16px rgba(190,82,100,0.25);">
        <i class="fa fa-check" style="color: #fff; font-size: 24px;"></i>
      </div>
      
      <div style="flex: 1;">
        <h1 style="font-size: 26px; font-weight: 700; color: #BE5264; margin: 0 0 6px 0; line-height: 1.2;">Order Confirmed!</h1>
        <p style="font-size: 14px; color: #6b6b6b; margin: 0; line-height: 1.5;">Thank you for choosing Logo Element Design. Our team will begin working on your project immediately.</p>
      </div>
    </div>

    <div class="order-details-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; background: #faf9f7; border-radius: 12px; padding: 1rem; border: 1px solid #ebe9e4;">
      <div style="text-align: center;">
        <div style="font-size: 11px; color: #888; font-weight: 500; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Order ID</div>
        <div style="font-size: 16px; font-weight: 700; color: #BE5264;">#<?= $short_id ?></div>
      </div>
      <div style="text-align: center; border-left: 1px solid #e0e0e0; border-right: 1px solid #e0e0e0;">
        <div style="font-size: 11px; color: #888; font-weight: 500; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Billed To</div>
        <div style="font-size: 16px; font-weight: 700; color: #BE5264;"><?= htmlspecialchars($linkData['customer_name']) ?></div>
      </div>
      <div style="text-align: center;">
        <div style="font-size: 11px; color: #888; font-weight: 500; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Status</div>
        <div style="font-size: 14px; font-weight: 600; color: #3B6D11; display: flex; align-items: center; justify-content: center; gap: 5px;">
          <span style="width: 6px; height: 6px; border-radius: 50%; background: #3B6D11;"></span>
          Successful
        </div>
      </div>
    </div>

  </div>
</div>

<!-- ══════════════════════════════════════
     SCROLL DOWN INDICATOR
══════════════════════════════════════ -->
<div style="text-align: center; margin: 15px auto 25px; padding: 0 20px;">
  <p style="font-size: 12px; color: #999; font-weight: 600; margin-bottom: 10px; letter-spacing: 1px; text-transform: uppercase;">Scroll Down for Exclusive Add-ons</p>
  <div class="scroll-arrow" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #BE5264 0%, #e07a8c 100%); box-shadow: 0 4px 12px rgba(190,82,100,0.2);">
    <i class="fa fa-chevron-down" style="font-size: 18px; color: #fff;"></i>
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
    <?php if($linkData['sale_type'] == "front"){ ?>
    <a href="brief-form.php?encrypted_lead_id=<?= $linkData['lead_uuid'] ?>" class="s-skip">Skip</a>
    <?php } ?>
    <button class="s-checkout" id="checkout-btn" disabled>
      <i class="fa fa-credit-card"></i>
      Submit
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

<?php endif; ?>

<!-- ══════════════════════════════════════
     SCRIPTS
══════════════════════════════════════ -->
<script src="assets/js/jquery-3.3.1.min.js"></script>
<script src="assets/js/custom.js"></script>
<script src="api.js?v=<?= time() ?>"></script>

<script>
<?php if ($linkData): ?>
/* ── PHP vars ── */
const LEAD_ID = '<?= htmlspecialchars($linkData['lead_uuid'] ?? $uuid, ENT_QUOTES, 'UTF-8') ?>';

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

      // Success - redirect to thanks.php
      window.location.href = 'thanks.php?id=' + encodeURIComponent(LEAD_ID);

    } catch (error) {
      console.error('Addon submission error:', error);
      alert('Error submitting addons: ' + error.message);
      
      // Re-enable button
      coBtn.disabled = false;
      coBtn.innerHTML = '<i class="fa fa-credit-card"></i> Submit';
    }
  });
}

/* ── Init ── */
renderGrid();
updateBar();
<?php endif; ?>
</script>

</body>
</html>
