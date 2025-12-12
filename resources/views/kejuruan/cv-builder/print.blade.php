<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV - {{ $data['full_name'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
            background: #fff;
        }

        .cv-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }

        /* Header */
        .cv-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }

        .cv-header h1 {
            font-size: 24pt;
            font-weight: 700;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .cv-header .contact-info {
            font-size: 10pt;
            color: #555;
        }

        .cv-header .contact-info span {
            margin: 0 10px;
        }

        .cv-header .links {
            margin-top: 8px;
            font-size: 10pt;
        }

        .cv-header .links a {
            color: #0066cc;
            text-decoration: none;
            margin: 0 10px;
        }

        /* Section */
        .cv-section {
            margin-bottom: 20px;
        }

        .cv-section h2 {
            font-size: 12pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid #999;
            padding-bottom: 5px;
            margin-bottom: 10px;
            color: #333;
        }

        /* Education & Experience Items */
        .cv-item {
            margin-bottom: 12px;
        }

        .cv-item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .cv-item-title {
            font-weight: 600;
            font-size: 11pt;
        }

        .cv-item-date {
            font-size: 10pt;
            color: #666;
            white-space: nowrap;
        }

        .cv-item-subtitle {
            font-size: 10pt;
            color: #555;
            font-style: italic;
        }

        .cv-item-description {
            font-size: 10pt;
            margin-top: 5px;
            text-align: justify;
        }

        /* Skills */
        .skills-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .skill-tag {
            background: #f0f0f0;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 10pt;
        }

        /* Portfolio */
        .portfolio-item {
            margin-bottom: 8px;
        }

        .portfolio-item strong {
            font-weight: 600;
        }

        .portfolio-item a {
            color: #0066cc;
            text-decoration: none;
            font-size: 10pt;
        }

        .portfolio-item p {
            font-size: 10pt;
            color: #666;
            margin-top: 2px;
        }

        /* Certifications */
        .cert-item {
            margin-bottom: 5px;
            font-size: 10pt;
        }

        .cert-item strong {
            font-weight: 600;
        }

        /* Languages */
        .languages-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .language-item {
            font-size: 10pt;
        }

        .language-item strong {
            font-weight: 600;
        }

        /* Print styles */
        @media print {
            body {
                background: #fff;
            }

            .cv-container {
                padding: 20px;
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }

            @page {
                margin: 15mm;
                size: A4;
            }
        }

        /* Print button */
        .print-controls {
            text-align: center;
            padding: 20px;
            background: #f5f5f5;
            margin-bottom: 20px;
        }

        .print-controls button {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 14pt;
            cursor: pointer;
            border-radius: 5px;
            margin: 0 10px;
        }

        .print-controls button:hover {
            background: #218838;
        }

        .print-controls button.secondary {
            background: #6c757d;
        }

        .print-controls button.secondary:hover {
            background: #545b62;
        }

        .print-controls p {
            margin-top: 10px;
            font-size: 10pt;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="print-controls no-print">
        <button onclick="window.print()">🖨️ Cetak / Simpan sebagai PDF</button>
        <button class="secondary" onclick="window.close()">✕ Tutup</button>
        <p>Pilih "Save as PDF" atau "Microsoft Print to PDF" pada dialog print untuk menyimpan sebagai file PDF.</p>
    </div>

    <div class="cv-container cv-builder">
        <!-- Header -->
        <div class="cv-header">
            <h1>{{ $data['full_name'] }}</h1>
            <div class="contact-info">
                @if(!empty($data['city']))
                    <span>📍 {{ $data['city'] }}</span>
                @endif
                <span>📞 {{ $data['phone'] }}</span>
                <span>✉️ {{ $data['email'] }}</span>
            </div>
            @if(!empty($data['linkedin']) || !empty($data['portfolio_link']))
                <div class="links">
                    @if(!empty($data['linkedin']))
                        <a href="{{ $data['linkedin'] }}">LinkedIn</a>
                    @endif
                    @if(!empty($data['portfolio_link']))
                        <a href="{{ $data['portfolio_link'] }}">Portfolio</a>
                    @endif
                </div>
            @endif
        </div>

        <!-- Profile Summary -->
        @if(!empty($data['profile_summary']))
            <div class="cv-section">
                <h2>Ringkasan Profil</h2>
                <p>{{ $data['profile_summary'] }}</p>
            </div>
        @endif

        <!-- Education -->
        @if(!empty($data['education']))
            <div class="cv-section">
                <h2>Pendidikan</h2>
                @foreach($data['education'] as $edu)
                    @if(!empty($edu['school']))
                        <div class="cv-item">
                            <div class="cv-item-header">
                                <span class="cv-item-title">{{ $edu['school'] }}</span>
                                <span class="cv-item-date">
                                    {{ $edu['year_start'] ?? '' }}{{ !empty($edu['year_start']) && !empty($edu['year_end']) ? ' - ' : '' }}{{ $edu['year_end'] ?? '' }}
                                </span>
                            </div>
                            <div class="cv-item-subtitle">
                                {{ $edu['major'] ?? '' }}
                                @if(!empty($edu['gpa']))
                                    | IPK: {{ $edu['gpa'] }}
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Work Experience -->
        @if(!empty($data['experience']))
            <div class="cv-section">
                <h2>Pengalaman Kerja</h2>
                @foreach($data['experience'] as $exp)
                    @if(!empty($exp['company']))
                        <div class="cv-item">
                            <div class="cv-item-header">
                                <span class="cv-item-title">{{ $exp['position'] ?? 'Position' }}</span>
                                <span class="cv-item-date">
                                    {{ $exp['year_start'] ?? '' }}{{ !empty($exp['year_start']) && !empty($exp['year_end']) ? ' - ' : '' }}{{ $exp['year_end'] ?? '' }}
                                </span>
                            </div>
                            <div class="cv-item-subtitle">{{ $exp['company'] }}</div>
                            @if(!empty($exp['description']))
                                <div class="cv-item-description">{{ $exp['description'] }}</div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Skills -->
        @if(!empty($data['skills']))
            <div class="cv-section">
                <h2>Keahlian</h2>
                <div class="skills-list">
                    @foreach($data['skills'] as $skill)
                        @if(!empty($skill))
                            <span class="skill-tag">{{ $skill }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Portfolio -->
        @if(!empty($data['portfolio']))
            <div class="cv-section">
                <h2>Portofolio</h2>
                @foreach($data['portfolio'] as $proj)
                    @if(!empty($proj['title']))
                        <div class="portfolio-item">
                            <strong>{{ $proj['title'] }}</strong>
                            @if(!empty($proj['link']))
                                - <a href="{{ $proj['link'] }}">{{ $proj['link'] }}</a>
                            @endif
                            @if(!empty($proj['description']))
                                <p>{{ $proj['description'] }}</p>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Certifications -->
        @if(!empty($data['certifications']))
            <div class="cv-section">
                <h2>Sertifikat & Pelatihan</h2>
                @foreach($data['certifications'] as $cert)
                    @if(!empty($cert['name']))
                        <div class="cert-item">
                            <strong>{{ $cert['name'] }}</strong>
                            @if(!empty($cert['issuer']))
                                - {{ $cert['issuer'] }}
                            @endif
                            @if(!empty($cert['year']))
                                ({{ $cert['year'] }})
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Languages -->
        @if(!empty($data['languages']))
            <div class="cv-section">
                <h2>Bahasa</h2>
                <div class="languages-list">
                    @foreach($data['languages'] as $lang)
                        @if(!empty($lang['name']))
                            <div class="language-item">
                                <strong>{{ $lang['name'] }}</strong>
                                @if(!empty($lang['level']))
                                    - {{ $lang['level'] }}
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <style>
        /* Print template: force header elements to black */
        .cv-builder h1,
        .cv-builder h2,
        .cv-builder h3,
        .cv-builder h4,
        .cv-builder h5,
        .cv-builder .cv-header,
        .cv-builder .cv-header * {
            color: #000 !important;
        }
    </style>

    <script>
        // Auto focus print dialog
        window.onload = function () {
            // Wait a moment for styles to load
            setTimeout(function () {
                // Focus the window
                window.focus();
            }, 500);
        };
    </script>
</body>

</html>