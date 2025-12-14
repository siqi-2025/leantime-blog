import { defineConfig, devices } from '@playwright/test';

/**
 * Playwright 配置 - Leantime Blog Plugin Tests
 *
 * 运行测试:
 * npm test                     # 运行所有测试
 * npm test -- --headed        # 显示浏览器
 * npm test -- --debug         # 调试模式
 * npm test -- --ui            # UI 模式
 */

export default defineConfig({
  // 测试目录
  testDir: '.',

  // 测试匹配模式
  testMatch: '**/*.spec.ts',

  // 全局超时时间（30 秒）
  timeout: 30 * 1000,

  // 断言超时时间（5 秒）
  expect: {
    timeout: 5000,
  },

  // 测试失败重试次数
  retries: process.env.CI ? 2 : 0,

  // 并行执行的工作线程数
  workers: process.env.CI ? 1 : undefined,

  // 报告器配置
  reporter: [
    ['list'],
    ['html', { outputFolder: 'playwright-report', open: 'never' }],
    ['json', { outputFile: 'test-results/results.json' }],
  ],

  // 使用配置
  use: {
    // 基础 URL
    baseURL: 'http://localhost:6007',

    // 跟踪配置（失败时记录）
    trace: 'on-first-retry',

    // 截图配置（失败时截图）
    screenshot: 'only-on-failure',

    // 视频配置（失败时录制）
    video: 'retain-on-failure',

    // 浏览器上下文选项
    viewport: { width: 1280, height: 720 },
    ignoreHTTPSErrors: true,

    // 导航超时
    navigationTimeout: 15 * 1000,
  },

  // 项目配置（多浏览器测试）
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },

    /* 可选：其他浏览器
    {
      name: 'firefox',
      use: { ...devices['Desktop Firefox'] },
    },

    {
      name: 'webkit',
      use: { ...devices['Desktop Safari'] },
    },
    */

    /* 可选：移动端测试
    {
      name: 'Mobile Chrome',
      use: { ...devices['Pixel 5'] },
    },

    {
      name: 'Mobile Safari',
      use: { ...devices['iPhone 12'] },
    },
    */
  ],

  // Web Server（可选，如果需要启动开发服务器）
  // webServer: {
  //   command: 'npm run start',
  //   url: 'http://localhost:6007',
  //   reuseExistingServer: !process.env.CI,
  // },

  // 输出目录
  outputDir: 'test-results',
});
