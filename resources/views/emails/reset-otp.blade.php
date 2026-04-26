<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #334155;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }

        .container {
            max-width: 560px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        .header {
            background: linear-gradient(135deg, #167d77 0%, #0f5f5a 100%);
            padding: 32px 24px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.025em;
        }

        .header p {
            color: rgba(255, 255, 255, 0.7);
            margin: 6px 0 0;
            font-size: 13px;
            font-weight: 400;
        }

        .content {
            padding: 32px;
        }

        .greeting {
            font-size: 17px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
        }

        .body-text {
            font-size: 14px;
            color: #475569;
        }

        .otp-card {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            margin: 24px 0;
            border: 1px solid #e2e8f0;
        }

        .otp-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.08em;
            margin: 0 0 12px;
        }

        .otp-code {
            font-size: 36px;
            font-weight: 800;
            color: #167d77;
            letter-spacing: 0.2em;
            margin: 0;
            font-family: 'Courier New', Courier, monospace;
        }

        .otp-expiry {
            font-size: 11px;
            color: #94a3b8;
            margin: 12px 0 0;
        }

        .otp-expiry strong {
            color: #64748b;
        }

        .note {
            font-size: 12px;
            color: #64748b;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            margin: 20px 0 0;
        }

        .sign-off {
            font-size: 14px;
            color: #475569;
            margin-top: 24px;
        }

        .footer {
            padding: 16px 32px;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            font-size: 11px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>HinaTourist</h1>
        </div>
        <div class="content">
            <div class="greeting">Reset Your Password</div>
            <p class="body-text">We received a request to reset your HinaTourist account password. Use the verification code below to proceed:</p>

            <div class="otp-card">
                <p class="otp-label">Your Verification Code</p>
                <h2 class="otp-code">{{ $otp }}</h2>
                <p class="otp-expiry">Expires in <strong>10 minutes</strong></p>
            </div>

            <div class="note">
                If you did not request a password reset, you can safely ignore this email. Your account remains secure.
            </div>

            <p class="sign-off">Thank you,<br>The HinaTourist Team</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} HinaTourist. All rights reserved.<br>
            Hinatuan, Surigao del Sur
        </div>
    </div>
</body>

</html>
