# BPJS Regulation Reference

- Kesehatan: 4% pemberi kerja + 1% pekerja, batas upah Rp12.000.000.
- JHT: 3,7% + 2%. JP: 2% + 1%, batas upah Rp10.042.300 (angka 2026 di kode).
- JKK: 0,24%–1,74% pemberi kerja (per tingkat risiko; rate per cabang di
  `bpjs_company_profiles.jkk_rate`). JKM: 0,3% pemberi kerja.
- Rate & cap dari tabel `bpjs_rates` per tahun per program.

> NEEDS LEGAL/ACCOUNTANT VALIDATION: angka batas upah berubah tiap tahun
> (PP, Perpres, Permenaker). Validasi sebelum production: cap JP tahun berjalan,
> rentang JKK per risiko, batas usia JP (belum divalidasi di kode), iuran
> sukarela freelancer, dan file SIPP upload-ready (format SAMPLE, belum
> terverifikasi ke BPJSTK).
