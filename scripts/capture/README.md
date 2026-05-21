# Playwright Screenshot Pipeline

Real-screenshot capture untuk marketing landing page (per CLAUDE.md §3).

## Setup (sekali)

```powershell
cd "D:\project laravel\hris"
npm install --save-dev playwright
npx playwright install chromium
```

## Jalankan

1. **Start app** (terminal 1):
   ```powershell
   cd "D:\project laravel\hris"
   php artisan serve --port=8000
   ```

2. **Seed demo data** (kalau belum):
   ```powershell
   php artisan migrate:fresh --seed
   ```

3. **Capture screenshots** (terminal 2):
   ```powershell
   node scripts/capture/take-screenshots.js
   ```

Output: `public/marketing/screenshots/*.png` (13 screenshots)

## Optimasi (optional)

Convert PNG → WebP untuk web:
```powershell
foreach ($f in Get-ChildItem "public\marketing\screenshots\*.png") {
    cwebp -q 85 $f.FullName -o ($f.FullName -replace '\.png$', '.webp')
}
```

## Pakai di Blade

```blade
<img src="{{ asset('marketing/screenshots/admin-payroll-runs.webp') }}"
     alt="Payroll Runs Admin"
     loading="lazy"
     class="rounded-xl shadow-2xl ring-1 ring-gray-200" />
```

## Update Cadence

Per CLAUDE.md §3: Setiap minor release UI berubah → re-run capture script. Jangan pakai mockup CSS — selalu real screenshot dari app running.
