# Payroll Formula (implementasi aktual)

## Urutan kalkulasi per karyawan (`PayrollCalculationService`)

1. Gaji pokok dari kontrak aktif.
2. Komponen earning/deduction: `fixed` | `formula` (terbatas `gaji_pokok`) |
   `attendance_based` (flat `default_amount × 22` — debt: belum sinkron hadir) |
   `manual` (= 0 — debt: input manual belum ada).
3. Basis BPJS = gapok + komponen ber-flag `is_bpjs_basis`.
4. Basis TER = total earning kena pajak (tanpa pengurang).
5. PPh 21 = TER(bruto) × rate kategori; net = earning − deduction − PPh − BPJS karyawan.
6. Simpan `PayrollItem`, `PayrollItemComponent`, `TaxCalculation`
   (bruto asli + info biaya jabatan), `BPJSCalculation`.
7. Run: `calculating → review`; lock/approve via panel.

## Jaminan

- Idempoten per run (re-run aman); `refresh()` guard anti double-approve.
- Periode & tahun regulasi dari tanggal payroll, bukan `now()`.
- `gross_basis` = bruto kena pajak (bukan bruto-setelah-pengurang).
