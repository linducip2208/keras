const { chromium } = require('playwright');
const path = require('path');

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  const page = await ctx.newPage();
  await page.goto('http://hris.test/#tour', { waitUntil: 'networkidle' });
  await page.waitForTimeout(1500);
  const tourSection = await page.$('#tour');
  if (!tourSection) { console.log('Section #tour not found'); await browser.close(); return; }
  const out = path.join(__dirname, '..', '..', 'public', 'marketing', 'screenshots', 'landing-tour-section.png');
  await tourSection.screenshot({ path: out });
  console.log('Saved:', out);
  await browser.close();
})();
