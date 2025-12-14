import { test, expect } from '@playwright/test';
import { login } from '../helpers/auth';
import { takeScreenshot } from '../helpers/common';

/**
 * 基础测试模板 - 复制此文件开始新的测试
 *
 * 使用方法：
 * 1. 复制此文件到测试目录
 * 2. 重命名为 your-plugin.spec.ts
 * 3. 修改测试套件名称和测试用例
 */

test.describe('插件名称 - 基础功能测试', () => {
  // 每个测试前登录
  test.beforeEach(async ({ page }) => {
    await login(page);
  });

  test('1.1 - 检查菜单项存在', async ({ page }) => {
    // 查找菜单项
    const menu = await page.locator('a:has-text("插件名称")');

    // 验证菜单可见
    await expect(menu).toBeVisible();

    // 截图
    await takeScreenshot(page, '01-menu-visible');
  });

  test('1.2 - 点击菜单跳转到列表页', async ({ page }) => {
    // 点击菜单
    await page.getByRole('link', { name: /插件名称/i }).click();

    // 验证 URL
    await page.waitForURL(/\/pluginname\/list/);

    // 验证页面标题
    await expect(page.locator('h1')).toContainText('列表标题');

    // 截图
    await takeScreenshot(page, '02-list-page', true);
  });

  test('1.3 - 创建新项目', async ({ page }) => {
    // 访问创建页面
    await page.goto('/pluginname/create');

    // 填写表单
    await page.fill('input[name="title"]', '测试标题');
    await page.fill('textarea[name="content"]', '测试内容');

    // 提交表单
    await page.getByRole('button', { name: /保存|save/i }).click();

    // 验证成功消息
    await expect(page.locator('.notification')).toContainText('创建成功');

    // 截图
    await takeScreenshot(page, '03-create-success');
  });
});
