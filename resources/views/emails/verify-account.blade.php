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
            background: linear-gradient(135deg, #008080 0%, #006666 100%);
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
            margin-bottom: 0;
        }

        .details-card {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 20px 24px;
            margin: 24px 0;
            border: 1px solid #e2e8f0;
        }

        .details-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #008080;
            letter-spacing: 0.08em;
            margin-bottom: 14px;
        }

        .detail-row {
            margin-bottom: 10px;
            font-size: 13px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-key {
            font-weight: 600;
            color: #64748b;
            min-width: 90px;
            flex-shrink: 0;
        }

        .detail-value {
            color: #1e293b;
            font-weight: 600;
            word-break: break-all;
            flex: 1;
            min-width: 0;
        }

        .action-container {
            text-align: center;
            margin: 28px 0;
        }

        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #008080;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.25);
        }

        .note {
            font-size: 12px;
            color: #64748b;
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 12px 16px;
            margin: 20px 0 0;
        }

        .note strong {
            color: #92400e;
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
            <div class="greeting">Welcome to the Team!</div>
            <p class="body-text">Your site attendant account has been successfully created. Please verify your email to
                activate your account and start managing visitor logs.</p>

            <div class="details-card">
                <div class="details-label">Your Login Credentials</div>
                <div class="detail-row">
                    <span class="detail-key">Email:</span>
                    <span class="detail-value">{{ $email }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-key">Password:</span>
                    <span class="detail-value">hinatourist</span>
                </div>
                <div class="detail-row">
                    <span class="detail-key">Tourist Spot:</span>
                    <span class="detail-value">{{ $area }}</span>
                </div>
            </div>

            <p class="body-text">Click the button below to verify your email address and activate your account.</p>

            <div class="action-container">
                <a href="{{ $url }}" class="button">Verify Email Address</a>
            </div>

            <div class="note">
                <strong>Security Note:</strong> For your security, you will be required to change your default password upon your first login.
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