<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logo Details - Logo Element Design</title>
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
        <div class="progress-line-fill" style="width: 37.5%;"></div>
        <div class="progress-steps">
            <div class="step completed"></div>
            <div class="step completed"></div>
            <div class="step"></div>
            <div class="step"></div>
        </div>
    </div>

    <div class="container">
        <h1 class="page-title">Please Fill Your Design Brief</h1>
        <p class="subtitle">Fill out the brief form so the designers understand the perfect logo you're looking for.</p>

        <div class="card">
            <form>
                <div class="form-group">
                    <label class="form-label">Exact name you want in your logo</label>
                    <input type="text" id="logo_name" class="form-control" placeholder="Mobile EV Charge and Go">
                </div>

                <div class="form-group">
                    <label class="form-label">What is your color preference?</label>
                    <input type="text" id="color_preference" class="form-control"
                        placeholder="Light Grays, yellow, Miami blue">
                </div>
            </form>
        </div>

        <div class="footer-actions">
            <a href="#" class="btn-next" onclick="event.preventDefault(); submitStep3();">Save & Continue</a>
        </div>
    </div>
    <script src="api.js"></script>
</body>

</html>