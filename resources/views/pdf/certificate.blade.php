<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Donation Certificate — CharityHub</title>
<style>
    @page {
        margin: 0;
        size: A4 landscape;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'DejaVu Sans', Arial, sans-serif;
        background: #fff;
        color: #1a1a2e;
        width: 297mm;
        height: 210mm;
        position: relative;
        overflow: hidden;
    }

    /* Decorative border */
    .border-outer {
        position: absolute;
        top: 6mm; left: 6mm; right: 6mm; bottom: 6mm;
        border: 3px solid #10b981;
    }
    .border-inner {
        position: absolute;
        top: 9mm; left: 9mm; right: 9mm; bottom: 9mm;
        border: 1px solid #d1fae5;
    }

    /* Background accent */
    .bg-accent {
        position: absolute;
        top: 0; right: 0;
        width: 100mm;
        height: 100%;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        clip-path: polygon(30% 0, 100% 0, 100% 100%, 0% 100%);
        z-index: 1;
    }

    /* Corner ornaments */
    .corner {
        position: absolute;
        width: 20mm;
        height: 20mm;
        border-color: #10b981;
        border-style: solid;
        z-index: 5;
    }
    .corner-tl { top: 12mm; left: 12mm; border-width: 3px 0 0 3px; }
    .corner-tr { top: 12mm; right: 12mm; border-width: 3px 3px 0 0; }
    .corner-bl { bottom: 12mm; left: 12mm; border-width: 0 0 3px 3px; }
    .corner-br { bottom: 12mm; right: 12mm; border-width: 0 3px 3px 0; }

    /* Content area */
    .content {
        position: absolute;
        top: 9mm;
        left: 9mm;
        width: 279mm;
        height: 192mm;
        padding: 20mm 20mm;
        z-index: 10;
    }
    .left-panel {
        width: 150mm;
    }
    .right-panel {
        position: absolute;
        top: 70mm;
        left: 195mm;
        width: 65mm;
        text-align: center;
    }

    /* Header */
    .org-name {
        font-size: 10pt;
        font-weight: bold;
        color: #10b981;
        letter-spacing: 3px;
        text-transform: uppercase;
        margin-bottom: 2mm;
    }
    .cert-title {
        font-size: 28pt;
        font-weight: bold;
        color: #064e3b;
        line-height: 1.1;
        margin-bottom: 5mm;
    }
    .cert-subtitle {
        font-size: 11pt;
        color: #6b7280;
        margin-bottom: 8mm;
    }

    /* Donor section */
    .this-is { font-size: 10pt; color: #6b7280; margin-bottom: 2mm; }
    .donor-name {
        font-size: 22pt;
        font-weight: bold;
        color: #064e3b;
        border-bottom: 2px solid #10b981;
        padding-bottom: 2mm;
        margin-bottom: 4mm;
        font-style: italic;
    }
    .donation-desc {
        font-size: 11pt;
        color: #374151;
        line-height: 1.6;
        max-width: 140mm;
    }
    .donation-desc strong {
        color: #064e3b;
        font-weight: bold;
    }

    /* Amount badge */
    .amount-badge {
        display: inline-block;
        background: #10b981;
        color: white;
        padding: 2mm 6mm;
        border-radius: 20px;
        font-size: 14pt;
        font-weight: bold;
        margin: 5mm 0;
    }

    /* Date & metadata */
    .meta {
        font-size: 9pt;
        color: #9ca3af;
        margin-top: 6mm;
    }

    /* QR code */
    .qr-container {
        background: white;
        border: 2px solid #d1fae5;
        border-radius: 4mm;
        padding: 3mm;
        text-align: center;
    }
    .qr-label {
        font-size: 7pt;
        color: #6b7280;
        text-align: center;
        margin-top: 2mm;
        word-break: break-all;
    }

    /* Signature */
    .signature-block {
        margin-top: 8mm;
        border-top: 1px solid #e5e7eb;
        padding-top: 3mm;
    }
    .sig-line { font-size: 8pt; color: #9ca3af; }

    /* Watermark */
    .watermark {
        position: absolute;
        bottom: 20mm;
        left: 50%;
        transform: translateX(-50%);
        font-size: 60pt;
        color: rgba(16, 185, 129, 0.04);
        font-weight: bold;
        letter-spacing: 5px;
        white-space: nowrap;
        z-index: 1;
    }

    /* Certificate ID */
    .cert-id {
        position: absolute;
        bottom: 20mm;
        right: 35mm;
        font-size: 7pt;
        color: #9ca3af;
        letter-spacing: 1px;
        z-index: 10;
    }

    /* Revoked overlay */
    .revoked-stamp {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 36pt;
        font-weight: bold;
        color: rgba(239, 68, 68, 0.3);
        border: 4px solid rgba(239, 68, 68, 0.3);
        padding: 3mm 8mm;
        z-index: 100;
        letter-spacing: 5px;
    }
</style>
</head>
<body>
    <div class="bg-accent"></div>
    <div class="border-outer"></div>
    <div class="border-inner"></div>
    <div class="corner corner-tl"></div>
    <div class="corner corner-tr"></div>
    <div class="corner corner-bl"></div>
    <div class="corner corner-br"></div>
    <div class="watermark">CHARITYHUB</div>

    <div class="cert-id">Certificate ID: {{ $donation->certificate_uuid }}</div>

    @if(isset($donation) && $donation->isRefunded())
        <div class="revoked-stamp">REVOKED</div>
    @endif

    <div class="content">
        <div class="left-panel">
            <div class="org-name">CharityHub Foundation</div>
            <div class="cert-title">Certificate of<br>Donation</div>
            <div class="cert-subtitle">This is to certify that</div>

            <div class="donor-name">{{ $donorName }}</div>

            <div class="donation-desc">
                has generously made a donation of
            </div>
            <div class="amount-badge">
                ${{ number_format($donation->amount, 2) }} {{ strtoupper($donation->currency ?? 'USD') }}
            </div>
            <div class="donation-desc">
                to support the campaign:<br>
                <strong>{{ $campaign->title }}</strong>
            </div>

            <div class="meta">
                Date of Donation: {{ $donation->created_at->format('F j, Y') }}<br>
                Type: {{ ucfirst(str_replace('_', ' ', $donation->type)) }}<br>
                Certificate Generated: {{ $generatedAt->format('F j, Y') }}
            </div>

            <div class="signature-block">
                <div class="sig-line">_________________________________</div>
                <div class="sig-line">CharityHub Administration</div>
            </div>
        </div>

        <div class="right-panel">
            {{-- Logo Heart --}}
            <div style="width:20mm;height:20mm;background:#10b981;border-radius:50%;margin: 0 auto 8mm auto; line-height: 20mm; text-align: center;">
                <svg style="width:12mm;height:12mm;fill:white;vertical-align: middle;" viewBox="0 0 24 24">
                    <path d="M12 21.593c-5.63-5.539-11-10.297-11-14.402 0-3.791 3.068-5.191 5.281-5.191 1.312 0 4.151.501 5.719 4.457 1.59-3.968 4.464-4.447 5.726-4.447 2.54 0 5.274 1.621 5.274 5.181 0 4.069-5.136 8.625-11 14.402z"/>
                </svg>
            </div>

            {{-- QR Code --}}
            <div class="qr-container">
                @if(isset($qrCodeData) && $qrCodeData)
                    <img src="{{ $qrCodeData }}" width="100" height="100" alt="Verification QR Code">
                @else
                    <div style="width:100px;height:100px;background:#f3f4f6;line-height:50px;display:inline-block;padding-top:25px;text-align:center;font-size:8pt;color:#6b7280;">QR<br>Code</div>
                @endif
                <div class="qr-label">Scan to verify<br>this certificate</div>
            </div>

            <div style="text-align:center;font-size:8pt;color:#6b7280; width: 50mm; margin: 2mm auto 0 auto;">
                Or visit:<br>
                <strong style="color:#064e3b;font-size:6pt;word-break:break-all; display:block; line-height:1.2;">{{ $verifyUrl }}</strong>
            </div>
        </div>
    </div>
</body>
</html>
