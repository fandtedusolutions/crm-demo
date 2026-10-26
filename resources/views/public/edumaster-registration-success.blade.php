<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful - {{ course_title(23) }}</title>
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
            0% {
                transform: scale(0.3);
                opacity: 0;
            }
            50% {
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        .success-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #28a745;
            margin-bottom: 20px;
            animation: fadeInUp 1s ease-out 0.3s both;
        }
        .success-message {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 40px;
            line-height: 1.6;
            animation: fadeInUp 1s ease-out 0.6s both;
        }
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .skill-park-logo {
            max-height: 100px;
            max-width: 200px;
            object-fit: contain;
            opacity: 0.9;
            transition: all 0.3s ease;
        }
        .skill-park-logo:hover {
            opacity: 1;
            transform: scale(1.05);
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @media (max-width: 768px) {
            .skill-park-logo {
                max-height: 50px;
                max-width: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="logo-container mb-4">
            <img src="{{ asset('skill-park-logo.png') }}" alt="Skill Park Logo" class="skill-park-logo">
        </div>
        
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1 class="success-title">Thank you!</h1>
        
        <p class="success-message">
            Your registration has been submitted successfully.
        </p>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

