<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nairafy Escrow Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .logo {
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
            text-align: left;
        }
        .button {
            display: inline-block;
            padding: 12px 20px;
            margin: 10px;
            font-size: 16px;
            color: #fff;
            text-decoration: none;
            border-radius: 30px;
            border: none;
            cursor: pointer;
        }
        .btn-green {
            background-color: #28a745;
        }
        .btn-red {
            background-color: #dc3545;
        }
        .footer {
            font-size: 12px;
            color: #888;
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{asset(config('app.logo_horizontal'))}}" alt="Nairafy Escrow Limited" width="300">
        </div>
        <div class="message">
            <p>Dear {{$user->name}},</p>
            <p>We are excited to inform you that you have been invited to join Nairafy Escrows. A pending transaction has also been requested on Nairafy Escrow Limited.</p>
            <a href="{{route('registration_complete' , $user->registration_token)}}" class="button btn-green">Complete Registration</a>
            <a href="{{route('registration_complete' , $user->registration_token)}}" class="button btn-red">Cancel this Invite</a>
            <p>Best regards,</p>
            <p><strong>Nairafy Escrow Limited</strong></p>
        </div>
        <div class="footer">
            <p><strong>Disclaimer:</strong> This email and any attachments are confidential and intended solely for the recipient. If you have received this email in error, please notify us immediately and delete it. Unauthorized use or distribution of this email is prohibited.</p>
        </div>
    </div>
</body>
</html>
