<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request</title>
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
            border-bottom: 2px solid #dc3545;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #dc3545;
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            background-color: #dc3545;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            background-color: #c82333;
        }
        .reset-section {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid #dee2e6;
            margin-top: 30px;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Password Reset</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{ $user->first_name }},</h2>
            
            <p>We received a request to reset the password for your {{ config('app.name') }} account. If you requested this change, please click the button below to set a new password:</p>
            
            <div class="reset-section">
                <h3>Reset Your Password</h3>
                <p>Click the button below to create a new password for your account:</p>
                
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
                
                <p><small>This reset link will expire in 60 minutes for security reasons.</small></p>
            </div>
            
            <div class="warning">
                <strong>⚠️ Security Notice:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>This link will expire in 1 hour</li>
                    <li>You can only use this link once</li>
                    <li>If you didn't request this reset, please ignore this email</li>
                    <li>Consider changing your password if you suspect unauthorized access</li>
                </ul>
            </div>
            
            <p><strong>Didn't request this?</strong> If you didn't request a password reset, you can safely ignore this email. Your password will remain unchanged.</p>
            
            <p>For your security, this request was made from IP address: {{ request()->ip() }}</p>
            
            <p>If you have any concerns about your account security, please contact our support team immediately.</p>
            
            <p>Best regards,<br>
            The {{ config('app.name') }} Team</p>
        </div>
        
        <div class="footer">
            <p>If you can't click the reset button, copy and paste this link into your browser:</p>
            <p><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
            <br>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
