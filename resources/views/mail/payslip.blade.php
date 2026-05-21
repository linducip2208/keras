@component('mail::message')
# Slip Gaji {{ $slipNumber }}

Halo {{ $employee->full_name }},

Slip gaji Anda untuk periode ini sudah tersedia dan terlampir di email ini sebagai PDF.

**Password PDF:** {{ $passwordHint }}

Slip gaji bersifat **rahasia** sesuai UU PDP 27/2022. Jangan diteruskan ke pihak yang tidak berkepentingan.

@component('mail::button', ['url' => config('app.url') . '/admin/login'])
Buka Portal Karyawan
@endcomponent

Jika ada pertanyaan, hubungi tim HR Anda.

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
