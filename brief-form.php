<?php
/**
 * Logo Design Questionnaire (Brief Form)
 * A clean, professional, and detailed form for clients to provide project details.
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logo Design Questionnaire - Logo Element Design</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #ff5722 0%, #e64a19 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --section-bg: #ffffff;
            --accent-color: #ff5722;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            color: #333;
        }

        .brief-header {
            background: #1a1a1a;
            color: white;
            padding: 60px 20px;
            text-align: center;
            margin-bottom: -50px;
        }

        .brief-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .brief-header p {
            opacity: 0.8;
            font-weight: 300;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-container {
            max-width: 900px;
            margin: 0 auto 100px;
            padding: 0 20px;
            position: relative;
            z-index: 2;
        }

        .quest-card {
            background: var(--glass-bg);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            padding: 50px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .section-block {
            margin-bottom: 60px;
            border-bottom: 1px solid #eee;
            padding-bottom: 40px;
        }

        .section-block:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .section-number {
            width: 40px;
            height: 40px;
            background: var(--primary-gradient);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            box-shadow: 0 5px 15px rgba(255, 87, 34, 0.3);
        }

        .section-title h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
        }

        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #edeff2;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #fbfbfb;
            font-family: inherit;
        }

        .form-group input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 2px dashed #edeff2;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #fbfbfb;
            font-family: inherit;
            cursor: pointer;
        }

        .form-group input[type="file"]:hover {
            border-color: var(--accent-color);
            background: #fff;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--accent-color);
            background: #fff;
            outline: none;
            box-shadow: 0 0 0 4px rgba(255, 87, 34, 0.05);
        }

        /* Checkbox & Radio Styles */
        .option-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .option-item {
            position: relative;
        }

        .option-item label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            background: #f8f9fa;
            border: 2px solid #edeff2;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 400;
            margin: 0;
            font-size: 14px;
        }

        .option-item input {
            display: none;
        }

        .option-item input:checked+label {
            background: rgba(255, 87, 34, 0.05);
            border-color: var(--accent-color);
            color: var(--accent-color);
            font-weight: 600;
        }

        .option-item input:checked+label::before {
            content: '✓';
            font-weight: 900;
        }

        .full-width {
            grid-column: span 2;
        }

        .btn-submit {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 18px 45px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 15px;
            cursor: pointer;
            width: 100%;
            box-shadow: 0 10px 25px rgba(255, 87, 34, 0.3);
            transition: all 0.3s;
            font-family: 'Outfit', sans-serif;
            margin-top: 20px;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(255, 87, 34, 0.4);
        }

        .hint {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
            display: block;
        }

        .file-upload-wrapper {
            position: relative;
        }

        .file-upload-label {
            display: inline-block;
            padding: 12px 24px;
            background: var(--primary-gradient);
            color: white;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            margin-bottom: 10px;
        }

        .file-upload-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 87, 34, 0.3);
        }

        .file-upload-label i {
            margin-right: 8px;
        }

        .file-name-display {
            font-size: 13px;
            color: #666;
            margin-top: 8px;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 8px;
            display: none;
        }

        .file-name-display.active {
            display: block;
        }

        .file-name-display i {
            color: var(--accent-color);
            margin-right: 6px;
        }

        /* Read-only View Styles */
        .brief-details-view {
            display: none;
        }

        .brief-details-view.active {
            display: block;
        }

        .detail-item {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-size: 14px;
            font-weight: 600;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 15px;
            color: #333;
            line-height: 1.6;
        }

        .detail-value.empty {
            color: #999;
            font-style: italic;
        }

        .detail-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 8px;
        }

        .detail-tag {
            background: rgba(255, 87, 34, 0.1);
            color: var(--accent-color);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
        }

        .submitted-badge {
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .submitted-badge i {
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .quest-card {
                padding: 30px 20px;
            }

            .brief-header h1 {
                font-size: 28px;
            }
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

</head>

<body>

    <header>
        <div class="logo">
            <img src="./assets/images/header-footer/black-logo.png" alt="Logo Element Design">
        </div>
        <div class="header-right">
            <a href="tel:+12792251157" class="phone">(279) 225-1157</a>
        </div>
    </header>

    <div class="brief-header">
        <h1>Logo Design Questionnaire</h1>
        <p>Your responses will help our design team understand your vision and create a brand that truly resonates with
            your audience.</p>
    </div>

    <div class="form-container">
        <div class="quest-card">
            <!-- Submitted Brief Details View -->
            <div id="briefDetailsView" class="brief-details-view">
                <div class="submitted-badge">
                    <i class="fa fa-check-circle"></i>
                    <span>Brief Submitted</span>
                </div>
                <div id="briefDetailsContent"></div>
            </div>

            <!-- Brief Form -->
            <form id="briefForm" style="display: none;">
                <!-- 1. Company Information -->
                <div class="section-block">
                    <div class="section-title">
                        <div class="section-number">1</div>
                        <h3>Company Information</h3>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Business Name</label>
                            <input type="text" name="business_name" placeholder="e.g., Logo Element Design" >
                        </div>
                        <div class="form-group">
                            <label>Tagline (if any)</label>
                            <input type="text" name="tagline" placeholder="e.g., Designing Your Future">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Industry / Field</label>
                        <input type="text" name="industry" placeholder="e.g., Technology, Fashion, Real Estate"
                            >
                    </div>
                    <div class="form-group">
                        <label>Brief description of your business and services</label>
                        <textarea name="business_description" rows="3"
                            placeholder="What do you do and what makes you unique?"></textarea>
                    </div>
                </div>

                <!-- 2. Target Audience -->
                <div class="section-block">
                    <div class="section-title">
                        <div class="section-number">2</div>
                        <h3>Target Audience</h3>
                    </div>
                    <div class="form-group">
                        <label>Who is your primary target audience?</label>
                        <textarea name="target_audience" rows="2"
                            placeholder="Age, gender, profession, interests, etc."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Audience Geographic Reach</label>
                        <div class="option-grid">
                            <div class="option-item">
                                <input type="radio" name="geo_reach" id="geo_local" value="Local">
                                <label for="geo_local">Local</label>
                            </div>
                            <div class="option-item">
                                <input type="radio" name="geo_reach" id="geo_national" value="National">
                                <label for="geo_national">National</label>
                            </div>
                            <div class="option-item">
                                <input type="radio" name="geo_reach" id="geo_intl" value="International">
                                <label for="geo_intl">International</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Brand Personality -->
                <div class="section-block">
                    <div class="section-title">
                        <div class="section-number">3</div>
                        <h3>Brand Personality</h3>
                    </div>
                    <label>Which words best describe your brand? (Select all that apply)</label>
                    <div class="option-grid">
                        <div class="option-item">
                            <input type="checkbox" name="personality[]" id="per_prof" value="Professional">
                            <label for="per_prof">Professional</label>
                        </div>
                        <div class="option-item">
                            <input type="checkbox" name="personality[]" id="per_mod" value="Modern">
                            <label for="per_mod">Modern</label>
                        </div>
                        <div class="option-item">
                            <input type="checkbox" name="personality[]" id="per_min" value="Minimal">
                            <label for="per_min">Minimal</label>
                        </div>
                        <div class="option-item">
                            <input type="checkbox" name="personality[]" id="per_lux" value="Luxury">
                            <label for="per_lux">Luxury</label>
                        </div>
                        <div class="option-item">
                            <input type="checkbox" name="personality[]" id="per_fun" value="Fun / Playful">
                            <label for="per_fun">Fun / Playful</label>
                        </div>
                        <div class="option-item">
                            <input type="checkbox" name="personality[]" id="per_bold" value="Bold">
                            <label for="per_bold">Bold</label>
                        </div>
                        <div class="option-item">
                            <input type="checkbox" name="personality[]" id="per_trust" value="Trustworthy">
                            <label for="per_trust">Trustworthy</label>
                        </div>
                        <div class="option-item">
                            <input type="checkbox" name="personality[]" id="per_ele" value="Elegant">
                            <label for="per_ele">Elegant</label>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 20px;">
                        <label>Other personality traits</label>
                        <input type="text" name="personality_other" placeholder="e.g., Organic, High-tech, Rugged">
                    </div>
                </div>

                <!-- 4. Logo Style Preferences -->
                <div class="section-block">
                    <div class="section-title">
                        <div class="section-number">4</div>
                        <h3>Logo Style Preferences</h3>
                    </div>
                    <label>Preferred logo style:</label>
                    <div class="option-grid">
                        <div class="option-item">
                            <input type="radio" name="logo_style" id="style_word" value="Wordmark">
                            <label for="style_word">Wordmark (text)</label>
                        </div>
                        <div class="option-item">
                            <input type="radio" name="logo_style" id="style_letter" value="Lettermark">
                            <label for="style_letter">Lettermark</label>
                        </div>
                        <div class="option-item">
                            <input type="radio" name="logo_style" id="style_icon" value="Icon">
                            <label for="style_icon">Icon / Symbol</label>
                        </div>
                        <div class="option-item">
                            <input type="radio" name="logo_style" id="style_comb" value="Combination">
                            <label for="style_comb">Combination Mark</label>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 25px;">
                        <label>Design Complexity</label>
                        <div class="option-grid">
                            <div class="option-item">
                                <input type="radio" name="complexity" id="comp_simple" value="Simple">
                                <label for="comp_simple">Simple / Clean</label>
                            </div>
                            <div class="option-item">
                                <input type="radio" name="complexity" id="comp_detailed" value="Detailed">
                                <label for="comp_detailed">Detailed / Complex</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Color Preferences -->
                <div class="section-block">
                    <div class="section-title">
                        <div class="section-number">5</div>
                        <h3>Color Preferences</h3>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Preferred Colors</label>
                            <input type="text" name="colors_like" placeholder="e.g., Navy Blue, Gold, Slate">
                        </div>
                        <div class="form-group">
                            <label>Colors to Avoid</label>
                            <input type="text" name="colors_hate" placeholder="e.g., Neon Pink, Brown">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Should the logo work in black & white?</label>
                        <div class="option-grid" style="grid-template-columns: repeat(2, 120px);">
                            <div class="option-item">
                                <input type="radio" name="bw_ready" id="bw_yes" value="Yes" checked>
                                <label for="bw_yes">Yes</label>
                            </div>
                            <div class="option-item">
                                <input type="radio" name="bw_ready" id="bw_no" value="No">
                                <label for="bw_no">No</label>
                            </div>
                        </div>
                        <span class="hint">Recommended for high versatility</span>
                    </div>
                </div>

                <!-- 6. Typography -->
                <div class="section-block">
                    <div class="section-title">
                        <div class="section-number">6</div>
                        <h3>Typography</h3>
                    </div>
                    <div class="form-group">
                        <label>Do you have any font preferences?</label>
                        <input type="text" name="font_pref" placeholder="e.g., Serif, Sans-serif, Modern Script">
                    </div>
                    <div class="form-group">
                        <label>Any fonts you like or dislike?</label>
                        <textarea name="font_notes" rows="2"
                            placeholder="Mention specific font names if you have any in mind."></textarea>
                    </div>
                </div>

                <!-- 7. Inspiration & References -->
                <div class="section-block">
                    <div class="section-title">
                        <div class="section-number">7</div>
                        <h3>Inspiration & References</h3>
                    </div>
                    <div class="form-group">
                        <label>Are there any logos you like? (Link or Name)</label>
                        <textarea name="logos_like" rows="2"
                            placeholder="Describe what you like about them."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Are there any logos you dislike?</label>
                        <textarea name="logos_hate" rows="2" placeholder="Why do you dislike them?"></textarea>
                    </div>
                </div>

                <!-- 8. Usage Requirements -->
                <div class="section-block">
                    <div class="section-title">
                        <div class="section-number">8</div>
                        <h3>Usage Requirements</h3>
                    </div>
                    <div class="form-group">
                        <label>Where will the logo be used mostly?</label>
                        <input type="text" name="usage" placeholder="e.g., Website, Social Media, Signage, Print">
                    </div>
                    <div class="form-group">
                        <label>Do you need variations?</label>
                        <textarea name="variations" rows="2"
                            placeholder="Horizontal, vertical, icon-only, etc."></textarea>
                    </div>
                </div>

                <!-- 9. Competitors -->
                <div class="section-block">
                    <div class="section-title">
                        <div class="section-number">9</div>
                        <h3>Competitors</h3>
                    </div>
                    <div class="form-group">
                        <label>List your main competitors</label>
                        <textarea name="competitors" rows="2" placeholder="Who else is in your space?"></textarea>
                    </div>
                    <div class="form-group">
                        <label>How would you like your logo to stand out from them?</label>
                        <textarea name="differentiation" rows="2"></textarea>
                    </div>
                </div>

                <!-- 10. Additional Information -->
                <div class="section-block">
                    <div class="section-title">
                        <div class="section-number">10</div>
                        <h3>Additional Information</h3>
                    </div>
                    <div class="form-group">
                        <label>Any symbols, icons, or imagery to include/avoid?</label>
                        <textarea name="imagery_notes" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Any additional comments or requirements:</label>
                        <textarea name="final_comments" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Attach Reference Files (Optional)</label>
                        <div class="file-upload-wrapper">
                            <label for="attachment" class="file-upload-label">
                                <i class="fa fa-cloud-upload"></i>
                                Choose File
                            </label>
                            <input type="file" name="attachment" id="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.zip" style="display: none;">
                            <div id="fileNameDisplay" class="file-name-display">
                                <i class="fa fa-file"></i>
                                <span id="fileName">No file chosen</span>
                            </div>
                        </div>
                        <span class="hint">Accepted formats: JPG, PNG, PDF, DOC, DOCX, ZIP (Max 10MB)</span>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Submit Questionnaire</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="api.js?v=<?= time() ?>"></script>
    <script>
        // File input handler - show selected file name
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('attachment');
            const fileNameDisplay = document.getElementById('fileNameDisplay');
            const fileNameSpan = document.getElementById('fileName');
            
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    if (this.files && this.files.length > 0) {
                        const file = this.files[0];
                        const fileName = file.name;
                        const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
                        
                        // Check file size
                        if (file.size > 10 * 1024 * 1024) { // 10MB
                            alert('File size exceeds 10MB. Please choose a smaller file.');
                            this.value = '';
                            fileNameDisplay.classList.remove('active');
                            return;
                        }
                        
                        fileNameSpan.textContent = `${fileName} (${fileSize} MB)`;
                        fileNameDisplay.classList.add('active');
                    } else {
                        fileNameSpan.textContent = 'No file chosen';
                        fileNameDisplay.classList.remove('active');
                    }
                });
            }
        });
        
        // Check if brief already exists on page load
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                // Get encrypted_lead_id from URL
                const urlParams = new URLSearchParams(window.location.search);
                const encryptedLeadId = urlParams.get('encrypted_lead_id');
                 
                // alert(encryptedLeadId);
                if (encryptedLeadId) {
                    // Check if brief exists
                    const response = await getLeadBrief(encryptedLeadId);
                    
                    console.log('Brief response:', response);
                    
                    // Handle different response structures
                    let briefData = null;
                    if (response) {
                        // Check if brief is nested in response.brief
                        if (response.brief) {
                            briefData = response.brief;
                        }
                        // Check if response itself is the brief (has business_name or industry field)
                        else if (response.business_name || response.industry || response.id) {
                            briefData = response;
                        }
                        // Check if brief is in data property
                        else if (response.data) {
                            briefData = response.data;
                        }
                    }
                    
                    if (briefData) {
                        // Brief exists, show details view
                        console.log('Brief found, showing details');
                        showBriefDetails(briefData);
                    } else {
                        // Brief doesn't exist, show form
                        console.log('Brief not found, showing form');
                        showBriefForm();
                    }
                } else {
                    // No encrypted ID, show form
                    console.log('No encrypted_lead_id in URL, showing form');
                    showBriefForm();
                }
            } catch (error) {
                console.error('Error checking brief:', error);
                // On error, show form
                showBriefForm();
            }
        });

        function showBriefDetails(briefData) {
            console.log('Showing brief details with data:', briefData);
            
            // Hide form
            const form = document.getElementById('briefForm');
            if (form) {
                form.style.display = 'none';
            }
            
            // Show details view
            const detailsView = document.getElementById('briefDetailsView');
            if (detailsView) {
                detailsView.classList.add('active');
            } else {
                console.error('briefDetailsView element not found');
                return;
            }
            
            // Build details HTML
            const detailsContent = document.getElementById('briefDetailsContent');
            let html = '';
            
            // Helper function to format value
            const formatValue = (value) => {
                if (!value || value === '' || value === null) {
                    return '<span class="detail-value empty">Not provided</span>';
                }
                if (Array.isArray(value)) {
                    return value.length > 0 ? value.join(', ') : '<span class="detail-value empty">Not provided</span>';
                }
                return value;
            };
            
            // Helper function to format tags
            const formatTags = (value) => {
                if (!value || value === '' || value === null) return '';
                if (Array.isArray(value)) {
                    if (value.length === 0) return '';
                    return '<div class="detail-tags">' + value.map(v => `<span class="detail-tag">${v}</span>`).join('') + '</div>';
                }
                return '<div class="detail-tags"><span class="detail-tag">' + value + '</span></div>';
            };
            
            // Section 1: Company Information
            html += '<div class="section-block">';
            html += '<div class="section-title"><div class="section-number">1</div><h3>Company Information</h3></div>';
            html += '<div class="detail-item"><div class="detail-label">Business Name</div><div class="detail-value">' + formatValue(briefData.business_name) + '</div></div>';
            html += '<div class="detail-item"><div class="detail-label">Tagline</div><div class="detail-value">' + formatValue(briefData.tagline) + '</div></div>';
            html += '<div class="detail-item"><div class="detail-label">Industry / Field</div><div class="detail-value">' + formatValue(briefData.industry) + '</div></div>';
            html += '<div class="detail-item"><div class="detail-label">Business Description</div><div class="detail-value">' + formatValue(briefData.business_description) + '</div></div>';
            html += '</div>';
            
            // Section 2: Target Audience
            html += '<div class="section-block">';
            html += '<div class="section-title"><div class="section-number">2</div><h3>Target Audience</h3></div>';
            html += '<div class="detail-item"><div class="detail-label">Primary Target Audience</div><div class="detail-value">' + formatValue(briefData.target_audience) + '</div></div>';
            html += '<div class="detail-item"><div class="detail-label">Geographic Reach</div><div class="detail-value">' + formatValue(briefData.geo_reach) + '</div></div>';
            html += '</div>';
            
            // Section 3: Brand Personality
            html += '<div class="section-block">';
            html += '<div class="section-title"><div class="section-number">3</div><h3>Brand Personality</h3></div>';
            html += '<div class="detail-item"><div class="detail-label">Personality Traits</div><div class="detail-value">' + formatTags(briefData.personality) + '</div></div>';
            html += '<div class="detail-item"><div class="detail-label">Other Personality Traits</div><div class="detail-value">' + formatValue(briefData.personality_other) + '</div></div>';
            html += '</div>';
            
            // Section 4: Logo Style Preferences
            html += '<div class="section-block">';
            html += '<div class="section-title"><div class="section-number">4</div><h3>Logo Style Preferences</h3></div>';
            html += '<div class="detail-item"><div class="detail-label">Preferred Logo Style</div><div class="detail-value">' + formatValue(briefData.logo_style) + '</div></div>';
            html += '<div class="detail-item"><div class="detail-label">Design Complexity</div><div class="detail-value">' + formatValue(briefData.complexity) + '</div></div>';
            html += '</div>';
            
            // Section 5: Color Preferences
            html += '<div class="section-block">';
            html += '<div class="section-title"><div class="section-number">5</div><h3>Color Preferences</h3></div>';
            html += '<div class="detail-item"><div class="detail-label">Preferred Colors</div><div class="detail-value">' + formatValue(briefData.colors_like) + '</div></div>';
            html += '<div class="detail-item"><div class="detail-label">Colors to Avoid</div><div class="detail-value">' + formatValue(briefData.colors_hate) + '</div></div>';
            html += '<div class="detail-item"><div class="detail-label">Black & White Ready</div><div class="detail-value">' + formatValue(briefData.bw_ready) + '</div></div>';
            html += '</div>';
            
            // Section 6: Typography
            html += '<div class="section-block">';
            html += '<div class="section-title"><div class="section-number">6</div><h3>Typography</h3></div>';
            html += '<div class="detail-item"><div class="detail-label">Font Preferences</div><div class="detail-value">' + formatValue(briefData.font_pref) + '</div></div>';
            html += '<div class="detail-item"><div class="detail-label">Font Notes</div><div class="detail-value">' + formatValue(briefData.font_notes) + '</div></div>';
            html += '</div>';
            
            // Section 7: Inspiration & References
            html += '<div class="section-block">';
            html += '<div class="section-title"><div class="section-number">7</div><h3>Inspiration & References</h3></div>';
            html += '<div class="detail-item"><div class="detail-label">Logos You Like</div><div class="detail-value">' + formatValue(briefData.logos_like) + '</div></div>';
            html += '<div class="detail-item"><div class="detail-label">Logos You Dislike</div><div class="detail-value">' + formatValue(briefData.logos_hate) + '</div></div>';
            html += '</div>';
            
            // Section 8: Usage Requirements
            html += '<div class="section-block">';
            html += '<div class="section-title"><div class="section-number">8</div><h3>Usage Requirements</h3></div>';
            html += '<div class="detail-item"><div class="detail-label">Where Logo Will Be Used</div><div class="detail-value">' + formatValue(briefData.usage) + '</div></div>';
            html += '<div class="detail-item"><div class="detail-label">Variations Needed</div><div class="detail-value">' + formatValue(briefData.variations) + '</div></div>';
            html += '</div>';
            
            // Section 9: Competitors
            html += '<div class="section-block">';
            html += '<div class="section-title"><div class="section-number">9</div><h3>Competitors</h3></div>';
            html += '<div class="detail-item"><div class="detail-label">Main Competitors</div><div class="detail-value">' + formatValue(briefData.competitors) + '</div></div>';
            html += '<div class="detail-item"><div class="detail-label">Differentiation</div><div class="detail-value">' + formatValue(briefData.differentiation) + '</div></div>';
            html += '</div>';
            
            // Section 10: Additional Information
            html += '<div class="section-block">';
            html += '<div class="section-title"><div class="section-number">10</div><h3>Additional Information</h3></div>';
            html += '<div class="detail-item"><div class="detail-label">Imagery Notes</div><div class="detail-value">' + formatValue(briefData.imagery_notes) + '</div></div>';
            html += '<div class="detail-item"><div class="detail-label">Additional Comments</div><div class="detail-value">' + formatValue(briefData.final_comments) + '</div></div>';
            
            // Show attachment if exists
            if (briefData.attachment) {
                const attachmentUrl = briefData.attachment_url || (API_BASE_URL + '/storage/' + briefData.attachment);
                const fileName = briefData.attachment.split('/').pop();
                html += '<div class="detail-item"><div class="detail-label">Attached File</div><div class="detail-value">';
                html += '<a href="' + attachmentUrl + '" target="_blank" style="color: var(--accent-color); text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">';
                html += '<i class="fa fa-paperclip"></i> ' + fileName;
                html += '</a></div></div>';
            }
            
            html += '</div>';
            
            detailsContent.innerHTML = html;
        }

        function showBriefForm() {
            // Show form
            const form = document.getElementById('briefForm');
            if (form) {
                form.style.display = 'block';
            }
            
            // Hide details view
            const detailsView = document.getElementById('briefDetailsView');
            if (detailsView) {
                detailsView.classList.remove('active');
            }
        }

        document.getElementById('briefForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitBtn = this.querySelector('.btn-submit');
            const originalText = submitBtn.innerText;

            submitBtn.innerText = 'Submitting your brief...';
            submitBtn.disabled = true;

            try {
                // Create FormData object to handle file upload
                const formData = new FormData(this);
                
                // Get encrypted_lead_id from URL
                const urlParams = new URLSearchParams(window.location.search);
                const encryptedLeadId = urlParams.get('encrypted_lead_id');
                
                if (encryptedLeadId) {
                    formData.append('encrypted_lead_id', encryptedLeadId);
                }
                
                // Handle personality array - FormData automatically handles multiple values with same name
                // No need to manually process it
                
                // Submit to API with FormData (will handle file upload)
                await submitLeadBrief(formData);

                // Success - show SweetAlert and reload page to show details
                await Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Thank you! Your logo questionnaire has been submitted successfully. Our team will review it and get back to you soon.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ff5722',
                    timer: 3000,
                    timerProgressBar: true
                });
                
                // Reload page to show submitted details
                window.location.reload();
                
            } catch (error) {
                // Error handling is done in apiRequest, but reset button state
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
            }
        });
    </script>
</body>

</html>