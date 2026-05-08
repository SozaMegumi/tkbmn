<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Card</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #cd2122; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 24px; font-weight: bold; color: #2194cd; margin: 0; }
        .subtitle { font-size: 14px; color: #555; }
        
        .student-info { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .student-info td { padding: 8px; border: 1px solid #ddd; }
        .label { background-color: #f8f9fa; font-weight: bold; width: 25%; }

        .grades-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .grades-table th, .grades-table td { border: 1px solid #000; padding: 10px; text-align: left; }
        .grades-table th { background-color: #2194cd; color: white; }
        
        .level-1 { color: #dc3545; font-weight: bold; }
        .level-2 { color: #fd7e14; font-weight: bold; }
        .level-3 { color: #198754; font-weight: bold; }

        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #777; }
        .signature-box { width: 100%; margin-top: 50px; }
        .signature-line { border-top: 1px solid #000; width: 200px; display: inline-block; padding-top: 5px; text-align: center; margin: 0 40px; }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="title">TABIKA KEMAS</h1>
        <p class="subtitle">Sistem Rekod Perkembangan Murid (KSPK)</p>
    </div>

    <table class="student-info">
        <tr>
            <td class="label">Nama Murid:</td>
            <td colspan="3"><strong>{{ $student->student_name }}</strong></td>
        </tr>
        <tr>
            <td class="label">MyKid:</td>
            <td>{{ $student->mykid ?? 'N/A' }}</td>
            <td class="label">Penggal / Ujian:</td>
            <td>{{ $assessment->name }}</td>
        </tr>
    </table>

    <table class="grades-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 35%;">Tunjang KSPK (Subject)</th>
                <th style="width: 25%;">Tahap Penguasaan</th>
                <th style="width: 35%;">Ulasan Guru (Remarks)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $index => $result)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $result->subject->subject_name ?? 'Unknown Subject' }}</td>
                    <td>
                        @if($result->mastery_level == 1) <span class="level-1">Tahap 1 (Belum Menguasai)</span>
                        @elseif($result->mastery_level == 2) <span class="level-2">Tahap 2 (Sedang Maju)</span>
                        @elseif($result->mastery_level == 3) <span class="level-3">Tahap 3 (Telah Menguasai)</span>
                        @else N/A @endif
                    </td>
                    <td>{{ $result->teacher_remarks ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <strong>Petunjuk Tahap Penguasaan KSPK:</strong><br>
        <span class="level-1">Tahap 1:</span> Belum menguasai kemahiran.<br>
        <span class="level-2">Tahap 2:</span> Sedang maju dan memerlukan bimbingan.<br>
        <span class="level-3">Tahap 3:</span> Telah menguasai kemahiran dengan baik.
    </div>

    <div class="signature-box" style="text-align: center;">
        <div class="signature-line">
            Tandatangan Guru
        </div>
        <div class="signature-line">
            Tandatangan Ibu Bapa
        </div>
    </div>

    <div class="footer">
        Dicetak oleh Sistem Pengurusan TABIKA KEMAS pada {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div>

</body>
</html>