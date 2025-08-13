<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #28a745;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            background-color: #28a745;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            background-color: #218838;
        }
        .verification-section {
            background-color: #e8f5e8;
            border: 1px solid #c3e6c3;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid #dee2e6;
            margin-top: 30px;
            color: #6c757d;
            font-size: 14px;
        }
        .features {
            list-style: none;
            padding: 0;
        }
        .features li {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .features li:before {
            content: "✓ ";
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to {{ config('app.name') }}!</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{ $user->first_name }},</h2>
            
            <p>Welcome to {{ config('app.name') }}! We're excited to have you join our community. Your account has been successfully created.</p>
            
            <div class="verification-section">
                <h3>📧 Verify Your Email Address</h3>
                <p>To get started and secure your account, please verify your email address by clicking the button below:</p>
                
                <a href="{{ $verificationUrl }}" class="button">Verify Email Address</a>
                
                <p><small>This verification link will expire in 60 minutes for security reasons.</small></p>
            </div>
            
            <h3>🎉 What's Next?</h3>
            <ul class="features">
                <li>Complete your profile setup</li>
                <li>Explore our marketplace features</li>
                <li>Connect with other users</li>
                <li>Start buying and selling</li>
            </ul>
            
            <p>If you have any questions or need assistance, our support team is here to help. Simply reply to this email or contact us through our help center.</p>
            
            <p>Best regards,<br>
            The {{ config('app.name') }} Team</p>
        </div>
        
        <div class="footer">
            <p>If you can't click the verification button, copy and paste this link into your browser:</p>
            <p><a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a></p>
            <br>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
