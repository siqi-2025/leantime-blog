# 推送到 GitHub 指南

## 前置步骤

1. **在 GitHub 创建仓库**
   - 访问：https://github.com/new
   - 仓库名：`leantime-blog`
   - 设置为 Public 或 Private
   - ❌ 不要勾选任何初始化选项

2. **获取 Personal Access Token**
   - 访问：https://github.com/settings/tokens
   - 点击 "Generate new token (classic)"
   - 勾选权限：`repo` (完整权限)
   - 生成并复制 token

## 推送步骤

### 方法 1：使用 Windows Credential Manager（推荐）

```bash
# 1. 添加远程仓库（替换为您的用户名）
git remote add origin https://github.com/您的用户名/leantime-blog.git

# 2. 推送代码
git push -u origin master

# 此时会弹出认证窗口：
# - Username: 您的 GitHub 用户名
# - Password: 您的 Personal Access Token (粘贴 token)

# 3. 推送标签
git push origin --tags
```

Windows Git 会自动将凭据保存到 Windows Credential Manager，下次推送不需要再输入。

### 方法 2：在 URL 中嵌入 Token（快速但不推荐）

```bash
# 1. 添加包含 token 的远程仓库
git remote add origin https://ghp_您的TOKEN@github.com/您的用户名/leantime-blog.git

# 2. 推送
git push -u origin master
git push origin --tags
```

⚠️ **安全警告**：此方法会在 `.git/config` 中明文存储 token，不建议使用。

## 验证推送成功

推送成功后，访问您的 GitHub 仓库：
```
https://github.com/您的用户名/leantime-blog
```

您应该能看到：
- 所有代码文件
- CHANGELOG.md
- 文档目录
- 标签 `v0.1.0-baseline`

## Token 安全提示

1. **永远不要**在代码、文档或公开对话中暴露 token
2. **使用后**如果 token 被泄露，立即撤销：https://github.com/settings/tokens
3. **定期轮换** token（每 3-6 个月）
4. **最小权限原则**：只授予必需的权限

## 如果推送失败

### 错误：Authentication failed
- 检查 token 是否正确
- 确认 token 有 `repo` 权限
- 检查 token 是否已过期

### 错误：Repository not found
- 确认仓库已在 GitHub 创建
- 检查用户名和仓库名是否正确
- 确认您有该仓库的访问权限

### 错误：Remote already exists
```bash
# 删除旧的 remote 并重新添加
git remote remove origin
git remote add origin https://github.com/您的用户名/leantime-blog.git
```
