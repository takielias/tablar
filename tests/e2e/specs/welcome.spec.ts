import { test, expect } from '@playwright/test';

test.describe('Welcome page', () => {
    test('renders Tablar-branded card on /', async ({ page }) => {
        const response = await page.goto('/');

        expect(response?.status()).toBe(200);
        await expect(page.getByRole('heading', { name: 'Welcome to Tablar' })).toBeVisible();
        await expect(page.locator('.page-center .card')).toBeVisible();
    });

    test('shows Login + Register CTAs when logged out', async ({ page }) => {
        await page.goto('/');

        await expect(page.getByRole('link', { name: 'Login' })).toBeVisible();
        await expect(page.getByRole('link', { name: 'Register' })).toBeVisible();
    });

    test('does not ship Tailwind, Nunito, or Laravel marketing SVG', async ({ page }) => {
        const html = await (await page.goto('/'))!.text();

        expect(html).not.toContain('Tailwind');
        expect(html).not.toContain('Nunito');
        expect(html).not.toContain('#EF3B2D');
        expect(html).not.toContain('Laracasts');
    });

    test('console is clean (no errors, no deprecations)', async ({ page }) => {
        const messages: string[] = [];
        page.on('console', (msg) => {
            if (msg.type() === 'error' || msg.type() === 'warning') {
                messages.push(`${msg.type()}: ${msg.text()}`);
            }
        });

        await page.goto('/');
        await page.waitForLoadState('networkidle');

        expect(messages).toEqual([]);
    });
});
