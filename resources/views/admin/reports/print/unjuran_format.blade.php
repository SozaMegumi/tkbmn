<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lampiran 3 - Unjuran PBMT {{ $data['year'] ?? '' }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px; 
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .header-title { font-weight: bold; text-align: left; margin-bottom: 15px; font-size: 14px;}
        .top-info table { width: 100%; margin-bottom: 15px; }
        .top-info td { padding: 4px; vertical-align: top; }
        
        table.calc-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.calc-table th, table.calc-table td { border: 1px solid #000; padding: 8px; vertical-align: middle; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .bg-grey { background-color: #f2f2f2 !important; }

        .signature-section { width: 100%; margin-top: 40px; page-break-inside: avoid; }
        .signature-box { width: 33%; display: inline-block; vertical-align: top; }

        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #000; color: #fff; cursor: pointer; border-radius: 5px;">Cetak Sekarang</button>
        <button onclick="window.close()" style="padding: 10px 20px; margin-left: 10px; cursor: pointer; border-radius: 5px;">Tutup</button>
    </div>

    <div style="text-align: right; font-weight: bold; font-size: 14px; margin-bottom: 20px;">Lampiran 3</div>

    <div class="header-title">
        UNJURAN PERMOHONAN PERUNTUKAN<br>
        PROGRAM BANTUAN MAKANAN TAMBAHAN (PBMT)<br>
        TABIKA DAN TASKA KEMAS
    </div>

    <p>Adalah saya ingin membuat permohonan peruntukan Bantuan Makanan Tambahan (Bahan Basah dan Kering) bagi kelas TABIKA / TASKA seperti berikut:-</p>

    <div class="top-info">
        <table>
            <tr><td width="3%">[1]</td><td width="30%">Nama TABIKA/TASKA</td><td>: <strong>TABIKA KEMAS BUSTANUL MAKWAN NAJWA</strong></td></tr>
            <tr><td></td><td>Kod SMPK [TABIKA]</td><td>:<strong>B4AH007PRA</strong></td></td></tr>
            <tr><td>[2]</td><td>Nombor Akaun Bank</td><td>:<strong>7011527158</strong></td></tr>
            <tr><td>[3]</td><td>Nama/Alamat Bank</td><td>:<strong>C.I.M.B BANK SDN BHB KAPAR</strong></td></tr>
            <tr><td>[4]</td><td>No eVendor</td><td>:<strong>6000298484</strong></td></tr>
        </table>
    </div>

    <p>Kenyataan unjuran permohonan peruntukan Program Bantuan Makanan Tambahan adalah seperti berikut:</p>

    <table class="calc-table">
        <tr>
            <td colspan="2" class="text-bold">Baki PBMT Sehingga 31 Disember {{ ($data['year'] ?? 2026) - 1 }}<br><small>[ Baki dalam Akaun Bank dan Tunai di Tangan ]</small></td>
            <td width="20%" class="text-right text-bold">RM {{ number_format($data['baki_lepas'] ?? 0, 2) }}</td>
        </tr>
        
        <tr>
            <td colspan="3" class="text-bold bg-grey">{{ $data['phase'] ?? 'FASA 1' }}</td>
        </tr>

        @foreach($data['rows'] ?? [] as $row)
        <tr>
            <td width="25%" class="text-bold">{{ $row['month'] ?? '' }}</td>
            <td class="text-center">
                {{ number_format($row['kadar'] ?? 3.00, 2) }}<br><small>Kadar</small>
                &nbsp;&nbsp;X&nbsp;&nbsp;
                {{ $row['hari'] ?? 0 }}<br><small>Jumlah Hari Persekolahan</small>
                &nbsp;&nbsp;X&nbsp;&nbsp;
                {{ $row['kanak'] ?? 0 }}<br><small>Jumlah Kanak-kanak</small>
            </td>
            <td class="text-right text-bold">
                {{ number_format($row['jumlah_bulan'] ?? 0, 2) }}
            </td>
        </tr>
        @endforeach
        
        <tr>
            <td colspan="2" class="text-bold text-right bg-grey">Jumlah Keseluruhan [RM]</td>
            <td class="text-right text-bold bg-grey">{{ number_format($data['jumlah_keseluruhan'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td colspan="2" class="text-bold text-right">Tolak Baki PBMT Sehingga 31 Disember {{ ($data['year'] ?? 2026) - 1 }}</td>
            <td class="text-right text-bold">{{ number_format($data['baki_lepas'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td colspan="2" class="text-bold text-right bg-grey">Jumlah Keseluruhan [RM] Penyaluran ke Akaun</td>
            <td class="text-right text-bold bg-grey" style="font-size: 15px;">{{ number_format($data['jumlah_bersih'] ?? 0, 2) }}</td>
        </tr>
    </table>

    <p style="text-align: justify;">Kenyataan yang diberikan adalah benar berdasarkan senarai kanak-kanak mengikut pendaftaran kanak-kanak serta kadar semasa tertakluk kepada tatacara kewangan yang berkuat kuasa.</p>

    <div class="signature-section">
        <div class="signature-box">
            [4] Tandatangan :<br><br><br><br>
            .....................................................<br>
            [5] Nama PM : <br>
            [Nama Pendidik Masyarakat TABIKA]<br><br>
            [6] Tarikh : ...................................
        </div>
        <div class="signature-box">
            DISEMAK OLEH :<br><br><br><br>
            [7] Tandatangan : ...............................<br><br>
            Cop Nama/Jawatan :<br>
            Penolong Pegawai Pendidikan Awal Kanak-kanak
        </div>
        <div class="signature-box">
            DISAHKAN OLEH :<br><br><br><br>
            [8] Tandatangan : ...............................<br><br>
            Cop Nama/Jawatan :<br>
            [Pegawai KEMAS Bahagian/Daerah]
        </div>
    </div>
</body>
</html>