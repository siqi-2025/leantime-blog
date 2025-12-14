# Leantime Playwright 测试完整指南

> Leantime 插件自动化测试模板和最佳实践
>
> **目的**: 提供可复用的测试脚本，避免重复编写登录等基础代码
> **适用场景**: Leantime 插件功能测试、UI 测试、集成测试

---

## 目录

1. [环境准备](#1-环境准备)
2. [测试项目结构](#2-测试项目结构)
3. [可复用模板](#3-可复用模板)
4. [常见测试场景](#4-常见测试场景)
5. [最佳实践](#5-最佳实践)
6. [避坑指南](#6-避坑指南)
7. [调试技巧](#7-调试技巧)

---

## 1. 环境准备

### 1.1 安装 Playwright

```bash
# 在项目根目录
cd tests/playwright

# 初始化 npm 项目（如果还没有）
npm init -y

# 安装 Playwright
npm install --save-dev @playwright/test

# 安装浏览器
npx playwright install chromium
```

### 1.2 配置文件

创建 `playwright.config.ts`：

```typescript
import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './',
  fullyParallel: false, // Leantime 测试需要顺序执行
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: 1, // 单线程执行，避免并发问题
  reporter: [
    ['html', { outputFolder: 'test-results/html-report' }],
    ['list'],
  ],
  use: {
    baseURL: 'http://your-leantime-url',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
  timeout: 30000, // 30 秒超时
  expect: {
    timeout: 10000, // 10 秒断言超时
  },
});
```

### 1.3 环境变量

创建 `.env` 文件：

```env
LEANTIME_URL=http://your-leantime-url
LEANTIME_USERNAME=admin@example.com
LEANTIME_PASSWORD=your_password
```

---

## 2. 测试项目结构

```
tests/playwright/
├── playwright.config.ts      # Playwright 配置
├── package.json             # npm 依赖
├── .env                     # 环境变量（不提交到 Git）
├── helpers/
│   ├── auth.ts             # 登录辅助函数
│   └── common.ts           # 通用辅助函数
├── templates/
│   ├── basic-test.spec.ts  # 基础测试模板
│   └── capture.js          # 截图脚本模板
├── blog.spec.ts            # 博客插件测试
└── test-results/           # 测试结果目录
    ├── screenshots/
    ├── videos/
    └── html-report/
```

---

## 3. 可复用模板

### 3.1 登录辅助函数（重要！）

创建 `helpers/auth.ts`：

```typescript
import { Page } from '@playwright/test';

/**
 * Leantime 登录函数 - 可在所有测试中复用
 *
 * ⚠️ 重要：使用 getByRole 选择器，不要使用 CSS 选择器
 */
export async function login(
  page: Page,
  username: string = process.env.LEANTIME_USERNAME || 'admin@example.com',
  password: string = process.env.LEANTIME_PASSWORD || 'your_password'
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
```

### 3.2 通用辅助函数

创建 `helpers/common.ts`：

```typescript
import { Page, expect } from '@playwright/test';

/**
 * 等待并点击元素
 */
export async function clickElement(page: Page, selector: string) {
  await page.waitForSelector(selector, { state: 'visible' });
  await page.click(selector);
}

/**
 * 检查菜单项是否存在
 */
export async function checkMenuItemExists(
  page: Page,
  menuText: string
): Promise<boolean> {
  const menu = await page.locator(`a:has-text("${menuText}")`).first();
  return await menu.isVisible();
}

/**
 * 截图保存（带时间戳）
 */
export async function takeScreenshot(
  page: Page,
  filename: string,
  fullPage: boolean = false
) {
  const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
  const path = `test-results/screenshots/${filename}-${timestamp}.png`;

  await page.screenshot({
    path,
    fullPage,
  });

  console.log(`✓ 截图已保存: ${path}`);
  return path;
}

/**
 * 等待元素可见
 */
export async function waitForElement(page: Page, selector: string) {
  await page.waitForSelector(selector, { state: 'visible', timeout: 10000 });
}

/**
 * 检查元素是否包含文本
 */
export async function elementContainsText(
  page: Page,
  selector: string,
  text: string
): Promise<boolean> {
  const element = await page.locator(selector);
  const content = await element.textContent();
  return content?.includes(text) || false;
}
```

### 3.3 基础测试模板

创建 `templates/basic-test.spec.ts`：

```typescript
import { test, expect } from '@playwright/test';
import { login } from '../helpers/auth';
import { takeScreenshot } from '../helpers/common';

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
```

### 3.4 截图脚本模板

创建 `templates/capture.js`：

```javascript
const { chromium } = require('playwright');

/**
 * 独立截图脚本 - 用于快速捕获界面
 * 使用方法：node capture.js
 */
(async () => {
  const browser = await chromium.launch({
    headless: false, // 显示浏览器，方便调试
  });
  const page = await browser.newPage();

  try {
    console.log('访问登录页面...');
    await page.goto('http://your-leantime-url');

    console.log('登录中...');
    await page.fill('input[name="username"]', 'admin@example.com');
    await page.fill('input[name="password"]', 'your_password');

    // ✅ 正确：使用 role-based 选择器
    await page.getByRole('button', { name: /登录|login|sign in/i }).click();

    console.log('等待页面加载...');
    await page.waitForTimeout(5000);

    console.log('保存截图...');
    await page.screenshot({
      path: 'test-results/manual-capture.png',
      fullPage: true,
    });

    console.log('✓ 截图已保存到: test-results/manual-capture.png');
  } catch (error) {
    console.error('错误:', error.message);
    await page.screenshot({
      path: 'test-results/error-screenshot.png',
    });
  } finally {
    await browser.close();
  }
})();
```

---

## 4. 常见测试场景

### 4.1 菜单测试

```typescript
test('检查菜单项存在', async ({ page }) => {
  await login(page);

  // 方法 1：使用 locator
  const blogMenu = await page.locator('a[href*="/blog"]');
  await expect(blogMenu).toBeVisible();

  // 方法 2：使用 getByRole
  const menuLink = await page.getByRole('link', { name: /blog/i });
  await expect(menuLink).toBeVisible();

  // 方法 3：使用文本搜索
  const menu = await page.getByText('Blog', { exact: true });
  await expect(menu).toBeVisible();
});
```

### 4.2 表单填写和提交

```typescript
test('创建博客文章', async ({ page }) => {
  await login(page);
  await page.goto('/blog/create');

  // 填写表单
  await page.fill('input[name="title"]', '测试文章标题');
  await page.fill('textarea[name="content"]', '这是测试内容');

  // 选择下拉选项
  await page.selectOption('select[name="category"]', 'tech');

  // 勾选复选框
  await page.check('input[name="published"]');

  // 提交表单
  await page.getByRole('button', { name: /发布|publish/i }).click();

  // 验证成功
  await expect(page).toHaveURL(/\/blog\/list/);
  await expect(page.locator('.success-message')).toBeVisible();
});
```

### 4.3 列表和分页

```typescript
test('查看文章列表', async ({ page }) => {
  await login(page);
  await page.goto('/blog/list');

  // 检查列表是否有数据
  const rows = await page.locator('table tbody tr');
  const count = await rows.count();
  expect(count).toBeGreaterThan(0);

  // 检查分页
  if (count >= 10) {
    const nextButton = page.getByRole('button', { name: /next|下一页/i });
    await nextButton.click();
    await page.waitForTimeout(1000);
  }

  // 截图
  await page.screenshot({
    path: 'test-results/blog-list.png',
    fullPage: true,
  });
});
```

### 4.4 编辑和删除

```typescript
test('编辑和删除文章', async ({ page }) => {
  await login(page);
  await page.goto('/blog/list');

  // 点击第一行的编辑按钮
  const editButton = page.locator('tbody tr:first-child button.edit').first();
  await editButton.click();

  // 修改标题
  await page.fill('input[name="title"]', '修改后的标题');
  await page.getByRole('button', { name: /保存|save/i }).click();

  // 验证成功
  await expect(page.locator('.success-message')).toContainText('保存成功');

  // 返回列表
  await page.goto('/blog/list');

  // 删除文章
  const deleteButton = page
    .locator('tbody tr:first-child button.delete')
    .first();
  await deleteButton.click();

  // 确认删除
  await page.getByRole('button', { name: /确认|confirm/i }).click();

  // 验证删除成功
  await expect(page.locator('.success-message')).toContainText('删除成功');
});
```

### 4.5 对话框处理

```typescript
test('处理确认对话框', async ({ page }) => {
  await login(page);

  // 监听对话框
  page.on('dialog', async (dialog) => {
    console.log('对话框消息:', dialog.message());
    await dialog.accept(); // 或 dialog.dismiss()
  });

  // 触发删除操作
  await page.goto('/blog/list');
  await page.locator('button.delete').first().click();
});
```

---

## 5. 最佳实践

### 5.1 选择器优先级

**推荐顺序**（从高到低）：

1. **Role-based 选择器**（最推荐）

```typescript
await page.getByRole('button', { name: /登录|login/i });
await page.getByRole('link', { name: /blog/i });
await page.getByRole('textbox', { name: /title/i });
```

2. **文本选择器**

```typescript
await page.getByText('Blog');
await page.getByPlaceholder('Enter title');
await page.getByLabel('Username');
```

3. **属性选择器**

```typescript
await page.locator('input[name="username"]');
await page.locator('a[href="/blog"]');
```

4. **CSS/XPath 选择器**（最不推荐）

```typescript
await page.locator('#submit-button'); // 脆弱，容易失效
await page.locator('button[type="submit"]'); // 不推荐
```

### 5.2 等待策略

```typescript
// ✅ 推荐：等待特定元素
await page.waitForSelector('.blog-list', { state: 'visible' });

// ✅ 推荐：等待 URL 变化
await page.waitForURL(/\/blog\/list/);

// ⚠️ 谨慎使用：固定时间等待（容易浪费时间）
await page.waitForTimeout(3000); // 仅用于调试

// ✅ 推荐：等待网络空闲
await page.waitForLoadState('networkidle');
```

### 5.3 断言技巧

```typescript
// 元素可见性
await expect(page.locator('.menu')).toBeVisible();
await expect(page.locator('.loading')).toBeHidden();

// 文本内容
await expect(page.locator('h1')).toHaveText('Blog Posts');
await expect(page.locator('.message')).toContainText('Success');

// URL 验证
await expect(page).toHaveURL(/\/blog\/list/);

// 数量验证
const items = page.locator('.blog-item');
await expect(items).toHaveCount(10);
```

### 5.4 截图和调试

```typescript
// 在测试失败时自动截图（配置文件中设置）
use: {
  screenshot: 'only-on-failure',
  video: 'retain-on-failure',
}

// 手动截图
await page.screenshot({
  path: 'test-results/debug.png',
  fullPage: true,
});

// 暂停测试进行手动调试
await page.pause();
```

---

## 6. 避坑指南

### ❌ 坑 1：使用 CSS 选择器查找按钮

```typescript
// ❌ 错误：容易超时
await page.click('button[type="submit"]');

// ✅ 正确：使用 role-based 选择器
await page.getByRole('button', { name: /登录|login|submit/i }).click();
```

**原因**：CSS 选择器可能匹配多个按钮或隐藏的按钮，导致点击失败或超时。

---

### ❌ 坑 2：硬编码等待时间

```typescript
// ❌ 错误：浪费时间
await page.waitForTimeout(5000);

// ✅ 正确：等待特定条件
await page.waitForSelector('.blog-list', { state: 'visible' });
await page.waitForURL(/\/blog/);
```

---

### ❌ 坑 3：不处理异步操作

```typescript
// ❌ 错误：没有等待导航完成
page.click('a[href="/blog"]'); // 忘记 await
await page.screenshot({ path: 'blog.png' }); // 可能还在跳转中

// ✅ 正确：等待导航完成
await page.click('a[href="/blog"]');
await page.waitForURL(/\/blog/);
await page.screenshot({ path: 'blog.png' });
```

---

### ❌ 坑 4：选择器太脆弱

```typescript
// ❌ 错误：依赖 HTML 结构
await page.click('.container > .sidebar > ul > li:nth-child(3) > a');

// ✅ 正确：使用语义化选择器
await page.getByRole('link', { name: /blog/i }).click();
```

---

### ❌ 坑 5：忽略登录状态

```typescript
// ❌ 错误：每个测试都手动登录
test('test 1', async ({ page }) => {
  await page.goto('/login');
  await page.fill('input[name="username"]', 'user');
  await page.fill('input[name="password"]', 'pass');
  await page.click('button');
  // ... 测试逻辑
});

// ✅ 正确：使用 beforeEach 或辅助函数
test.beforeEach(async ({ page }) => {
  await login(page);
});

test('test 1', async ({ page }) => {
  // 直接开始测试逻辑
});
```

---

## 7. 调试技巧

### 7.1 开启调试模式

```bash
# 显示浏览器窗口
npx playwright test --headed

# 逐步调试
npx playwright test --debug

# 只运行特定测试
npx playwright test --grep "菜单测试"
```

### 7.2 使用 Playwright Inspector

```typescript
// 在代码中暂停
await page.pause();

// 逐步执行
await page.screenshot({ path: 'step-1.png' });
await page.pause();
await page.click('.next-button');
await page.screenshot({ path: 'step-2.png' });
```

### 7.3 查看测试报告

```bash
# 运行测试
npm test

# 查看 HTML 报告
npx playwright show-report test-results/html-report
```

### 7.4 日志和追踪

```typescript
// 启用详细日志
DEBUG=pw:api npx playwright test

// 启用追踪
use: {
  trace: 'on', // 总是记录追踪
}

// 查看追踪
npx playwright show-trace test-results/trace.zip
```

---

## 8. 性能优化

### 8.1 复用浏览器上下文

```typescript
import { test as base } from '@playwright/test';

const test = base.extend({
  // 复用已登录的浏览器上下文
  authenticatedPage: async ({ browser }, use) => {
    const context = await browser.newContext();
    const page = await context.newPage();
    await login(page);
    await use(page);
    await context.close();
  },
});

test('use authenticated page', async ({ authenticatedPage }) => {
  // authenticatedPage 已经登录
});
```

### 8.2 并行执行（谨慎）

```typescript
// playwright.config.ts
export default defineConfig({
  workers: 3, // 3 个并行测试
  fullyParallel: true,
});
```

**注意**：Leantime 测试可能有数据库依赖，建议单线程执行。

---

## 9. 完整示例

### Blog 插件测试套件

```typescript
import { test, expect } from '@playwright/test';
import { login } from '../helpers/auth';
import { takeScreenshot } from '../helpers/common';

test.describe('Blog Plugin - 完整测试套件', () => {
  test.beforeEach(async ({ page }) => {
    await login(page);
  });

  test('1.1 - 菜单可见性', async ({ page }) => {
    const menu = page.getByRole('link', { name: /blog/i });
    await expect(menu).toBeVisible();
  });

  test('1.2 - 访问列表页', async ({ page }) => {
    await page.getByRole('link', { name: /blog/i }).click();
    await page.waitForURL(/\/blog\/showList/);
    await expect(page.locator('h1')).toContainText('Blog Posts');
    await takeScreenshot(page, 'blog-list', true);
  });

  test('1.3 - 创建文章', async ({ page }) => {
    await page.goto('/blog/create');
    await page.fill('input[name="title"]', 'Playwright 测试文章');
    await page.fill('textarea[name="content"]', '这是自动化测试创建的文章');
    await page.getByRole('button', { name: /发布/i }).click();
    await expect(page.locator('.success')).toBeVisible();
  });

  test('1.4 - 编辑文章', async ({ page }) => {
    await page.goto('/blog/showList');
    await page.locator('.edit-button').first().click();
    await page.fill('input[name="title"]', '修改后的标题');
    await page.getByRole('button', { name: /保存/i }).click();
    await expect(page).toHaveURL(/\/blog\/showList/);
  });

  test('1.5 - 删除文章', async ({ page }) => {
    await page.goto('/blog/showList');

    page.on('dialog', (dialog) => dialog.accept());

    await page.locator('.delete-button').first().click();
    await expect(page.locator('.success')).toContainText('删除成功');
  });
});
```

---

## 10. 总结

### 关键要点

1. **使用辅助函数**：登录、截图等固定流程提取为辅助函数
2. **优先使用 role-based 选择器**：避免脆弱的 CSS 选择器
3. **合理等待**：使用条件等待而非固定时间
4. **充分截图**：失败时自动截图，成功时手动截图留档
5. **模块化测试**：每个功能独立测试，便于定位问题

### Token 消耗分析

在本项目开发中，Playwright 相关的 token 消耗主要来自：
- ❌ 多次尝试错误的选择器（`button[type="submit"]`）
- ❌ 反复调试登录流程
- ❌ 重复编写类似的测试代码

**使用本模板后**，预计可以减少 **60-80%** 的测试相关 token 消耗。

### 可复用资源

- `helpers/auth.ts` - 登录辅助函数（**必备**）
- `helpers/common.ts` - 通用辅助函数
- `templates/basic-test.spec.ts` - 测试模板
- `templates/capture.js` - 截图脚本模板
- `playwright.config.ts` - 配置文件

---

**文档版本**: 1.0
**最后更新**: 2025-12-14
**作者**: Claude Code
**项目**: Leantime Blog Plugin
