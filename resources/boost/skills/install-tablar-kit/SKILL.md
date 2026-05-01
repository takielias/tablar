---
name: install-tablar-kit
description: Step-by-step interactive installer for takielias/tablar-kit on top of an existing takielias/tablar app — composer require, config publish, confirm-modal mount in master.blade.php, JS init wiring, build step. Pause for user approval at every shell command. Idempotent on rerun.
---

# Install Tablar Kit

Slash command: `/laravel-boost:install-tablar-kit`

This skill is invoked explicitly by the user. Walk through the install one step at a time. **Pause and wait for user approval before running every shell command** — never chain steps together.

## Pre-flight checks (run silently before step 1)

Before any install action, verify:

1. **tablar installed**:
   ```bash
   composer show takielias/tablar
   ```
   If missing → abort. Tell user: "tablar-kit layers on top of takielias/tablar. Install tablar first via `composer require takielias/tablar && php artisan tablar:install`, then re-invoke this slash command."

2. **Laravel version**:
   ```bash
   php artisan --version
   ```
   tablar-kit supports Laravel 11, 12, 13. If older, abort with a version hint.

3. **master.blade.php published**:
   ```bash
   test -f resources/views/vendor/tablar/master.blade.php && echo PRESENT || echo MISSING
   ```
   If missing → tell user to run `php artisan tablar:install` first to publish the layout.

4. **tablar-kit not already installed** (idempotent guard):
   ```bash
   composer show takielias/tablar-kit
   ```
   If present → skip step 1, jump straight to verification at step 5.

If all four checks pass, summarize state to the user and ask for approval to proceed.

## Step 1 — Composer require

Tell user the command, ask for approval, then run:

```bash
composer require takielias/tablar-kit
```

Service provider `TakiElias\TablarKit\TablarKitServiceProvider` is auto-discovered via `extra.laravel.providers` — no manual registration needed.

If composer fails on Laravel version constraint → confirm `composer.json` Laravel constraint matches `^11.0|^12.0|^13.0`. If not, suggest `composer require takielias/tablar-kit:dev-main` as a temporary workaround pending a tagged release.

## Step 2 — Publish config

```bash
php artisan vendor:publish --tag=tablar-kit-config
```

Writes `config/tablar-kit.php`. Idempotent — if file already exists with same hash, Laravel will prompt; user can skip safely.

If user has a stale published config from an older version (missing `confirm` alias keys), they need `--force` here. Default behavior is to re-publish only if file changes.

## Step 3 — Mount confirm-modal in master layout

Patch `resources/views/vendor/tablar/master.blade.php` to mount the singleton confirm modal once before `</body>`.

**Before patching, search for existing mount:**
```bash
grep -n "x-confirm-modal" resources/views/vendor/tablar/master.blade.php
```

If found → already mounted, skip to step 4.

If not found → propose this insertion before the closing `</body>` tag:

```blade
@if (class_exists(\TakiElias\TablarKit\Components\Modals\ConfirmModal::class))
    <x-confirm-modal />
@endif
```

The `class_exists` guard makes the layout safe even if tablar-kit is later removed. Show the user the diff and ask approval before writing.

## Step 4 — Wire JS plugin

Append a single import line to `resources/js/tabler-init.js` (or whatever the published JS init file is — check after `tablar:install`):

```js
import '../../vendor/takielias/tablar-kit/resources/js/plugins/confirm-modal.js';
```

Idempotent: grep for `confirm-modal.js` first; skip if already imported.

Path note: the import resolves through Vite's filesystem traversal — it pulls directly from the vendor folder so users get plugin updates with `composer update`. If the user has a custom JS structure, adjust the relative path.

## Step 5 — Build assets

```bash
npm install
npm run build      # or: npm run dev for HMR
```

Required: Vite manifest must contain the new plugin import. Without `npm run build` the modal JS won't load and `<x-confirm>` triggers will be silent.

## Step 6 — Smoke test

Suggest one of:

- Visit a route that already uses `<x-confirm>` (e.g. tablar-demo's `/tablar-kit/datatable` if user is dogfooding).
- Add a quick Blade snippet to any view:
  ```blade
  <x-confirm
      :url="url('/')"
      method="GET"
      title="Test confirm?"
      message="Click confirm to fire the modal."
      button="Confirm"
      class="btn btn-primary">
      Open test confirm
  </x-confirm>
  ```
  Click the button — the modal should appear.

## Configuration recap

After install, top-3 config keys to know (from `config/tablar-kit.php`):

- `tablar-kit.confirm.toast` (bool, default true) — success toast on AJAX confirm response.
- `tablar-kit.components` — Blade component alias map (`confirm`, `confirm-modal` are pre-registered).
- File browser disk — verify exact key (`jodit_disk` or `file_browser.disk`); read your published config.

## Idempotent rerun

Every step is safe to re-run. If user invokes the slash command again:

- Pre-flight finds tablar-kit already installed → skip step 1.
- Step 2 → no-op if config matches.
- Step 3 → grep skips if already mounted.
- Step 4 → grep skips if already imported.
- Step 5 → safe to re-run.
- Step 6 → always offer.

## Error recovery

| Symptom | Cause | Fix |
|---|---|---|
| `Unable to locate a class or view for component [confirm]` | Service provider not booted (cache stale). | `php artisan optimize:clear` + retry. |
| Modal opens but does nothing on confirm | CSRF token meta missing. | Add `<meta name="csrf-token" content="{{ csrf_token() }}">` to layout `<head>`. |
| Modal opens but other Bootstrap dropdowns stop working | Full `bootstrap` bundle imported elsewhere — DataAPI double-bound. | Search project for `import 'bootstrap'` or `from 'bootstrap'`; replace with individual file imports (`bootstrap/js/dist/dropdown` etc.). |
| `Cannot assign __PHP_Incomplete_Class` Carbon error on file browser | Laravel 11+ cache safeguard rejects deserialized Carbon objects. | Add Carbon to `config('cache.serializable_classes')` allowlist. See `tablar-kit-file-browser-development` skill. |

## Related

- Skill `tablar-kit-confirm-modal-development` — full confirm-modal API after install.
- Skill `tablar-kit-forms-development` — FormBuilder.
- Slash command `/laravel-boost:install-tablar-crud-generator` — next sibling.
- Source: `packages/takielias/tablar-kit/src/TablarKitServiceProvider.php`, `resources/js/plugins/confirm-modal.js`.

## Out of scope

- Installing tablar itself (require this first; aborted in pre-flight if missing).
- Wiring tablar-kit's Boost guidelines — `boost:update --discover` handles automatically after composer require.
- Configuring per-feature settings (file browser disk, FormBuilder defaults) — defer to the relevant `*-development` skills after install.
