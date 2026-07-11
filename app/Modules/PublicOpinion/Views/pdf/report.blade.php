<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $poll->title }} - Research Brief</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1a1a1a;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            font-size: 13px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #1a1a1a;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .logo-cell {
            width: 50%;
            vertical-align: middle;
        }
        .logo-img {
            height: 35px;
        }
        .brand-text {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: -0.5px;
        }
        .meta-cell {
            width: 50%;
            text-align: right;
            vertical-align: middle;
            font-size: 11px;
            color: #666;
        }
        .report-title {
            font-size: 22px;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 10px;
            color: #000;
        }
        .category-badge {
            display: inline-block;
            background: #f3f4f6;
            color: #374151;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 3px;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-top: 25px;
            margin-bottom: 10px;
            color: #111827;
        }
        .meta-grid {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meta-grid td {
            padding: 6px 10px;
            border: 1px solid #f3f4f6;
        }
        .meta-grid td.label {
            font-weight: bold;
            color: #4b5563;
            width: 30%;
            background: #fafafa;
        }
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
        }
        .stats-table th {
            background: #111827;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px 12px;
            font-size: 11px;
            text-transform: uppercase;
        }
        .stats-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .stats-table tr:nth-child(even) {
            background: #f9fafb;
        }
        .synthesis-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            padding: 20px;
            font-size: 12px;
            white-space: pre-wrap;
            color: #374151;
            font-family: Arial, sans-serif;
        }
        .footer {
            margin-top: 40px;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

    <!-- Branded Header -->
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if($logoBase64)
                    <img src="data:image/png;base64,{{ $logoBase64 }}" class="logo-img" alt="Metrica Logo">
                @else
                    <span class="brand-text">METRICA RESEARCH</span>
                @endif
            </td>
            <td class="meta-cell">
                METRICA RESEARCH INTELLIGENCE PLATFORM<br>
                CLASSIFICATION: PUBLIC ACCESS<br>
                Downloaded by: {{ $email }}
            </td>
        </tr>
    </table>

    <!-- Title and Category -->
    <div class="report-title">{{ $poll->title }}</div>
    <span class="category-badge">{{ $poll->category }}</span>

    <!-- Metadata Grid -->
    <div class="section-title">Research Parameters</div>
    <table class="meta-grid">
        <tr>
            <td class="label">Region covered</td>
            <td>{{ $poll->region ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Sample Size</td>
            <td>{{ number_format($poll->sample_size) }} respondents</td>
        </tr>
        <tr>
            <td class="label">Research Period</td>
            <td>{{ $poll->research_date ? $poll->research_date->format('M d, Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Release Date</td>
            <td>{{ $poll->release_date ? $poll->release_date->format('M d, Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Total System Downloads</td>
            <td>{{ number_format($poll->initial_downloads + $poll->download_count) }} times</td>
        </tr>
    </table>

    <!-- Methodology -->
    <div class="section-title">Methodology Statement</div>
    <div style="margin-bottom: 25px; color: #4b5563;">
        {{ $poll->methodology ?? 'No methodology detailed.' }}
    </div>

    <!-- Ranked Options Table -->
    <div class="section-title">Data Metrics & Ranked Distribution</div>
    <table class="stats-table">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Option / Cohort Name</th>
                <th style="text-align: right;">Vote Value</th>
                <th style="text-align: right;">Percentage Share</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalVotes = collect($poll->options)->sum('votes');
            @endphp
            @foreach($poll->options as $rank => $opt)
                @php
                    $pct = $totalVotes > 0 ? ($opt['votes'] / $totalVotes) * 100 : 0;
                @endphp
                <tr>
                    <td>Rank #{{ $rank + 1 }}</td>
                    <td style="font-weight: bold;">{{ $opt['name'] }}</td>
                    <td style="text-align: right;">{{ number_format($opt['votes']) }}</td>
                    <td style="text-align: right; font-weight: bold; color: #111827;">{{ round($pct, 1) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Synthesis text -->
    <div class="section-title">Executive Research Synthesis</div>
    <div class="synthesis-box">{!! nl2br(e($poll->ai_report)) !!}</div>

    <!-- Footer -->
    <div class="footer">
        &copy; {{ date('Y') }} Metrica Polls Ltd. All rights reserved. ISO 27001 Certified & GDPR Compliant.
    </div>

</body>
</html>
