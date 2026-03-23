<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .email-content {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #667eea;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #999;
            margin: 5px 0 0 0;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #333;
        }
        .verification-section {
            background-color: #f0f4ff;
            padding: 20px;
            border-radius: 4px;
            margin: 30px 0;
            border-left: 4px solid #667eea;
        }
        .verification-section p {
            color: #666;
            margin: 0 0 20px 0;
        }
        .verification-button {
            display: inline-block;
            background-color: #667eea;
            color: #fff;
            padding: 14px 40px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        .verification-button:hover {
            background-color: #5568d3;
        }
        .alternative-link {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 13px;
            color: #999;
        }
        .alternative-link p {
            margin: 10px 0;
        }
        .alternative-link a {
            color: #667eea;
            text-decoration: none;
            word-break: break-all;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #999;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-content">
            <!-- Header -->
            <div class="header">
                <h1>Thread & Trend</h1>
                <p>Premium Clothing Store</p>
            </div>

            <!-- Greeting -->
            <div class="greeting">
                <p>Hi {{ $user->fname }},</p>
                <p>Welcome to Thread & Trend! Thank you for registering an account with us.</p>
            </div>

            <!-- Verification Section -->
            <div class="verification-section">
                <p><strong>Verify Your Email Address</strong></p>
                <p>To complete your registration and unlock full access to your account, please verify your email address by clicking the button below.</p>
                
                <a href="{{ $verificationUrl }}" class="verification-button">Verify Email Address</a>
                
                <p style="margin-top: 20px; font-size: 13px; color: #999;">
                    This verification link will expire in 24 hours.
                </p>
            </div>

            <!-- Alternative Link -->
            <div class="alternative-link">
                <p><strong>Can't click the button?</strong></p>
                <p>Copy and paste this link into your browser:</p>
                <p><a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a></p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>© {{ date('Y') }} Thread & Trend. All rights reserved.</p>
                <p>If you didn't create this account, you can ignore this email.</p>
                <p>
                    Email: support@threadtrend.com | Phone: (555) 123-4567
                </p>
            </div>
        </div>
    </div>
</body>
</html>
