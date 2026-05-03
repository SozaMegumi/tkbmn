<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rumusan Berkelompok {{ $data['year'] ?? '' }}</title>
    <style>
        @page { size: landscape; margin: 15mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000; background: #fff; margin: 0; padding: 0; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;}
        
        .header-title { font-weight: bold; text-align: center; margin-bottom: 20px; font-size: 14px; text-transform: uppercase;}
        .parlimen-text { font-weight: bold; text-transform: uppercase; margin-bottom: 10px; font-size: 12px;}
        
        table.calc-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; text-align: center; }
        table.calc-table th, table.calc-table td { border: 1px solid #000; padding: 10px 5px; vertical-align: middle; }
        table.calc-table th { background-color: #fbd4b4 !important; font-weight: bold; font-size: 12px; }
        
        .bg-grey { background-color: #e6b8b7 !important; font-weight: bold;}
        .text-bold { font-weight: bold; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .signature-section { width: 100%; margin-top: 50px; display: table; table-layout: fixed;}
        .signature-box { display: table-cell; width: 25%; text-align: center; vertical-align: bottom; font-weight: bold; padding: 0 10px;}
        .sig-line { border-bottom: 1px dotted #000; margin-bottom: 5px; height: 50px; }

        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #000; color: #fff; cursor: pointer;">Cetak Sekarang</button>
        <button onclick="window.close()" style="padding: 10px 20px; margin-left: 10px;">Tutup</button>
    </div>

    <div style="text-align: right; font-weight: bold; font-size: 12px; margin-bottom: 10px;">Lampiran 2</div>

    <div class="parlimen-text">PARLIMEN : {{ $data['parlimen'] ?? '' }}</div>

    <div class="header-title">
        BORANG RUMUSAN BERKELOMPOK<br>
        TABIKA DAN TASKA KEMAS NEGERI {{ $data['negeri'] ?? 'SELANGOR' }} TAHUN {{ $data['year'] ?? '' }}<br>
        {{ $data['phase'] ?? 'FASA 1' }}<br>
        (PENYALURAN KE AKAUN TABIKA/TASKA)
    </div>

    <table class="calc-table">
        <thead>
            <tr>
                <th width="5%">BIL</th>
                <th width="15%">KOD PEMBEKAL<br>(e-VENDOR)</th>
                <th width="25%">NAMA TABIKA/TASKA</th>
                <th width="15%">NAMA BANK</th>
                <th width="15%">NO. AKAUN</th>
                <th width="15%">JUMLAH PERMOHONAN (RM)</th>
                <th width="10%">CATATAN</th>
            </tr>
        </thead>
        <tbody>
            @php $count = 1; @endphp
            @foreach($data['rows'] ?? [] as $row)
            <tr>
                <td>{{ $count++ }}</td>
                <td>{{ $row['kod_vendor'] ?? '' }}</td>
                <td class="text-left">{{ $row['nama_tabika'] ?? '' }}</td>
                <td>{{ $row['nama_bank'] ?? '' }}</td>
                <td>{{ $row['no_akaun'] ?? '' }}</td>
                <td class="text-bold">{{ number_format($row['jumlah'] ?? 0, 2) }}</td>
                <td>{{ $row['catatan'] ?? '' }}</td>
            </tr>
            @endforeach
            
            <!-- Fill empty rows to always show exactly 5 rows total -->
            @for($i = $count; $i <= 5; $i++)
            <tr>
                <td>{{ $i }}</td>
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor

            <tr>
                <td colspan="5" class="bg-grey text-right" style="padding-right: 20px;">JUMLAH KESELURUHAN (RM)</td>
                <td class="bg-grey text-bold" style="font-size: 14px;">{{ number_format($data['jumlah_keseluruhan'] ?? 0, 2) }}</td>
                <td class="bg-grey"></td>
            </tr>
        </tbody>
    </table>

    <div style="font-weight: bold; font-style: italic; font-size: 11px;">
        Hanya LIMA (5) TABIKA sahaja dalam satu format ATAU tidak melebihi RM50,000
    </div>

    <div class="signature-section">
        <div class="signature-box">
            DISEDIAKAN OLEH [PT/PM KHAS]<br>
            <div class="sig-line"></div>
            Nama:<br>Jawatan:
        </div>
        <div class="signature-box">
            DISEMAK OLEH PPPAK<br>(PARLIMEN)<br>
            <div class="sig-line"></div>
            Nama:<br>Jawatan:
        </div>
        <div class="signature-box">
            DISAHKAN OLEH PKD<br><br>
            <div class="sig-line"></div>
            Nama:<br>Jawatan:
        </div>
        <div class="signature-box">
            DISEMAK<br>UNIT PAK NEGERI<br>
            <div class="sig-line"></div>
            Nama:<br>Jawatan:
        </div>
    </div>
</body>
</html>