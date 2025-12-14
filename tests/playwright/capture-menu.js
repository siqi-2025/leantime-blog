const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();

  console.log('访问登录页面...');
  await page.goto('http://localhost:6007');

  console.log('登录中...');
  await page.fill('input[name="username"]', 'gerry@1mlab.net');
  await page.fill('input[name="password"]', 'F@ny172217');
  await page.getByRole('button', { name: /登录|login/i }).click();

  console.log('等待页面加载...');
  await page.waitForTimeout(5000);

  console.log('保存截图...');
  await page.screenshot({
    path: 'test-results/menu-screenshot.png',
    fullPage: true
  });

  console.log('✓ 截图已保存到: test-results/menu-screenshot.png');

  await browser.close();
})();
