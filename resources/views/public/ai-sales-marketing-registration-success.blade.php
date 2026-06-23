<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful - AI-Integrated Sales & Marketing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .success-container {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 90%;
            position: relative;
            overflow: hidden;
        }
        .success-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #28a745, #20c997, #28a745);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .natdemy-logo {
            max-height: 80px;
            max-width: 200px;
            object-fit: contain;
            margin-bottom: 20px;
        }
        .success-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: bounceIn 1s ease-out;
        }
        .success-icon i {
            font-size: 60px;
            color: white;
        }
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        .success-title {
            font-size: 2rem;
            font-weight: 700;
            color: #28a745;
            margin-bottom: 20px;
        }
        .success-message {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 30px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <img src="{{ asset('images/natdemy-logo.png') }}" alt="Natdemy Logo" class="natdemy-logo">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        <h1 class="success-title">Registration Successful!</h1>
        <p class="success-message">
            Thank you for registering for <strong>AI-Integrated Sales & Marketing</strong>.
            Your application has been submitted successfully. Our team will review your details and contact you shortly.
        </p>
        <p class="text-muted mb-0">
            <i class="fas fa-envelope me-2"></i>You will receive a confirmation email if an email address was provided.
        </p>
    </div>
</body>
</html>
