<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ID Card - {{ $convertedLead->name }}</title>
    <style>
        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
        }

        .page {
            position: relative;
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .page-front {
            @if($convertedLead->course_id == 5)
                background-image: url('{{ public_path("assets/images/e-school-frnd.jpg") }}');
            @elseif(in_array($convertedLead->course_id, [6, 7]))
                background-image: url('{{ public_path("assets/images/eduthanzeel-frnd.jpg") }}');
            @elseif(in_array($convertedLead->course_id, [1, 2, 9, 16]))
                background-image: url('{{ public_path("assets/images/natdemy-frnd.jpg") }}');
            @elseif(in_array($convertedLead->course_id, [3, 4, 8, 10, 11, 12, 13, 14, 15, 23, 37]))
                background-image: url('{{ public_path("assets/images/skill-park-frnd.jpg") }}');
            @else
                background-image: url('{{ public_path("assets/images/natdemy-frnd.jpg") }}');
            @endif
        }

        .page-back {
            @if($convertedLead->course_id == 5)
                background-image: url('{{ public_path("assets/images/e-school-back.jpg") }}');
            @elseif(in_array($convertedLead->course_id, [6, 7]))
                background-image: url('{{ public_path("assets/images/eduthanzeel-back.jpg") }}');
            @elseif(in_array($convertedLead->course_id, [1, 2, 9, 16]))
                background-image: url('{{ public_path("assets/images/natdemy-back.jpg") }}');
            @elseif(in_array($convertedLead->course_id, [3, 4, 8, 10, 11, 12, 13, 14, 15, 23, 37]))
                background-image: url('{{ public_path("assets/images/skill-park-back.jpg") }}');
            @else
                background-image: url('{{ public_path("assets/images/natdemy-back.jpg") }}');
            @endif
        }

        .content {
            position: relative;
            z-index: 10;
            width: 100%;
            height: 100%;
        }

        /* Circle photo container for mPDF */
        .photo-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 20;
            padding-top: 160px; /* Adjust to move photo up slightly */
        }

        .circle-photo {
            width: 380px;
            height: 380px;
            border-radius: 50%;
            /* border: 4px solid #fff; */
            /* box-shadow: 0 4px 8px rgba(0,0,0,0.2); */
            margin: 0 auto;
            background-color: #fff;
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }

        .text-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 20;
            width: 100%;
            margin-top: 70px; /* Position below the photo */
        }

        .student-name {
            font-size: 45px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            text-align: center;
        }

        .register-number {
            font-size: 25px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 8px;
            text-align: center;
            margin-top: 43px;
            margin-left: 155px;
        }

        .parent-info {
            font-size: 20px;
            color: #000000;
            text-align: left;
            margin-top: 53px;
            margin-left: 360px;
        }
        .phone-number {
            font-size: 20px;
            color: #000000;
            text-align: left;
            margin-top: 15px;
            margin-left: 360px;
        }
        .email {
            font-size: 20px;
            color: #000000;
            text-align: left;
            margin-top: 17px;
            margin-left: 360px;
        }

        .course-info {
            font-size: 20px;
            color: #000000;
            text-align: left;
            margin-top: 18px;
            margin-left: 360px;
        }

        .batch-info {
            font-size: 20px;
            color: #000000;
            text-align: left;
            margin-top: 18px;
            margin-left: 360px;
            margin-right: 100px;
        }
        .issue-date {
            font-size: 20px;
            color: #000000;
            text-align: left;
            margin-top: 68px;
            margin-left: 390px;
        }

        .hod-contact {
            font-size: 20px;
            color: #000000;
            font-weight: bold;
            text-align: center;
            padding-top: 905px;
            margin-left: 170px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- First Page - Front -->
    <div class="page page-front">
        <div class="content">
            <!-- Photo Circle -->
            <div class="photo-container">
                @if($circularImagePath)
                    <div class="circle-photo" style="background-image: url('{{ public_path($circularImagePath) }}');"></div>
                @elseif($convertedLead->leadDetail && $convertedLead->leadDetail->passport_photo)
                    <div class="circle-photo" style="background-image: url('{{ public_path('storage/' . $convertedLead->leadDetail->passport_photo) }}');"></div>
                @else
                    <div class="circle-photo" style="background-image: url('{{ public_path('assets/images/place-holder.png') }}');"></div>
                @endif
            </div>

            <!-- Text Container -->
            <div class="text-container">
                <div class="student-name">{{ strtoupper($convertedLead->name) }}</div>
                @if($convertedLead->register_number)
                    <div class="register-number">{{ $convertedLead->register_number }}</div>
                @endif
                <div class="parent-info">{{ strtoupper($convertedLead->leadDetail->father_name) ?? '-' }}</div>
                <div class="phone-number">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->personal_code, $convertedLead->leadDetail->personal_number) }}</div>
                <div class="email">{{ $convertedLead->leadDetail->email }}</div>
                <div class="course-info">{{ $convertedLead->course->title ?? '-' }}</div>
                <div class="batch-info">{{ $convertedLead->batch->title ?? '-' }}</div>
                <div class="issue-date">{{ date('d-m-Y') }}</div>
            </div>
        </div>
    </div>
    
    <div class="page-break"></div>
    
    <!-- Second Page - Back -->
    <div class="page page-back">
        <div class="content">
            <div class="hod-contact">{{ $convertedLead->course->hod_number ?? '-' }}</div>
        </div>
    </div>
</body>
</html>
