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
