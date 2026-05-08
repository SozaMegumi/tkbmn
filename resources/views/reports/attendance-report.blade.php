<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kehadiran</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .text-center { text-align: center; }
        .text-uppercase { text-transform: uppercase; }
        .font-bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .header-info { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2 class="text-center font-bold text-uppercase">Laporan Kehadiran Harian Murid</h2>
    
    <div class="header-info">
        <strong>Kelas:</strong> <span class="text-uppercase">{{ $classroom->class_name ?? 'N/A' }}</span><br>
        <strong>Guru:</strong> <span class="text-uppercase">{{ $teacher->name ?? 'N/A' }}</span><br>
        <strong>Tarikh:</strong> {{ \Carbon\Carbon::parse($date)->format('d / m / Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">Bil</th>
                <th style="width: 40%;">Nama Murid</th>
                <th style="width: 20%;">Status</th>
                <th style="width: 35%;">Catatan / Sebab</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                @php $status = $student->attendance->status ?? 'Belum Ditanda'; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-uppercase">{{ $student->student_name }}</td>
                    <td class="font-bold text-uppercase">{{ $status }}</td>
                    <td>{{ $student->attendance->reason ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>