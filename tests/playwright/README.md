# Leantime Blog Plugin - Playwright E2E Tests

## 📋 测试概述

本测试套件使用 **Playwright** 对 Leantime Blog 插件进行端到端（E2E）测试。

### 测试范围

- ✅ **登录功能** - 验证 Leantime 登录流程
- ✅ **博客插件访问** - 检查菜单和页面可访问性
- ✅ **创建文章 (Web)** - 通过 Web 界面创建文章
- ✅ **Markdown 渲染** - 验证 Markdown 正确渲染为 HTML
- ✅ **API 接口** - 测试所有 JSON RPC API 端点
- ✅ **文件上传** - 测试 Markdown 文件上传功能
- ✅ **编辑和删除** - 测试文章编辑和删除功能
- ✅ **分类和标签** - 测试分类和标签管理
- ✅ **项目关联** - 测试文章与项目的关联
- ✅ **安全性** - XSS 防护、API 速率限制
- ✅ **性能** - 响应时间和并发请求

---

## 🚀 快速开始

### 前置要求

1. **Node.js** >= 18.x
2. **Leantime** 运行在 `http://localhost:6007`
3. **测试账户**:
   - 用户名: `gerry@1mlab.net`
   - 密码: `F@ny172217`
4. **API Key**: `lt_tzBvPbF0vHPf51n4fzmmDhA3KkFKNo2M_vW5PZ5ODdkzlVFvJILheSat51RWkZJqB`

### 安装依赖

```bash
cd D:\github\leantime\leantime-blog\tests\playwright

# 安装 npm 依赖
npm install

# 安装 Playwright 浏览器
npx playwright install
```

### 运行测试

```bash
# 运行所有测试（无头模式）
npm test

# 显示浏览器运行
npm run test:headed

# 调试模式（逐步执行）
npm run test:debug

# UI 模式（交互式测试）
npm run test:ui

# 查看测试报告
npm run test:report
```

---

## 📂 文件结构

```
tests/playwright/
├── blog.spec.ts           # 主测试文件
├── playwright.config.ts   # Playwright 配置
├── package.json           # NPM 依赖
├── README.md              # 本文件
├── test-results/          # 测试结果和截图
│   ├── 01-login-success.png
│   ├── 02-blog-list.png
│   └── results.json
└── playwright-report/     # HTML 测试报告
```

---

## 🧪 测试套件说明

### 1. 基础功能 (Blog Plugin - 基础功能)

- **1.1 登录 Leantime** - 验证用户登录流程
- **1.2 访问博客列表页面** - 检查博客列表页面可访问
- **1.3 检查博客菜单存在** - 验证顶部菜单包含 Blog 选项

### 2. 创建文章 (Blog Plugin - 创建文章)

- **2.1 访问创建文章页面** - 检查创建表单元素
- **2.2 创建简单的博客文章** - 完整的创建流程测试
- **2.3 验证 Markdown 渲染** - 检查 Markdown 正确渲染

### 3. API 接口 (Blog Plugin - API 接口)

- **3.1 测试 API 认证** - 验证 API Key 认证
- **3.2 通过 API 发布文章** - 调用 `publishPost` API
- **3.3 上传 Markdown 文件 (API)** - 调用 `uploadMarkdown` API
- **3.4 列出文章 (API)** - 调用 `listPosts` API

### 4. 编辑和删除 (Blog Plugin - 编辑和删除)

- **4.1 编辑文章** - 修改现有文章
- **4.2 删除文章（软删除）** - 测试文章删除功能

### 5. 分类和标签 (Blog Plugin - 分类和标签)

- **5.1 创建分类** - 添加新的博客分类
- **5.2 查看分类列表** - 验证分类列表显示

### 6. 项目关联 (Blog Plugin - 项目关联)

- **6.1 创建文章并关联项目** - 测试文章与项目的关联

### 7. 性能和安全 (Blog Plugin - 性能和安全)

- **7.1 测试 API 速率限制** - 验证速率限制功能
- **7.2 测试 XSS 防护** - 检查 XSS 攻击防护

### 8. 文件上传 (Blog Plugin - 文件上传)

- **8.1 上传 Markdown 文件（Web 界面）** - 测试文件上传功能

---

## 📊 测试报告

测试完成后，会生成以下报告：

### HTML 报告

```bash
npm run test:report

# 或直接打开
explorer playwright-report/index.html
```

### JSON 报告

```bash
cat test-results/results.json
```

### 截图和视频

- 成功测试的截图：`test-results/*.png`
- 失败测试的视频：`test-results/*.webm`

---

## 🔧 测试配置

### 修改测试环境

编辑 `blog.spec.ts` 中的配置：

```typescript
const LEANTIME_URL = 'http://localhost:6007';
const TEST_USERNAME = 'gerry@1mlab.net';
const TEST_PASSWORD = 'F@ny172217';
const API_KEY = 'lt_tzBvPbF0vHPf51n4fzmmDhA3KkFKNo2M_vW5PZ5ODdkzlVFvJILheSat51RWkZJqB';
```

### 修改 Playwright 配置

编辑 `playwright.config.ts`：

```typescript
export default defineConfig({
  timeout: 30 * 1000,        // 全局超时
  retries: 2,                // 失败重试次数
  workers: 4,                // 并行执行数
  use: {
    baseURL: 'http://localhost:6007',
    viewport: { width: 1280, height: 720 },
  },
});
```

---

## 🐛 故障排查

### 1. 测试超时

**问题**: `Timeout 30000ms exceeded`

**解决方案**:
- 增加超时时间：`playwright.config.ts` 中修改 `timeout`
- 检查 Leantime 是否正常运行：`curl http://localhost:6007`
- 检查网络连接

### 2. 登录失败

**问题**: `Failed to login`

**解决方案**:
- 验证账户密码：`gerry@1mlab.net / F@ny172217`
- 检查 Leantime 登录页面 URL
- 查看 `test-results/` 中的截图

### 3. API 测试失败

**问题**: `API Key invalid`

**解决方案**:
- 确认 API Key 正确
- 检查 API Key 是否过期
- 在 Leantime 中重新生成 API Key

### 4. 找不到元素

**问题**: `Locator not found`

**解决方案**:
- 运行 `npm run test:headed` 查看浏览器
- 使用 `npm run test:codegen` 生成正确的选择器
- 检查 Leantime 版本是否匹配

---

## 📝 添加新测试

### 示例：添加评论测试

```typescript
test.describe('Blog Plugin - 评论功能', () => {

  test('添加评论到文章', async ({ page }) => {
    await login(page);

    // 访问文章详情
    await page.goto(`${LEANTIME_URL}/blog/view/1`);

    // 填写评论
    await page.fill('textarea[name="comment"]', '这是一条测试评论');

    // 提交
    await page.click('button[type="submit"]:has-text("提交评论")');

    // 验证评论显示
    await expect(page.locator('.comment-content')).toContainText('这是一条测试评论');

    await page.screenshot({
      path: 'test-results/comment-added.png',
      fullPage: true
    });
  });
});
```

---

## 🎯 最佳实践

1. **使用 Page Object Model**: 将页面操作封装为可复用的函数
2. **独立测试**: 每个测试应该独立运行，不依赖其他测试
3. **清理数据**: 测试完成后清理测试数据（如果可能）
4. **有意义的断言**: 使用清晰的断言消息
5. **截图和视频**: 失败时自动截图帮助调试

---

## 📞 支持

如有问题，请查看：

- [Playwright 官方文档](https://playwright.dev/)
- [Leantime API 文档](D:\github\leantime\leantime-blog\docs\API文档.md)
- [插件开发指南](D:\github\leantime\leantime-blog\docs\02-开发指南.md)

---

## 📄 许可证

MIT License - 详见项目根目录 LICENSE 文件

---

**测试套件版本**: v1.0.0
**最后更新**: 2025-12-13
**维护者**: Claude Code
