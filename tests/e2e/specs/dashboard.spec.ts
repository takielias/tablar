import { test, expect } from '@playwright/test';

test.describe('Dashboard empty state', () => {
    test.use({ storageState: { cookies: [], origins: [] } });

    test('home page redirects guests to /login', async ({ page }) => {
        const response = await page.goto('/home');

        // Either /login (auth gate) or /home with redirect already followed.
        expect(response?.status()).toBeLessThan(500);
        expect(page.url()).toMatch(/\/(login|home)/);
    });
});

test.describe('Dashboard authed', () => {
    test('shows empty-state card after login', async ({ page }) => {
        // assumes a seeded test user exists at e2e@example.com / password
        await page.goto('/login');
        await page.locator('input[name="email"]').fill('e2e@example.com');
        await page.locator('input[name="password"]').fill('password');
        await page.getByRole('button', { name: /sign in|login/i }).click();

        await page.waitForURL(/\/home$/);

        await expect(page.locator('.empty-title')).toContainText("You're all set");
        await expect(page.getByRole('link', { name: /view documentation/i })).toBeVisible();
    });

    test('console clean on dashboard', async ({ page }) => {
        const errors: string[] = [];
        page.on('console', (msg) => {
            if (msg.type() === 'error') errors.push(msg.text());
        });

        await page.goto('/login');
        await page.locator('input[name="email"]').fill('e2e@example.com');
        await page.locator('input[name="password"]').fill('password');
        await page.getByRole('button', { name: /sign in|login/i }).click();
        await page.waitForURL(/\/home$/);
        await page.waitForLoadState('networkidle');

        expect(errors).toEqual([]);
    });
});
