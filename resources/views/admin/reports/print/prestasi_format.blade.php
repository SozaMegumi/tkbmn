<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Prestasi {{ $data['year'] ?? '' }}</title>
    <style>
        @page { size: landscape; margin: 10mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000; background: #fff; margin: 0; padding: 15px; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;}
        
        .header-title { font-weight: bold; text-align: center; margin-bottom: 20px; font-size: 13px; text-transform: uppercase;}
        .top-info { font-weight: bold; margin-bottom: 15px; font-size: 12px; }
        .top-info td { padding: 3px 10px 3px 0; }
        
        table.calc-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; text-align: center; }
        table.calc-table th, table.calc-table td { border: 1px solid #000; padding: 6px 4px; vertical-align: middle; }
        table.calc-table th { background-color: #fbd4b4 !important; font-weight: bold; font-size: 10px; }
        
        .bg-grey { background-color: #f2f2f2 !important; font-weight: bold;}
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .catatan-col { white-space: pre-line; text-align: left; font-size: 10px; }

        .signature-section { width: 100%; margin-top: 40px; page-break-inside: avoid; }
        .signature-box { width: 45%; display: inline-block; vertical-align: top; font-weight: bold;}

        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #000; color: #fff; cursor: pointer;">Cetak Sekarang</button>
        <button onclick="window.close()" style="padding: 10px 20px; margin-left: 10px;">Tutup</button>
    </div>

    <div style="text-align: right; font-weight: bold; font-size: 12px; margin-bottom: 10px;">MBMT/7/1/TBK.TSK</div>

    <div class="header-title">
        LAPORAN RUMUSAN PRESTASI PERUNTUKAN PERBELANJAAN MENGIKUT FASA<br>
        PROGRAM BANTUAN MAKANAN TAMBAHAN TABIKA/TASKA KEMAS TAHUN {{ $data['year'] ?? '' }}
    </div>

    <table class="top-info">
        <!-- Dynamically prints NAMA TABIKA or NAMA TASKA -->
        <tr><td width="20%">NAMA {{ $data['kategori'] ?? 'TABIKA' }}</td><td>: {{ $data['nama_tabika'] ?? '' }}</td></tr>
        <tr><td>PARLIMEN / DAERAH</td><td>: {{ $data['daerah'] ?? '' }}</td></tr>
        <tr><td>NEGERI</td><td>: {{ $data['negeri'] ?? '' }}</td></tr>
    </table>

    <table class="calc-table">
        <thead>
            <tr>
                <th rowspan="2" width="5%">FASA</th>
                <th rowspan="2" width="10%">BULAN</th>
                <th colspan="3">JUMLAH PERUNTUKAN DITERIMA (RM)<br><small>(ikut hari sekolah+koku+kanak-kanak)</small></th>
                <th colspan="3">JUMLAH PERBELANJAAN SEBENAR (RM)<br><small>(ikut hari persekolahan, bilangan kanak-kanak & resit bayaran pembekal)</small></th>
                <th rowspan="2" width="8%">BAKI PERUNTUKAN (RM)</th>
                <th rowspan="2" width="15%">CATATAN<br><small>[sila nyatakan cuti atau penutupan kelas akibat wabak/ Cuti Peristiwa 4 Hari]</small></th>
            </tr>
            <tr>
                <th width="7%">JUMLAH KANAK-KANAK</th>
                <th width="7%">BIL HARI PERSEKOLAHAN</th>
                <th width="9%">PERUNTUKAN DITERIMA (RM)</th>
                <th width="7%">JUMLAH KANAK-KANAK</th>
                <th width="7%">BIL HARI</th>
                <th width="9%">PERBELANJAAN SEBENAR (RM)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['rows'] ?? [] as $index => $row)
            <tr>
                @if($index === 0)
                <td rowspan="9" class="text-bold" style="font-size: 14px;">
                    {{ str_replace('FASA ', '', $data['phase'] ?? '1') }}
                </td>
                @endif
                <td class="text-left text-bold">{{ $row['label'] }}</td>
                <td>{{ $row['kanak_p'] ?: '' }}</td>
                <td>{{ $row['hari_p'] ?: '' }}</td>
                <td>{{ $row['peruntukan'] ? number_format($row['peruntukan'], 2) : '' }}</td>
                
                <td>{{ $row['kanak_b'] ?: '' }}</td>
                <td>{{ $row['hari_b'] ?: '' }}</td>
                <td>{{ $row['perbelanjaan'] ? number_format($row['perbelanjaan'], 2) : '' }}</td>
                
                <td class="text-bold">{{ number_format($row['baki'], 2) }}</td>
                <td class="catatan-col">{{ $row['catatan'] }}</td>
            </tr>
            @endforeach

            <tr>
                <td colspan="2" class="bg-grey text-right" style="padding-right:15px;">JUMLAH KESELURUHAN</td>
                <td class="bg-grey"></td>
                <td class="bg-grey">{{ $data['total_hari_peruntukan'] }}</td>
                <td class="bg-grey" style="color: darkred;">{{ number_format($data['jumlah_peruntukan_total'], 2) }}</td>
                <td class="bg-grey"></td>
                <td class="bg-grey">{{ $data['total_hari_perbelanjaan'] }}</td>
                <td class="bg-grey" style="color: darkred;">{{ number_format($data['jumlah_perbelanjaan_total'], 2) }}</td>
                <td class="bg-grey" style="font-size: 13px; color: darkred;">{{ number_format($data['jumlah_baki_total'], 2) }}</td>
                <td class="bg-grey text-left" style="font-size: 9px;">Baki peruntukan PBMT sahaja (Tunai di Tangan & Acc Bank)</td>
            </tr>
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            DISEDIAKAN OLEH :<br><br><br><br>
            .......................................................................<br>
            PENDIDIK MASYARAKAT TABIKA/TASKA<br><br>
            TARIKH : ...............................................<br><br>
            COP PM :
        </div>
        <div class="signature-box" style="float: right;">
            DISEMAK OLEH:<br><br><br><br>
            .......................................................................<br>
            PENOLONG PENDIDIKAN AWAL KANAK-KANAK (PPPAK)<br><br>
            TARIKH : ...............................................<br><br>
            COP PPPAK :
        </div>
    </div>
</body>
</html>