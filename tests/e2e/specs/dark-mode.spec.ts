import { test, expect } from '@playwright/test';

test.describe('Dark-mode toggle', () => {
    test('respects prefers-color-scheme: dark on first load', async ({ browser }) => {
        const ctx = await browser.newContext({ colorScheme: 'dark' });
        const page = await ctx.newPage();

        await page.goto('/');
        const theme = await page.locator('html').getAttribute('data-bs-theme');

        expect(theme).toBe('dark');
        await ctx.close();
    });

    test('respects prefers-color-scheme: light on first load', async ({ browser }) => {
        const ctx = await browser.newContext({ colorScheme: 'light' });
        const page = await ctx.newPage();

        await page.goto('/');
        const theme = await page.locator('html').getAttribute('data-bs-theme');

        expect(theme).toBe('light');
        await ctx.close();
    });

    test('toggle button flips data-bs-theme and persists across reload', async ({ page }) => {
        await page.goto('/');

        const initialTheme = await page.locator('html').getAttribute('data-bs-theme');
        const targetValue = initialTheme === 'dark' ? 'light' : 'dark';

        // Click the corresponding toggle button
        await page.locator(`[data-bs-theme-value="${targetValue}"]`).first().click();

        await expect(page.locator('html')).toHaveAttribute('data-bs-theme', targetValue);

        const stored = await page.evaluate(() => localStorage.getItem('tablar.theme'));
        expect(stored).toBe(targetValue);

        // Reload — preference should persist
        await page.reload();
        await expect(page.locator('html')).toHaveAttribute('data-bs-theme', targetValue);
    });
});
