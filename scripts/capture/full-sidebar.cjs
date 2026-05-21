const { chromium } = require('playwright');
const path = require('path');

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 2000 } });
  const page = await ctx.newPage();
  await page.goto('http://hris.test/admin/login', { waitUntil: 'networkidle' });
  await page.fill('input[id="form.email"]', 'owner@hris.test');
  await page.fill('input[id="form.password"]', 'password');
  await page.click('button[type="submit"]');
  await page.waitForURL(/.*\/admin/, { timeout: 15000 }).catch(() => {});
  await page.waitForTimeout(1500);
  // Expand all groups
  const collapsed = await page.$$('button.fi-sidebar-group-button[aria-expanded="false"]');
  for (const btn of collapsed) { await btn.click(); await page.waitForTimeout(100); }
  await page.waitForTimeout(500);
  const out = path.join(__dirname, '..', '..', 'public', 'marketing', 'screenshots', 'full-sidebar.png');
  await page.screenshot({ path: out, fullPage: true });
  console.log('Saved:', out);
  await browser.close();
})();
