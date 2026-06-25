@php
    use Carbon\Carbon;

    $issuedAt = isset($issuedAt) && $issuedAt
        ? ($issuedAt instanceof Carbon ? $issuedAt : Carbon::parse($issuedAt))
        : now();

    $certificateNumber = $certificateNumber ?? ('CERT-CRS-' . now()->format('Ymd') . '-0001');
    $participantName = $user->full_name ?? $user->name ?? '-';
    $participantNameLength = mb_strlen(trim($participantName));
    $participantNameFontSize = match (true) {
        $participantNameLength >= 40 => '48px',
        $participantNameLength >= 34 => '54px',
        $participantNameLength >= 28 => '60px',
        default => '70px',
    };
    $formatTopicStatus = static function (?string $status): string {
        return match ($status) {
            'Present' => 'Hadir',
            default => $status ?: 'Online',
        };
    };

    $toDataUri = static function (?string $path): ?string {
        if (!$path || !file_exists($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    };

    $background = $background ?? $toDataUri(public_path('images/certificate/front-bg.png'));
    $backgroundBack = $backgroundBack ?? $toDataUri(public_path('images/certificate/back-bg.png'));
    $logo = $logo ?? $toDataUri(public_path('images/logo.png'));
    $beatrixAntiquaFontPath = public_path('font/beatrix-antiqua.regular.ttf');
    $faithFontPath = public_path('font/faith.ttf');
    $toFileUri = static function (string $path): string {
        return 'file:///' . ltrim(str_replace('\\', '/', $path), '/');
    };
    $signature1 = $signature1 ?? $toDataUri(public_path('images/certificate/sign1.jpeg'));
    $signature2 = $signature2 ?? $toDataUri(public_path('images/certificate/sign2.jpeg'));
    $signatures = $signatures ?? [
        [
            'name' => 'Dr. Timotius Hardono',
            'title' => 'Gembala GBI MS CK5',
            'image' => $signature1,
        ],
        [
            'name' => 'Dr. Indrawati Kabul',
            'title' => 'Rektor Miracle Institute',
            'image' => $signature2,
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat - {{ $course->title ?? '-' }}</title>
    <style>
        @page { margin: 0; size: 297mm 210mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @if(file_exists($beatrixAntiquaFontPath))
        @font-face {
            font-family: 'beatrix antiqua';
            src: url('{{ $toFileUri($beatrixAntiquaFontPath) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @endif

        @if(file_exists($faithFontPath))
        @font-face {
            font-family: 'faith';
            src: url('{{ $toFileUri($faithFontPath) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @endif

        html,
        body {
            width: 297mm;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
        }

        .page {
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

        .background img {
            width: 100%;
            height: 100%;
        }

        .wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 297mm;
            height: 210mm;
        }

        .content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 85%;
            text-align: center;
        }

        .page-break {
            page-break-before: always;
        }

        .participant-name {
            font-family: 'faith', 'Brush Script MT', 'Lucida Handwriting', cursive;
            font-size: 70px;
            color: #1f5684;
            font-weight: normal;
            font-style: normal;
            line-height: 0.64;
            text-shadow:
                -1px -1px 0 #f5cd72,
                1px -1px 0 #f5cd72,
                -1px 1px 0 #f5cd72,
                1px 1px 0 #f5cd72;
            border-bottom: 1px solid #041321;
            display: inline-block;
            margin-top: -4px;
            padding-bottom: 4px;
            min-width: 520px;
        }

        .certificate-logo {
            margin-bottom: 10px;
            text-align: center;
        }

        .certificate-logo img {
            width: 50mm;
            height: auto;
        }

        .certificate-title {
            font-family: 'beatrix antiqua', 'Times New Roman', serif;
            font-size: 46px;
            color: #0d3b66;
            line-height: 0.64;
            font-weight: normal;
            font-style: normal;
            letter-spacing: 3px;
            margin: 5px 0;
        }

        .second-content {
            padding: 20mm 20mm 24mm 20mm;
            height: 210mm;
            position: relative;
        }

        .second-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -80%);
            width: 150mm;
            text-align: center;
            opacity: 0.13;
            z-index: 0;
        }

        .second-watermark img {
            width: 100%;
            height: auto;
        }

        .second-title {
            text-align: center;
            font-size: 24px;
            color: #000;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .material-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 14px;
        }

        .material-table {
            width: 100%;
            min-height: 100mm;
            border-collapse: collapse;
            font-size: 16px;
        }

        .material-table th,
        .material-table td {
            border: 1px solid #000;
            padding: 8px 10px;
        }

        .material-table th {
            background: #f2f2f2;
            text-align: center;
        }

        .material-table td {
            background: #ffffff;
            text-align: center;
        }

        .material-table td.no,
        .material-table td.duration {
            text-align: center;
            width: 15%;
        }

        .material-table td.topic {
            width: 60%;
        }

        .signatures-grid {
            width: 60%;
            max-width: 180mm;
            margin: 16px auto 0 auto;
            text-align: center;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .signatures-grid.bottom {
            position: absolute;
            left: 50%;
            bottom: 16mm;
            transform: translateX(-50%);
        }

        .signature-item {
            width: 50%;
            vertical-align: top;
            padding: 0 5mm;
            font-size: 16px;
        }

        .signature-date {
            font-size: 20px;
            color: #000;
            margin: 0 0 6px 0;
            font-weight: bold;
        }
        
        .signature-image {
            height: 16mm;
            text-align: center;
            margin-bottom: 4px;
        }

        .signature-image.has-signature {
            height: 16mm;
        }

        .signature-image.no-signature {
            height: 10mm;
            padding-top: 0;
            margin-bottom: 2px;
        }

        .signature-image img {
            max-height: 20mm;
            max-width: 100%;
            width: auto;
            height: auto;
        }

        .signature-name {
            font-size: 20px;
            font-weight: bold;
            color: #0d3b66;
            margin: 0;
            line-height: 42px;
        }

        .signature-title {
            font-size: 18px;
            color: #000;
            margin: 0;
        }

        .second-content > *:not(.second-watermark) {
            position: relative;
            z-index: 1;
        }

        .assessment-label {
            margin-top: 14px;
            font-size: 13px;
            text-transform: uppercase;
            color: #4b5563;
            letter-spacing: 0.6px;
            text-align: left;
        }

        .assessment-value {
            margin-top: 4px;
            font-size: 22px;
            font-weight: bold;
            color: #0d3b66;
            line-height: 1.2;
            text-align: left;
        }

        .assessment-note {
            margin-top: 4px;
            font-size: 12px;
            color: #374151;
            line-height: 1.4;
            text-align: left;
        }

        .achievement-message {
            margin-top: 14px;
            font-size: 15px;
            line-height: 1.6;
            text-align: left;
            color: #1f2937;
        }
    </style>
</head>
<body>
    <div class="page">
        @if($background)
            <div class="background">
                <img src="{{ $background }}" alt="">
            </div>
        @endif

        <div class="wrapper">
            <div class="content">
                <div class="certificate-logo">
                    @if($logo)
                        <img src="{{ $logo }}" alt="Miracle Institute Logo">
                    @endif
                </div>
                <h2 class="certificate-title">SERTIFIKAT</h2>
                <p style="font-size: 20px; color: #1f5684; letter-spacing: 3px;">PENCAPAIAN</p>
                <p style="font-size: 16px; color: #000; font-weight: bold; margin: 6px 0;">No. {{ $certificateNumber }}</p>
                <p style="font-size: 16px; color: #000;">Sertifikat ini dengan bangga diberikan kepada</p>
                <div class="participant-name">
                    <span style="font-size: {{ $participantNameFontSize }};">{{ $participantName }}</span>
                </div>
                <div style="font-size: 18px; color: #000; margin: 10px 0; max-width: 80%; margin-left: auto; margin-right: auto;">
                    Telah mengikuti dan menyelesaikan <strong>{{ $course->title ?? '-' }}</strong>
                    sebagai <strong>Peserta</strong> dalam program pemuridan online MIRACLE INSTITUTE.
                </div>
                <p style="font-size: 20px; color: #000; margin: 20px 0 0 0;">
                    <strong>{{ $issuedAt->locale('id')->isoFormat('D MMMM Y') }}</strong>
                </p>
                <table class="signatures-grid">
                    <tr>
                        @foreach($signatures as $signer)
                            <td class="signature-item">
                                <div class="signature-image {{ !empty($signer['image']) ? 'has-signature' : 'no-signature' }}">
                                    @if(!empty($signer['image']))
                                        <img src="{{ $signer['image'] }}" alt="Signature">
                                    @endif
                                </div>
                                <div class="signature-line">
                                    <p class="signature-name">{{ $signer['name'] ?? '-' }}</p>
                                </div>
                                <p class="signature-title">{{ $signer['title'] ?? '-' }}</p>
                            </td>
                        @endforeach
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="page-break page">
        @if($backgroundBack)
            <div class="background">
                <img src="{{ $backgroundBack }}" alt="">
            </div>
        @endif

        <div class="wrapper">
            <div class="second-content">
                @if($logo)
                    <div class="second-watermark">
                        <img src="{{ $logo }}" alt="">
                    </div>
                @endif

                <h1 class="second-title">
                    Ringkasan Materi Pembelajaran
                </h1>

                <p class="material-title">
                    Materi untuk {{ ($course->title ?? '-') }}.
                </p>

                <table class="material-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Sesi</th>
                            <th>Hadir/Online</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backTopics ?? [] as $index => $topic)
                            <tr>
                                <td class="no">{{ $index + 1 }}</td>
                                <td class="topic">{{ $topic['topic_name'] ?? '-' }}</td>
                                <td class="duration">{{ $formatTopicStatus($topic['topic_status'] ?? null) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center;">Belum ada materi tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="achievement-message">
                    Selamat, <strong>{{ $participantName }}</strong>. Anda telah berhasil menyelesaikan
                    <strong>{{ $course->title ?? '-' }}</strong>
                    @if($achievementSummary['assessment_score'] !== null)
                        dengan nilai asesmen <strong>{{ $achievementSummary['assessment_score'] }}</strong>.
                    @else
                        dan memenuhi persyaratan kelulusan topik pembelajaran.
                    @endif
                </div>
                
                <table class="signatures-grid bottom">
                    <tr>
                        @foreach($signatures as $signer)
                            <td class="signature-item">
                                <p class="signature-date">{{ $issuedAt->locale('id')->isoFormat('D MMMM Y') }}</p>
                                <div class="signature-image {{ !empty($signer['image']) ? 'has-signature' : 'no-signature' }}">
                                    @if(!empty($signer['image']))
                                        <img src="{{ $signer['image'] }}" alt="Signature">
                                    @endif
                                </div>
                                <div class="signature-line">
                                    <p class="signature-name">{{ $signer['name'] ?? '-' }}</p>
                                </div>
                                <p class="signature-title">{{ $signer['title'] ?? '-' }}</p>
                            </td>
                        @endforeach
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
