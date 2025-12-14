const { chromium } = require('playwright');

/**
 * 独立截图脚本模板 - 用于快速捕获界面
 *
 * 使用方法：
 * 1. 复制此文件并重命名
 * 2. 修改登录凭证和截图路径
 * 3. 运行：node your-script.js
 */

(async () => {
  const browser = await chromium.launch({
    headless: false, // 显示浏览器，方便调试
  });
  const page = await browser.newPage();

  try {
    console.log('访问登录页面...');
    await page.goto('http://localhost:6007');

    console.log('登录中...');
    await page.fill('input[name="username"]', 'gerry@1mlab.net');
    await page.fill('input[name="password"]', 'F@ny172217');

    // ✅ 正确：使用 role-based 选择器
    await page.getByRole('button', { name: /登录|login|sign in/i }).click();

    // ❌ 错误：不要使用这种选择器
    // await page.click('button[type="submit"]'); // 容易超时！

    console.log('等待页面加载...');
    await page.waitForTimeout(5000);

    // 可选：导航到特定页面
    // await page.goto('http://localhost:6007/blog/showList');
    // await page.waitForTimeout(2000);

    console.log('保存截图...');
    await page.screenshot({
      path: 'test-results/manual-capture.png',
      fullPage: true, // 完整页面截图
    });

    console.log('✓ 截图已保存到: test-results/manual-capture.png');
  } catch (error) {
    console.error('错误:', error.message);

    // 错误时也保存截图
    await page.screenshot({
      path: 'test-results/error-screenshot.png',
    });

    console.log('✓ 错误截图已保存');
  } finally {
    await browser.close();
  }
})();
