# Leantime Blog Plugin

[![版本](https://img.shields.io/badge/version-v0.1.0--baseline-blue.svg)](https://github.com/siqi-2025/leantime-blog/releases/tag/v0.1.0-baseline)
[![Leantime](https://img.shields.io/badge/Leantime-3.x+-green.svg)](https://leantime.io/)
[![License](https://img.shields.io/badge/license-MIT-orange.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/tests-11%2F18_passing-yellow.svg)](#测试状态)

为 Leantime 项目管理系统添加博客功能的插件（开发中）。

**GitHub 仓库**: https://github.com/siqi-2025/leantime-blog

---

## 📊 项目状态

**当前版本**: v0.1.0-baseline  
**发布日期**: 2025-12-14  
**开发阶段**: 🚧 Alpha（核心功能开发中）

### 完成情况

| 功能模块 | 状态 | 说明 |
|---------|------|------|
| ✅ 菜单注册 | 已完成 | Blog 菜单显示在主导航栏 |
| ✅ 文章列表 | 已完成 | 可查看所有文章列表 |
| ✅ 文章编辑 | 已完成 | 可编辑现有文章 |
| ✅ 文章删除 | 已完成 | 可删除文章 |
| ⏳ 文章创建 | 待开发 | Controllers/Create.php |
| ⏳ 文章详情 | 待开发 | Controllers/View.php |
| ⏳ Markdown 支持 | 待开发 | Markdown 渲染功能 |
| ⏳ API 服务 | 待开发 | Services/BlogApiService.php |
| ⏳ 分类管理 | 待开发 | 文章分类功能 |

### 测试状态

- **总测试数**: 18
- **通过**: 11 ✅
- **待实现**: 7 ⏳

通过的测试:
- ✅ 用户登录
- ✅ Blog 菜单显示
- ✅ 文章列表页面
- ✅ 文章编辑功能
- ✅ 文章删除功能
- ✅ 分类列表
- ✅ API 错误处理

待实现的测试:
- ⏳ 文章创建页面
- ⏳ Markdown 文件上传
- ⏳ Markdown 渲染
- ⏳ API 端点功能

---

## 🎯 v0.1.0-baseline 变更

### 新增

- ✅ 完整的插件开发指南文档 ([docs/plugin-development-guide.md](docs/plugin-development-guide.md))
- ✅ Playwright 测试指南和模板 ([docs/playwright-testing-guide.md](docs/playwright-testing-guide.md))
- ✅ 开发路线图 ([docs/development-roadmap.md](docs/development-roadmap.md))
- ✅ 静态代码分析报告 ([docs/STATIC-ANALYSIS-REPORT.md](docs/STATIC-ANALYSIS-REPORT.md))
- ✅ 版本管理规范（在插件开发指南开头）
- ✅ Playwright 测试辅助函数 (`tests/playwright/helpers/`)
- ✅ 测试模板文件 (`tests/playwright/templates/`)

### 修改

- ✅ 优化核心插件文件结构
- ✅ 改进菜单注册逻辑（使用官方 Registration 服务）

### 修复

- ✅ 清理 26 个冗余代码文件（已归档至 `_archive/20251214-baseline/`）
- ✅ 修复文档和代码命名混乱问题（禁止使用 final、修正版等后缀）

### 文件统计

- **清理前**: 21+ 文件
- **清理后**: 9 个核心文件
- **归档**: 26 个冗余文件

详见 [CHANGELOG.md](CHANGELOG.md)

---

## 📁 当前项目结构

```
leantime-blog/
├── README.md                       # 本文件
├── CHANGELOG.md                    # 变更日志
├── .gitignore                      # Git 忽略配置
├── push-to-github.bat              # GitHub 推送脚本
├── PUSH-GUIDE.md                   # 推送指南
│
├── docs/                           # 📚 文档目录
│   ├── plugin-development-guide.md     # 插件开发指南 (1100+ 行)
│   ├── playwright-testing-guide.md     # Playwright 测试指南
│   ├── development-roadmap.md          # 开发路线图
│   └── STATIC-ANALYSIS-REPORT.md       # 静态代码分析报告
│
├── Controllers/                    # 🎮 控制器 (4/6 完成)
│   ├── Index.php                   # ✅ 首页重定向
│   ├── ShowAll.php                 # ✅ 文章列表
│   ├── Edit.php                    # ✅ 文章编辑
│   ├── Delete.php                  # ✅ 文章删除
│   ├── Create.php                  # ⏳ 待开发
│   └── View.php                    # ⏳ 待开发
│
├── Services/                       # 🔧 服务层 (1/2 完成)
│   ├── Blog.php                    # ✅ Blog 服务（基础 CRUD）
│   └── BlogApiService.php          # ⏳ 待开发
│
├── Views/                          # 🎨 视图 (3/5 完成)
│   ├── showAll.blade.php           # ✅ 列表页面
│   ├── edit.blade.php              # ✅ 编辑页面
│   ├── partials/                   # ✅ 公共组件
│   ├── create.blade.php            # ⏳ 待开发
│   └── view.blade.php              # ⏳ 待开发
│
├── register.php                    # ✅ 插件注册入口
│
└── tests/                          # 🧪 测试目录
    └── playwright/
        ├── blog.spec.ts            # 核心功能测试 (18 tests)
        ├── helpers/                # 测试辅助函数
        └── templates/              # 测试模板
```

---

## 🚀 快速开始

### 前置要求

- ✅ Leantime 3.x 已安装并运行
- ✅ PHP 8.1+
- ✅ MySQL 8.0+
- ✅ Git

### 安装步骤

```bash
# 1. 克隆项目到 Leantime 插件目录
cd /path/to/leantime/app/Plugins
git clone https://github.com/siqi-2025/leantime-blog.git Blog

# 2. 重启 Leantime（插件会自动注册）
# Docker 环境:
docker-compose restart web

# 3. 访问 Blog 功能
# http://your-leantime-url/blog/showAll
```

⚠️ **注意**: 当前版本仅包含基础功能，完整功能请等待后续版本。

---

## 📖 文档

- [插件开发指南](docs/plugin-development-guide.md) - 完整的开发文档（1100+ 行）
- [Playwright 测试指南](docs/playwright-testing-guide.md) - E2E 测试教程
- [开发路线图](docs/development-roadmap.md) - 未来计划
- [静态代码分析](docs/STATIC-ANALYSIS-REPORT.md) - 代码质量报告
- [变更日志](CHANGELOG.md) - 版本历史

---

## 🧪 测试

### 运行 Playwright 测试

```bash
# 安装依赖
cd tests/playwright
npm install

# 运行所有测试
npx playwright test

# 运行特定测试
npx playwright test --grep "菜单显示"

# 查看测试报告
npx playwright show-report
```

### 当前测试结果 (v0.1.0-baseline)

```
Running 18 tests using 2 workers

✅ 已通过 (11/18):
  ✓ 1.1 - 用户登录测试
  ✓ 2.1 - Blog 菜单显示测试
  ✓ 3.1 - 文章列表页面测试
  ✓ 4.3 - 文章编辑测试
  ✓ 4.4 - 文章删除测试
  ✓ 5.1 - 分类列表测试
  ✓ 7.1-7.5 - API 错误处理测试

⏳ 待实现 (7/18):
  ⏳ 4.1 - 文章创建页面（待开发 Controllers/Create.php）
  ⏳ 4.2 - Markdown 上传（待开发 API）
  ⏳ 6.1 - Markdown 渲染（待开发）

测试用时: ~45s
```

---

## 🛣️ 开发路线图

### v0.2.0 (下一个版本)

- [ ] 实现文章创建功能 (`Controllers/Create.php`)
- [ ] 实现文章详情页面 (`Controllers/View.php`)
- [ ] 添加 Markdown 渲染支持
- [ ] 完成剩余 7 个测试

### v0.3.0

- [ ] 实现 Blog API 服务 (`Services/BlogApiService.php`)
- [ ] JSON RPC API 端点
- [ ] API 认证和授权

### v1.0.0

- [ ] 文件上传功能
- [ ] 图片管理
- [ ] 完整的 CRUD 功能
- [ ] 生产环境就绪

详见 [development-roadmap.md](docs/development-roadmap.md)

---

## 📊 开发进度时间线

```
2025-12-13  项目初始化，创建基础控制器和视图
2025-12-14  代码清理，建立版本管理规范
2025-12-14  ✅ v0.1.0-baseline 发布
            - 9 个核心文件
            - 11/18 测试通过
            - 完整开发文档
            - Git 基线建立
```

---

## 🔧 技术栈

- **框架**: Leantime 3.x (基于 Laravel)
- **语言**: PHP 8.1+
- **数据库**: MySQL 8.0+
- **前端**: Blade 模板, SimpleMDE (计划)
- **测试**: Playwright, PHPUnit
- **版本控制**: Git, 语义化版本

---

## 🤝 贡献

欢迎贡献代码、报告问题或提出建议！

### 贡献流程

1. Fork 本项目
2. 创建特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 提交 Pull Request

### 版本管理规范

- 遵循 [语义化版本](https://semver.org/lang/zh-CN/)
- 禁止使用 `final`、`修正版`、`最终版` 等后缀
- 所有版本必须在 CHANGELOG.md 记录
- 使用 Git 标签标记版本

详见 [插件开发指南 - 版本管理](docs/plugin-development-guide.md#版本管理规范)

---

## 📄 许可证

MIT License - 详见 [LICENSE](LICENSE) 文件

---

## 📞 支持

- **问题反馈**: [GitHub Issues](https://github.com/siqi-2025/leantime-blog/issues)
- **GitHub 仓库**: https://github.com/siqi-2025/leantime-blog
- **文档**: [docs/](docs/)

---

## 🙏 致谢

- [Leantime](https://leantime.io/) - 优秀的项目管理系统
- [Playwright](https://playwright.dev/) - 强大的 E2E 测试框架
- [Claude Code](https://claude.com/claude-code) - AI 辅助开发工具

---

**版本**: v0.1.0-baseline  
**最后更新**: 2025-12-14  
**维护者**: siqi-2025  
**状态**: 🚧 开发中

