import { defineConfig, devices } from '@playwright/test';

/**
 * Tablar e2e — drives a running Laravel app through the welcome,
 * auth, dashboard, and dark-mode flows.
 *
 * Set TABLAR_E2E_BASE_URL to the running app (defaults to a DDEV
 * site). The companion `scripts/run-fresh-install.sh` provisions one
 * end to end, prints the resolved URL, and exports it as
 * TABLAR_E2E_BASE_URL for this suite.
 */
export default defineConfig({
    testDir: './specs',
    fullyParallel: true,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 1 : 0,
    workers: process.env.CI ? 1 : undefined,
    reporter: [
        ['list'],
        ['html', { open: 'never', outputFolder: 'playwright-report' }],
    ],
    use: {
        baseURL: process.env.TABLAR_E2E_BASE_URL || 'https://tablar-demo.ddev.site',
        trace: 'on-first-retry',
        screenshot: 'only-on-failure',
        ignoreHTTPSErrors: true,
    },
    projects: [
        {
            name: 'chromium-light',
            use: {
                ...devices['Desktop Chrome'],
                colorScheme: 'light',
            },
        },
        {
            name: 'chromium-dark',
            use: {
                ...devices['Desktop Chrome'],
                colorScheme: 'dark',
            },
        },
    ],
});
