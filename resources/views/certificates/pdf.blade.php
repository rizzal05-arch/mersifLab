<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Georgia', serif;
            background: white;
            width: 100%;
            height: 100%;
        }

        .certificate-container {
            width: 100%;
            height: 100%;
            position: relative;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            padding: 40px;
        }

        .certificate-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .certificate-title {
            font-size: 2.5em;
            color: #2c3e50;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 300;
        }

        .certificate-subtitle {
            font-size: 1.1em;
            color: #7f8c8d;
            font-style: italic;
        }

        .certificate-border {
            border: 3px solid #667eea;
            border-radius: 10px;
            padding: 30px;
            margin: 20px 0;
            position: relative;
            background: linear-gradient(45deg, #f8f9fa 25%, transparent 25%, transparent 75%, #f8f9fa 75%, #f8f9fa),
                        linear-gradient(45deg, #f8f9fa 25%, transparent 25%, transparent 75%, #f8f9fa 75%, #f8f9fa);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
        }

        .certificate-content {
            text-align: center;
        }

        .certificate-recipient {
            font-size: 1.3em;
            color: #2c3e50;
            margin: 20px 0;
            font-weight: 600;
        }

        .certificate-text {
            font-size: 1em;
            color: #34495e;
            line-height: 1.6;
            margin: 20px 0;
        }

        .certificate-course {
            font-size: 1.5em;
            color: #667eea;
            font-weight: 600;
            margin: 20px 0;
            text-transform: uppercase;
        }

        .certificate-details {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }

        .certificate-detail {
            text-align: center;
        }

        .certificate-detail-label {
            font-size: 0.8em;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .certificate-detail-value {
            font-size: 1em;
            color: #2c3e50;
            font-weight: 600;
        }

        .certificate-seal {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 80px;
            height: 80px;
            border: 2px solid #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            font-size: 1.5em;
            color: #667eea;
        }

        .certificate-signature {
            position: absolute;
            bottom: 20px;
            left: 20px;
            text-align: center;
        }

        .signature-line {
            width: 150px;
            height: 2px;
            background: #2c3e50;
            margin: 10px 0;
        }

        .signature-text {
            font-size: 0.8em;
            color: #7f8c8d;
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
