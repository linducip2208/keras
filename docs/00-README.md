# HRIS — Phase 0 Documentation

> Dokumen perencanaan lengkap untuk produk HRIS (Human Resource Information System) Indonesia. Ini adalah single source of truth sebelum scaffold kode dimulai.
>
> Project: `D:\project laravel\hris`  ·  Started: 2026-05-21  ·  Owner: user

## Daftar Dokumen

| # | Dokumen | Isi |
|---|---|---|
| 01 | [VISION](01-VISION.md) | Visi, misi, target user, value proposition, success metrics, business model, positioning vs kompetitor |
| 02 | [SCOPE](02-SCOPE.md) | MVP scope, in/out-of-scope, asumsi, constraint, non-functional requirements, glossary |
| 03 | [ARCHITECTURE](03-ARCHITECTURE.md) | Tech stack, system architecture, layer, service, deployment, security architecture |
| 04 | [MODULES](04-MODULES.md) | Breakdown 29 modul: purpose, user stories, fitur detail, dependency, key entities |
| 05 | [ERD](05-ERD.md) | Entity Relationship Diagram + 100+ tabel + constraint + index |
| 06 | [BUSINESS-FLOWS](06-BUSINESS-FLOWS.md) | Alur bisnis end-to-end per proses (hire, attend, pay, perform, exit) |
| 07 | [ROADMAP](07-ROADMAP.md) | Phased delivery 6 fase 13 minggu + deliverable + milestone + risk |
| 08 | [INTEGRATIONS](08-INTEGRATIONS.md) | Dynamic integration spec (bank, e-sign, WA, LLM, OCR, accounting) |
| 09 | [MARKETING-FRONT-PAGE](09-MARKETING-FRONT-PAGE.md) | Landing page wajib `/`, real screenshots via Playwright, demo accounts table |
| 10 | [FEATURE-LIST](10-FEATURE-LIST.md) | Flat checklist 300+ fitur dengan tier MVP/P2-4/P5+/Future — review cepat |

## Quick Links
- **Feature list lengkap (200+ fitur vs 7 kompetitor):** [`../FEATURES.md`](../FEATURES.md)
- **Global rule pSEO:** `C:\Users\lindu cipta\.claude\CLAUDE.md` §1
- **Global rule no-hardcoded-providers:** `C:\Users\lindu cipta\.claude\CLAUDE.md` §2
- **Reference pSEO template:** `D:\project laravel\whitelabel\whitelabel\app\Http\Controllers\ProgrammaticSeoController.php`
- **Reference dynamic providers:** `D:\project laravel\foodscan\docs\10-AI-PROVIDERS.md`

## Reading Order

**Untuk product owner / stakeholder:** 01 → 02 → 07 → 04
**Untuk engineer:** 03 → 05 → 04 → 06 → 08
**Untuk reviewer Phase 0:** 01 → 02 → 03 → 04 → 05 → 06 → 07 → 08 (linear)

## Status

Phase 0 docs **DRAFT v1** — menunggu approval user sebelum lanjut ke Phase 1 (scaffold code).

| Doc | Status |
|---|---|
| 00-README | ✅ Draft |
| 01-VISION | ✅ Draft |
| 02-SCOPE | ✅ Draft |
| 03-ARCHITECTURE | ✅ Draft |
| 04-MODULES | ✅ Draft |
| 05-ERD | ✅ Draft |
| 06-BUSINESS-FLOWS | ✅ Draft |
| 07-ROADMAP | ✅ Draft |
| 08-INTEGRATIONS | ✅ Draft |
| 09-MARKETING-FRONT-PAGE | ✅ Draft |
| 10-FEATURE-LIST | ✅ Draft |

## Approval Checklist

Sebelum lanjut Phase 1, user harus konfirmasi:
- [ ] Vision + positioning OK
- [ ] Scope MVP OK (modul yang masuk Phase 1)
- [ ] Architecture & stack OK
- [ ] ERD struktur OK
- [ ] Business flow per proses OK
- [ ] Roadmap & timeline OK
- [ ] Integration pattern OK
- [ ] Pricing & monetization model OK
