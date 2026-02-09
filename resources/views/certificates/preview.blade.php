<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Preview</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Georgia', serif;
            background: #f0f0f0;
            padding: 20px;
        }

        .certificate-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
        }

        .certificate-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            pointer-events: none;
        }

        .certificate-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .certificate-title {
            font-size: 3em;
            color: #2c3e50;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 300;
        }

        .certificate-subtitle {
            font-size: 1.2em;
            color: #7f8c8d;
            font-style: italic;
        }

        .certificate-border {
            border: 3px solid #667eea;
            border-radius: 10px;
            padding: 40px;
            margin: 20px 0;
            position: relative;
            background: linear-gradient(45deg, #f8f9fa 25%, transparent 25%, transparent 75%, #f8f9fa 75%, #f8f9fa),
                        linear-gradient(45deg, #f8f9fa 25%, transparent 25%, transparent 75%, #f8f9fa 75%, #f8f9fa);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
        }

        .certificate-content {
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .certificate-recipient {
            font-size: 1.5em;
            color: #2c3e50;
            margin: 20px 0;
            font-weight: 600;
        }

        .certificate-text {
            font-size: 1.1em;
            color: #34495e;
            line-height: 1.6;
            margin: 20px 0;
        }

        .certificate-course {
            font-size: 1.8em;
            color: #667eea;
            font-weight: 600;
            margin: 20px 0;
            text-transform: uppercase;
        }

        .certificate-details {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }

        .certificate-detail {
            text-align: center;
        }

        .certificate-detail-label {
            font-size: 0.9em;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .certificate-detail-value {
            font-size: 1.1em;
            color: #2c3e50;
            font-weight: 600;
        }

        .certificate-seal {
            position: absolute;
            bottom: 30px;
            right: 30px;
            width: 100px;
            height: 100px;
            border: 3px solid #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            font-size: 2em;
            color: #667eea;
        }

        .certificate-signature {
            position: absolute;
            bottom: 30px;
            left: 30px;
            text-align: center;
        }

        .signature-line {
            width: 200px;
            height: 2px;
            background: #2c3e50;
            margin: 10px 0;
        }

        .signature-text {
            font-size: 0.9em;
            color: #7f8c8d;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .certificate-container {
                box-shadow: none;
                border-radius: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-header">
            <h1 class="certificate-title">Certificate of Completion</h1>
            <p class="certificate-subtitle">This is to certify that</p>
        </div>

        <div class="certificate-border">
            <div class="certificate-content">
                <p class="certificate-recipient">{{ $certificate->user->name }}</p>
                
                <p class="certificate-text">
                    has successfully completed the course
                </p>
                
                <h2 class="certificate-course">{{ $certificate->course->name }}</h2>
                
                <p class="certificate-text">
                    with distinction and has demonstrated mastery of the subject matter.
                </p>
            </div>
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
            <i class="fas fa-award"></i>
        </div>

        <div class="certificate-signature">
            <div class="signature-line"></div>
            <div class="signature-text">Authorized Signature</div>
        </div>
    </div>
</body>
</html>
