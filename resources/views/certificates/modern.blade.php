<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        body { margin: 0; font-family: 'Helvetica', 'Arial', sans-serif; }

        .container {
            position: relative;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
        }

        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        /* NAMA STUDENT */
        .student-name {
            position: absolute;
            top: 315px;
            width: 100%;
            text-align: center;
            font-size: 52px;
            font-weight: bold;
            color: #2D5496;
            text-transform: uppercase;
        }

        /* NAMA KURSUS */
        .course-container {
            position: absolute;
            top: 430px; /* Dinaikkan sedikit untuk ruang nilai */
            width: 100%;
            text-align: center;
        }

        .course-name {
            display: inline-block;
            max-width: 800px;
            font-size: 26px;
            font-weight: bold;
            color: #3b82f6;
            line-height: 1.2;
        }

        /* NILAI AKHIR (Final Score) */
        .score-container {
            position: absolute;
            top: 515px; /* Di bawah nama kursus */
            width: 100%;
            text-align: center;
            font-size: 18px;
            color: #444;
        }

        .score-box {
            font-weight: bold;
            color: #2D5496;
            font-size: 22px;
        }

        /* NAMA WEB KURSUS */
        .web-name-container {
            position: absolute;
            top: 552px; /* Diturunkan sedikit agar tidak menabrak nilai */
            width: 100%;
            text-align: center;
            font-size: 22px;
        }

        .site-name-text {
            font-weight: bold;
            margin-left: 435px; /* Menyesuaikan teks "diselenggarakan oleh:" */
        }

        /* FOOTER KIRI: KODE & TANGGAL */
        .footer-left {
            position: absolute;
            bottom: 85px;
            left: 175px;
            font-size: 16px;
            line-height: 1.5;
        }

        /* FOOTER KANAN: MENTOR */
        .footer-right {
            position: absolute;
            bottom: 85px;
            right: 175px;
            text-align: center;
            width: 350px;
        }

        .mentor-name {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            border-bottom: 1px solid #000;
            display: inline-block;
            margin-bottom: 3px;
            padding: 0 10px;
        }

        .mentor-title {
            font-size: 14px;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="{{ public_path('img/serbaru.png') }}" class="background">

        <div class="student-name">{{ $student->name }}</div>

        <div class="course-container">
            <div class="course-name">{{ $course->name }}</div>
        </div>

        <div class="score-container">
            dengan Nilai Akhir: <span class="score-box">{{ $enrollment->grade ?? '0' }}/100</span>
        </div>

        <div class="web-name-container">
            <span class="site-name-text">{{ $siteName }}</span>
        </div>

        <div class="footer-left">
            <strong>{{ $enrollment->certificate }}</strong><br>
            {{ $enrollment->issued_at ? $enrollment->issued_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
        </div>

        <div class="footer-right">
            <div class="mentor-name">{{ $mentor->name ?? 'Administrator' }}</div>
            <div class="mentor-title">Mentor {{ $course->name }}</div>
        </div>
    </div>
</body>
</html>
