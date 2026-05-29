<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kad Laporan KEMAS</title>
    <style>
        /* Gaya asas untuk mencetak Kertas A4 */
        body { font-family: 'Arial', sans-serif; background-color: #f0f2f5; color: #000; margin: 0; padding: 20px; font-size: 13px; }
        .page-container { 
            width: 210mm; min-height: 297mm; /* Saiz A4 */
            margin: 0 auto 20px auto; padding: 15mm; 
            background: #fff; box-sizing: border-box; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        /* Header Tabika */
        .header { text-align: center; margin-bottom: 25px; }
        .header h3 { margin: 0 0 5px 0; font-size: 18px; font-weight: bold; text-decoration: underline; }
        .header h4 { margin: 0; font-size: 16px; font-weight: bold; text-transform: uppercase; }
        
        /* Maklumat Murid */
        .info-table { width: 100%; margin-bottom: 20px; font-weight: bold; }
        .info-table td { padding: 6px 0; vertical-align: bottom; }
        .dash-line { border-bottom: 1px dashed #000; padding-left: 5px; }
        
        /* Jadual Markah */
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 10px; text-align: center; }
        table.data-table th { background-color: #e2e8f0; font-weight: bold; }
        table.data-table td.text-left { text-align: left; padding-left: 15px; }
        
        /* Ruangan Ulasan */
        .ulasan-title { font-weight: bold; margin-bottom: 8px; text-decoration: underline; }
        .ulasan-box { width: 100%; min-height: 80px; border: 1px solid #000; padding: 12px; box-sizing: border-box; margin-bottom: 40px; font-style: italic; }
        
        /* Tandatangan */
        .signature-area { width: 100%; margin-top: 40px; display: flex; justify-content: space-between; }
        .sig-box { width: 45%; text-align: center; }
        .sig-line { border-top: 1px dashed #000; margin-bottom: 5px; width: 80%; margin-left: auto; margin-right: auto; }

        /* Fungsi Butang Cetak */
        .print-btn-container { text-align: center; margin-bottom: 30px; }
        .btn-print { background: #0d6efd; color: #fff; padding: 12px 25px; border: none; border-radius: 50px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 6px rgba(13,110,253,0.3); }
        .btn-print:hover { background: #0b5ed7; }

        /* Arahan Khas Pencetak (Hanya berfungsi bila Print ditekan) */
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .page-container { width: 100%; box-shadow: none; margin: 0; padding: 0; border: none; }
            .page-break { page-break-after: always; } /* Ini pastikan setiap murid dapat 1 muka surat A4 berasingan */
            .d-print-none { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="print-btn-container d-print-none">
        <button class="btn-print" onclick="window.print()">
            🖨️ Cetak Semua Kad Laporan (Print)
        </button>
        <p style="margin-top: 10px; color: #666;">Tip: Pilih "Save as PDF" di tetingkap cetakan untuk menyimpan salinan ke dalam komputer anda.</p>
    </div>

    @forelse($students as $student)
    <div class="page-container {{ !$loop->last ? 'page-break' : '' }}">
        
        <div class="header">
            <h3>BORANG PENTAKSIRAN MURID</h3>
            <h4>{{ $classroom->class_name ?? 'TABIKA KEMAS BUSTANUL MAKWAN NAJWA' }}</h4>
        </div>

        <table class="info-table">
            <tr>
                <td width="15%">NAMA MURID</td>
                <td width="2%">:</td>
                <td width="53%" class="dash-line">{{ strtoupper($student->student_name) }}</td>
                <td width="10%" style="padding-left: 15px;">UMUR</td>
                <td width="2%">:</td>
                <td width="18%" class="dash-line">{{ \Carbon\Carbon::parse($student->dob)->age ?? '6' }} TAHUN</td>
            </tr>
            <tr>
                <td>NO. MYKID</td>
                <td>:</td>
                <td class="dash-line">{{ $student->mykid }}</td>
                <td style="padding-left: 15px;">SESI</td>
                <td>:</td>
                <td class="dash-line">{{ $assessment->title ?? $assessment->name ?? date('Y') }}</td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2" width="6%">BIL</th>
                    <th rowspan="2" width="49%">TUNJANG / KOMPONEN</th>
                    <th colspan="3">TAHAP PENGUASAAN (TP)</th>
                </tr>
                <tr>
                    <th width="15%">TP 1</th>
                    <th width="15%">TP 2</th>
                    <th width="15%">TP 3</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subjects as $index => $subject)
                    @php
                        // Semak markah murid ini dari database
                        $sId = $student->student_id ?? $student->id;
                        $subId = $subject->subject_id ?? $subject->id;
                        $grade = $results[$sId][$subId] ?? '';
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-left fw-bold">TUNJANG {{ strtoupper($subject->komponen ?? $subject->subject_name) }}</td>
                        
                        <td style="font-weight: bold; font-size: 16px;">{{ $grade == '1' ? '/' : '' }}</td>
                        <td style="font-weight: bold; font-size: 16px;">{{ $grade == '2' ? '/' : '' }}</td>
                        <td style="font-weight: bold; font-size: 16px;">{{ $grade == '3' ? '/' : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="ulasan-title">ULASAN GURU :</div>
        <div class="ulasan-box">
            {{ $teacherRemarks[$student->student_id ?? $student->id] ?? 'Tiada ulasan direkodkan.' }}
        </div>

        <div class="signature-area">
            <div class="sig-box">
                <div class="sig-line"></div>
                Tandatangan Guru Tabika<br><br>
                Nama: <b>{{ auth()->guard('teacher')->user()->full_name ?? '.....................................' }}</b>
            </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                Tandatangan Ibu Bapa / Penjaga<br><br>
                Nama: ...........................................
            </div>
        </div>

    </div>
    @empty
        <div style="text-align: center; padding: 50px;">
            <h3>Tiada data pelajar ditemui untuk kelas ini.</h3>
        </div>
    @endforelse

</body>
</html>