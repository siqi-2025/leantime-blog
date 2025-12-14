@echo off
REM ============================================================
REM Leantime Blog Plugin - 推送到 GitHub 脚本
REM ============================================================
REM
REM 使用前请先在 GitHub 创建仓库，然后替换下面的仓库地址
REM
REM ============================================================

cd /d D:\github\leantime\leantime-blog

echo ============================================
echo 步骤 1: 添加远程仓库
echo ============================================
echo.
echo 请替换下面的 GitHub 仓库地址为您的实际地址:
echo git remote add origin https://github.com/你的用户名/leantime-blog.git
echo.
pause

REM 替换成您的 GitHub 仓库地址
git remote add origin https://github.com/你的用户名/leantime-blog.git

echo.
echo ============================================
echo 步骤 2: 确认当前分支
echo ============================================
git branch

echo.
echo ============================================
echo 步骤 3: 推送代码和标签到 GitHub
echo ============================================
git push -u origin master
git push origin --tags

echo.
echo ============================================
echo 完成！
echo ============================================
echo.
echo 访问您的 GitHub 仓库查看代码:
echo https://github.com/你的用户名/leantime-blog
echo.
pause
