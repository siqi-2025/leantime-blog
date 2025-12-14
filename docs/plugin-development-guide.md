# Leantime 插件开发完整指南

> 从零开始开发 Leantime 插件的实战经验总结
>
> **文档版本**: v1.0.0
> **最后更新**: 2025-12-14
> **项目**: Leantime Blog Plugin
> **插件版本**: v0.1.0-alpha
> **开发周期**: 2025-12-14
> **迭代次数**: 15+ 轮
> **最终状态**: 菜单成功显示，核心功能可用

---

## ⚠️ 版本管理规范（必读）

### 文档版本管理

**强制要求**: 所有项目文档和代码文件必须遵循以下版本管理规则

#### 1. 文档命名规范

**✅ 正确命名**:
```
plugin-development-guide.md          # 文档主文件（唯一）
README.md                            # 项目说明（唯一）
development-roadmap.md               # 开发路线图（唯一）
```

**❌ 错误命名**（禁止使用）:
```
❌ plugin-guide-final.md
❌ plugin-guide-final-v2.md
❌ plugin-guide-修正版.md
❌ plugin-guide-clear.md
❌ plugin-guide-最终版.md
```

**原则**:
- 每个文档只保留一个主文件
- 通过文档内部的版本号管理变更
- 禁止使用 "final", "clear", "修正版", "最终版" 等后缀

---

#### 2. 文档版本号格式

**在每个文档开头必须包含**:

```markdown
> **文档版本**: v1.2.3
> **最后更新**: 2025-12-14
> **作者**: Your Name
> **状态**: 活跃维护中 | 已废弃 | 草稿
```

**版本号规则** (遵循语义化版本 Semantic Versioning):

- **v1.0.0**: 主版本.次版本.修订号
  - **主版本号 (Major)**: 不兼容的重大变更（如完全重写）
  - **次版本号 (Minor)**: 向下兼容的功能新增
  - **修订号 (Patch)**: 向下兼容的问题修正、文字修改

**示例**:
```
v0.1.0 → v0.2.0  (添加新章节)
v0.2.0 → v0.2.1  (修正错别字)
v0.2.1 → v1.0.0  (文档完善，正式发布)
```

---

#### 3. 代码文件版本管理

**✅ 正确的文件管理**:

```
plugin/Blog/register.php             # 当前生产版本（唯一）
plugin/Blog/Controllers/List.php     # 控制器（唯一）
```

**❌ 错误的文件管理**（禁止）:

```
❌ plugin/Blog-register-final.php
❌ plugin/Blog-register-fixed.php
❌ plugin/Blog-register-v2.php
❌ plugin/Blog-register-debug.php
```

**版本管理方式**:

1. **使用 Git 管理版本**（推荐）
   ```bash
   git tag v0.1.0
   git commit -m "feat: 添加菜单注册功能"
   ```

2. **代码内版本号**（register.php 中的 info() 方法）
   ```php
   public static function info(): array
   {
       return [
           'name' => 'Leantime Blog',
           'version' => '0.1.0',  // ← 插件版本号
           // ...
       ];
   }
   ```

3. **临时文件管理**
   - 开发过程中的测试文件：移到 `_archive/` 目录
   - 调试脚本：移到 `_debug/` 目录
   - 定期清理，建立基线

---

#### 4. 版本变更日志 (CHANGELOG)

**创建 CHANGELOG.md 文件**:

```markdown
# 变更日志

## [v1.0.0] - 2025-12-14

### 新增
- 完整的插件开发指南文档
- Playwright 测试模板

### 修改
- 优化菜单注册逻辑
- 改进错误处理

### 修复
- 修复 MySQL ONLY_FULL_GROUP_BY 错误
- 修复命名空间冲突

## [v0.1.0] - 2025-12-13

### 新增
- 基础插件结构
- 菜单注册功能
```

---

#### 5. 代码清理规则

**定期执行代码清理**:

1. **识别冗余文件**
   ```bash
   # 查找所有带版本后缀的文件
   find . -name "*-final*" -o -name "*-fixed*" -o -name "*-debug*"
   ```

2. **移动到临时目录**
   ```bash
   mkdir -p _archive/$(date +%Y%m%d)
   mv plugin/*-final.php _archive/$(date +%Y%m%d)/
   ```

3. **测试功能正常**
   ```bash
   # 运行所有测试
   npm test
   # 手动测试核心功能
   ```

4. **确认后删除**
   ```bash
   rm -rf _archive/$(date +%Y%m%d)/
   ```

---

#### 6. 文件命名约定总结

| 文件类型 | 命名格式 | 示例 | 禁止 |
|---------|---------|------|------|
| **主文档** | `{名称}.md` | `README.md` | `README-final.md` |
| **主代码** | `{名称}.php` | `register.php` | `register-v2.php` |
| **控制器** | `{动作}.php` | `ShowList.php` | `ShowList-fixed.php` |
| **测试文件** | `{模块}.spec.ts` | `blog.spec.ts` | `blog-test.spec.ts` |
| **配置文件** | `{名称}.config.js` | `playwright.config.ts` | `playwright.final.config.ts` |
| **临时文件** | `_archive/{名称}` | `_archive/old-register.php` | 根目录临时文件 |

---

#### 7. 基线建立流程

**每个里程碑后建立基线**:

1. **代码清理** → 删除所有临时文件
2. **测试验证** → 确保所有功能正常
3. **版本标记** → Git tag + 更新版本号
4. **文档更新** → 更新 CHANGELOG 和版本号
5. **备份归档** → 可选，重要基线做备份

**基线示例**:
```bash
# 1. 清理代码（移动到临时目录）
mkdir -p _archive/pre-v1.0.0
mv plugin/*-debug.php _archive/pre-v1.0.0/

# 2. 运行测试
npm test

# 3. 提交并打标签
git add .
git commit -m "chore: 清理冗余文件，建立 v1.0.0 基线"
git tag v1.0.0

# 4. 更新文档版本号
# 手动更新所有文档的版本号

# 5. 删除临时目录
rm -rf _archive/pre-v1.0.0/
```

---

## 目录

1. [开发环境准备](#1-开发环境准备)
2. [插件基础结构](#2-插件基础结构)
3. [核心技术点](#3-核心技术点)
4. [踩过的坑与解决方案](#4-踩过的坑与解决方案)
5. [最佳实践](#5-最佳实践)
6. [调试技巧](#6-调试技巧)
7. [测试验证](#7-测试验证)

---

## 1. 开发环境准备

### 1.1 必需软件

- **Docker Desktop** - 运行 Leantime 容器
- **MySQL 8.0+** - 数据库（注意 SQL 模式）
- **Node.js 16+** - Playwright 测试
- **Git** - 源码管理

### 1.2 Leantime 安装

```bash
# docker-compose.yml 配置
services:
  leantime-web-prod:
    image: leantime/leantime:latest
    ports:
      - "6007:80"
    volumes:
      - ./plugin:/var/www/html/app/Plugins
    environment:
      LEAN_DB_HOST: leantime-mysql-prod
      LEAN_DB_USER: leantime
      LEAN_DB_PASSWORD: password
      LEAN_DB_DATABASE: leantime
```

### 1.3 获取 Leantime 源码

```bash
# 克隆官方仓库用于研究
git clone https://github.com/Leantime/leantime.git leantime-source

# 关键文件位置：
# - app/Core/Events/EventDispatcher.php - 事件系统
# - app/Domain/Plugins/Services/Registration.php - 插件注册服务
# - app/Domain/Plugins/Repositories/Plugins.php - 插件加载器
# - app/Domain/Menu/Repositories/Menu.php - 菜单系统
```

---

## 2. 插件基础结构

### 2.1 标准目录结构

```
plugin/Blog/
├── register.php              # 插件注册文件（入口）
├── Controllers/
│   └── ShowList.php         # 控制器
├── Services/
│   └── BlogApiService.php   # API 服务
├── Models/
│   └── BlogPost.php         # 数据模型
├── Views/
│   └── list.blade.php       # 视图模板（Blade）
└── Repositories/
    └── BlogRepository.php   # 数据访问层
```

### 2.2 register.php - 核心注册文件

```php
<?php

namespace Leantime\Plugins\Blog;

use Leantime\Core\Events\EventDispatcher;
use Leantime\Domain\Plugins\Services\Registration;

class register
{
    /**
     * 插件信息
     */
    public static function info(): array
    {
        return [
            'name' => 'Leantime Blog',
            'version' => '1.0.0',
            'description' => 'A comprehensive blog plugin',
            'author' => 'Your Name',
            'homepage' => 'https://github.com/yourusername/leantime-blog',
            'requires' => [
                'leantime' => '>=3.0.0',
                'php' => '>=8.1',
            ],
        ];
    }

    /**
     * 插件启动 - 这个方法在插件加载时被调用
     */
    public static function boot(): void
    {
        // 1. 注册 PSR-4 自动加载器
        self::registerAutoloader();

        // 2. 注册菜单项
        self::registerMenuItems();

        // 3. 注册 API 路由
        self::registerApiRoutes();
    }

    /**
     * PSR-4 自动加载器
     */
    private static function registerAutoloader(): void
    {
        spl_autoload_register(function ($class) {
            $prefix = 'Leantime\\Plugins\\Blog\\';
            $base_dir = __DIR__ . '/';
            $len = strlen($prefix);

            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            $relative_class = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            if (file_exists($file)) {
                require $file;
            }
        });
    }

    /**
     * 注册菜单项 - 使用官方 Registration 服务
     */
    private static function registerMenuItems(): void
    {
        $registration = new Registration('Blog');

        // 添加到个人菜单（左侧边栏）
        $registration->addMenuItem(
            [
                'title' => 'Blog',
                'icon' => 'fa fa-newspaper-o',
                'tooltip' => 'Manage blog posts',
                'href' => '/blog/showList',
            ],
            'personal', // 菜单类型：personal, default, full_menu
            [90] // 位置权重
        );

        // 也可以添加到项目菜单
        $registration->addMenuItem(
            [
                'title' => 'Blog',
                'icon' => 'fa fa-newspaper-o',
                'tooltip' => 'Blog posts',
                'href' => '/blog/showList',
            ],
            'default',
            [45]
        );
    }

    /**
     * 注册 API 路由
     */
    private static function registerApiRoutes(): void
    {
        EventDispatcher::add_filter_listener(
            'leantime.core.api.jsonrpc',
            function ($methods) {
                return self::registerApiMethods($methods);
            }
        );
    }

    private static function registerApiMethods(array $methods): array
    {
        $methods['leantime.rpc.blog.listPosts'] = [
            'class' => '\\Leantime\\Plugins\\Blog\\Services\\BlogApiService',
            'method' => 'listPosts',
        ];

        return $methods;
    }
}

// ⚠️ 重要：必须在文件末尾调用 boot()
register::boot();
```

### 2.3 Controller 示例

```php
<?php

namespace Leantime\Plugins\Blog\Controllers;

use Illuminate\Support\Facades\DB;
use Leantime\Core\Controller\Controller;
use Leantime\Core\Controller\Frontcontroller;

class ShowList extends Controller
{
    /**
     * GET 请求处理
     */
    public function get(): mixed
    {
        // 从数据库获取数据
        $posts = DB::table('zp_blog_posts')
            ->whereNull('deleted_at')
            ->where('status', 'published')
            ->orderBy('created_at', 'DESC')
            ->get();

        // 传递数据到视图
        $this->tpl->assign('posts', $posts);

        // 渲染视图
        return $this->tpl->display('blog.list');
    }

    /**
     * POST 请求处理
     */
    public function post(array $params): mixed
    {
        // 处理表单提交
        return Frontcontroller::redirect('/blog/showList');
    }
}
```

---

## 3. 核心技术点

### 3.1 插件加载流程

```
1. Leantime 启动
   ↓
2. LoadPlugins 中间件触发
   ↓
3. 触发事件：leantime.core.middleware.loadplugins.handle.pluginsStart
   ↓
4. 查询数据库获取已启用插件（zp_plugins 表）
   ↓
5. 遍历插件目录，包含 register.php
   ↓
6. register::boot() 被调用
   ↓
7. 插件注册菜单、路由、事件监听器
   ↓
8. 菜单渲染时触发菜单事件
   ↓
9. 插件的菜单项被添加到界面
```

**关键代码位置**：`app/Core/Events/EventDispatcher.php:493-541`

### 3.2 事件系统详解

#### 事件命名规则

Leantime 使用 **短名称** 在代码中触发事件，但在监听时需要使用 **完整路径**：

```php
// ❌ 错误：直接使用短名称
EventDispatcher::add_filter_listener(
    'menuStructures.personal',  // 这不会被触发
    function ($menu) { ... }
);

// ✅ 正确：使用完整路径
EventDispatcher::add_filter_listener(
    'leantime.domain.menu.repositories.menu.getMenuStructure.menuStructures.personal',
    function ($menu) { ... }
);

// ✅ 最佳：使用官方 Registration 服务（自动处理路径）
$registration = new Registration('Blog');
$registration->addMenuItem([...], 'personal', [90]);
```

#### DispatchesEvents Trait

Leantime 的 Repository 类使用 `DispatchesEvents` trait，它会自动添加上下文前缀：

```php
// Menu.php 中的代码：
$filter = "menuStructures.$menuType";  // 短名称：menuStructures.personal
$this->menuStructures[$menuType] = self::dispatch_filter($filter, ...);

// DispatchesEvents trait 自动转换为：
// leantime.domain.menu.repositories.menu.getMenuStructure.menuStructures.personal
```

**完整路径格式**：`leantime.{domain}.{subdomain}.repositories.{class}.{method}.{eventName}`

### 3.3 菜单注册方式对比

#### 方式 1：直接使用 EventDispatcher（不推荐）

```php
// ❌ 复杂且容易出错
EventDispatcher::add_filter_listener(
    'leantime.domain.menu.repositories.menu.getMenuStructure.menuStructures.personal',
    function ($menu) {
        $menu[90] = [
            'title' => "<span class='fa fa-newspaper-o'></span> Blog",
            'tooltip' => 'Manage blog posts',
            'href' => '/blog/showList',
            'type' => 'item',
            'module' => 'Blog',
        ];
        return $menu;
    },
    50 // 优先级
);
```

#### 方式 2：使用官方 Registration 服务（推荐）

```php
// ✅ 简单、清晰、不容易出错
$registration = new Registration('Blog');

$registration->addMenuItem(
    [
        'title' => 'Blog',
        'icon' => 'fa fa-newspaper-o',
        'tooltip' => 'Manage blog posts',
        'href' => '/blog/showList',
    ],
    'personal', // 菜单类型
    [90]       // 位置
);
```

### 3.4 数据库访问

Leantime 使用 Laravel 的 Illuminate Database：

```php
use Illuminate\Support\Facades\DB;

// 查询构建器
$posts = DB::table('zp_blog_posts')
    ->where('status', 'published')
    ->orderBy('created_at', 'DESC')
    ->get();

// 插入
DB::table('zp_blog_posts')->insert([
    'title' => 'New Post',
    'content' => 'Content here',
    'created_at' => date('Y-m-d H:i:s'),
]);

// 更新
DB::table('zp_blog_posts')
    ->where('id', $id)
    ->update(['title' => 'Updated Title']);

// 删除（软删除）
DB::table('zp_blog_posts')
    ->where('id', $id)
    ->update(['deleted_at' => date('Y-m-d H:i:s')]);
```

---

## 4. 踩过的坑与解决方案

### 🔴 坑 1：MySQL ONLY_FULL_GROUP_BY 错误

#### 问题描述

```
SQLSTATE[42000]: Syntax error or access violation: 1055
Expression #1 of SELECT list is not in GROUP BY clause and contains
nonaggregated column 'leantime.zp_plugins.id' which is not functionally
dependent on columns in GROUP BY clause
```

#### 错误原因

Leantime 核心代码在 `app/Domain/Plugins/Repositories/Plugins.php:44` 使用了不兼容 MySQL 8.0 严格模式的 SQL：

```php
$query = 'SELECT id, foldername, name, enabled FROM zp_plugins WHERE 1=1 ';
$query .= ' GROUP BY name ';  // ❌ 只分组 name，但选择了 id, foldername, enabled
```

#### 解决方案

**方案 1**：修复核心代码（临时方案）

```php
// 注释掉 GROUP BY
// $query .= ' GROUP BY name ';
```

**方案 2**：修改 MySQL 配置（生产环境方案）

```sql
-- 查看当前 SQL 模式
SELECT @@sql_mode;

-- 移除 ONLY_FULL_GROUP_BY
SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
```

**方案 3**：正确的 GROUP BY（提交给官方）

```php
$query .= ' GROUP BY id, foldername, name, enabled ';
```

#### 影响范围

这是 **Leantime 核心 Bug**，影响所有插件加载。建议提交 Issue 给官方。

---

### 🔴 坑 2：命名空间冲突

#### 问题描述

```
Class "Leantime\Plugins\LeantimeBlog\Controllers\BlogController" does not exist
```

#### 错误原因

项目中存在两个插件目录：
- `plugin/Blog/` - 当前正确的目录
- `plugin/LeantimeBlog/` - 历史遗留目录

Leantime 加载了错误的命名空间。

#### 解决方案

```bash
# 删除重复的旧目录
rm -rf plugin/LeantimeBlog/

# 确保只有一个插件目录
ls -la plugin/
# 应该只看到 Blog/
```

#### 避坑建议

- 一个插件只保留一个目录
- 目录名应该与数据库 `zp_plugins.foldername` 一致
- 命名空间遵循：`Leantime\Plugins\{FolderName}\`

---

### 🔴 坑 3：菜单事件不触发

#### 问题描述

插件加载成功，`register::boot()` 被调用，但菜单不显示。

#### 错误原因

使用了错误的事件名称：

```php
// ❌ 错误：使用短名称
EventDispatcher::add_filter_listener(
    'menuStructures.personal',  // 事件永远不会被触发
    function ($menu) { ... }
);

// ❌ 错误：路径不完整
EventDispatcher::add_filter_listener(
    'leantime.domain.menu.getMenuStructure',
    function ($menu) { ... }
);
```

#### 解决方案

**方案 1**：使用完整事件路径

```php
EventDispatcher::add_filter_listener(
    'leantime.domain.menu.repositories.menu.getMenuStructure.menuStructures.personal',
    function ($menu) { ... }
);
```

**方案 2**：使用官方 Registration 服务（推荐）

```php
$registration = new Registration('Blog');
$registration->addMenuItem([...], 'personal', [90]);
```

#### 调试技巧

在 `register.php` 中添加日志：

```php
EventDispatcher::add_filter_listener(
    'leantime.domain.menu.repositories.menu.getMenuStructure.menuStructures.personal',
    function ($menu) {
        error_log('✓ Blog menu event triggered');
        // ...
        return $menu;
    }
);
```

查看日志：

```bash
docker exec leantime-web-prod tail -f /var/www/html/storage/logs/leantime.log
```

---

### 🔴 坑 4：Docker 文件同步延迟（Windows）

#### 问题描述

修改 `register.php` 后，容器内的文件没有更新。

#### 错误原因

Windows + Docker Desktop + 卷挂载存在文件同步缓存延迟。

#### 解决方案

**方案 1**：手动复制文件（最可靠）

```bash
docker cp D:/github/leantime/leantime-blog/plugin/Blog/register.php \
    leantime-web-prod:/var/www/html/app/Plugins/Blog/register.php

# 清除缓存
docker exec leantime-web-prod php /var/www/html/bin/leantime cache:clearAll
```

**方案 2**：重启容器

```bash
docker-compose restart leantime-web-prod
```

**方案 3**：使用 WSL2（长期方案）

在 WSL2 环境中开发，避免 Windows 文件系统性能问题。

---

### 🔴 坑 5：数据库字段 NULL 值错误

#### 问题描述

```
TypeError: Cannot assign null to property InstalledPlugin::$authors of type array|string
```

#### 错误原因

数据库中插件记录的某些字段为 NULL，但 PHP 模型要求非空类型。

#### 解决方案

更新数据库记录：

```php
$pdo = DB::connection()->getPdo();

$authors = json_encode([
    ['name' => 'Claude Code', 'email' => 'noreply@anthropic.com']
]);
$homepage = 'https://github.com/leantime/leantime-blog';

$pdo->prepare("UPDATE zp_plugins SET authors = ?, homepage = ? WHERE foldername = 'Blog'")
    ->execute([$authors, $homepage]);
```

#### 避坑建议

在 `register.php` 的 `info()` 方法中提供完整信息：

```php
public static function info(): array
{
    return [
        'name' => 'Leantime Blog',
        'version' => '1.0.0',
        'description' => 'Complete description here',
        'author' => 'Your Name',  // ⚠️ 必需
        'homepage' => 'https://...',  // ⚠️ 必需
        'requires' => [
            'leantime' => '>=3.0.0',
            'php' => '>=8.1',
        ],
    ];
}
```

---

### 🔴 坑 6：EventDispatcher 导入错误

#### 问题描述

```
Class "Leantime\Core\Events" not found
```

#### 错误原因

```php
// ❌ 错误的导入
use Leantime\Core\Events;

Events::add_filter_listener(...);  // Events 是命名空间，不是类
```

#### 解决方案

```php
// ✅ 正确的导入
use Leantime\Core\Events\EventDispatcher;

EventDispatcher::add_filter_listener(...);
```

---

## 5. 最佳实践

### 5.1 插件开发清单

- [ ] 使用官方 `Registration` 服务注册菜单
- [ ] 在 `register.php` 末尾调用 `register::boot()`
- [ ] 提供完整的 `info()` 信息
- [ ] 使用 PSR-4 自动加载器
- [ ] 遵循 Leantime 命名约定
- [ ] 数据库表名使用 `zp_` 前缀
- [ ] 使用 Illuminate DB 查询构建器
- [ ] 添加适当的日志记录
- [ ] 编写 Playwright 自动化测试

### 5.2 目录命名规范

```
plugin/
  └── PluginName/              # 与数据库 foldername 一致
      ├── register.php         # 必需
      ├── Controllers/         # 控制器
      ├── Services/           # 业务逻辑
      ├── Repositories/       # 数据访问
      ├── Models/             # 数据模型
      └── Views/              # 视图模板
```

### 5.3 命名空间规范

```php
namespace Leantime\Plugins\PluginName;           // 根命名空间
namespace Leantime\Plugins\PluginName\Controllers;
namespace Leantime\Plugins\PluginName\Services;
namespace Leantime\Plugins\PluginName\Repositories;
```

### 5.4 路由命名规范

```php
'/pluginname/actionName'  // 小写插件名 + 驼峰动作
'/blog/showList'
'/blog/createPost'
'/blog/editPost'
```

---

## 6. 调试技巧

### 6.1 查看日志

```bash
# 实时日志
docker exec leantime-web-prod tail -f /var/www/html/storage/logs/leantime.log

# 查看最近 50 行
docker exec leantime-web-prod tail -50 /var/www/html/storage/logs/leantime.log

# 查看今天的日志
docker exec leantime-web-prod tail -100 /var/www/html/storage/logs/leantime-$(date +%Y-%m-%d).log
```

### 6.2 添加调试日志

```php
// 在 register.php 中
public static function boot(): void
{
    error_log('✓ Blog plugin boot() called');

    EventDispatcher::add_filter_listener(
        'leantime.domain.menu.repositories.menu.getMenuStructure.menuStructures.personal',
        function ($menu) {
            error_log('✓ Blog menu event triggered');
            error_log('Menu before: ' . json_encode(array_keys($menu)));

            // 添加菜单项...

            error_log('Menu after: ' . json_encode(array_keys($menu)));
            return $menu;
        }
    );
}
```

### 6.3 检查插件是否加载

```bash
# 查看数据库中的插件状态
docker exec leantime-mysql-prod mysql -uleantime -ppassword leantime \
  -e "SELECT id, foldername, name, enabled FROM zp_plugins WHERE foldername='Blog'"

# 检查文件是否存在
docker exec leantime-web-prod ls -la /var/www/html/app/Plugins/Blog/

# 验证 PHP 语法
docker exec leantime-web-prod php -l /var/www/html/app/Plugins/Blog/register.php
```

### 6.4 清除缓存

```bash
# 清除所有缓存
docker exec leantime-web-prod php /var/www/html/bin/leantime cache:clearAll

# 重启容器
docker-compose restart leantime-web-prod
```

---

## 7. 测试验证

### 7.1 手动测试步骤

1. 启用插件
   - 访问后台 → Settings → Plugins
   - 确认插件显示为 "Installed" 和 "Enabled"

2. 验证菜单
   - 刷新页面
   - 检查左侧边栏是否显示 "Blog" 菜单项
   - 点击菜单，验证是否跳转到 `/blog/showList`

3. 测试功能
   - 创建博客文章
   - 编辑文章
   - 删除文章
   - 查看列表

### 7.2 Playwright 自动化测试

见《Playwright 测试模板和使用指南》文档。

---

## 8. 常见问题 FAQ

### Q1: 插件已启用但菜单不显示？

**检查清单**：
1. 确认数据库中 `enabled = 1`
2. 检查 `register.php` 语法是否正确
3. 验证事件名称是否完整
4. 查看日志是否有错误
5. 清除缓存并刷新页面

### Q2: 如何调试菜单事件？

在 `register.php` 中添加日志：

```php
EventDispatcher::add_filter_listener(
    'leantime.domain.menu.repositories.menu.getMenuStructure.menuStructures.personal',
    function ($menu) {
        error_log('✓ Event triggered');
        return $menu;
    }
);
```

如果日志没有输出，说明事件名称错误或插件未加载。

### Q3: Windows 下文件修改不生效？

使用 `docker cp` 手动复制文件：

```bash
docker cp plugin/Blog/register.php leantime-web-prod:/var/www/html/app/Plugins/Blog/
docker exec leantime-web-prod php /var/www/html/bin/leantime cache:clearAll
```

### Q4: 如何添加子菜单？

```php
$registration->addMenuItem(
    [
        'title' => 'All Posts',
        'href' => '/blog/showList',
        'icon' => 'fa fa-list',
    ],
    'personal',
    [90, 10] // [父菜单位置, 子菜单位置]
);
```

---

## 9. 参考资料

### 官方文档
- [Plugin Development](https://docs.leantime.io/development/plugin-development)
- [Plugin Configuration](https://docs.leantime.io/installation/plugin-configuration)

### 源码研究
- `app/Core/Events/EventDispatcher.php` - 事件系统
- `app/Domain/Plugins/Services/Registration.php` - 插件注册
- `app/Domain/Menu/Repositories/Menu.php` - 菜单系统

### 数据库表
- `zp_plugins` - 插件元数据
- `zp_blog_posts` - 博客文章（示例）

---

## 10. 总结

### 关键经验

1. **使用官方 API**：优先使用 `Registration` 服务，避免直接操作 EventDispatcher
2. **完整的事件路径**：菜单事件需要完整路径，不能使用短名称
3. **MySQL 兼容性**：注意 ONLY_FULL_GROUP_BY 模式
4. **文件同步**：Windows 下使用 `docker cp` 确保文件更新
5. **充分测试**：使用 Playwright 编写自动化测试
6. **日志调试**：添加适当的日志帮助排查问题

### 开发时间估算

- 基础插件结构：1-2 小时
- 菜单集成（含调试）：2-4 小时
- 数据库和 CRUD：2-3 小时
- 视图和样式：3-5 小时
- API 集成：2-3 小时
- 测试和文档：2-3 小时

**总计**：约 12-20 小时（取决于功能复杂度）

### 避免的弯路

通过本指南，后续开发者可以避免：
- ❌ 15+ 轮迭代调试菜单事件
- ❌ 多次修改命名空间和目录结构
- ❌ 反复研究事件系统源码
- ❌ 手动测试菜单显示（应使用自动化）

---

**文档版本**: 1.0
**最后更新**: 2025-12-14
**作者**: Claude Code
**项目**: Leantime Blog Plugin
