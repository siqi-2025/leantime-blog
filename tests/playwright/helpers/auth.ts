import { Page } from '@playwright/test';

/**
 * Leantime 登录函数 - 可在所有测试中复用
 *
 * ⚠️ 重要：使用 getByRole 选择器，不要使用 CSS 选择器
 */
export async function login(
  page: Page,
  username: string = process.env.LEANTIME_USERNAME || 'gerry@1mlab.net',
  password: string = process.env.LEANTIME_PASSWORD || 'F@ny172217'
) {
  // 访问登录页面
  await page.goto('/');

  // 填写用户名和密码
  await page.fill('input[name="username"]', username);
  await page.fill('input[name="password"]', password);

  // ✅ 正确：使用 role-based 选择器
  await page.getByRole('button', { name: /登录|login|sign in/i }).click();

  // ❌ 错误：不要使用 CSS 选择器
  // await page.click('button[type="submit"]'); // 容易超时！

  // 等待登录成功 - 检查是否跳转到主页
  await page.waitForURL(/dashboard|projects|main/i, { timeout: 10000 });

  console.log('✓ 登录成功');
}

/**
 * 检查是否已登录
 */
export async function isLoggedIn(page: Page): Promise<boolean> {
  const url = page.url();
  return !url.includes('/auth/login');
}

/**
 * 登出
 */
export async function logout(page: Page) {
  await page.getByRole('button', { name: /logout|登出/i }).click();
  await page.waitForURL(/login/i);
}
