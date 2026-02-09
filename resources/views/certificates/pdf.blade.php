<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: white;
            width: 100%;
            height: 100%;
        }

        .certificate-container {
            width: 100%;
            height: 100%;
            position: relative;
            background: white;
            padding: 40px;
        }

        .certificate-border {
            border: 4px solid #0f3460;
            border-radius: 15px;
            padding: 30px;
            position: relative;
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.98) 100%);
            min-height: 600px;
        }

        .certificate-border::before,
        .certificate-border::after {
            content: '';
            position: absolute;
            width: 50px;
            height: 50px;
            border: 2px solid #0f3460;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #0f3460;
        }

        .certificate-border::before {
            top: -25px;
            left: -25px;
        }

        .certificate-border::after {
            bottom: -25px;
            right: -25px;
            color: #16213e;
            border-color: #16213e;
        }

        .certificate-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .certificate-logo-container {
            text-align: center;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .certificate-logo-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        .certificate-company-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.6em;
            font-weight: 700;
            color: #0f3460;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin: 0;
            position: relative;
        }

        .certificate-company-name::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            right: 0;
            height: 2px;
            background: #0f3460;
        }

        .certificate-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5em;
            font-weight: 900;
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 50%, #16213e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 3px;
            line-height: 1.2;
        }

        .certificate-subtitle {
            font-size: 1.1em;
            color: #7f8c8d;
            font-weight: 300;
            margin-bottom: 20px;
        }

        .certificate-content {
            text-align: center;
        }

        .certificate-recipient {
            font-family: 'Playfair Display', serif;
            font-size: 1.8em;
            font-weight: 700;
            color: #2c3e50;
            margin: 20px 0;
            position: relative;
            display: inline-block;
        }

        .certificate-recipient::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #667eea, transparent);
        }

        .certificate-text {
            font-size: 1em;
            color: #34495e;
            line-height: 1.6;
            margin: 20px 0;
            font-weight: 300;
        }

        .certificate-course {
            font-family: 'Playfair Display', serif;
            font-size: 1.6em;
            font-weight: 700;
            color: #0f3460;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            display: inline-block;
            padding: 10px 30px;
            border: 2px solid #0f3460;
            border-radius: 30px;
            background: rgba(15, 52, 96, 0.05);
        }

        .certificate-description {
            font-size: 0.9em;
            color: #5a6c7d;
            line-height: 1.5;
            margin: 20px 0;
            font-style: italic;
        }

        .certificate-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            margin-bottom: 100px;
        }

        .certificate-detail {
            text-align: center;
            flex: 1;
        }

        .certificate-detail-label {
            font-size: 0.8em;
            color: #7f8c8d;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .certificate-detail-value {
            font-family: 'Playfair Display', serif;
            font-size: 1em;
            color: #2c3e50;
            font-weight: 700;
        }

        .certificate-seal {
            position: absolute;
            bottom: 20px;
            right: 30px;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 50%, #16213e 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
        }

        .certificate-seal i {
            font-size: 2em;
            color: white;
        }

        .certificate-signature {
            position: absolute;
            bottom: 20px;
            left: 30px;
            text-align: center;
        }

        .signature-line {
            width: 180px;
            height: 2px;
            background: linear-gradient(90deg, #2c3e50, #0f3460);
            margin: 10px 0;
        }

        .signature-text {
            font-size: 0.8em;
            color: #7f8c8d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .signature-name {
            font-family: 'Playfair Display', serif;
            font-size: 1em;
            font-weight: 700;
            color: #2c3e50;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <div class="certificate-header">
                <div class="certificate-logo-container">
                    <img src="{{ public_path('images/logo.png') }}" alt="MersifLab Logo" class="certificate-logo-img">
                    <div class="certificate-company-name">MersifLab</div>
                </div>
                <h1 class="certificate-title" style="font-size: 2em;">Certificate of Completion</h1>
                <p class="certificate-subtitle">This is to proudly certify that</p>
            </div>

            <div class="certificate-content">
                <p class="certificate-recipient">{{ $certificate->user->name }}</p>
                
                <p class="certificate-text">
                    has successfully demonstrated exceptional dedication and mastery by completing the comprehensive course
                </p>
                
                <h2 class="certificate-course">{{ $certificate->course->name }}</h2>
                
                <p class="certificate-description">
                    This achievement represents not just the completion of coursework, but the acquisition of valuable skills and knowledge that will contribute to future success.
                </p>
            </div>

            <div class="certificate-details">
                <div class="certificate-detail">
                    <div class="certificate-detail-label">Certificate ID</div>
                    <div class="certificate-detail-value">{{ $certificate->certificate_code }}</div>
                </div>
                
                <div class="certificate-detail">
                    <div class="certificate-detail-label">Date Issued</div>
                    <div class="certificate-detail-value">{{ $certificate->issued_at->format('F d, Y') }}</div>
                </div>
            </div>

            <div class="certificate-seal">
                <i class="fas fa-medal"></i>
            </div>

            <div class="certificate-signature">
                <div class="signature-name">Zhang Jiahao</div>
                <div class="signature-line"></div>
                <div class="signature-text">Head of LMS MersifLab</div>
            </div>
        </div>
    </div>
</body>
</html>
