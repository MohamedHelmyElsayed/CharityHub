<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; background: #f9fafb; margin: 0; padding: 0; }
.container { max-width: 560px; margin: 0 auto; padding: 20px; }
.card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
.header { background: linear-gradient(135deg, #059669 0%, #0d9488 100%); padding: 40px 30px; text-align: center; }
.header h1 { color: white; margin: 0; font-size: 28px; }
.header p { color: rgba(255,255,255,0.8); margin: 8px 0 0; }
.body { padding: 30px; }
.amount-box { background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 12px; padding: 20px; text-align: center; margin: 20px 0; }
.amount { font-size: 36px; font-weight: bold; color: #059669; }
.detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
.detail-label { color: #6b7280; font-size: 14px; }
.detail-value { font-weight: 600; color: #1f2937; font-size: 14px; }
.btn { display: block; background: #059669; color: white; text-decoration: none; padding: 14px 30px; border-radius: 10px; text-align: center; font-weight: bold; margin: 20px 0; }
.footer { text-align: center; color: #9ca3af; font-size: 12px; padding: 20px; }
</style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="header">
            <div style="font-size:32px;margin-bottom:8px;">💚</div>
            <h1>Thank You, {{ $donorName }}!</h1>
            <p>Your donation has been received and verified.</p>
        </div>
        <div class="body">
            <div class="amount-box">
                <div style="color:#6b7280;font-size:14px;margin-bottom:4px;">You donated</div>
                <div class="amount">${{ number_format($donation->amount, 2) }}</div>
                <div style="color:#6b7280;font-size:14px;margin-top:4px;">to <strong style="color:#1f2937;">{{ $campaign->title }}</strong></div>
            </div>

            <div style="margin:20px 0;">
                <div class="detail-row">
                    <span class="detail-label">Donation Type</span>
                    <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $donation->type)) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date</span>
                    <span class="detail-value">{{ $donation->created_at->format('M j, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Certificate ID</span>
                    <span class="detail-value" style="font-family:monospace;font-size:12px;">{{ substr($donation->certificate_uuid, 0, 20) }}...</span>
                </div>
            </div>

            <p style="color:#6b7280;font-size:14px;line-height:1.6;">
                Your personalized donation certificate is attached to this email as a PDF. You can also verify it online at any time using the link below.
            </p>

            <a href="{{ $verifyUrl }}" class="btn">Verify Your Certificate Online →</a>

            <p style="color:#9ca3af;font-size:12px;text-align:center;">
                Your generosity makes a real difference. Thank you for being part of the CharityHub community.
            </p>
        </div>
    </div>
    <div class="footer">
        © {{ date('Y') }} CharityHub Foundation. This email was sent to you because you made a donation.<br>
        Your data is handled in compliance with GDPR.
    </div>
</div>
</body>
</html>
