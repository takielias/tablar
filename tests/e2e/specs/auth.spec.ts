import { test, expect } from '@playwright/test';

test.describe('Auth scaffolding', () => {
    test('login page renders with form fields', async ({ page }) => {
        const response = await page.goto('/login');

        expect(response?.status()).toBe(200);
        await expect(page.locator('input[name="email"]')).toBeVisible();
        await expect(page.locator('input[name="password"]')).toBeVisible();
        await expect(page.getByRole('button', { name: /sign in|login/i })).toBeVisible();
    });

    test('register page renders with form fields', async ({ page }) => {
        const response = await page.goto('/register');

        expect(response?.status()).toBe(200);
        await expect(page.locator('input[name="name"]')).toBeVisible();
        await expect(page.locator('input[name="email"]')).toBeVisible();
        await expect(page.locator('input[name="password"]')).toBeVisible();
    });

    test('successful registration redirects to /home', async ({ page }) => {
        await page.goto('/register');

        const email = `test+${Date.now()}@example.com`;
        await page.locator('input[name="name"]').fill('Tablar Tester');
        await page.locator('input[name="email"]').fill(email);
        await page.locator('input[name="password"]').fill('password123');
        await page.locator('input[name="password_confirmation"]').fill('password123');

        await page.getByRole('button', { name: /register|create/i }).click();
        await page.waitForURL(/\/home$/);

        expect(page.url()).toMatch(/\/home$/);
    });
});
