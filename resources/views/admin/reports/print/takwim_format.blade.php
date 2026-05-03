<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Takwim {{ $data['year'] ?? '' }}</title>
    <style>
        /* Force Landscape Printing */
        @page { size: landscape; margin: 15mm; }

        /* Increased Base Font Size */
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px; 
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            /* THIS FORCES COLORS TO SHOW IN THE BROWSER PREVIEW */
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        h3 { 
            text-align: center; 
            font-size: 18px; 
            margin-bottom: 25px; 
            text-transform: uppercase; 
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        
        th, td {
            border: 1px solid #000; 
            padding: 12px 8px; 
            vertical-align: middle;
        }
        
        /* The specific colors */
        .bg-header { background-color: #fbd4b4 !important; font-weight: bold; text-align: center; font-size: 13px;}
        .bg-footer { background-color: #e6b8b7 !important; font-weight: bold; font-size: 14px;}
        
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        
        .notes-column { white-space: pre-line; text-align: left; vertical-align: top;}

        @media print {
            .no-print { display: none !important; }
            /* THIS FORCES COLORS TO STAY WHEN PRINTING TO PDF/PAPER */
             {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 12px 24px; background: #000; color: #fff; cursor: pointer; font-size: 16px; border: none; border-radius: 5px;">Cetak Sekarang</button>
        <button onclick="window.close()" style="padding: 12px 24px; margin-left: 10px; cursor: pointer; font-size: 16px; border: 1px solid #000; border-radius: 5px;">Tutup</button>
    </div>

    <h3>TAKWIM SESI PERSEKOLAHAN TAHUN {{ $data['year'] ?? '' }}</h3>

    <table>
        <thead>
            <tr>
                <th class="bg-header" rowspan="2" style="width: 5%;">BIL</th>
                <th class="bg-header" colspan="3">TABIKA</th>
                <th class="bg-header" colspan="3">TASKA</th>
            </tr>
            <tr>
                <th class="bg-header" style="width: 12%;">BULAN</th>
                <th class="bg-header" style="width: 15%;">HARI PERSEKOLAHAN</th>
                <th class="bg-header" style="width: 20%;">CATATAN</th>
                <th class="bg-header" style="width: 12%;">BULAN</th>
                <th class="bg-header" style="width: 15%;">HARI PERSEKOLAHAN</th>
                <th class="bg-header" style="width: 20%;">CATATAN</th>
            </tr>
        </thead>
        <tbody>
            <!-- Safety net: ?? [] prevents crash if 'rows' is empty -->
            @foreach($data['rows'] ?? [] as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center text-bold">{{ $row['month'] ?? '' }}</td>
                <td class="text-center">{{ $row['tabika_days'] ?? '' }}</td>
                <td class="notes-column">{{ $row['tabika_notes'] ?? '' }}</td>
                
                <td class="text-center text-bold">{{ $row['month'] ?? '' }}</td>
                <td class="text-center">{{ $row['taska_days'] ?? '' }}</td>
                <td class="notes-column">{{ $row['taska_notes'] ?? '' }}</td>
            </tr>
            @endforeach
            
            <tr>
                <td class="bg-footer text-bold" colspan="2" style="padding: 15px;">JUMLAH HARI</td>
                <!-- Safety net: ?? 0 prevents crash -->
                <td class="bg-footer text-center" style="color: darkred; font-size: 16px;">{{ $data['total_tabika_days'] ?? 0 }}</td>
                <td class="bg-footer"></td>
                <td class="bg-footer"></td>
                <td class="bg-footer text-center" style="color: darkred; font-size: 16px;">{{ $data['total_taska_days'] ?? 0 }}</td>
                <td class="bg-footer"></td>
            </tr>
            
        </tbody>
    </table>

</body>
</html>