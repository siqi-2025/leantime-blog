# Leantime Blog 插件静态代码分析报告

> **分析时间**: 2025-12-14
> **分析方法**: 静态引用关系分析
> **目的**: 确定文件有效性，清理冗余代码

---

## 📊 分析概览

| 类别 | 文件数 | 状态 |
|------|--------|------|
| **核心生产文件** | 2 | ✅ 保留 |
| **辅助函数** | 2 | ✅ 保留 |
| **主要测试** | 1 | ✅ 保留 |
| **配置文件** | 1 | ✅ 保留 |
| **模板文件** | 2 | ✅ 保留 |
| **冗余文件** | 7 | ⚠️ 建议删除 |
| **总计** | 15 | - |

---

## 🎯 核心生产文件（必须保留）

### 1. `plugin/Blog/register.php`

**作用**: 插件注册入口，Leantime 自动加载
**被引用**: Leantime 核心加载器
**引用其他**:
- `Leantime\Domain\Plugins\Services\Registration`
- `Leantime\Core\Events\EventDispatcher`
- `Controllers/List.php` (通过 PSR-4 autoloader)

**引用关系**:
```
Leantime Core
    ↓
[include] plugin/Blog/register.php
    ↓
[register::boot()]
    ├─ PSR-4 Autoloader (加载 Controllers/*)
    ├─ Registration Service (注册菜单)
    └─ EventDispatcher (注册 API)
```

**版本信息**:
- `version: '1.0.0'`

**有效性**: ✅ **核心文件，必须保留**

---

### 2. `plugin/Blog/Controllers/List.php`

**作用**: 博客列表控制器
**被引用**: `register.php` 的 PSR-4 autoloader
**引用其他**:
- `Illuminate\Support\Facades\DB`
- `Leantime\Core\Controller\Controller`

**路由**: `/blog/showList` → `Controllers\List::get()`

**引用关系**:
```
Menu Click (/blog/showList)
    ↓
Leantime Router
    ↓
PSR-4 Autoload: Leantime\Plugins\Blog\Controllers\List
    ↓
List::get()
    ↓
查询数据库 zp_blog_posts
    ↓
渲染视图 blog.list
```

**有效性**: ✅ **核心文件，必须保留**

---

## 🧩 辅助函数（推荐保留）

### 3. `tests/playwright/helpers/auth.ts`

**作用**: 登录辅助函数（可复用）
**被引用**:
- ✅ `blog.spec.ts:18` - `import { login } from './helpers/auth';` (实际使用自己的 login 函数，未使用此文件)

**导出函数**:
- `login(page, username?, password?)` - 登录 Leantime
- `isLoggedIn(page)` - 检查登录状态
- `logout(page)` - 登出

**引用情况**:
```
tests/playwright/blog.spec.ts
    ↓
[NO] 未实际使用 helpers/auth.ts
     blog.spec.ts 内部定义了自己的 login 函数（第 27-39 行）
```

**有效性**: ⚠️ **当前未被使用，但建议保留（未来模板可用）**

**建议**: 修改 `blog.spec.ts` 使用此辅助函数，或删除 `blog.spec.ts` 中重复的 login 函数

---

### 4. `tests/playwright/helpers/common.ts`

**作用**: 通用辅助函数
**被引用**: ❌ 无任何文件引用

**导出函数**:
- `clickElement()`, `checkMenuItemExists()`, `takeScreenshot()`, `waitForElement()`, `elementContainsText()`

**有效性**: ⚠️ **当前未被使用，但建议保留（工具函数）**

---

## ✅ 主要测试文件（保留）

### 5. `tests/playwright/blog.spec.ts`

**作用**: 完整的 E2E 测试套件
**被引用**: Playwright 测试运行器
**引用其他**:
- `@playwright/test` (第 18 行)
- ❌ 未实际使用 `helpers/auth.ts` (自己定义了 login 函数)

**测试套件**:
1. Blog Plugin - 基础功能 (3 tests)
2. Blog Plugin - 创建文章 (3 tests)
3. Blog Plugin - API 接口 (4 tests)
4. Blog Plugin - 编辑和删除 (2 tests)
5. Blog Plugin - 分类和标签 (2 tests)
6. Blog Plugin - 项目关联 (1 test)
7. Blog Plugin - 性能和安全 (2 tests)
8. Blog Plugin - 文件上传 (1 test)

**总计**: 18 个测试用例

**有效性**: ✅ **主要测试文件，必须保留**

---

## ⚙️ 配置文件（保留）

### 6. `tests/playwright/playwright.config.ts`

**作用**: Playwright 配置
**被引用**: Playwright CLI
**引用其他**: `@playwright/test`

**配置项**:
- baseURL: `http://localhost:6007`
- timeout: 30s
- screenshot: only-on-failure
- video: retain-on-failure

**有效性**: ✅ **配置文件，必须保留**

---

## 📝 模板文件（保留）

### 7. `tests/playwright/templates/basic-test.spec.ts`

**作用**: 测试模板
**被引用**: ❌ 无（供开发者参考）
**引用其他**: `helpers/auth.ts`, `helpers/common.ts`

**有效性**: ✅ **文档/模板，建议保留**

---

### 8. `tests/playwright/templates/capture-template.js`

**作用**: 截图脚本模板
**被引用**: ❌ 无（供开发者参考）

**有效性**: ✅ **文档/模板，建议保留**

---

## ❌ 冗余文件（建议删除）

### 9. `tests/playwright/check-menu.spec.ts`

**作用**: 检查菜单存在
**功能重复**: ✅ 与 `blog.spec.ts` 的 test 1.3 功能完全重复

**对比**:
| 特性 | check-menu.spec.ts | blog.spec.ts (test 1.3) |
|------|-------------------|------------------------|
| 登录 | ✅ | ✅ |
| 查找菜单 | ✅ | ✅ |
| 点击菜单 | ❌ | ✅ |
| 验证跳转 | ❌ | ✅ |
| 保存HTML | ✅ | ❌ |

**问题**:
- 使用旧的 `button[type="submit"]` 选择器（容易超时）
- 使用 `require('fs')` 写文件（不规范）

**有效性**: ❌ **冗余，建议删除**

**建议操作**: 移至 `_archive/`

---

### 10. `capture-menu.js` (根目录)

**路径**: `D:/github/leantime/leantime-blog/capture-menu.js`

**作用**: 登录并截图
**功能重复**: ✅ 与 `tests/playwright/capture-menu.js` **完全相同**

**对比**:
```bash
# 根目录版本
D:/github/leantime/leantime-blog/capture-menu.js

# tests 目录版本
D:/github/leantime/leantime-blog/tests/playwright/capture-menu.js
```

**差异**: 仅路径不同，代码完全相同（13 行 vs 28 行，13 行版本缺少注释）

**有效性**: ❌ **完全冗余，建议删除根目录版本**

**建议操作**: 删除根目录版本，保留 `tests/playwright/capture-menu.js`

---

### 11. `tests/playwright/capture-menu.js`

**作用**: 登录并截图
**被引用**: ❌ 无（独立脚本）

**功能**:
- 登录 Leantime
- 等待 5 秒
- 保存截图

**有效性**: ⚠️ **独立工具脚本，可保留或删除**

**建议**:
- **选项 A**: 保留作为快速截图工具
- **选项 B**: 删除，使用 `templates/capture-template.js` 替代

---

### 12. `tests/playwright/enable-plugin.js`

**作用**: 启用插件的自动化脚本
**被引用**: ❌ 无（独立脚本）

**功能**:
- 登录 Leantime
- 访问 `/setting/plugins`
- 查找 Blog 插件
- 点击启用按钮

**有效性**: ⚠️ **一次性脚本，插件已启用后无用**

**建议操作**: 移至 `_archive/` （插件已启用）

---

### 13. `tests/playwright/test-blog-page.js`

**作用**: 测试博客页面加载
**被引用**: ❌ 无（独立脚本）

**功能**:
- 登录
- 访问 `/blog/list`
- 截图
- 检查页面内容

**功能重复**: ✅ 与 `blog.spec.ts` 的 test 1.2 功能重复

**有效性**: ❌ **冗余，建议删除**

**建议操作**: 移至 `_archive/`

---

### 14. `tests/test-menu-quick.js`

**路径**: `D:/github/leantime/leantime-blog/tests/test-menu-quick.js`

**作用**: 快速检查菜单
**被引用**: ❌ 无（独立脚本）

**功能**:
- 登录
- 查找菜单项
- 保存截图和 HTML

**功能重复**: ✅ 与 `check-menu.spec.ts` 功能相同

**问题**:
- 使用旧的 `button[type="submit"]` 选择器
- 保存文件到 `tests/` 根目录（不规范）

**有效性**: ❌ **冗余，建议删除**

**建议操作**: 移至 `_archive/`

---

## 📈 引用关系图

```
生产环境:
  Leantime Core → plugin/Blog/register.php
                     ↓
                  PSR-4 Autoloader
                     ↓
                  Controllers/List.php
                     ↓
                  Database (zp_blog_posts)

测试环境:
  Playwright CLI → playwright.config.ts
                     ↓
                  blog.spec.ts (主要测试)
                     ├─ 内部 login() 函数 ✅
                     └─ helpers/auth.ts ❌ 未使用

  独立脚本 (无依赖):
    - check-menu.spec.ts ❌ (冗余)
    - capture-menu.js (根目录) ❌ (重复)
    - capture-menu.js (tests/) ⚠️ (工具)
    - enable-plugin.js ⚠️ (一次性)
    - test-blog-page.js ❌ (冗余)
    - test-menu-quick.js ❌ (冗余)

  模板 (供参考):
    - templates/basic-test.spec.ts ✅
    - templates/capture-template.js ✅
```

---

## 🎯 清理建议

### 第一轮清理（安全，无风险）

**移至 `_archive/20251214-baseline/`**:

1. ❌ `capture-menu.js` (根目录) - 完全重复
2. ❌ `tests/playwright/check-menu.spec.ts` - 功能重复
3. ❌ `tests/playwright/test-blog-page.js` - 功能重复
4. ❌ `tests/test-menu-quick.js` - 功能重复
5. ⚠️ `tests/playwright/enable-plugin.js` - 一次性脚本（插件已启用）

**保留原因**:
- 这些文件的功能已被 `blog.spec.ts` 覆盖
- 或者是完全重复的文件
- 删除后不影响任何生产或测试功能

---

### 第二轮清理（可选）

**考虑删除**:

1. ⚠️ `tests/playwright/capture-menu.js` - 独立截图工具
   - **保留理由**: 快速手动截图
   - **删除理由**: 有 `templates/capture-template.js` 替代

---

### 代码优化（不删除文件，改进代码）

1. **修改 `blog.spec.ts`**: 使用 `helpers/auth.ts` 的 login 函数
   ```typescript
   // 修改前（第 27-39 行）:
   async function login(page: Page) {
     // ... 内部实现
   }

   // 修改后:
   import { login } from './helpers/auth';
   // 删除内部 login 函数定义
   ```

2. **修改 `helpers/auth.ts`**: 更新 waitForURL 模式
   ```typescript
   // 从:
   await page.waitForURL(/dashboard|projects|main/i, { timeout: 10000 });

   // 改为:
   await page.waitForURL(/.*dashboard|.*projects|.*blog.*/, { timeout: 10000 });
   ```

---

## 📋 清理执行计划

### Step 1: 移动冗余文件（已完成）

```bash
mkdir -p _archive/20251214-baseline
mv plugin/Blog-register*.php _archive/20251214-baseline/
mv plugin/Blog-ShowList*.php _archive/20251214-baseline/
mv check-*.php clear-*.php debug-*.php fix-*.php test-*.php _archive/20251214-baseline/
```

### Step 2: 继续移动测试冗余文件

```bash
cd D:/github/leantime/leantime-blog

# 移动根目录重复文件
mv capture-menu.js _archive/20251214-baseline/

# 移动tests冗余文件
mv tests/playwright/check-menu.spec.ts _archive/20251214-baseline/
mv tests/playwright/test-blog-page.js _archive/20251214-baseline/
mv tests/playwright/enable-plugin.js _archive/20251214-baseline/
mv tests/test-menu-quick.js _archive/20251214-baseline/
```

### Step 3: 运行 Playwright 测试验证

```bash
cd tests/playwright
npm test
```

**预期结果**: 所有测试通过（18 个测试用例）

### Step 4: 确认后删除归档

```bash
# 如果测试全部通过
rm -rf _archive/20251214-baseline/
```

---

## 📊 清理后的文件结构

```
leantime-blog/
├── plugin/
│   └── Blog/
│       ├── register.php              ✅ 核心
│       └── Controllers/
│           └── List.php              ✅ 核心
│
├── tests/
│   └── playwright/
│       ├── blog.spec.ts              ✅ 主测试
│       ├── playwright.config.ts      ✅ 配置
│       ├── capture-menu.js          ⚠️ 工具（可选保留）
│       ├── helpers/
│       │   ├── auth.ts              ✅ 辅助函数
│       │   └── common.ts            ✅ 辅助函数
│       └── templates/
│           ├── basic-test.spec.ts   ✅ 模板
│           └── capture-template.js  ✅ 模板
│
└── docs/
    ├── plugin-development-guide.md  ✅ 文档
    ├── playwright-testing-guide.md  ✅ 文档
    ├── development-roadmap.md        ✅ 文档
    └── README.md                     ✅ 文档

总文件数: 14 个（清理前: 21+ 个）
减少: 7+ 个冗余文件
```

---

## ✅ 结论

### 当前状态

- **核心功能文件**: 100% 有效，无冗余
- **测试文件**: 存在大量冗余（5 个冗余文件）
- **文档**: 完整，无冗余
- **模板**: 有用，建议保留

### 主要问题

1. **文件重复**: `capture-menu.js` 有两个版本
2. **功能重复**: 多个测试脚本测试相同的功能
3. **辅助函数未使用**: `helpers/auth.ts` 和 `helpers/common.ts` 未被实际使用
4. **一次性脚本未删除**: `enable-plugin.js` 等

### 推荐操作

1. ✅ 移动 5 个明确冗余的文件到 `_archive/`
2. ✅ 运行 Playwright 测试验证
3. ✅ 测试通过后删除 `_archive/`
4. ⚠️ 可选：优化 `blog.spec.ts` 使用 `helpers/auth.ts`

---

**报告结束** | 下一步：执行清理计划 → 运行 Playwright 动态测试
