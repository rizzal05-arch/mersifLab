<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            width: 100%;
            height: 100%;
            background: white;
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* ===== WATERMARK ===== */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 180px;
            font-weight: 900;
            color: rgba(15, 52, 96, 0.04);
            font-family: 'Montserrat', sans-serif;
            letter-spacing: 15px;
            text-transform: uppercase;
            white-space: nowrap;
            z-index: 0;
            text-align: center;
            line-height: 1;
        }

        /* ===== CONTAINER ===== */
        .certificate-container {
            width: 100%;
            height: 100vh;
            margin: 0;
            background: white;
            padding: 20px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .certificate-border {
            width: 90%;
            max-width: 1200px;
            height: auto;
            min-height: 85vh;
            border: 4px solid #0f3460;
            border-radius: 10px;
            padding: 30px;
            position: relative;
            background: white;
            overflow: hidden;
        }

        /* Corner circles */
        .corner {
            position: absolute;
            width: 16px;
            height: 16px;
            border: 2px solid #0f3460;
            border-radius: 50%;
            background: white;
            z-index: 2;
        }
        .corner-tl { top: -8px; left: -8px; }
        .corner-tr { top: -8px; right: -8px; }
        .corner-bl { bottom: -8px; left: -8px; }
        /* Hapus corner-br */

        /* ===== SEMUA KONTEN z-index di atas watermark ===== */
        .cert-header,
        .cert-content,
        .cert-divider,
        .details-table,
        .footer-table {
            position: relative;
            z-index: 1;
        }

        /* ===== HEADER ===== */
        .cert-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo-row { width: 100%; margin-bottom: 15px; }
        .logo-row td { text-align: center; vertical-align: middle; }

        .logo-img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            vertical-align: middle;
            margin-right: 12px;
            border-radius: 8px;
            background: white;
            padding: 3px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: inline-block;
        }

        .company-name {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 700;
            color: #0f3460;
            letter-spacing: 2px;
            text-transform: uppercase;
            border-bottom: 2px solid #0f3460;
            padding-bottom: 3px;
            vertical-align: middle;
        }

        .cert-title {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 900;
            color: #0f3460;
            text-transform: uppercase;
            letter-spacing: 4px;
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .cert-subtitle {
            font-size: 12px;
            color: #7f8c8d;
            font-weight: 300;
        }

        /* ===== CONTENT ===== */
        .cert-content { text-align: center; }

        .cert-recipient {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin: 15px 0 10px 0;
            display: inline-block;
            border-bottom: 3px solid #0f3460;
            padding-bottom: 4px;
        }

        .cert-text {
            font-size: 12px;
            color: #34495e;
            line-height: 1.6;
            margin: 15px 0;
            font-weight: 300;
        }

        .cert-course {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            font-weight: 700;
            color: #0f3460;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: inline-block;
            padding: 10px 25px;
            border: 2px solid #0f3460;
            border-radius: 30px;
            background: rgba(15, 52, 96, 0.05);
            margin: 15px 0;
        }

        .cert-description {
            font-size: 11px;
            color: #5a6c7d;
            line-height: 1.5;
            margin: 15px 0;
            font-style: italic;
        }

        /* ===== DIVIDER ===== */
        .cert-divider {
            border: none;
            border-top: 2px solid #e0e0e0;
            margin: 20px 0;
        }

        /* ===== DETAILS ===== */
        .details-table { width: 100%; margin-bottom: 20px; }
        .details-table td {
            text-align: center;
            vertical-align: top;
            width: 50%;
        }
        .detail-label {
            font-size: 10px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .detail-value {
            font-family: 'Playfair Display', serif;
            font-size: 14px;
            color: #2c3e50;
            font-weight: 700;
        }

        /* ===== FOOTER ===== */
        .footer-table { width: 100%; margin-top: 30px; }
        .footer-table td { vertical-align: bottom; }
        .td-signature { width: 50%; text-align: left; }
        .td-seal { width: 50%; text-align: right; }

        .signature-name {
            font-family: 'Playfair Display', serif;
            font-size: 14px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 4px;
        }
        .signature-line {
            width: 160px;
            height: 2px;
            background: #0f3460;
            margin: 6px 0;
        }
        .signature-text {
            font-size: 9px;
            color: #7f8c8d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .seal-circle {
            display: inline-block;
            width: 70px;
            height: 70px;
            background: #0f3460;
            border-radius: 50%;
            border: 3px solid white;
            outline: 2px solid #0f3460;
            text-align: center;
            padding-top: 12px;
        }
        .seal-star {
            font-size: 20px;
            color: white;
            line-height: 1;
            display: block;
        }
        .seal-label {
            font-size: 9px;
            color: white;
            font-weight: 700;
            letter-spacing: 1px;
            display: block;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-border">

            <!-- Watermark di DALAM border supaya tembus ke seluruh area sertif -->
            <div class="watermark">MERSIFLAB</div>

            <!-- Corner decorations -->
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <!-- Corner BR dihapus -->

            <!-- Header -->
            <div class="cert-header">
                <table class="logo-row" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            {{-- Gunakan base64 encode dengan PHP --}}
                            @php
                                $logoPath = public_path('images/logo.png');
                                $logoData = file_get_contents($logoPath);
                                $logoBase64 = base64_encode($logoData);
                                $logoSrc = 'data:image/png;base64,' . $logoBase64;
                            @endphp
                            <img src="{{ $logoSrc }}" alt="MersifLab" style="width: 60px; height: 60px; object-fit: contain; vertical-align: middle; margin-right: 12px; border-radius: 8px; background: white; padding: 3px; box-shadow: 0 2px 8px rgba(0,0,0,0.0.1); display: inline-block;">
                            <span class="company-name" style="display: inline-block; vertical-align: middle;">MersifLab</span>
                        </td>
                    </tr>
                </table>
                <h1 class="cert-title">Certificate of Completion</h1>
                <p class="cert-subtitle">This is to proudly certify that</p>
            </div>

            <!-- Content -->
            <div class="cert-content">
                <div class="cert-recipient">{{ $certificate->user->name }}</div>
                <p class="cert-text">
                    has successfully demonstrated exceptional dedication and mastery by completing the comprehensive course
                </p>
                <div class="cert-course">{{ $certificate->course->name }}</div>
                <p class="cert-description">
                    This achievement represents not just the completion of coursework, but the acquisition of valuable skills
                    and knowledge that will contribute to future success.
                </p>
            </div>

            <!-- Divider -->
            <hr class="cert-divider">

            <!-- Details -->
            <table class="details-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <div class="detail-label">Certificate ID</div>
                        <div class="detail-value">{{ $certificate->certificate_code }}</div>
                    </td>
                    <td>
                        <div class="detail-label">Date Issued</div>
                        <div class="detail-value">{{ $certificate->issued_at->format('F d, Y') }}</div>
                    </td>
                </tr>
            </table>

            <!-- Footer -->
            <table class="footer-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="td-signature">
                        <div class="signature-name">Zhang Jiahao</div>
                        <div class="signature-line"></div>
                        <div class="signature-text">Head of LMS MersifLab</div>
                    </td>
                </tr>
            </table>

        </div>
    </div>
</body>
</html>