---
name: install-laravel-ajax-builder
description: Step-by-step interactive installer for takielias/lab (laravel-ajax-builder) — composer require, lab:install artisan command (patches resources/js/app.js and scaffolds config), CSRF meta tag check, smoke @submit + @alert Blade directive test. Pause for user approval at every shell command.
---

# Install Laravel Ajax Builder (Lab)

Slash command: `/laravel-boost:install-laravel-ajax-builder`

Note: composer name is `takielias/lab` (the GitHub repo is `laravel-ajax-builder`, but the Packagist + composer name is `lab`).

Walk one step at a time. **Pause for user approval before every shell command.**

## Pre-flight checks

1. **Laravel version**:
   ```bash
   php artisan --version
   ```
   Lab supports Laravel 11.x / 12.x / 13.x.

2. **PHP ≥ 8.3** (Lab's minimum):
   ```bash
   php -v
   ```

3. **resources/js/app.js exists** (lab:install patches it):
   ```bash
   test -f resources/js/app.js && echo PRESENT || echo MISSING
   ```
   If missing — Laravel default skeleton has it; recent Vite-based skeletons too. If missing, user is on a custom JS structure and lab:install will error. Either restore `resources/js/app.js` OR install JS pieces manually.

4. **package.json present** (lab:install requires it):
   ```bash
   test -f package.json && echo PRESENT || echo MISSING
   ```

5. **CSRF meta tag in main layout** — required for fetch requests:
   ```bash
   grep -rn "csrf-token" resources/views/layouts/ resources/views/vendor/tablar/master.blade.php 2>/dev/null
   ```
   If missing, plan to add: `<meta name="csrf-token" content="{{ csrf_token() }}">` inside `<head>`. tablar's published `master.blade.php` already includes it.

6. **Already installed?**:
   ```bash
   composer show takielias/lab
   ```
   If present → skip step 1, jump to step 4 (smoke).

## Step 1 — Composer require

```bash
composer require takielias/lab
```

Service provider `Takielias\Lab\LabServiceProvider` auto-discovered. Facade alias `Lab` → `Takielias\Lab\Facades\Lab` registered automatically.

## Step 2 — Run lab:install

```bash
php artisan lab:install
```

What it does (read `src/Commands/InstallLAB.php`):
- Checks `package.json` exists.
- Checks `resources/js/app.js` exists.
- Patches `resources/js/app.js` to import Lab's JS helpers (the `window.ajax*` family + `.ajax-submit-button` auto-binding).
- Scaffolds `config/lab.php` (publishes the package config).

After it finishes, the command prints:
```
LAB is now installed 🚀
Once the installation is done, run "npm run dev"
```

## Step 3 — Build assets

```bash
npm install      # if dependencies need refresh
npm run dev      # or: npm run build
```

This compiles the Lab JS helpers into the Vite manifest. Without build, `window.ajaxPost` will be undefined.

## Step 4 — Add CSRF meta tag (if missing)

Confirmed in pre-flight check 5. If missing from the active layout, propose this insertion inside `<head>`:

```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

If user is on tablar layout — already present.

## Step 5 — Smoke test

Lab is a **server-side response builder**, not a JS DSL. The flow is:
- Client sends a fetch request (or uses `<button class="ajax-submit-button">` to auto-submit a form).
- Server controller builds the response with `Lab::...->setMessage(...)->asAjax()` and returns `JsonResponse`.
- Client JS reads `message`, `redirect`, `alert`, etc. from the JSON and renders accordingly.

For smoke, suggest a minimal route + view:

**Route** (`routes/web.php`):
```php
Route::post('/lab-smoke', function () {
    return Lab::setMessage('It works!')->setAlertView('success')->renderAlert()->asAjax();
})->middleware('web');
```

**View** (any Blade view):
```blade
<form action="/lab-smoke" method="POST">
    @csrf
    @alert
    <button type="submit" class="ajax-submit-button btn btn-primary">@submit('Test')</button>
</form>
```

The `@alert` directive emits the alert-target div, `@submit` emits the button label markup. The `.ajax-submit-button` class triggers Lab's JS auto-binder.

Click the button → expect:
- POST to `/lab-smoke` via fetch (no full page reload).
- Alert area populates with "It works!".
- `<button>` shows loading state during request.

Verify in DevTools Network tab — request should be a fetch (Type: `xhr` or `fetch`), NOT a form-submission navigation.

## Configuration recap

`config/lab.php` after install:

- Default validation error display mode (top alert vs per-field).
- Fade-out timing.
- Redirect delay.
- Default alert types/icons.

Inspect after install for the full key list. Every key has a fluent setter on `Lab` (e.g. `setFadeOutTime`, `setRedirectDelay`).

## Idempotent rerun

- Pre-flight skips composer require if installed.
- `lab:install` is safe to re-run — won't double-patch `app.js` if pattern already present (reads + checks before writing).
- Config publish skipped if file matches.

## Common pitfalls

- **`Lab::` undefined** — facade alias missing. Should auto-discover; if not, run `php artisan optimize:clear` then `php artisan package:discover --ansi`.
- **`.ajax-submit-button` clicks do nothing** — JS not built; run `npm run dev` or `npm run build`.
- **POST returns 419 Page Expired** — CSRF meta tag missing in layout. Add it.
- **Lab response renders as raw JSON in browser** — fetch handler in JS not wired; `lab:install` failed silently. Re-run `lab:install` and check `resources/js/app.js` for the Lab import lines.
- **Multiple alerts stack on resubmit** — `@alert` directive renders one slot; previous content accumulates. Clear via `setMessage(null)` or use `disableFadeOut()->setFadeOutTime(...)` to control lifecycle.

## Combining with tablar-kit confirm-modal

For a delete flow:
- `<x-confirm>` triggers AJAX DELETE on user approval.
- Backend returns `Lab::setMessage('Deleted')->renderAlert()->asAjax()`.
- Confirm-modal toast surfaces the message via `tablar-kit.confirm.toast`.

This is a common pattern — both packages cooperate cleanly because both expect the same JSON `message` key.

## Related

- Skill `laravel-ajax-builder-development` — full Lab method catalog (server-side fluent API).
- Skill `tablar-kit-confirm-modal-development` — pair confirm modal with Lab responses.
- Source: `packages/takielias/laravel-ajax-builder/src/Lab.php`, `src/Commands/InstallLAB.php`, `resources/js/`.

## Out of scope

- Full Lab method reference — defer to dev skill.
- Blade directive internals (`@alert`, `@submit`) — defer to dev skill.
- jQuery migration path — phase-7 revamp dropped jQuery; no migration story for L11+ users.
