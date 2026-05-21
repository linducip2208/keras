/**
 * Playwright screenshot pipeline — capture admin & landing pages.
 * Run: npm install --save-dev playwright; npx playwright install chromium
 * Then: node scripts/capture/take-screenshots.js
 *
 * Per CLAUDE.md §3: Marketing page wajib pakai REAL screenshots dari app running.
 */
const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const BASE_URL = process.env.APP_URL || 'http://127.0.0.1:8000';
const OUT_DIR = path.resolve(__dirname, '../../public/marketing/screenshots');

if (!fs.existsSync(OUT_DIR)) fs.mkdirSync(OUT_DIR, { recursive: true });

// [name, url-path, viewport, login-as | null]
const SCREENS = [
  // Public marketing
  ['landing-home', '/', { width: 1440, height: 900 }, null],
  ['landing-pseo-best', '/best-aplikasi-hris-2026', { width: 1440, height: 900 }, null],
  ['landing-pseo-compare', '/compare/hris-vs-talenta', { width: 1440, height: 900 }, null],
  ['landing-pseo-industry', '/best-aplikasi-hris-untuk-manufaktur', { width: 1440, height: 900 }, null],
  ['landing-pair-wizard', '/__pair', { width: 1440, height: 900 }, null],

  // Filament admin (auth required)
  ['admin-login', '/admin/login', { width: 1440, height: 900 }, null],
  ['admin-dashboard', '/admin', { width: 1440, height: 900 }, 'owner@hris.test'],
  ['admin-employees', '/admin/employees', { width: 1440, height: 900 }, 'owner@hris.test'],
  ['admin-companies', '/admin/companies', { width: 1440, height: 900 }, 'owner@hris.test'],
  ['admin-payroll-runs', '/admin/payroll-runs', { width: 1440, height: 900 }, 'owner@hris.test'],
  ['admin-attendance-logs', '/admin/attendance-logs', { width: 1440, height: 900 }, 'owner@hris.test'],
  ['admin-leave-requests', '/admin/leave-requests', { width: 1440, height: 900 }, 'owner@hris.test'],

  // Mobile viewport landing
  ['landing-mobile-home', '/', { width: 390, height: 844 }, null],
];

(async () => {
  console.log(`Output dir: ${OUT_DIR}`);
  console.log(`Base URL  : ${BASE_URL}`);

  const browser = await chromium.launch({ headless: true });

  for (const [name, urlPath, viewport, loginAs] of SCREENS) {
    const context = await browser.newContext({ viewport });
    const page = await context.newPage();

    try {
      if (loginAs) {
        await page.goto(`${BASE_URL}/admin/login`, { waitUntil: 'networkidle', timeout: 60000 });
        await page.fill('input[type=email]', loginAs);
        await page.fill('input[type=password]', 'password');
        await Promise.all([
          page.waitForURL(/\/admin/, { timeout: 30000 }),
          page.click('button[type=submit]'),
        ]);
      }

      await page.goto(`${BASE_URL}${urlPath}`, { waitUntil: 'networkidle', timeout: 60000 });
      await page.waitForTimeout(1000); // settle animations

      const outPath = path.join(OUT_DIR, `${name}.png`);
      await page.screenshot({ path: outPath, fullPage: false });
      const size = (fs.statSync(outPath).size / 1024).toFixed(0);
      console.log(`✓ ${name}.png  (${size}kb)`);
    } catch (e) {
      console.error(`✗ ${name} — ${e.message}`);
    } finally {
      await context.close();
    }
  }

  await browser.close();
  console.log('\nDone. Screenshots di:', OUT_DIR);
})();
