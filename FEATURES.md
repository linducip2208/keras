# HRIS — Feature Inventory & Competitive Blueprint

> Dokumen ini meng-compile **seluruh fitur HRIS** dari 7 produk acuan di pasar Indonesia, plus differentiator yang akan jadi keunggulan produk kita.
>
> Tanggal: 2026-05-21 · Stack target: Laravel 13 + MySQL + Filament 4 (admin) + Flutter (mobile karyawan) · License: v3 (whitelabel.co.id)

---

## 1. Produk Acuan (Benchmarks)

| Kode | Produk | Positioning | URL |
|---|---|---|---|
| **KK** | KantorKu (by Dealls) | UKM → enterprise, all-in-one, ISO 27001 | kantorku.id |
| **TLT** | Mekari Talenta | SMB → enterprise, market leader Indonesia | talenta.co |
| **GDJ** | Gadjian | UKM payroll-first, sejak 2016 | gadjian.com |
| **SLK** | Sleekr HR | UKM ramah pemula (sekarang bagian Mekari) | sleekr.co |
| **LNV** | LinovHR | Mid → enterprise, **cloud + on-premise**, talent-heavy | linovhr.com |
| **BWY** | BroadwaysHR | SMB → mid, payroll + absensi terintegrasi | broadwayshr.com |
| **KO** | KaryaONE | UKM/UMKM/koperasi, fingerprint integration | karyaone.com |

Legenda checklist di feature table:
- ✅ = ada di produk tsb (confirmed dari riset/landing/dokumentasi resmi)
- ⚪ = tidak/belum confirmed (asumsi tidak ada di tier marketing)
- 🆕 = differentiator kita (tidak ada di mayoritas competitor)

---

## 2. Modul Lengkap (29 Modul)

Daftar 29 modul yang akan kita support — sudah mencakup superset semua kompetitor.

### Core HR (M01–M08)
1. **M01 Employee Database & Master Data** — single source of truth
2. **M02 Organization & Org Chart** — multi-level, multi-entity
3. **M03 Contract & Document Management** — PKWT/PKWTT, BPJS card, KTP, NPWP
4. **M04 Letter Generator (HR Letter)** — SK, paklaring, referensi, mutasi, demosi
5. **M05 Approval Workflow Engine** — multi-level, parallel, conditional rules
6. **M06 Announcement & Broadcast** — pengumuman, polling, acknowledgment
7. **M07 Employee Self-Service (ESS)** — mobile + web portal
8. **M08 HR Letter & Digital Signature** — e-sign integration (BSrE/Privy)

### Time, Attendance & Leave (M09–M13)
9. **M09 Shift & Schedule Management** — fixed, rotating, split, custom
10. **M10 Attendance (Clock-in/Out)** — GPS + selfie + fingerprint + face recognition
11. **M11 Overtime Management** — request → approval → payroll
12. **M12 Leave Management** — cuti tahunan, sakit, melahirkan, izin, unpaid
13. **M13 Time Off Policy & Holiday Calendar** — Indonesia + custom company

### Payroll & Compensation (M14–M18)
14. **M14 Payroll Engine** — gaji, prorate, multi-period
15. **M15 Tax PPh 21 / 26** — TER 2024, gross/net/gross-up
16. **M16 BPJS Kesehatan & Ketenagakerjaan** — JHT, JP, JKK, JKM, auto SIPP file
17. **M17 Bank Disbursement** — BCA/Mandiri/BNI/BRI/CIMB batch CSV/XML
18. **M18 Reimbursement & Expense** — policy + budget + receipt OCR

### Talent & Performance (M19–M24)
19. **M19 Recruitment / ATS** — career page, pipeline, AI screening
20. **M20 Onboarding & Probation** — checklist + buddy + 90-day review
21. **M21 OKR & KPI** — cascading, check-in, alignment view
22. **M22 Performance Appraisal & 360°** — review cycle, 9-box matrix
23. **M23 Competency & Career Path** — skill matrix, succession planning
24. **M24 Learning & Development (LMS)** — course, certification, training hours

### Operations & Analytics (M25–M29)
25. **M25 Asset Management** — laptop, ID card, SIM kendaraan, return on offboarding
26. **M26 Travel & Business Trip** — SPPD, advance, settlement
27. **M27 Loan / Koperasi Karyawan** — pinjaman + auto-deduct payroll
28. **M28 Exit Management** — clearance, exit interview, paklaring
29. **M29 Analytics & Reporting** — dashboard + custom report builder + AI insight

---

## 3. Feature Matrix per Modul

### M01 — Employee Database

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Data pribadi + keluarga + emergency contact | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Data karir (riwayat posisi, mutasi, promosi) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Riwayat pendidikan + sertifikasi | ⚪ | ✅ | ✅ | ⚪ | ✅ | ✅ | ✅ | ✅ |
| Bank account multiple | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Dokumen upload (KTP, NPWP, ijazah, BPJS) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Expiry alert (kontrak, BPJS, SIM, paspor) | ✅ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Custom field per perusahaan | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | 🆕 ✅ |
| Bulk import Excel + validation | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Audit log per field (siapa ubah apa kapan) | ⚪ | ⚪ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | 🆕 ✅ |
| QR code badge ID karyawan | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |

### M02 — Organization & Org Chart

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Org chart visual drag-drop | ✅ | ✅ | ⚪ | ✅ | ✅ | ✅ | ⚪ | ✅ |
| Multi-level unlimited | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Multi-company / multi-entity (group) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Department, division, job title, job level | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Cost center per dept | ⚪ | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Reporting line (manager + dotted line) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | 🆕 ✅ |
| Branch / lokasi kerja | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Position history & movement | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |

### M03 — Contract & Document

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| PKWT, PKWTT, magang, harian, freelance | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Probation tracking + auto-extend reminder | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Contract expiry alert (60/30/7 hari) | ✅ | ✅ | ⚪ | ✅ | ✅ | ✅ | ⚪ | ✅ |
| Auto-renewal workflow | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Contract template variable | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| E-signature integration (BSrE/Privy) | ⚪ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| WhatsApp delivery + read receipt | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |

### M04 — HR Letter Generator

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| SK pengangkatan | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Surat paklaring / referensi | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| SK mutasi / promosi / demosi | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Surat peringatan (SP1/SP2/SP3) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Surat penugasan / SPPD | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Custom template + variable merge | ⚪ | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Nomor surat auto-increment per kategori | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| PDF + e-sign + arsip otomatis | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | 🆕 ✅ |

### M05 — Approval Workflow Engine

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Multi-level sequential | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Parallel approval (semua harus approve) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Conditional routing (by nominal/dept/role) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Delegation saat manager cuti | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Auto-escalation timeout | ⚪ | ⚪ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | 🆕 ✅ |
| Approval via mobile push + WhatsApp | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ⚪ | 🆕 ✅ |
| Comment & attachment per step | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |

### M06 — Announcement & Broadcast

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Broadcast all / per dept / per branch | ✅ | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Rich text + image + attachment | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Read receipt / acknowledgment tracking | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Polling / quick survey | ⚪ | ⚪ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | 🆕 ✅ |
| Pin priority + expire date | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Birthday + work anniversary auto-post | ⚪ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |

### M07 — Employee Self-Service (ESS)

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| View slip gaji + tax form | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Request cuti / izin / sakit | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Request lembur | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Request reimbursement | ✅ | ✅ | ⚪ | ✅ | ✅ | ✅ | ⚪ | ✅ |
| Update profile (alamat, contact, bank) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Download SK / surat keterangan PDF | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Mobile app iOS + Android | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **WhatsApp bot full-fitur** (cuti, absen, slip) | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Web portal responsive | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Family info update + dependents BPJS | ⚪ | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ |

### M09 — Shift & Schedule Management

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Fixed shift (kantor 9-5) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Rotating shift (pagi/siang/malam) | ✅ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Split shift (potong jam istirahat) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Flexible / no shift (WFA) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Auto-roster generator | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Shift swap antar karyawan | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Schedule template per dept | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Calendar view (weekly/monthly) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

### M10 — Attendance (Clock-in/Out)

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| **GPS clock-in + geofence** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ⚪ | ✅ |
| **Selfie photo** with timestamp | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ⚪ | ✅ |
| **Face recognition** (anti-spoofing) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | 🆕 ✅ |
| Fingerprint device integration (SDK) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ✅ | ✅ |
| QR code scan check-in | ⚪ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Multi-location radius (cabang berbeda) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ⚪ | ✅ |
| WFH / WFA mode | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ⚪ | ✅ |
| Real-time live tracking dashboard | ✅ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Mock GPS / fake location detection | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | 🆕 ✅ |
| Offline mode + auto-sync | ⚪ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Late/early-leave/absent auto-flag | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Auto-deduct salary on absence | ⚪ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Attendance correction request | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ⚪ | ✅ |

### M11 — Overtime

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Request lembur pre/post (sebelum/sesudah jam) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ⚪ | ✅ |
| Auto-calculate berdasarkan Kepmenaker 102 | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Tarif beda weekday vs weekend/libur | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Cap lembur per bulan | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Lembur ditukar cuti pengganti | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Budget lembur per dept (warning saat over) | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Auto-flow ke payroll | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

### M12 — Leave Management

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Cuti tahunan dengan saldo per user | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Cuti sakit (dengan/tanpa surdok) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Cuti melahirkan + ayah | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Cuti menikah / khusus / besar | ⚪ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Unpaid leave auto-deduct payroll | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Custom leave type per perusahaan | ⚪ | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Carry-over saldo tahun depan + expiry | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Half-day leave | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Cuti bersama auto-deduct dari saldo | ⚪ | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Cuti ulang tahun (perk) | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Leave conflict warning (overlap team) | ⚪ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |

### M13 — Holiday Calendar

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Pre-loaded tanggal merah Indonesia | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Cuti bersama dari pemerintah | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Custom holiday per company/branch | ⚪ | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Religion-specific holiday | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Calendar export (iCal/Google) | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |

### M14 — Payroll Engine

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Komponen gaji custom unlimited | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Earning + deduction + benefit | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Formula builder (excel-like) | ⚪ | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Pro-rata mid-month join/resign | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Multi-period (mingguan/dua-mingguan/bulanan) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Off-cycle payroll (bonus, THR, koreksi) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| THR & bonus generator | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Komisi penjualan (sales incentive) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Variable allowance from attendance | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Multi-currency (untuk expat) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Payslip PDF + email + WhatsApp | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | 🆕 ✅ |
| Payslip lock password (NIK/DOB) | ⚪ | ✅ | ✅ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Payroll lock & approval | ⚪ | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Audit trail payroll changes | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | 🆕 ✅ |

### M15 — Tax PPh 21/26

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| **PPh 21 TER 2024** (peraturan terbaru) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Metode Gross / Nett / Gross-up | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| PPh 21 final (resign, severance) | ⚪ | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| PPh 26 untuk WNA | ⚪ | ✅ | ✅ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Status PTKP (TK/K0–K3) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Form 1721-A1 generator | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Form 1721-I (bulanan) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| e-Bupot integration / export | ⚪ | ✅ | ✅ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Direct submit ke DJP API | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Auto-update saat aturan baru | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

### M16 — BPJS

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| BPJS Kesehatan auto-calc | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| BPJS Ketenagakerjaan: JHT, JP, JKK, JKM | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Cap upah BPJS (Kesehatan 12jt, JP 10jt) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Dependents (suami/istri + 3 anak) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Auto-generate file SIPP** (upload-ready) | ⚪ | ✅ | ✅ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Direct submit ke SIPP API | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Klaim JKK / JKM workflow | ⚪ | ⚪ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | 🆕 ✅ |
| Iuran berbeda per cabang/UMK | ⚪ | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ |

### M17 — Bank Disbursement

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Batch file BCA (KlikBCA Bisnis CSV) | ⚪ | ✅ | ✅ | ✅ | ✅ | ✅ | ⚪ | ✅ |
| Batch file Mandiri (Cash Management) | ⚪ | ✅ | ✅ | ✅ | ✅ | ✅ | ⚪ | ✅ |
| Batch file BNI Direct | ⚪ | ✅ | ✅ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Batch file BRI Cash Mgmt | ⚪ | ✅ | ✅ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Batch file CIMB BizChannel | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| **Instant disbursement** (Flip/Xendit API) | ✅ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Bank validation (cek nomor rekening valid) | ⚪ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Re-run failed transfer + retry | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Disbursement history + reconciliation | ⚪ | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ |

### M18 — Reimbursement & Expense

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Kategori custom (transport, meals, medical) | ✅ | ✅ | ⚪ | ✅ | ✅ | ✅ | ⚪ | ✅ |
| Policy limit per kategori / role | ✅ | ✅ | ⚪ | ✅ | ✅ | ✅ | ⚪ | ✅ |
| Budget tracking real-time | ✅ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Upload bukti foto/PDF | ✅ | ✅ | ⚪ | ✅ | ✅ | ✅ | ⚪ | ✅ |
| **OCR receipt** (auto-extract nominal & vendor) | ⚪ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Flexible approval rules | ✅ | ✅ | ⚪ | ✅ | ✅ | ✅ | ⚪ | ✅ |
| Auto-flow ke payroll atau separate | ✅ | ✅ | ⚪ | ✅ | ✅ | ✅ | ⚪ | ✅ |
| Multi-currency reimbursement | ⚪ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ✅ |
| Per-diem (uang harian dinas) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Corporate card reconciliation | ⚪ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |

### M19 — Recruitment / ATS

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Career page publik / job board | ✅ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ✅ | ✅ |
| Job posting share ke channel (LinkedIn, etc) | ✅ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| **LinkedIn integration** (import lowongan) | ✅ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ✅ |
| **AI CV screening + match score** | ✅ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ✅ |
| Multi-filter (skill, lokasi, exp, salary) | ✅ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ✅ | ✅ |
| Pipeline kanban (stage drag-drop) | ✅ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ✅ | ✅ |
| Interview scheduler + calendar invite | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ✅ | ✅ |
| Interview feedback form + scorecard | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| 1-click WhatsApp/Email blast kandidat | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Talent pool / candidate database | ✅ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Offer letter generator + e-sign | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Referral program tracking | ⚪ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Auto-reject email template | ⚪ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ✅ |
| Background check integration | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |

### M20 — Onboarding & Probation

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Onboarding checklist per role | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ✅ | ✅ |
| Document collection (KTP, ijazah, NPWP) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ✅ | ✅ |
| Asset assignment workflow | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Welcome email + intro slide | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Buddy assignment | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Probation 30/60/90-day review | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Auto-end probation reminder | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |

### M21 — OKR & KPI

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Cascading goals unlimited level | ✅ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Company → dept → team → individual | ✅ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| OKR monitoring & progress | ✅ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| KPI weight & scoring | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Check-in mingguan / bulanan | ✅ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Alignment view (siapa support siapa) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Reminder in-app + email | ✅ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| OKR retrospective tool | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |

### M22 — Performance Appraisal 360°

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| 360° review (self, peer, manager, subordinate) | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ | ✅ |
| Custom appraisal form builder | ✅ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| **9-box matrix** (performance × potential) | ✅ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Calibration meeting tool | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Cycle scheduler (annual/quarterly) | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ | ✅ |
| Goal-linked appraisal | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Anonymous peer feedback | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Continuous feedback (anytime kudos) | ⚪ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| AI summary 360 feedback | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |

### M23 — Competency & Succession

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Competency framework / model | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Skill matrix per role | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Skill gap analysis | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Career path visualization | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| **Succession planning** (talent pool) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Promotion-ready dashboard | ⚪ | ⚪ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| IDP (Individual Development Plan) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |

### M24 — Learning & Development (LMS)

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Internal course catalog | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ✅ | ✅ |
| Video / PDF / SCORM upload | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Quiz & assessment | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Certification + expiry tracking | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Training hours tracking | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| External training expense link | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Mandatory training auto-assign per role | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Course rating + feedback | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| **AI-generated quiz dari materi** | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |

### M25 — Asset Management

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Asset register (laptop, hp, kendaraan) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Assignment to employee + handover form | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Return on offboarding (auto-checklist) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Maintenance schedule | ⚪ | ⚪ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Depreciation tracking | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |

### M26 — Travel / Business Trip (SPPD)

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Trip request workflow | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Cash advance (uang muka) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Per-diem auto-calc | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Settlement & expense report | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| SPPD letter auto-generate | ⚪ | ⚪ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Travel agent integration (Traveloka Biz) | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |

### M27 — Loan / Koperasi Karyawan

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Cash advance request | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Installment loan + interest | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Auto-deduct payroll per cicilan | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Loan policy (max % gaji, max tenor) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Koperasi simpan-pinjam mode | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |

### M28 — Exit Management

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Resignation request workflow | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Clearance checklist (asset return, finance) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Exit interview form | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Final payment (pesangon, sisa cuti) | ⚪ | ✅ | ✅ | ✅ | ✅ | ✅ | ⚪ | ✅ |
| Pesangon UU CK calculator | ⚪ | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Paklaring auto-generate | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Access revocation checklist (email, SaaS) | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Boomerang tag (rehirable / no-rehire) | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |

### M29 — Analytics & Reporting

| Fitur | KK | TLT | GDJ | SLK | LNV | BWY | KO | Kita |
|---|---|---|---|---|---|---|---|---|
| Attendance dashboard | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Overtime cost analysis | ✅ | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Payroll cost by dept / cost center | ✅ | ✅ | ✅ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Headcount + turnover rate | ✅ | ✅ | ⚪ | ⚪ | ✅ | ✅ | ⚪ | ✅ |
| Performance distribution | ✅ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Custom report builder (drag-drop) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Scheduled report email | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |
| Export Excel + PDF + CSV | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **AI insight & narrative summary** | ⚪ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Predictive analytics (turnover risk) | ⚪ | ✅ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| Compensation benchmark vs market | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | ⚪ | 🆕 ✅ |
| BI integration (Metabase, Looker) | ⚪ | ✅ | ⚪ | ⚪ | ✅ | ⚪ | ⚪ | ✅ |

---

## 4. Differentiator (🆕 Yang Bikin Kita Menang)

Fitur-fitur yang **mayoritas competitor tidak punya** — wajib jadi marketing message:

### Tier 1 — Killer Features (impactful, demo-able)
1. **WhatsApp Bot Full-fitur ESS** — clock-in, request cuti, lihat slip, approve via WA. Huge for blue-collar + low-tech karyawan.
2. **AI Insight & Narrative Summary** — laporan "narasi" otomatis ("turnover naik 15% di dept A karena…")
3. **Predictive Turnover Risk** — flag karyawan high-risk resign berdasarkan signal data
4. **Compensation Benchmark vs Market** — partner data Mercer/Kelly Indonesia
5. **Face Recognition + Anti-spoofing** — clock-in anti-titip absen
6. **Mock GPS Detection** — anti fake location
7. **Offline Mode + Auto-sync** — lokasi sinyal jelek tetap bisa absen
8. **OCR Receipt** — foto bon → auto-extract nominal & vendor
9. **Bank Account Validation** — cek nomor rekening sebelum disburse
10. **Multi-company / Multi-entity (group)** — satu admin → banyak PT

### Tier 2 — Power User Features
11. **Direct submit DJP API** — bukan cuma export file
12. **Direct submit SIPP API** — paperless BPJS reporting
13. **Klaim JKK/JKM workflow** — bantu HR urus klaim BPJS
14. **E-signature BSrE/Privy integration** — kontrak full digital
15. **Audit log per field** — siapa ubah apa kapan, compliance ready
16. **Auto-escalation timeout** — approval mandeg → auto naik
17. **Travel agent integration (Traveloka Biz)** — booking tiket dari dalam app
18. **AI-generated quiz dari materi** — LMS more usable
19. **Koperasi simpan-pinjam mode** — captures market UKM/koperasi karyawan
20. **Access revocation checklist** — offboarding aman (email, GitHub, Slack revoke)

### Tier 3 — Nice Touches (delight)
21. Cuti ulang tahun (perk)
22. Calendar export iCal/Google
23. Religion-specific holiday
24. Polling / quick survey
25. Continuous feedback (kudos anytime)
26. Birthday + work anniversary auto-broadcast
27. Boomerang tag (rehirable / no-rehire)
28. Budget lembur warning per dept
29. Referral program tracking
30. Background check integration

---

## 5. Mandatory Cross-cutting Requirements

Wajib di-implement sebagai backbone, bukan modul terpisah:

### 5.1 Tech & Architecture
- **No hardcoded providers** — semua integrasi (LLM, payment, SMS, WA gateway, bank) dynamic via DB config (per CLAUDE.md global rule)
- **Multi-tenant** dari hari pertama
- **License v3** (whitelabel.co.id RSA + AES-256-GCM lock file) untuk dijual sebagai produk
- **API-first** — semua fitur expose REST API + token auth (Sanctum)
- **Webhook engine** — outbound webhook ke client systems
- **Audit log** comprehensive (sentral, semua entity, immutable)
- **Soft delete** + restore semua entity
- **Background job** (Laravel queue + Redis) untuk payroll batch

### 5.2 Security & Compliance
- **ISO 27001-ready** (claim setara KK)
- Encryption at rest untuk PII + payroll + API keys
- 2FA mandatory untuk admin
- Row-level security per company/branch
- Session timeout configurable
- IP whitelist optional
- **UU PDP compliance** (consent tracking, data export, data deletion request)
- Backup harian + point-in-time restore

### 5.3 Programmatic SEO (wajib per CLAUDE.md global)
- `/best-aplikasi-hris-{tahun}`
- `/best-hris-untuk-{industri}` (manufaktur, retail, F&B, startup, klinik, kontraktor, ...)
- `/compare/hris-{a}-vs-{b}` (kantorku-vs-talenta, etc.)
- `/alternatives-to-{competitor}`
- `/hris-untuk-{kota}` (jakarta, surabaya, bandung, medan, ...)
- `/aplikasi-payroll-{kota}`
- `/aplikasi-absensi-{industri}`
- `/harga-software-hris-{tahun}`
- Schema: ItemList + FAQPage + Product + LocalBusiness
- Dynamic sitemap.xml + robots.txt
- 300+ kata konten bermakna per page (generate dari DB)

### 5.4 Mobile App (Flutter)
- iOS + Android single codebase
- Clock-in dengan GPS + selfie + face recognition
- Request cuti, lembur, reimbursement
- Approve request (untuk manager)
- Push notification (FCM)
- Offline mode
- Slip gaji + dokumen download
- Announcement feed
- Org chart browse
- Asset assigned ke saya

### 5.5 Admin Panel (Filament 4)
- Full CRUD 29 modul
- Multi-resource dashboard
- Bulk action + export
- Permission per resource (role-based)
- Activity log built-in
- Notification center

---

## 6. Roadmap (Phased Release)

### Phase 0 — Foundation (Week 1)
- Project scaffold Laravel 13 + Filament 4 + MySQL
- License v3 install + pairing wizard
- Multi-tenant + auth + role/permission (Spatie)
- Audit log + soft delete + queue setup
- 29 module migrations & models
- Seeder lengkap (demo company, demo karyawan, holidays Indonesia)

### Phase 1 — MVP Core (Week 2–4)
Modul: M01, M02, M03, M07, M09, M10, M11, M12, M13, M14, M15, M16
- Employee DB + org chart
- Contract + ESS
- Attendance + leave + overtime + holiday
- Payroll + PPh 21 TER + BPJS

### Phase 2 — Disbursement & Reimbursement (Week 5)
Modul: M17, M18, M04, M05, M06
- Bank batch (BCA, Mandiri, BNI, BRI, CIMB)
- Instant disburse (Flip/Xendit)
- Reimbursement + OCR
- HR letter generator
- Approval workflow engine
- Announcement

### Phase 3 — Talent (Week 6–7)
Modul: M19, M20, M21, M22
- ATS + AI screening
- Onboarding
- OKR/KPI
- Appraisal 360 + 9-box

### Phase 4 — Advanced (Week 8–9)
Modul: M23, M24, M25, M26, M27, M28, M29
- Competency + succession
- LMS
- Asset
- Travel/SPPD
- Loan
- Exit management
- Analytics + AI insight

### Phase 5 — Mobile + Differentiators (Week 10–12)
- Flutter app full-feature
- WhatsApp bot ESS
- Face recognition
- Mock GPS detection
- DJP / SIPP direct submit
- E-signature integration

### Phase 6 — pSEO + Marketing (Week 13)
- 7 jenis pSEO routes
- Sitemap + robots
- Public landing page + pricing
- Demo account + sandbox

---

## 7. Pricing Strategy (Reference)

Benchmark dari competitor (per karyawan / bulan untuk SaaS):

| Vendor | Starter | Mid | Enterprise |
|---|---|---|---|
| Talenta | ~Rp 25k/user | ~Rp 50k | Custom |
| KantorKu | ~Rp 20k/user | ~Rp 40k | Custom |
| Gadjian | ~Rp 12k/user | ~Rp 25k | Custom |
| Sleekr | ~Rp 15k/user | ~Rp 30k | Custom |
| LinovHR | Cloud + On-premise (one-time) | — | — |

**Posisi kita:** Rp 15–20k/user/bulan untuk SMB (di bawah Talenta, di atas Gadjian), dengan **license-once self-host option** untuk segmen yang ga mau SaaS.

---

## 8. Open Questions (Konfirmasi User)

Yang masih perlu input dari user (boleh dijawab kapan saja):

1. **Brand name** — tetap "hris" sebagai folder/codename, atau ada nama produk?
2. **Pricing model** — SaaS only, license-once only, atau dual model?
3. **Initial target segment** — UKM (<100), mid-market (100–500), atau enterprise (>500)?
4. **Industry vertical fokus pertama** — generic, atau spesifik (manufaktur, retail, F&B, hospitality)?
5. **Integration priority** — Accurate / Jurnal / Xero (accounting), Slack (chat), Google Workspace (calendar/SSO)?

> **Default tanpa konfirmasi:** generic SaaS multi-tenant untuk UKM → mid, brand `hris`, dual pricing (SaaS + license-once via whitelabel.co.id), fokus tahap 1 generic semua industry, accounting integration tahap 2.

---

## 9. Reference

- KantorKu — https://kantorku.id/fitur/
- Mekari Talenta — https://www.talenta.co/en/features/
- Gadjian — https://www.gadjian.com/
- Sleekr — https://sleekr.co/hr/
- LinovHR — https://www.linovhr.com/software-hris/
- BroadwaysHR — https://broadwayshr.com/
- KaryaONE — https://karyaone.com/
- Reference pSEO template — `D:\project laravel\whitelabel\whitelabel\app\Http\Controllers\ProgrammaticSeoController.php`
- Reference no-hardcoded-providers — `D:\project laravel\foodscan\docs\10-AI-PROVIDERS.md`
- Reference Phase 0 docs pattern — `D:\project laravel\eventloket\` / `freelance\` / `crmoffice\`

---

*Dokumen ini akan jadi sumber kebenaran untuk Phase 0 docs (vision, scope, ERD, roadmap) project HRIS. Update dokumen ini setiap kali ada fitur baru atau perubahan scope.*
