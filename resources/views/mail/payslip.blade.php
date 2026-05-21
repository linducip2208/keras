<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Slip Gaji {{ $slipNumber }}</title>
<style>
    body { font-family: -apple-system, sans-serif; max-width: 600px; margin: 0 auto; padding: 24px; color: #1a1a1a; line-height: 1.6; }
    .header { background: #f97316; color: white; padding: 16px 24px; border-radius: 8px 8px 0 0; }
    .body { background: white; padding: 24px; border: 1px solid #e5e5e5; border-top: 0; }
    .button { display: inline-block; background: #f97316; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; margin: 16px 0; }
    .footer { color: #888; font-size: 12px; margin-top: 16px; }
    .password-hint { background: #fff7ed; border-left: 3px solid #f97316; padding: 12px; margin: 16px 0; border-radius: 4px; }
</style>
</head>
<body>
<div class="header">
    <h2 style="margin:0">Slip Gaji {{ $slipNumber }}</h2>
</div>
<div class="body">
    <p>Halo <strong>{{ $employee->full_name }}</strong>,</p>
    <p>Slip gaji Anda untuk periode ini sudah tersedia. PDF terlampir di email ini.</p>

    <div class="password-hint">
        <strong>🔐 Password PDF:</strong> {{ $passwordHint }}
    </div>

    <p>Slip gaji bersifat <strong>rahasia</strong> sesuai UU PDP 27/2022. Jangan diteruskan ke pihak yang tidak berkepentingan.</p>

    <a href="{{ config('app.url') }}/admin/login" class="button">Buka Portal Karyawan</a>

    <p>Jika ada pertanyaan, hubungi tim HR Anda.</p>

    <p>Terima kasih,<br>{{ config('app.name') }}</p>
</div>
<div class="footer">
    Email ini dikirim otomatis oleh sistem HRIS. Mohon tidak balas email ini.
</div>
</body>
</html>
