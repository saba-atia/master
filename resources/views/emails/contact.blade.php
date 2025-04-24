<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Demo Request</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
        }
        h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 28px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 15px;
        }
        .info-card {
            background-color: #f8fafc;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 15px 0;
            border-radius: 0 4px 4px 0;
        }
        .info-label {
            font-weight: 600;
            color: #2c3e50;
            display: inline-block;
            width: 120px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #7f8c8d;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .highlight {
            color: #3498db;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/IMG/logo.png'))) }}" alt="Company Logo" class="logo">            <p>You have received a new demo request from your website</p>
        </div>

        <div class="info-card">
            <p><span class="info-label">Full Name:</span> <span class="highlight">{{ $data['fullName'] }}</span></p>
            <p><span class="info-label">Company:</span> {{ $data['company'] }}</p>
            <p><span class="info-label">Email:</span> <a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a></p>
            <p><span class="info-label">Phone:</span> <a href="tel:{{ $data['phone'] }}">{{ $data['phone'] }}</a></p>
            <p><span class="info-label">Job Title:</span> {{ $data['jobTitle'] }}</p>
            <p><span class="info-label">Country:</span> {{ $data['country'] }}</p>
            <p><span class="info-label">Industry:</span> {{ $data['industry'] }}</p>
        </div>

        <div class="footer">
            <p>This email was generated automatically. Please do not reply directly to this message.</p>
            <p>&copy; {{ date('Y') }} Your Company Name. All rights reserved.</p>
        </div>
    </div>
</body>
</html>