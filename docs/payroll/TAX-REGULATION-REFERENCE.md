# Tax Regulation Reference

- Dasar: **PMK 168/2023** tentang tarif efektif PPh 21 (TER), berlaku Jan 2024.
- Implementasi: kategori A (TK/0, TK/1, K/0), B (TK/2, TK/3, K/1, K/2),
  C (K/3); rate per bracket dari tabel `ter_rates` per tahun.
- TER dikenakan langsung ke bruto bulanan. Biaya jabatan (5%, maks 500rb/bln)
  + iuran karyawan hanya untuk koreksi progresif tahunan (Masa Pajak Terakhir).
- PTKP default TK/0 Rp54.000.000 dari `ptkp_rules`.

> NEEDS LEGAL/ACCOUNTANT VALIDATION: bracket TER per tahun di seeder
> (`TerBracketBCSeeder`), perlakuan THR/bonus/gross-up, PPh 26 WNA,
> DTP, dan rekonsiliasi Desember. Jangan jadikan konstanta 2026 permanen —
> gunakan baris `ter_rates`/`ptkp_rules` per tahun.
