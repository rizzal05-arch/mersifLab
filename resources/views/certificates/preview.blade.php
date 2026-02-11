<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Preview</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            padding: 30px;
            min-height: 100vh;
        }

        .certificate-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 60px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            position: relative;
            overflow: hidden;
        }

        .certificate-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%230f3460' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1;
        }

        .certificate-border {
            border: 4px solid #0f3460;
            border-radius: 15px;
            padding: 50px;
            position: relative;
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.98) 100%);
            backdrop-filter: blur(10px);
            z-index: 2;
        }

        .certificate-border::before,
        .certificate-border::after {
            content: '';
            position: absolute;
            width: 60px;
            height: 60px;
            border: 3px solid #0f3460;
            border-radius: 50%;
        }

        .certificate-border::before {
            top: -30px;
            left: -30px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #0f3460;
        }

        .certificate-border::after {
            bottom: -30px;
            right: -30px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #16213e;
        }

        .certificate-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            z-index: 3;
        }

        .certificate-logo-container {
            text-align: center;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .certificate-logo-img {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }

        .certificate-company-name {
            font-family: 'Playfair Display', serif;
            font-size: 2.2em;
            font-weight: 700;
            color: #0f3460;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 0;
            position: relative;
        }

        .certificate-company-name::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            right: 0;
            height: 2px;
            background: #0f3460;
        }

        .certificate-title {
            font-family: 'Playfair Display', serif;
            font-size: 3.5em;
            font-weight: 900;
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 50%, #16213e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 4px;
            line-height: 1.2;
        }

        .certificate-subtitle {
            font-size: 1.3em;
            color: #7f8c8d;
            font-weight: 300;
            margin-bottom: 30px;
        }

        .certificate-content {
            text-align: center;
            position: relative;
            z-index: 3;
        }

        .certificate-recipient {
            font-family: 'Playfair Display', serif;
            font-size: 2.2em;
            font-weight: 700;
            color: #2c3e50;
            margin: 30px 0;
            position: relative;
            display: inline-block;
        }

        .certificate-recipient::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #0f3460, transparent);
        }

        .certificate-text {
            font-size: 1.2em;
            color: #34495e;
            line-height: 1.8;
            margin: 30px 0;
            font-weight: 300;
        }

        .certificate-course {
            font-family: 'Playfair Display', serif;
            font-size: 2em;
            font-weight: 700;
            color: #0f3460;
            margin: 30px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            display: inline-block;
            padding: 15px 40px;
            border: 2px solid #0f3460;
            border-radius: 50px;
            background: rgba(15, 52, 96, 0.05);
        }

        .certificate-description {
            font-size: 1.1em;
            color: #5a6c7d;
            line-height: 1.6;
            margin: 30px 0;
            font-style: italic;
        }

        .certificate-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid #e0e0e0;
            position: relative;
            z-index: 3;
            margin-bottom: 120px;
        }

        .certificate-detail {
            text-align: center;
            flex: 1;
        }

        .certificate-detail-label {
            font-size: 0.9em;
            color: #7f8c8d;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .certificate-detail-value {
            font-family: 'Playfair Display', serif;
            font-size: 1.3em;
            color: #2c3e50;
            font-weight: 700;
        }

        .certificate-seal {
            position: absolute;
            bottom: 30px;
            right: 40px;
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 50%, #16213e 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(15, 52, 96, 0.4);
            z-index: 4;
            border: 5px solid white;
        }

        .certificate-seal i {
            font-size: 3em;
            color: white;
        }

        .certificate-signature {
            position: absolute;
            bottom: 30px;
            left: 40px;
            text-align: center;
            z-index: 4;
        }

        .signature-line {
            width: 250px;
            height: 2px;
            background: linear-gradient(90deg, #2c3e50, #0f3460);
            margin: 15px 0;
        }

        .signature-text {
            font-size: 0.9em;
            color: #7f8c8d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .signature-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.1em;
            font-weight: 700;
            color: #2c3e50;
            margin-top: 5px;
        }

        .certificate-decoration {
            position: absolute;
            width: 100px;
            height: 100px;
            opacity: 0.1;
            z-index: 1;
        }

        .decoration-1 {
            top: 20px;
            left: 20px;
            border: 3px solid #667eea;
            border-radius: 50%;
            transform: rotate(-15deg);
        }

        .decoration-2 {
            bottom: 20px;
            right: 20px;
            border: 3px solid #764ba2;
            border-radius: 50%;
            transform: rotate(15deg);
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .certificate-container {
                box-shadow: none;
                border-radius: 0;
                padding: 40px;
                max-width: 100%;
            }

            .certificate-border {
                border-image: none;
                border: 4px solid #667eea;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-decoration decoration-1"></div>
        <div class="certificate-decoration decoration-2"></div>
        
        <div class="certificate-border">
            <div class="certificate-border::before">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="certificate-border::after">
                <i class="fas fa-award"></i>
            </div>
            
            <div class="certificate-header">
                <div class="certificate-logo-container">
                    <img src="{{ asset('images/logo.png') }}" alt="MersifLab Logo" class="certificate-logo-img">
                    <div class="certificate-company-name">MersifLab</div>
                </div>
                <h1 class="certificate-title" style="font-size: 2.8em;">Certificate of Completion</h1>
                <p class="certificate-subtitle">This is to proudly certify that</p>
            </div>

            <div class="certificate-content">
                <p class="certificate-recipient">{{ $certificate->user->name }}</p>
                
                <p class="certificate-text">
                    has successfully completed all learning materials in the course
                </p>
                
                <h2 class="certificate-course">{{ $certificate->course->name }}</h2>
                
                <p class="certificate-description">
                    This certificate is awarded as proof of course completion through the learning process provided in this program.
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
