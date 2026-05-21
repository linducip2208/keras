<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $slipNumber }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a1a; margin: 0; padding: 20px; }
        h1, h2, h3 { margin: 0; }
        .header { display: table; width: 100%; border-bottom: 3px solid #f97316; padding-bottom: 10px; margin-bottom: 20px; }
        .header-left { display: table-cell; vertical-align: top; }
        .header-right { display: table-cell; vertical-align: top; text-align: right; }
        .company-name { font-size: 18px; font-weight: bold; color: #f97316; }
        .slip-title { font-size: 14px; font-weight: bold; }
        .info-grid { display: table; width: 100%; margin-bottom: 15px; border: 1px solid #e5e5e5; }
        .info-row { display: table-row; }
        .info-cell { display: table-cell; padding: 6px 10px; border-bottom: 1px solid #f0f0f0; }
        .info-label { width: 140px; color: #666; }
        .section-title { background: #f97316; color: white; padding: 6px 10px; font-weight: bold; margin: 15px 0 0; }
        table.line-items { width: 100%; border-collapse: collapse; margin: 0; }
        table.line-items td { padding: 6px 10px; border-bottom: 1px solid #f0f0f0; }
        table.line-items td.label { width: 70%; }
        table.line-items td.amount { text-align: right; font-family: 'DejaVu Sans Mono', monospace; }
        .total-row td { font-weight: bold; background: #fef3e8; border-top: 2px solid #f97316; }
        .net-pay { background: #16a34a; color: white; padding: 12px; text-align: center; font-size: 16px; font-weight: bold; margin-top: 20px; }
        .footer { margin-top: 30px; padding-top: 10px; border-top: 1px solid #e5e5e5; color: #888; font-size: 9px; }
        .signatures { display: table; width: 100%; margin-top: 40px; }
        .sig-box { display: table-cell; width: 33%; text-align: center; vertical-align: top; padding: 0 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <div class="company-name">{{ $company->name }}</div>
            <div style="font-size:9px;color:#666">{{ $company->address }}</div>
            <div style="font-size:9px;color:#666">NPWP: {{ $company->npwp }}</div>
        </div>
        <div class="header-right">
            <div class="slip-title">SLIP GAJI</div>
            <div>{{ $slipNumber }}</div>
            <div style="font-size:10px;color:#666">Periode: {{ $periodLabel }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell info-label">Nomor Karyawan</div>
            <div class="info-cell">{{ $employee->employee_no }}</div>
            <div class="info-cell info-label">Tanggal Bergabung</div>
            <div class="info-cell">{{ optional($employee->join_date)->format('d M Y') ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell info-label">Nama Karyawan</div>
            <div class="info-cell">{{ $employee->full_name }}</div>
            <div class="info-cell info-label">Departemen</div>
            <div class="info-cell">{{ $employee->department?->name ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell info-label">Jabatan</div>
            <div class="info-cell">{{ $employee->jobTitle?->name ?? '-' }}</div>
            <div class="info-cell info-label">Status</div>
            <div class="info-cell">{{ ucfirst($employee->employment_status) }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell info-label">Status PTKP</div>
            <div class="info-cell">{{ str_replace('_', '/', $employee->ptkp_status) }}</div>
            <div class="info-cell info-label">Cabang</div>
            <div class="info-cell">{{ $employee->branch?->name ?? '-' }}</div>
        </div>
    </div>

    {{-- EARNINGS --}}
    <div class="section-title">A. PENDAPATAN</div>
    <table class="line-items">
        @foreach ($earnings as $c)
            <tr>
                <td class="label">{{ $c->component->name }}</td>
                <td class="amount">Rp {{ number_format($c->amount, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td class="label">Total Pendapatan (Bruto)</td>
            <td class="amount">Rp {{ number_format($item->total_earnings, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- DEDUCTIONS --}}
    <div class="section-title">B. POTONGAN</div>
    <table class="line-items">
        @foreach ($deductions as $c)
            <tr>
                <td class="label">{{ $c->component->name }}</td>
                <td class="amount">Rp {{ number_format($c->amount, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr>
            <td class="label">PPh 21 (TER 2024)</td>
            <td class="amount">Rp {{ number_format($item->pph_21, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">BPJS Karyawan (Kesehatan + JHT + JP)</td>
            <td class="amount">Rp {{ number_format($item->bpjs_employee, 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td class="label">Total Potongan</td>
            <td class="amount">Rp {{ number_format($item->total_deductions + $item->pph_21 + $item->bpjs_employee, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- NET PAY --}}
    <div class="net-pay">
        GAJI BERSIH (Diterima): Rp {{ number_format($item->net_pay, 0, ',', '.') }}
    </div>

    {{-- INFO BPJS Employer (untuk transparansi) --}}
    <div style="margin-top:15px;padding:10px;background:#f9f9f9;font-size:9px;color:#666;border-left:3px solid #f97316">
        <strong>BPJS Employer (tidak dipotong dari gaji):</strong> Rp {{ number_format($item->bpjs_employer, 0, ',', '.') }} —
        dibayar oleh perusahaan ke BPJSTK & BPJS Kesehatan.
    </div>

    {{-- ATTENDANCE STATS --}}
    <div style="margin-top:15px;font-size:10px">
        <strong>Statistik Kehadiran Periode Ini:</strong>
        Hari Kerja: {{ $item->working_days }} ·
        Hadir: {{ $item->present_days }} ·
        Cuti: {{ $item->leave_days }} ·
        Absen: {{ $item->absent_days }} ·
        Jam Lembur: {{ $item->overtime_hours }}h
    </div>

    <div class="signatures">
        <div class="sig-box">
            <div style="border-bottom:1px solid #1a1a1a;margin-bottom:5px;height:50px"></div>
            <div>Karyawan</div>
            <div style="font-weight:bold">{{ $employee->full_name }}</div>
        </div>
        <div class="sig-box">
            <div style="border-bottom:1px solid #1a1a1a;margin-bottom:5px;height:50px"></div>
            <div>HR Manager</div>
        </div>
        <div class="sig-box">
            <div style="border-bottom:1px solid #1a1a1a;margin-bottom:5px;height:50px"></div>
            <div>Finance</div>
        </div>
    </div>

    <div class="footer">
        <strong>Confidential.</strong> Slip ini berisi data gaji pribadi yang dilindungi UU PDP 27/2022.
        Dilarang menyebarluaskan tanpa izin. Generated by HRIS {{ now()->format('d M Y H:i') }}.
        Password PDF: NIK karyawan (16 digit).
    </div>
</body>
</html>
