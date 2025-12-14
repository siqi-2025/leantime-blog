# 变更日志

本文档记录 Leantime Blog 插件的所有重要变更。

格式遵循 [Keep a Changelog](https://keepachangelog.com/zh-CN/1.0.0/)，
版本号遵循 [语义化版本](https://semver.org/lang/zh-CN/)。

---

## [v0.1.0-baseline] - 2025-12-14

### 新增
- 完整的插件开发指南文档 (docs/plugin-development-guide.md)
- Playwright 测试指南和模板 (docs/playwright-testing-guide.md)
- 开发路线图 (docs/development-roadmap.md)
- 静态代码分析报告 (docs/STATIC-ANALYSIS-REPORT.md)
- 版本管理规范（在插件开发指南开头）
- Playwright 测试辅助函数 (tests/playwright/helpers/)
- 测试模板文件 (tests/playwright/templates/)

### 修改
- 优化核心插件文件结构
- 改进菜单注册逻辑（使用官方 Registration 服务）

### 修复
- 清理 26 个冗余代码文件（已归档至 _archive/20251214-baseline/）
- 修复文档和代码命名混乱问题（禁止使用 final、修正版等后缀）

### 测试
- ✅ 核心功能测试通过：登录、菜单显示、编辑、删除
- ⏳ 待实现功能：创建文章页面、API 端点

### 文件统计
- 清理前：21+ 文件
- 清理后：9 个核心文件
- 归档：26 个冗余文件

---

## [Unreleased]

### 计划新增
- 文章创建控制器 (Controllers/Create.php)
- 文章详情控制器 (Controllers/View.php)
- Blog API 服务 (Services/BlogApiService.php)
- Blade 视图模板 (Views/create.blade.php, Views/view.blade.php)

---

## 版本说明

- **v0.1.0-baseline**: 代码清理后的稳定基线版本
- 未来版本遵循语义化版本规范：
  - **主版本号 (Major)**: 不兼容的 API 修改
  - **次版本号 (Minor)**: 向下兼容的功能新增
  - **修订号 (Patch)**: 向下兼容的问题修正
