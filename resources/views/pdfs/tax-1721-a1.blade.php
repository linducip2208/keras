<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>1721-A1 - {{ $form_no }}</title>
<style>
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a1a1a; margin: 0; padding: 20px; }
    h1, h2, h3 { margin: 0; }
    .header { text-align: center; border-bottom: 2px solid #1a1a1a; padding-bottom: 10px; margin-bottom: 15px; }
    .h-title { font-size: 14px; font-weight: bold; }
    .h-sub { font-size: 11px; margin-top: 4px; }
    .h-no { font-size: 10px; margin-top: 4px; color: #555; }
    .grid { display: table; width: 100%; margin-bottom: 12px; }
    .row { display: table-row; }
    .cell { display: table-cell; padding: 4px 8px; vertical-align: top; }
    .label { width: 35%; color: #555; }
    .value { font-weight: 600; }
    .section-title { background: #1a1a1a; color: white; padding: 5px 10px; font-weight: bold; margin: 12px 0 0; font-size: 11px; }
    table.calc { width: 100%; border-collapse: collapse; margin: 0; }
    table.calc th { background: #eee; padding: 5px 8px; text-align: left; font-size: 9px; }
    table.calc td { padding: 5px 8px; border-bottom: 1px solid #eee; }
    table.calc td.num { text-align: right; font-family: 'DejaVu Sans Mono', monospace; }
    .total-row td { font-weight: bold; background: #f7f7f7; border-top: 2px solid #1a1a1a; }
    .signature { margin-top: 30px; display: table; width: 100%; }
    .sig-l, .sig-r { display: table-cell; width: 50%; vertical-align: top; padding: 0 10px; }
    .sig-box { border-bottom: 1px solid #1a1a1a; height: 50px; margin-top: 60px; margin-bottom: 5px; }
    .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #eee; color: #888; font-size: 8px; }
    .month-label { width: 50px; }
</style>
</head>
<body>

<div class="header">
    <div style="font-size:9px;color:#666;float:left">FORMULIR 1721 - A1</div>
    <div style="font-size:9px;color:#666;float:right">DEPARTEMEN KEUANGAN<br>DIREKTORAT JENDERAL PAJAK</div>
    <div style="clear:both"></div>
    <div class="h-title">BUKTI PEMOTONGAN PAJAK PENGHASILAN PASAL 21</div>
    <div class="h-sub">BAGI PEGAWAI TETAP ATAU PENERIMA PENSIUN ATAU TUNJANGAN HARI TUA/JAMINAN HARI TUA BERKALA</div>
    <div class="h-no">Nomor: {{ $form_no }} · Masa Pajak: Januari - Desember {{ $year }}</div>
</div>

<div class="section-title">A. IDENTITAS PEMOTONG</div>
<div class="grid">
    <div class="row">
        <div class="cell label">1. NPWP</div>
        <div class="cell value">{{ $company->npwp ?? '-' }}</div>
    </div>
    <div class="row">
        <div class="cell label">2. Nama</div>
        <div class="cell value">{{ $company->name }}</div>
    </div>
    <div class="row">
        <div class="cell label">3. Alamat</div>
        <div class="cell value">{{ $company->address ?? '-' }}</div>
    </div>
</div>

<div class="section-title">B. IDENTITAS PENERIMA PENGHASILAN</div>
<div class="grid">
    <div class="row">
        <div class="cell label">4. NPWP</div>
        <div class="cell value">{{ optional($employee)->npwp ? '***** (encrypted)' : '-' }}</div>
    </div>
    <div class="row">
        <div class="cell label">5. NIK</div>
        <div class="cell value">{{ $employee->nik ? '***** (encrypted)' : '-' }}</div>
    </div>
    <div class="row">
        <div class="cell label">6. Nama</div>
        <div class="cell value">{{ $employee->full_name }}</div>
    </div>
    <div class="row">
        <div class="cell label">7. Alamat</div>
        <div class="cell value">{{ $employee->ktp_address ?? $employee->current_address ?? '-' }}</div>
    </div>
    <div class="row">
        <div class="cell label">8. Status / Tanggungan</div>
        <div class="cell value">{{ $ptkp_status }}</div>
    </div>
    <div class="row">
        <div class="cell label">9. Jabatan</div>
        <div class="cell value">{{ optional($employee->jobTitle)->name ?? '-' }}</div>
    </div>
</div>

<div class="section-title">C. RINCIAN PENGHITUNGAN PPh PASAL 21 (TER 2024 — PMK 168/2023)</div>
<table class="calc">
    <thead>
        <tr>
            <th class="month-label">Bulan</th>
            <th>Penghasilan Bruto</th>
            <th>PTKP</th>
            <th>Kat. TER</th>
            <th>Tarif TER</th>
            <th>PPh 21 Dipotong</th>
        </tr>
    </thead>
    <tbody>
        @php $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des']; @endphp
        @for ($m = 1; $m <= 12; $m++)
            @php $calc = $calculations->firstWhere('period_month', $m); @endphp
            <tr>
                <td>{{ $months[$m-1] }}</td>
                <td class="num">{{ $calc ? 'Rp ' . number_format($calc->gross_basis, 0, ',', '.') : '-' }}</td>
                <td class="num">{{ $calc ? 'Rp ' . number_format($calc->ptkp_amount, 0, ',', '.') : '-' }}</td>
                <td>{{ $calc ? $calc->ter_category : '-' }}</td>
                <td class="num">{{ $calc ? number_format($calc->ter_rate, 4, ',', '.') . '%' : '-' }}</td>
                <td class="num">{{ $calc ? 'Rp ' . number_format($calc->pph_21_amount, 0, ',', '.') : '-' }}</td>
            </tr>
        @endfor
        <tr class="total-row">
            <td>TOTAL</td>
            <td class="num">Rp {{ number_format($annual_gross, 0, ',', '.') }}</td>
            <td colspan="3"></td>
            <td class="num">Rp {{ number_format($annual_tax, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

<div class="signature">
    <div class="sig-l">
        <div style="font-size:9px;color:#666">Penerima Penghasilan</div>
        <div class="sig-box"></div>
        <div style="font-weight:bold">{{ $employee->full_name }}</div>
    </div>
    <div class="sig-r" style="text-align:right">
        <div style="font-size:9px;color:#666">Pemotong Pajak (Pemberi Kerja)</div>
        <div class="sig-box"></div>
        <div style="font-weight:bold">{{ $company->name }}</div>
        <div style="font-size:9px;color:#666">{{ now()->format('d F Y') }}</div>
    </div>
</div>

<div class="footer">
    Dokumen ini digenerate oleh HRIS Indonesia. Sah digunakan untuk lampiran SPT Tahunan PPh Orang Pribadi (Form 1770).
    Verifikasi keaslian dapat melalui {{ config('app.url') }}/verify/1721-a1/{{ $form_no }}.
</div>

</body>
</html>
