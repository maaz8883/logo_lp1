# Brief Form URL in Email - Implementation Guide

## Overview
After payment success, customers should receive an email with a link to fill out the brief form. The URL format is:
```
http://localhost/brand/brief-form.php?encrypted_lead_id=ENCRYPTED_ID
```

## Laravel Implementation

### Option 1: In Payment Verification Controller

When payment is verified, send email with brief form URL:

```php
// In your PaymentController or LeadController
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

public function verifyPayment($id) {
    $lead = Lead::findOrFail($id);
    
    // Encrypt the lead ID
    $encryptedLeadId = Crypt::encryptString($lead->id);
    
    // Generate brief form URL
    $baseUrl = config('app.frontend_url', 'http://localhost/brand');
    $briefFormUrl = $baseUrl . '/brief-form.php?encrypted_lead_id=' . urlencode($encryptedLeadId);
    
    // Send email
    Mail::send('emails.payment-success', [
        'customerName' => $lead->name,
        'briefFormUrl' => $briefFormUrl,
        'packageName' => $lead->package_name ?? 'Standard Package',
    ], function($message) use ($lead) {
        $message->to($lead->email)
                ->subject('Payment Successful - Complete Your Logo Brief');
    });
    
    return response()->json(['message' => 'Payment verified']);
}
```

### Option 2: In Email Template

Create email template: `resources/views/emails/payment-success.blade.php`

```html
<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
</head>
<body>
    <h1>Thank You, {{ $customerName }}!</h1>
    
    <p>Your payment for <strong>{{ $packageName }}</strong> has been received successfully.</p>
    
    <p>To help us create the perfect logo for your brand, please complete our brief form:</p>
    
    <p style="margin: 20px 0;">
        <a href="{{ $briefFormUrl }}" 
           style="background-color: #ff5722; color: white; padding: 15px 30px; 
                  text-decoration: none; border-radius: 5px; display: inline-block;">
            Fill Out Brief Form
        </a>
    </p>
    
    <p>Or copy and paste this link into your browser:</p>
    <p style="color: #666; word-break: break-all;">{{ $briefFormUrl }}</p>
    
    <p>If you have any questions, feel free to contact us.</p>
    
    <p>Best regards,<br>Pixel Brand Design Team</p>
</body>
</html>
```

### Option 3: Using Helper Function (if using PHP helper)

If you want to use the helper function from `payment-helpers.php`:

```php
// In Laravel Controller
require_once base_path('../brand/payment-helpers.php');

$briefFormUrl = getBriefFormUrl($lead->id, config('app.frontend_url'));

if (is_array($briefFormUrl) && isset($briefFormUrl['error'])) {
    // Handle error
    Log::error('Failed to generate brief form URL: ' . $briefFormUrl['error']);
} else {
    // Use $briefFormUrl in email
    Mail::send('emails.payment-success', [
        'briefFormUrl' => $briefFormUrl,
        // ... other data
    ], ...);
}
```

## API Endpoint Needed

Make sure you have this endpoint in Laravel to get encrypted lead ID:

```php
// routes/api.php
Route::get('/leads/{id}/encrypted-id', [LeadController::class, 'getEncryptedId']);

// LeadController.php
public function getEncryptedId($id) {
    $lead = Lead::findOrFail($id);
    return response()->json([
        'encrypted_lead_id' => Crypt::encryptString($lead->id)
    ]);
}
```

## Example Email Content

The email should include:
1. Payment confirmation message
2. Brief form button/link
3. Direct URL (as backup)
4. Instructions on what to do next

## Testing

Test the URL format:
```
http://localhost/brand/brief-form.php?encrypted_lead_id=eyJpdiI6IkFOcUUrQWxQb0tlMnFjalFiUFh2MFE9PSIsInZhbHVlIjoibnlFemNpcFZZVHBYZUZrcnpRV20xZz09IiwibWFjIjoiYmRlY2U2YjJiMTc4NTY3NjIxMzczMGZiODUzMDI3MjdmMDNjNjVmOGI4MzY2NjYxZDVkYjhhYWRlMTM0OTEwMyIsInRhZyI6IiJ9
```

Make sure:
- The encrypted_lead_id is properly URL encoded
- The brief form page can decrypt and use it
- The URL works when clicked from email
