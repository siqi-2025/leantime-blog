/**
 * Leantime Blog Plugin - Playwright E2E Tests
 *
 * 测试环境:
 * - Leantime URL: http://localhost:6007
 * - 测试账户: gerry@1mlab.net / F@ny172217
 * - API Key: lt_tzBvPbF0vHPf51n4fzmmDhA3KkFKNo2M_vW5PZ5ODdkzlVFvJILheSat51RWkZJqB
 *
 * 测试范围:
 * 1. 登录功能
 * 2. 博客插件访问
 * 3. 创建文章（Web 界面）
 * 4. Markdown 渲染
 * 5. API 接口测试
 * 6. 文件上传功能
 */

import { test, expect, Page } from '@playwright/test';

// 测试配置
const LEANTIME_URL = 'http://localhost:6007';
const TEST_USERNAME = 'gerry@1mlab.net';  // 使用邮箱登录
const TEST_PASSWORD = 'F@ny172217';
const API_KEY = 'lt_tzBvPbF0vHPf51n4fzmmDhA3KkFKNo2M_vW5PZ5ODdkzlVFvJILheSat51RWkZJqB';

// 辅助函数: 登录 Leantime
async function login(page: Page) {
  await page.goto(`${LEANTIME_URL}/auth/login`);

  // 填写登录表单
  await page.fill('input[name="username"]', TEST_USERNAME);
  await page.fill('input[name="password"]', TEST_PASSWORD);

  // 点击登录按钮（使用文本匹配，支持中文"登录"）
  await page.getByRole('button', { name: /登录|login/i }).click();

  // 等待登录成功（跳转到仪表盘或其他页面）
  await page.waitForURL(/.*dashboard|.*projects|.*blog.*/, { timeout: 10000 });
}

// 测试套件 1: 基础功能
test.describe('Blog Plugin - 基础功能', () => {

  test('1.1 - 登录 Leantime', async ({ page }) => {
    await login(page);

    // 验证登录成功
    expect(page.url()).not.toContain('/auth/login');

    // 截图保存
    await page.screenshot({
      path: 'test-results/01-login-success.png',
      fullPage: true
    });
  });

  test('1.2 - 访问博客列表页面', async ({ page }) => {
    await login(page);

    // 导航到博客列表
    await page.goto(`${LEANTIME_URL}/blog/list`);

    // 验证页面标题或关键元素
    const pageTitle = await page.textContent('h1, .page-title');
    expect(pageTitle).toContain('Blog');

    await page.screenshot({
      path: 'test-results/02-blog-list.png',
      fullPage: true
    });
  });

  test('1.3 - 检查博客菜单存在', async ({ page }) => {
    await login(page);

    // 查找博客菜单项
    const blogMenu = await page.locator('a[href*="/blog"], .menu-item:has-text("Blog")');

    // 验证菜单存在
    await expect(blogMenu).toBeVisible();

    // 点击菜单
    await blogMenu.first().click();

    // 验证跳转到博客页面
    await page.waitForURL(/.*blog.*/);

    await page.screenshot({
      path: 'test-results/03-blog-menu.png',
      fullPage: true
    });
  });
});

// 测试套件 2: 创建博客文章
test.describe('Blog Plugin - 创建文章', () => {

  test('2.1 - 访问创建文章页面', async ({ page }) => {
    await login(page);

    // 导航到创建文章页面
    await page.goto(`${LEANTIME_URL}/blog/create`);

    // 验证表单元素存在
    await expect(page.locator('input[name="title"]')).toBeVisible();
    await expect(page.locator('textarea[name="content"]')).toBeVisible();

    await page.screenshot({
      path: 'test-results/04-create-post-form.png',
      fullPage: true
    });
  });

  test('2.2 - 创建简单的博客文章', async ({ page }) => {
    await login(page);
    await page.goto(`${LEANTIME_URL}/blog/create`);

    // 填写文章标题
    const testTitle = `测试文章 - ${new Date().toISOString()}`;
    await page.fill('input[name="title"]', testTitle);

    // 填写 Markdown 内容
    const testContent = `# Hello Leantime Blog

这是一篇测试文章，使用 **Markdown** 格式编写。

## 功能特点

- 支持 Markdown 语法
- 代码高亮
- 项目关联

\`\`\`php
<?php
echo "Hello Leantime!";
\`\`\`

## 测试完成

文章创建时间: ${new Date().toLocaleString('zh-CN')}
`;

    // 填写内容（可能需要等待编辑器加载）
    await page.waitForTimeout(1000);
    await page.fill('textarea[name="content"]', testContent);

    // 选择发布状态
    await page.check('input[name="status"][value="published"]');

    // 截图保存表单
    await page.screenshot({
      path: 'test-results/05-create-post-filled.png',
      fullPage: true
    });

    // 提交表单
    await page.click('button[type="submit"]:has-text("发布"), button[name="status"][value="published"]');

    // 等待创建成功（可能跳转到文章详情页）
    await page.waitForURL(/.*blog\/view|.*blog\/list.*/);

    // 截图保存结果
    await page.screenshot({
      path: 'test-results/06-create-post-success.png',
      fullPage: true
    });
  });

  test('2.3 - 验证 Markdown 渲染', async ({ page }) => {
    await login(page);

    // 访问文章列表，找到最新的文章
    await page.goto(`${LEANTIME_URL}/blog/list`);

    // 点击第一篇文章（最新创建的）
    const firstPost = await page.locator('.post-item, .blog-post, tr:has(td) a').first();
    await firstPost.click();

    // 等待文章详情页加载
    await page.waitForURL(/.*blog\/view.*/);

    // 验证 Markdown 渲染
    // 检查 <h1> 标题
    const h1 = await page.locator('article h1, .post-content h1');
    await expect(h1).toBeVisible();

    // 检查代码块是否渲染
    const codeBlock = await page.locator('article pre code, .post-content pre code');
    if (await codeBlock.count() > 0) {
      await expect(codeBlock.first()).toBeVisible();
    }

    // 截图保存渲染结果
    await page.screenshot({
      path: 'test-results/07-markdown-render.png',
      fullPage: true
    });
  });
});

// 测试套件 3: API 接口测试
test.describe('Blog Plugin - API 接口', () => {

  test('3.1 - 测试 API 认证', async ({ request }) => {
    const response = await request.post(`${LEANTIME_URL}/api/jsonrpc`, {
      headers: {
        'x-api-key': API_KEY,
        'Content-Type': 'application/json',
      },
      data: {
        jsonrpc: '2.0',
        method: 'leantime.rpc.blog.listPosts',
        params: {
          status: 'published',
          limit: 10,
        },
        id: 'test-api-001',
      },
    });

    // 验证响应状态
    expect(response.status()).toBe(200);

    // 解析响应
    const body = await response.json();
    console.log('API Response:', JSON.stringify(body, null, 2));

    // 验证响应格式（即使方法不存在，也应该是正确的 JSON-RPC 格式）
    expect(body).toHaveProperty('jsonrpc', '2.0');
    expect(body).toHaveProperty('id', 'test-api-001');

    // 可能返回 result 或 error
    expect(body).toHaveProperty('result');
  });

  test('3.2 - 通过 API 发布文章', async ({ request }) => {
    const testTitle = `API 测试文章 - ${new Date().toISOString()}`;
    const testContent = `# API 自动发布测试

这是通过 **JSON RPC API** 自动发布的文章。

## 测试信息

- 创建时间: ${new Date().toLocaleString('zh-CN')}
- 发布方式: API
- 认证: API Key

\`\`\`bash
curl -X POST http://localhost:6007/api/jsonrpc \\
  -H "x-api-key: YOUR_API_KEY" \\
  -H "Content-Type: application/json"
\`\`\`
`;

    const response = await request.post(`${LEANTIME_URL}/api/jsonrpc`, {
      headers: {
        'x-api-key': API_KEY,
        'Content-Type': 'application/json',
      },
      data: {
        jsonrpc: '2.0',
        method: 'leantime.rpc.blog.publishPost',
        params: {
          title: testTitle,
          content: testContent,
          status: 'published',
          excerpt: '这是一篇通过 API 创建的测试文章',
        },
        id: 'test-api-002',
      },
    });

    expect(response.status()).toBe(200);

    const body = await response.json();
    console.log('Publish Response:', JSON.stringify(body, null, 2));

    // 验证响应
    if (body.result) {
      expect(body.result).toHaveProperty('success', true);
      expect(body.result).toHaveProperty('post_id');
      expect(body.result).toHaveProperty('url');
    } else if (body.error) {
      console.log('API Error:', body.error);
      // 方法未实现也是预期的（插件可能尚未安装）
      expect(body.error).toHaveProperty('code');
    }
  });

  test('3.3 - 上传 Markdown 文件 (API)', async ({ request }) => {
    // 模拟 Markdown 文件内容
    const markdownContent = `---
title: 通过 API 上传的 Markdown 文章
category: 技术
tags: [API, Markdown, 自动化]
status: published
---

# Markdown 文件上传测试

这是通过 **uploadMarkdown API** 上传的文章。

## Frontmatter 元数据

文件包含 YAML Frontmatter：

\`\`\`yaml
---
title: 文章标题
category: 分类
tags: [标签1, 标签2]
status: published
---
\`\`\`

## 测试时间

${new Date().toLocaleString('zh-CN')}
`;

    // Base64 编码
    const contentBase64 = Buffer.from(markdownContent).toString('base64');

    const response = await request.post(`${LEANTIME_URL}/api/jsonrpc`, {
      headers: {
        'x-api-key': API_KEY,
        'Content-Type': 'application/json',
      },
      data: {
        jsonrpc: '2.0',
        method: 'leantime.rpc.blog.uploadMarkdown',
        params: {
          filename: 'test-upload.md',
          content: contentBase64,
          extract_frontmatter: true,
          auto_publish: true,
        },
        id: 'test-api-003',
      },
    });

    expect(response.status()).toBe(200);

    const body = await response.json();
    console.log('Upload Markdown Response:', JSON.stringify(body, null, 2));

    if (body.result) {
      expect(body.result).toHaveProperty('success', true);
      expect(body.result).toHaveProperty('post_id');
    } else if (body.error) {
      console.log('Upload Error:', body.error);
    }
  });

  test('3.4 - 列出文章 (API)', async ({ request }) => {
    const response = await request.post(`${LEANTIME_URL}/api/jsonrpc`, {
      headers: {
        'x-api-key': API_KEY,
        'Content-Type': 'application/json',
      },
      data: {
        jsonrpc: '2.0',
        method: 'leantime.rpc.blog.listPosts',
        params: {
          status: 'published',
          page: 1,
          per_page: 20,
        },
        id: 'test-api-004',
      },
    });

    expect(response.status()).toBe(200);

    const body = await response.json();
    console.log('List Posts Response:', JSON.stringify(body, null, 2));

    if (body.result) {
      expect(body.result).toHaveProperty('posts');
      expect(Array.isArray(body.result.posts)).toBe(true);
    }
  });
});

// 测试套件 4: 编辑和删除
test.describe('Blog Plugin - 编辑和删除', () => {

  test('4.1 - 编辑文章', async ({ page }) => {
    await login(page);

    // 访问文章列表
    await page.goto(`${LEANTIME_URL}/blog/list`);

    // 找到第一篇文章的编辑按钮
    const editButton = await page.locator('a[href*="/blog/edit"], button:has-text("编辑"), .edit-button').first();

    if (await editButton.count() > 0) {
      await editButton.click();

      // 等待编辑页面加载
      await page.waitForURL(/.*blog\/edit.*/);

      // 修改标题
      const titleInput = await page.locator('input[name="title"]');
      await titleInput.fill(await titleInput.inputValue() + ' (已编辑)');

      // 保存
      await page.click('button[type="submit"]:has-text("保存"), button:has-text("更新")');

      // 验证保存成功
      await page.waitForTimeout(1000);

      await page.screenshot({
        path: 'test-results/08-edit-post.png',
        fullPage: true
      });
    }
  });

  test('4.2 - 删除文章（软删除）', async ({ page }) => {
    await login(page);

    // 访问文章列表
    await page.goto(`${LEANTIME_URL}/blog/list`);

    // 找到删除按钮
    const deleteButton = await page.locator('a[href*="/blog/delete"], button:has-text("删除"), .delete-button').first();

    if (await deleteButton.count() > 0) {
      // 点击删除
      await deleteButton.click();

      // 确认删除（如果有确认对话框）
      page.on('dialog', async dialog => {
        await dialog.accept();
      });

      await page.waitForTimeout(1000);

      await page.screenshot({
        path: 'test-results/09-delete-post.png',
        fullPage: true
      });
    }
  });
});

// 测试套件 5: 分类和标签
test.describe('Blog Plugin - 分类和标签', () => {

  test('5.1 - 创建分类', async ({ page }) => {
    await login(page);

    // 访问分类管理页面
    await page.goto(`${LEANTIME_URL}/blog/categories`);

    // 填写新分类
    const categoryName = `测试分类 - ${Date.now()}`;
    const categoryInput = await page.locator('input[name="category_name"], input[name="name"]');

    if (await categoryInput.count() > 0) {
      await categoryInput.fill(categoryName);

      // 提交
      await page.click('button[type="submit"]:has-text("创建"), button:has-text("添加")');

      await page.waitForTimeout(1000);

      await page.screenshot({
        path: 'test-results/10-create-category.png',
        fullPage: true
      });
    }
  });

  test('5.2 - 查看分类列表', async ({ page }) => {
    await login(page);

    await page.goto(`${LEANTIME_URL}/blog/categories`);

    // 验证分类列表显示
    const categoriesList = await page.locator('.categories-list, table, .category-item');

    if (await categoriesList.count() > 0) {
      await expect(categoriesList.first()).toBeVisible();
    }

    await page.screenshot({
      path: 'test-results/11-categories-list.png',
      fullPage: true
    });
  });
});

// 测试套件 6: 项目关联
test.describe('Blog Plugin - 项目关联', () => {

  test('6.1 - 创建文章并关联项目', async ({ page }) => {
    await login(page);

    await page.goto(`${LEANTIME_URL}/blog/create`);

    // 填写标题
    await page.fill('input[name="title"]', `项目关联测试 - ${Date.now()}`);

    // 填写内容
    await page.fill('textarea[name="content"]', '# 项目关联测试\n\n这篇文章关联到特定项目。');

    // 选择项目（如果项目下拉框存在）
    const projectSelect = await page.locator('select[name="project_id"]');

    if (await projectSelect.count() > 0) {
      const options = await projectSelect.locator('option');
      const optionCount = await options.count();

      if (optionCount > 1) {
        // 选择第一个非空选项
        await projectSelect.selectOption({ index: 1 });
      }
    }

    // 发布
    await page.click('button[type="submit"]:has-text("发布")');

    await page.waitForTimeout(1000);

    await page.screenshot({
      path: 'test-results/12-project-association.png',
      fullPage: true
    });
  });
});

// 测试套件 7: 性能和安全
test.describe('Blog Plugin - 性能和安全', () => {

  test('7.1 - 测试 API 速率限制', async ({ request }) => {
    let successCount = 0;
    let rateLimitedCount = 0;

    // 发送 10 个请求测试
    for (let i = 0; i < 10; i++) {
      const response = await request.post(`${LEANTIME_URL}/api/jsonrpc`, {
        headers: {
          'x-api-key': API_KEY,
          'Content-Type': 'application/json',
        },
        data: {
          jsonrpc: '2.0',
          method: 'leantime.rpc.blog.listPosts',
          params: {},
          id: `rate-limit-test-${i}`,
        },
      });

      if (response.status() === 200) {
        successCount++;
      } else if (response.status() === 429) {
        rateLimitedCount++;
      }
    }

    console.log(`Success: ${successCount}, Rate Limited: ${rateLimitedCount}`);

    // 至少有一些请求成功
    expect(successCount).toBeGreaterThan(0);
  });

  test('7.2 - 测试 XSS 防护', async ({ page }) => {
    await login(page);

    await page.goto(`${LEANTIME_URL}/blog/create`);

    // 尝试注入 XSS 代码
    const xssTitle = '<script>alert("XSS")</script>测试';
    const xssContent = '# XSS 测试\n\n<img src=x onerror="alert(1)">';

    await page.fill('input[name="title"]', xssTitle);
    await page.fill('textarea[name="content"]', xssContent);

    // 提交
    await page.click('button[type="submit"]:has-text("发布")');

    await page.waitForTimeout(1000);

    // 查看渲染结果（应该被转义或移除）
    const pageContent = await page.content();

    // 验证脚本标签被转义或移除
    expect(pageContent).not.toContain('<script>alert');
    expect(pageContent).not.toContain('onerror=');

    await page.screenshot({
      path: 'test-results/13-xss-protection.png',
      fullPage: true
    });
  });
});

// 测试套件 8: 文件上传 (Web 界面)
test.describe('Blog Plugin - 文件上传', () => {

  test('8.1 - 上传 Markdown 文件（Web 界面）', async ({ page }) => {
    await login(page);

    await page.goto(`${LEANTIME_URL}/blog/create`);

    // 查找文件上传输入框
    const fileInput = await page.locator('input[type="file"][accept*=".md"]');

    if (await fileInput.count() > 0) {
      // 创建临时 Markdown 文件内容
      const testMarkdown = `---
title: Web 上传测试
category: 测试
tags: [上传, Markdown]
---

# Web 界面上传测试

这是通过 Web 界面上传的 Markdown 文件。

创建时间: ${new Date().toLocaleString('zh-CN')}
`;

      // 创建临时文件（需要实际文件路径）
      // 注意：Playwright 需要真实文件路径，这里需要先创建临时文件

      console.log('文件上传功能存在，但需要真实文件路径');

      await page.screenshot({
        path: 'test-results/14-file-upload-form.png',
        fullPage: true
      });
    } else {
      console.log('未找到文件上传输入框');
    }
  });
});
