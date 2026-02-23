<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Logo Style - Logo Element Design</title>
    <link rel="stylesheet" href="style.css">
    
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

    <header>
        <div class="logo">
            <img src="./assets/images/header-footer/black-logo.png" alt="">
        </div>
        <div class="header-right">
            <a href="tel:+12792251157" class="phone">(279) 225-1157</a>
            <!-- <a href="#" class="btn-chat">Chat With Us</a> -->
        </div>
    </header>

    <div class="progress-container">
        <div class="progress-line"></div>
        <div class="progress-line-fill" style="width: 12.5%;"></div>
        <div class="progress-steps">
            <div class="step completed"></div>
            <div class="step"></div>
            <div class="step"></div>
            <div class="step"></div>
        </div>
    </div>
    <!-- Correction: The Logo Style screenshot shows the FIRST tick as checked, and others empty. 
         Wait, let's look at uploaded_image_2_...png (Logo Styles). It has a checkmark on the first circle? 
         No, it has a checkmark on the first circle. So we are on Step 1.
    -->
    <style>
        .progress-line-fill {
            width: 15%;
        }

        .step:nth-child(1) {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .step:nth-child(1)::after {
            content: '✓';
        }

        /* Actually if it's the current step it might just be active. But SS shows checkmark. Let's make Step 1 checked/active. */
    </style>

    <div class="container">
        <h1 class="page-title">Please Fill Your Design Brief</h1>
        <p class="subtitle">Fill out the brief form so the designers understand the perfect logo you're looking for.</p>

        <div class="card">
            <form action="logo-details.html">
                <div class="grid-container">
                    <!-- 1. Wordmark -->
                    <label class="style-card">
                        <div class="style-image">DISCOUNT</div>
                        <div class="style-title">Wordmark</div>
                        <div class="style-desc">Unique typographic treatments help them remember your name.</div>
                        <input type="radio" class="style-checkbox">
                    </label>

                    <!-- 2. Emblem -->
                    <label class="style-card">
                        <div class="style-image" style="background: #222;">EMBLEM</div>
                        <div class="style-title">Emblem</div>
                        <div class="style-desc">Build a legacy with a badge, seal or crest.</div>
                        <input type="radio" class="style-checkbox">
                    </label>

                    <!-- 3. Abstract -->
                    <label class="style-card">
                        <div class="style-image" style="background: #101030;">ABSTRACT</div>
                        <div class="style-title">Abstract</div>
                        <div class="style-desc">Truly custom abstract images stand for you and you alone.</div>
                        <input type="radio" class="style-checkbox">
                    </label>

                    <!-- 4. Mascot -->
                    <label class="style-card">
                        <div class="style-image" style="background: #8b3a3a;">MASCOT</div>
                        <div class="style-title">Mascot</div>
                        <div class="style-desc">Fun brands start with a memorable character.</div>
                        <input type="radio" class="style-checkbox">
                    </label>

                    <!-- 5. Pictorial -->
                    <label class="style-card">
                        <div class="style-image" style="background: #f4f4f4; color: #333;">PICTORIAL</div>
                        <div class="style-title">Pictorial</div>
                        <div class="style-desc">Link your business with the image of your choice: instant brand
                            personality.</div>
                        <input type="radio" class="style-checkbox">
                    </label>

                    <!-- 6. Combination -->
                    <label class="style-card">
                        <div class="style-image">COMBINATION</div>
                        <div class="style-title">Combination</div>
                        <div class="style-desc">Words and images mixed together to create the perfect combo.</div>
                        <input type="radio" class="style-checkbox">
                    </label>
                </div>
            </form>
        </div>

        <div class="footer-actions">
            <a href="logo-details.html" class="btn-next">Save & Continue</a>
        </div>
    </div>
    <script src="api.js"></script>
    <script>
        // Override the Next button to call our API function
        document.querySelector('.btn-next').addEventListener('click', (e) => {
            e.preventDefault();
            submitStep2();
        });

        // Add values to checkboxes for easier JS access
        const types = ['Wordmark', 'Emblem', 'Abstract', 'Mascot', 'Pictorial', 'Combination'];
        const cards = document.querySelectorAll('.style-card');
        cards.forEach((card, index) => {
            const input = card.querySelector('input');
            input.setAttribute('name', 'logo_style'); // Group them
            input.value = types[index]; // Set value matches API expected enum
            // Prevent default label click to handle it purely in JS if needed or let default behavior work
        });
    </script>
</body>

</html>