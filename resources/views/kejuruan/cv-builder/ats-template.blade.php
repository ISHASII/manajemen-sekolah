<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV - {{ $data['full_name'] }}</title>
    <style>
        /* ATS-Friendly CV Template
         * This template is designed to be easily parsed by Applicant Tracking Systems
         * Features: Simple layout, standard fonts, minimal formatting
         */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
        }

        /* Header */
        header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        h1 {
            font-size: 22pt;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .contact-info {
            font-size: 10pt;
        }

        .contact-info span {
            margin: 0 8px;
        }

        /* Sections */
        section {
            margin-bottom: 18px;
        }

        h2 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #666;
            padding-bottom: 3px;
            margin-bottom: 10px;
        }

        /* Items */
        .item {
            margin-bottom: 12px;
        }

        .item-header {
            display: table;
            width: 100%;
        }

        .item-title {
            display: table-cell;
            font-weight: bold;
        }

        .item-date {
            display: table-cell;
            text-align: right;
            font-size: 10pt;
        }

        .item-subtitle {
            font-size: 10pt;
            font-style: italic;
            color: #333;
        }

        .item-description {
            font-size: 10pt;
            margin-top: 5px;
        }

        /* Skills List */
        .skills-list {
            font-size: 10pt;
        }

        /* Lists */
        ul {
            margin-left: 20px;
            font-size: 10pt;
        }

        li {
            margin-bottom: 3px;
        }

        /* Links */
        a {
            color: #000;
            text-decoration: underline;
        }

        /* Print */
        @media print {
            body {
                padding: 0;
            }

            @page {
                margin: 20mm;
                size: A4;
            }
        }
    </style>
</head>

<body class="cv-builder">
    <!-- HEADER -->
    <header>
        <h1>{{ $data['full_name'] }}</h1>
        <div class="contact-info">
            @if(!empty($data['city']))<span>{{ $data['city'] }}</span> |@endif
            <span>{{ $data['phone'] }}</span> |
            <span>{{ $data['email'] }}</span>
            @if(!empty($data['linkedin']))| <span><a href="{{ $data['linkedin'] }}">LinkedIn</a></span>@endif
            @if(!empty($data['portfolio_link']))| <span><a
            href="{{ $data['portfolio_link'] }}">Portfolio</a></span>@endif
        </div>
    </header>
    <style>
        /* ATS template header text color forced to black */
        .cv-builder h1,
        .cv-builder h2,
        .cv-builder h3,
        .cv-builder h4,
        .cv-builder h5,
        .cv-builder header,
        .cv-builder header * {
            color: #000 !important;
        }
    </style>

    <!-- PROFILE SUMMARY -->
    @if(!empty($data['profile_summary']))
        <section>
            <h2>Ringkasan Profil</h2>
            <p>{{ $data['profile_summary'] }}</p>
        </section>
    @endif

    <!-- EDUCATION -->
    @if(!empty($data['education']))
        <section>
            <h2>Pendidikan</h2>
            @foreach($data['education'] as $edu)
                @if(!empty($edu['school']))
                    <div class="item">
                        <div class="item-header">
                            <span class="item-title">{{ $edu['school'] }}</span>
                            <span
                                class="item-date">{{ $edu['year_start'] ?? '' }}{{ !empty($edu['year_start']) && !empty($edu['year_end']) ? ' - ' : '' }}{{ $edu['year_end'] ?? '' }}</span>
                        </div>
                        <div class="item-subtitle">
                            {{ $edu['major'] ?? '' }}@if(!empty($edu['gpa'])) | IPK: {{ $edu['gpa'] }}@endif
                        </div>
                    </div>
                @endif
            @endforeach
        </section>
    @endif

    <!-- WORK EXPERIENCE -->
    @if(!empty($data['experience']))
        <section>
            <h2>Pengalaman Kerja</h2>
            @foreach($data['experience'] as $exp)
                @if(!empty($exp['company']))
                    <div class="item">
                        <div class="item-header">
                            <span class="item-title">{{ $exp['position'] ?? '' }}</span>
                            <span
                                class="item-date">{{ $exp['year_start'] ?? '' }}{{ !empty($exp['year_start']) && !empty($exp['year_end']) ? ' - ' : '' }}{{ $exp['year_end'] ?? '' }}</span>
                        </div>
                        <div class="item-subtitle">{{ $exp['company'] }}</div>
                        @if(!empty($exp['description']))
                            <div class="item-description">{{ $exp['description'] }}</div>
                        @endif
                    </div>
                @endif
            @endforeach
        </section>
    @endif

    <!-- SKILLS -->
    @if(!empty($data['skills']))
        <section>
            <h2>Keahlian</h2>
            <p class="skills-list">{{ implode(' • ', array_filter($data['skills'])) }}</p>
        </section>
    @endif

    <!-- PORTFOLIO -->
    @if(!empty($data['portfolio']))
        <section>
            <h2>Portofolio</h2>
            <ul>
                @foreach($data['portfolio'] as $proj)
                    @if(!empty($proj['title']))
                        <li>
                            <strong>{{ $proj['title'] }}</strong>
                            @if(!empty($proj['link'])) - <a href="{{ $proj['link'] }}">{{ $proj['link'] }}</a>@endif
                            @if(!empty($proj['description']))<br>{{ $proj['description'] }}@endif
                        </li>
                    @endif
                @endforeach
            </ul>
        </section>
    @endif

    <!-- CERTIFICATIONS -->
    @if(!empty($data['certifications']))
        <section>
            <h2>Sertifikat & Pelatihan</h2>
            <ul>
                @foreach($data['certifications'] as $cert)
                    @if(!empty($cert['name']))
                        <li>
                            <strong>{{ $cert['name'] }}</strong>@if(!empty($cert['issuer'])) - {{ $cert['issuer'] }}@endif
                            @if(!empty($cert['year']))({{ $cert['year'] }})@endif
                        </li>
                    @endif
                @endforeach
            </ul>
        </section>
    @endif

    <!-- LANGUAGES -->
    @if(!empty($data['languages']))
        <section>
            <h2>Bahasa</h2>
            <ul>
                @foreach($data['languages'] as $lang)
                    @if(!empty($lang['name']))
                        <li>{{ $lang['name'] }}@if(!empty($lang['level'])) - {{ $lang['level'] }}@endif</li>
                    @endif
                @endforeach
            </ul>
        </section>
    @endif
</body>

</html>